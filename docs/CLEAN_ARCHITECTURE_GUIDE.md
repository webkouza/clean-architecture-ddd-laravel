# クリーンアーキテクチャ + DDD を中学生でも理解できるガイド

このプロジェクトのコードを使って、一つひとつ丁寧に説明します。

---

## まず「なぜこんな設計が必要なの？」から

### 普通に書くとこうなる（Fatコントローラー）

```php
// ❌ 全部コントローラーに書いてしまった例
class BookController extends Controller
{
    public function store(Request $request)
    {
        // バリデーション
        if (empty($request->title)) { return response()->json(['error' => 'タイトルが空'], 400); }
        if (strlen($request->isbn) !== 13) { return response()->json(['error' => 'ISBNが不正'], 400); }

        // 重複チェック（DBに直接アクセス）
        $existing = DB::table('books')->where('isbn', $request->isbn)->first();
        if ($existing) { return response()->json(['error' => '重複'], 422); }

        // 保存
        DB::table('books')->insert([...]);

        return response()->json(['success' => true]);
    }
}
```

これの何が問題？

- コントローラーが長くなって読みにくい
- 同じバリデーションを別の場所でも書くことになる
- テストが書きにくい
- 「ISBNのルールが変わった」ときに、どこを直せばいいか分からなくなる

---

## このプロジェクトの構造

```
リクエスト（ブラウザ）
    ↓
┌─────────────────────────────────────────┐
│ Http層（外の世界との窓口）               │
│   BookController.php                    │
│   RegisterBookRequest.php               │
└─────────────────┬───────────────────────┘
                  ↓ コマンドを渡す
┌─────────────────────────────────────────┐
│ Application層（やることの手順書）        │
│   RegisterBookUseCase.php               │
│   RegisterBookCommand.php               │
│   RegisterBookResponse.php              │
└─────────────────┬───────────────────────┘
                  ↓ ドメインオブジェクトを使う
┌─────────────────────────────────────────┐
│ Domain層（ビジネスの核心）               │
│   Book.php（エンティティ）              │
│   Title.php / Author.php / ISBN.php     │
│   BookRepositoryInterface.php           │
└─────────────────┬───────────────────────┘
                  ↓ インターフェース経由
┌─────────────────────────────────────────┐
│ Infrastructure層（技術的な実装）         │
│   EloquentBookRepository.php            │
│   BookModel.php                         │
└─────────────────────────────────────────┘
```

矢印の向きが大事です。上から下への一方通行です。
Domain層は誰にも依存していません。これが「クリーン」の意味です。

---

## 各ファイルの役割を実際のコードで理解する

---

### 1. 値オブジェクト（ValueObject）
**「正しい値しか存在できない」という仕組み**

`app/Domain/Book/ValueObject/Title.php`

```php
class Title
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw new \InvalidArgumentException('タイトルは空にできません');
        }

        if (mb_strlen($trimmed) > 200) {
            throw new \InvalidArgumentException('タイトルは200文字以内で入力してください');
        }

        $this->value = $trimmed;
    }
}
```

ポイント：
- `new Title('')` とすると即エラーになる
- つまり「Titleオブジェクトが存在している = 正しいタイトルである」が保証される
- バリデーションのルールがここに集まっている

`app/Domain/Book/ValueObject/ISBN.php` はもう少し複雑です。

```php
class ISBN
{
    public function __construct(string $value)
    {
        // ハイフンや空白を除去して数字だけにする
        $cleaned = preg_replace('/[^0-9X]/', '', strtoupper($value));

        // 10桁か13桁でないとエラー
        if (!$this->isValidISBN($cleaned)) {
            throw new \InvalidArgumentException('有効なISBNを入力してください');
        }

        $this->value = $cleaned;
    }
}
```

ISBNのルールが変わったら、このファイルだけ直せばOKです。

---

### 2. エンティティ（Entity）
**「IDを持ち、ビジネスのルールを知っているオブジェクト」**

`app/Domain/Book/Entity/Book.php`

```php
class Book
{
    private BookId $id;
    private Title $title;    // ← 値オブジェクトを使っている
    private Author $author;  // ← 値オブジェクトを使っている
    private ISBN $isbn;      // ← 値オブジェクトを使っている
    private bool $isAvailable;

    // 本を貸し出す（ビジネスルール）
    public function lend(): void
    {
        if (!$this->isAvailable) {
            throw new \DomainException('この本は既に貸し出し中です');
        }
        $this->isAvailable = false;
    }

    // 本を返却する（ビジネスルール）
    public function return(): void
    {
        if ($this->isAvailable) {
            throw new \DomainException('この本は既に返却済みです');
        }
        $this->isAvailable = true;
    }
}
```

ポイント：
- `lend()`（貸し出し）と `return()`（返却）というビジネスのルールがここにある
- 「貸し出し中の本をさらに貸し出そうとしたらエラー」というルールが自然に書ける
- DBのことは何も知らない。純粋にビジネスのことだけ考えている

---

### 3. リポジトリインターフェース（Repository Interface）
**「保存・取得の約束事だけ書いた設計図」**

`app/Domain/Book/Repository/BookRepositoryInterface.php`

```php
interface BookRepositoryInterface
{
    public function save(Book $book): void;
    public function findById(BookId $id): ?Book;
    public function findByISBN(ISBN $isbn): ?Book;
    public function findAvailableBooks(): array;
    public function findAll(): array;
    public function delete(BookId $id): void;
}
```

ポイント：
- これは「設計図」であり「約束」です。実装は書いていない
- Domain層にあるのに、DBのことを何も書いていない
- 「MySQLで保存しようが、PostgreSQLで保存しようが、このメソッドが使えれば何でもいい」という意味

---

### 4. ユースケース（UseCase）
**「1つの機能の手順書」**

`app/Application/Book/UseCase/RegisterBookUseCase.php`

```php
class RegisterBookUseCase
{
    public function execute(RegisterBookCommand $command): RegisterBookResponse
    {
        // 手順1: 値オブジェクトを作る（ここでバリデーションも走る）
        $title = new Title($command->getTitle());
        $author = new Author($command->getAuthor());
        $isbn = new ISBN($command->getISBN());

        // 手順2: 同じISBNの本がすでにないか確認
        $existingBook = $this->bookRepository->findByISBN($isbn);
        if ($existingBook !== null) {
            throw new \DomainException('このISBNの本は既に登録されています');
        }

        // 手順3: IDを生成してエンティティを作る
        $bookId = new BookId($this->generateNewId());
        $book = new Book($bookId, $title, $author, $isbn);

        // 手順4: 保存する
        $this->bookRepository->save($book);

        // 手順5: 結果を返す
        return new RegisterBookResponse(
            $book->getId()->getValue(),
            $book->getTitle()->getValue(),
            $book->getAuthor()->getValue(),
            $book->getISBN()->getValue()
        );
    }
}
```

ポイント：
- 「本を登録する」という1つの機能の手順が全部ここに書いてある
- HTTPのことは知らない（`$request` が出てこない）
- DBの詳細も知らない（`BookModel::create()` が出てこない）
- `RegisterBookCommand` という入力を受け取り、`RegisterBookResponse` という出力を返す

---

### 5. コマンドとレスポンス（Command / Response）
**「ユースケースへの入力と出力の入れ物」**

`app/Application/Book/UseCase/RegisterBookCommand.php`

```php
class RegisterBookCommand
{
    public function __construct(
        private string $title,
        private string $author,
        private string $isbn
    ) {}

    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getISBN(): string { return $this->isbn; }
}
```

`app/Application/Book/UseCase/RegisterBookResponse.php`

```php
class RegisterBookResponse
{
    public function toArray(): array
    {
        return [
            'id'     => $this->id,
            'title'  => $this->title,
            'author' => $this->author,
            'isbn'   => $this->isbn,
        ];
    }
}
```

ポイント：
- CommandはHTTPリクエストの知識を持たない純粋なデータの入れ物
- ResponseはJSONの知識を持たない純粋なデータの入れ物
- これにより、ユースケースはWebからでもCLIからでも呼び出せる

---

### 6. リポジトリの実装（Infrastructure）
**「インターフェースの約束を実際に実現する」**

`app/Infrastructure/Book/Repository/EloquentBookRepository.php`

```php
class EloquentBookRepository implements BookRepositoryInterface
{
    public function save(Book $book): void
    {
        $model = BookModel::find($book->getId()->getValue());

        if ($model === null) {
            $model = new BookModel();
            $model->id = $book->getId()->getValue();
        }

        // ドメインエンティティ → Eloquentモデルへ変換
        $model->title        = $book->getTitle()->getValue();
        $model->author       = $book->getAuthor()->getValue();
        $model->isbn         = $book->getISBN()->getValue();
        $model->is_available = $book->isAvailable();

        $model->save();
    }

    // Eloquentモデル → ドメインエンティティへ変換
    private function modelToEntity(BookModel $model): Book
    {
        return new Book(
            new BookId($model->id),
            new Title($model->title),
            new Author($model->author),
            new ISBN($model->isbn),
            $model->is_available
        );
    }
}
```

ポイント：
- `implements BookRepositoryInterface` でインターフェースの約束を守っている
- DBから取得したデータをドメインエンティティに変換している（`modelToEntity`）
- ここだけがEloquentを知っている。他の層はEloquentを知らない

---

### 7. コントローラー（Controller）
**「外の世界とアプリをつなぐ薄い窓口」**

`app/Http/Controllers/BookController.php`

```php
class BookController extends Controller
{
    public function __construct(
        private RegisterBookUseCase $registerBookUseCase
    ) {}

    public function store(RegisterBookRequest $request): JsonResponse
    {
        try {
            // HTTPリクエスト → コマンドに変換
            $command = new RegisterBookCommand(
                $request->validated('title'),
                $request->validated('author'),
                $request->validated('isbn')
            );

            // ユースケースを呼ぶだけ
            $response = $this->registerBookUseCase->execute($command);

            // レスポンスをHTTPレスポンスに変換
            return response()->json([
                'success' => true,
                'data'    => $response->toArray()
            ], 201);

        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
```

ポイント：
- ビジネスロジックが一切ない
- やっていることは「変換して渡す」だけ
- エラーハンドリングもここで行う

---

### 8. 依存性の注入（AppServiceProvider）
**「インターフェースと実装を結びつける接着剤」**

`app/Providers/AppServiceProvider.php`

```php
$this->app->bind(
    BookRepositoryInterface::class,   // インターフェース（約束）
    EloquentBookRepository::class     // 実装（実際の処理）
);
```

ポイント：
- ユースケースは `BookRepositoryInterface` しか知らない
- Laravelが「インターフェースが必要なら `EloquentBookRepository` を渡す」と決めている
- ここを変えるだけで、DBをMySQLからMongoDBに変えることができる

---

## データの流れを追ってみよう

「本を登録する」ボタンを押したときの流れです。

```
① ブラウザ → POST /books { title: "PHP入門", author: "山田", isbn: "9784123456789" }

② RegisterBookRequest がバリデーション（空でないか、文字数など）

③ BookController::store() が呼ばれる
   → RegisterBookCommand を作る

④ RegisterBookUseCase::execute() が呼ばれる
   → new Title("PHP入門")    ← 値オブジェクト作成（200文字以内チェック）
   → new Author("山田")      ← 値オブジェクト作成（100文字以内チェック）
   → new ISBN("9784123456789") ← 値オブジェクト作成（13桁チェック）
   → findByISBN() で重複チェック
   → new Book(...) でエンティティ作成
   → save() で保存

⑤ EloquentBookRepository::save() が呼ばれる
   → BookModel を使ってDBに保存

⑥ RegisterBookResponse が返ってくる

⑦ コントローラーが JSON レスポンスを返す

⑧ ブラウザに「登録成功」が表示される
```

---

## まとめ：各層が「知っていること」と「知らないこと」

| ファイル | 知っていること | 知らないこと |
|---|---|---|
| `BookController` | HTTP、リクエスト、レスポンス | DB、ビジネスルール |
| `RegisterBookUseCase` | 登録の手順、ドメインオブジェクト | HTTP、DB |
| `Book`（エンティティ） | 貸し出し・返却のルール | HTTP、DB |
| `Title` / `ISBN` など | 値の正しさのルール | HTTP、DB、他のクラス |
| `BookRepositoryInterface` | 保存・取得の約束 | DBの種類、HTTP |
| `EloquentBookRepository` | Eloquent、DB操作 | HTTP、ビジネスルール |

この「知らないこと」を守ることが、クリーンアーキテクチャの本質です。

---

## よくある疑問

**Q. なぜFormRequestでバリデーションしているのに、値オブジェクトでもバリデーションするの？**

FormRequestはHTTPの世界のバリデーションです。値オブジェクトはドメインの世界のバリデーションです。
ユースケースはHTTPを知らないので、FormRequestに頼れません。
値オブジェクトがあれば、CLIやテストからユースケースを呼んでも安全です。

**Q. BookModelとBook（エンティティ）の違いは？**

`BookModel` はDBとやり取りするためのEloquentのクラスです。
`Book` はビジネスのルール（貸し出し・返却）を持つクラスです。
DBの都合とビジネスの都合を分けることで、どちらも変更しやすくなります。

**Q. AppServiceProviderのbindって何をしているの？**

ユースケースのコンストラクタには `BookRepositoryInterface` と書いてあります。
でもインターフェースは実体がないので、Laravelが「じゃあ `EloquentBookRepository` を渡すよ」と自動的に解決してくれます。
これを「依存性の注入（DI）」と言います。
