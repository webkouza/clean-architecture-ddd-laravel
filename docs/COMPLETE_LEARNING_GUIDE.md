# 🏗️ 中学生でも分かるクリーンアーキテクチャ + DDD 完全学習ガイド

## 📚 目次

1. [はじめに](#はじめに)
2. [プロジェクト概要](#プロジェクト概要)
3. [基本概念の理解](#基本概念の理解)
4. [ディレクトリ構成](#ディレクトリ構成)
5. [各層の詳細解説](#各層の詳細解説)
6. [実際の処理の流れ](#実際の処理の流れ)
7. [ファイル間の繋がり](#ファイル間の繋がり)
8. [バリデーションの使い分け](#バリデーションの使い分け)
9. [インターフェースと依存性逆転](#インターフェースと依存性逆転)
10. [実践的な使い方](#実践的な使い方)
11. [まとめ](#まとめ)

---

## はじめに

このガイドは、中学生プログラマーでも理解できるように、クリーンアーキテクチャとドメイン駆動設計（DDD）をLaravelで実装する方法を詳しく説明します。

### 🎯 学習目標

- クリーンアーキテクチャの基本概念を理解する
- DDDの重要な要素（エンティティ、値オブジェクト、リポジトリ）を理解する
- 実際に動作するシステムを通じて各層の役割を学ぶ
- ファイル間の繋がりと依存関係を理解する

---

## プロジェクト概要

### 📖 作成したシステム

**図書館管理システム**を例に、以下の機能を実装しました：

- 本の登録
- バリデーション（値オブジェクト）
- ビジネスルール（エンティティ）
- データ永続化（リポジトリ）

### 🌐 アクセス方法

```
http://localhost:8080
```

### 🏗️ 使用技術

- **Laravel 12.x** (フレームワーク)
- **Laravel Sail** (Docker環境)
- **MySQL** (データベース)
- **Vue.js + Inertia.js** (フロントエンド)
- **Tailwind CSS** (スタイリング)

---

## 基本概念の理解

### 🏠 クリーンアーキテクチャとは

クリーンアーキテクチャは「家の構造」のようなものです：

```
🏠 家の例え：

4階（屋根）: コントローラー
   ↓ 「お客さんが来たよ！」と伝える
3階: ユースケース  
   ↓ 「じゃあ、本を登録しよう」と指示
2階: ドメイン（ビジネスルール）
   ↓ 「本のタイトルは空じゃダメ！」とチェック
1階（地下）: データベース
   「データを保存するよ」
```

### 🎯 重要なルール

**依存の向き**: 下の階は上の階を知らない！
- 2階（ドメイン）は、4階（コントローラー）の存在を知らない
- これが「依存性逆転の原則」です

### 😵 普通のプログラム vs クリーンアーキテクチャ

#### 悪い例（普通のプログラム）

```php
class BadBookController 
{
    public function registerBook($title, $author, $isbn)
    {
        // 😵 バリデーションがコントローラーに書いてある
        if (empty($title)) {
            return "タイトルが空です";
        }
        
        // 😵 データベース接続もコントローラーに書いてある
        $pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'pass');
        
        // 😵 重複チェックもコントローラーに書いてある
        $stmt = $pdo->prepare("SELECT * FROM books WHERE isbn = ?");
        $stmt->execute([$isbn]);
        if ($stmt->fetch()) {
            return "既に登録されています";
        }
        
        // 😵 保存処理もコントローラーに書いてある
        $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn) VALUES (?, ?, ?)");
        $stmt->execute([$title, $author, $isbn]);
        
        return "登録成功";
    }
}
```

**問題点:**
- 全部が1つのクラスに混ざっている
- テストできない（データベースがないとテストできない）
- 変更しにくい（データベースを変更したら、コントローラーも変更が必要）

#### 良い例（クリーンアーキテクチャ）

各責任を分離：
- **値オブジェクト**: バリデーション
- **エンティティ**: ビジネスルール
- **ユースケース**: 処理の流れ
- **リポジトリ**: データ保存
- **コントローラー**: HTTP処理

---

## ディレクトリ構成

### 📁 プロジェクト構造

```
app/
├── Domain/           ← 🏠 家の中心部（ビジネスの核心）
│   └── Book/
│       ├── Entity/          ← 📚 本そのもの
│       ├── ValueObject/     ← 🏷️ 本の属性（タイトル、著者など）
│       └── Repository/      ← 📋 本の保存方法の約束事
│
├── Application/      ← 🎯 家の管理人（やりたいことを実行）
│   └── Book/
│       └── UseCase/         ← 📝 「本を登録する」などの作業手順
│
├── Infrastructure/   ← 🔧 家の設備（データベース、外部サービス）
│   └── Book/
│       ├── Model/           ← 🗄️ データベースのテーブル設計
│       └── Repository/      ← 💾 実際のデータ保存処理
│
└── Http/            ← 🚪 家の玄関（外部との接点）
    └── Controllers/         ← 📞 外部からの要求を受け付ける
```

### 🎯 各層の役割

| 層 | 役割 | 特徴 | 例 |
|---|---|---|---|
| **Domain** | ビジネスルールとビジネス概念を定義 | フレームワークに依存しない、純粋なPHP | Book.php, Title.php |
| **Application** | ユースケース（やりたいこと）を実行 | ドメインオブジェクトを組み合わせて処理 | RegisterBookUseCase.php |
| **Infrastructure** | 外部システム（DB、API）との接続 | フレームワーク依存、技術的な詳細 | EloquentBookRepository.php |
| **Http** | 外部（ブラウザ、API）との接点 | HTTPリクエスト・レスポンスの処理 | BookController.php |

### 🔄 依存の向き（重要！）

```
Http → Application → Domain ← Infrastructure
                      ↑
                   すべてがここに依存
```

---

## 各層の詳細解説

### 📝 1. 値オブジェクト（Value Object）

#### 概念

値オブジェクトは「値そのもの」を表現します。例えば、本のタイトルは単なる文字列ではなく、「タイトル」という意味を持った値です。

#### 実装例: Title.php

```php
<?php
namespace App\Domain\Book\ValueObject;

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

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Title $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

#### 特徴

- **不変（Immutable）**: 一度作ったら変更できない
- **バリデーション**: コンストラクタで値をチェック
- **等価性**: 値が同じなら同じオブジェクト
- **意味の明確化**: 単なる文字列ではなく「タイトル」

#### 利点

```php
// ❌ 普通の書き方
$title = "ハリーポッター";  // ただの文字列
// バリデーションを毎回書く必要がある
if (empty($title)) { ... }
if (strlen($title) > 200) { ... }

// ✅ 値オブジェクトを使った書き方
$title = new Title("ハリーポッター");  // Titleオブジェクト
// バリデーションは自動で実行される！
// 不正な値でTitleオブジェクトは作れない
```

### 📚 2. エンティティ（Entity）

#### 概念

エンティティは「現実世界のモノ」をプログラムで表現したものです。

#### 特徴

1. **ID**を持つ（BookId）
2. **状態**を持つ（貸し出し中かどうか）
3. **行動**ができる（lend(), return()）

#### 実装例: Book.php

```php
<?php
namespace App\Domain\Book\Entity;

class Book
{
    private BookId $id;
    private Title $title;
    private Author $author;
    private ISBN $isbn;
    private bool $isAvailable;

    public function __construct(
        BookId $id,
        Title $title,
        Author $author,
        ISBN $isbn,
        bool $isAvailable = true
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->isbn = $isbn;
        $this->isAvailable = $isAvailable;
    }

    /**
     * 本を貸し出す（ビジネスルール）
     */
    public function lend(): void
    {
        if (!$this->isAvailable) {
            throw new \DomainException('この本は既に貸し出し中です');
        }
        
        $this->isAvailable = false;
    }

    /**
     * 本を返却する（ビジネスルール）
     */
    public function return(): void
    {
        if ($this->isAvailable) {
            throw new \DomainException('この本は既に返却済みです');
        }
        
        $this->isAvailable = true;
    }

    // ゲッター省略...
}
```

#### 重要なポイント

```php
// ❌ 悪い例：コントローラーでビジネスルール
if ($book->isAvailable) {
    $book->isAvailable = false;  // 直接変更
}

// ✅ 良い例：エンティティがビジネスルールを持つ
$book->lend();  // 「貸し出す」という行動
```

### 🎯 3. ユースケース（Use Case）

#### 概念

ユースケースは「アプリケーションでできること」を表現します。

#### 実装例: RegisterBookUseCase.php

```php
<?php
namespace App\Application\Book\UseCase;

class RegisterBookUseCase
{
    private BookRepositoryInterface $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function execute(RegisterBookCommand $command): RegisterBookResponse
    {
        // 1. 値オブジェクトを作成（バリデーション含む）
        $title = new Title($command->getTitle());
        $author = new Author($command->getAuthor());
        $isbn = new ISBN($command->getISBN());

        // 2. 重複チェック
        $existingBook = $this->bookRepository->findByISBN($isbn);
        if ($existingBook !== null) {
            throw new \DomainException('このISBNの本は既に登録されています');
        }

        // 3. 新しいIDを生成
        $bookId = new BookId($this->generateNewId());

        // 4. エンティティを作成
        $book = new Book($bookId, $title, $author, $isbn);

        // 5. 保存
        $this->bookRepository->save($book);

        // 6. レスポンスを返す
        return new RegisterBookResponse(
            $book->getId()->getValue(),
            $book->getTitle()->getValue(),
            $book->getAuthor()->getValue(),
            $book->getISBN()->getValue()
        );
    }
}
```

#### 役割

- ビジネスフローの制御
- ドメインオブジェクトの組み合わせ
- トランザクション制御

### 🔌 4. リポジトリ（Repository）

#### インターフェース（約束事）

```php
<?php
namespace App\Domain\Book\Repository;

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

#### 実装（EloquentBookRepository.php）

```php
<?php
namespace App\Infrastructure\Book\Repository;

class EloquentBookRepository implements BookRepositoryInterface
{
    public function save(Book $book): void
    {
        $model = BookModel::find($book->getId()->getValue());
        
        if ($model === null) {
            $model = new BookModel();
            $model->id = $book->getId()->getValue();
        }
        
        // ドメインエンティティからEloquentモデルへ変換
        $model->title = $book->getTitle()->getValue();
        $model->author = $book->getAuthor()->getValue();
        $model->isbn = $book->getISBN()->getValue();
        $model->is_available = $book->isAvailable();
        
        $model->save();
    }

    public function findByISBN(ISBN $isbn): ?Book
    {
        $model = BookModel::where('isbn', $isbn->getValue())->first();
        
        if ($model === null) {
            return null;
        }
        
        return $this->modelToEntity($model);
    }

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

### 🚪 5. コントローラー（Controller）

#### 実装例: BookController.php

```php
<?php
namespace App\Http\Controllers;

class BookController extends Controller
{
    private RegisterBookUseCase $registerBookUseCase;

    public function __construct(RegisterBookUseCase $registerBookUseCase)
    {
        $this->registerBookUseCase = $registerBookUseCase;
    }

    public function store(RegisterBookRequest $request): JsonResponse
    {
        try {
            // 1. HTTPリクエストからコマンドを作成
            $command = new RegisterBookCommand(
                $request->validated('title'),
                $request->validated('author'),
                $request->validated('isbn')
            );

            // 2. ユースケースを実行
            $response = $this->registerBookUseCase->execute($command);

            // 3. HTTPレスポンスを返す
            return response()->json([
                'success' => true,
                'message' => '本が正常に登録されました',
                'data' => $response->toArray()
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'バリデーションエラー',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
```

#### コントローラーの責任

**✅ やること:**
- HTTPリクエストの受け取り
- データの取り出し
- ユースケースの呼び出し
- HTTPレスポンスの返却

**❌ やらないこと:**
- ビジネスロジック
- データベース操作
- 複雑な計算

---

## 実際の処理の流れ

### 🎬 「ハリーポッター」を登録する場合

#### ユーザーの操作

1. タイトル: 「ハリーポッター」
2. 著者: 「J.K.ローリング」
3. ISBN: 「9784915512377」
4. 「登録」ボタンをクリック！

#### システムの処理

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ステップ1: ブラウザ → サーバーへ送信
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
送信内容:
{
  "title": "ハリーポッター",
  "author": "J.K.ローリング",
  "isbn": "9784915512377"
}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ステップ2: BookController.php が受け取る
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
処理内容:
1. HTTPリクエストを受け取る
2. データを取り出す
3. RegisterBookCommandを作る
4. RegisterBookUseCaseに渡す

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ステップ3: RegisterBookUseCase.php が処理
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
処理内容:
1. 値オブジェクトを作る
   - new Title('ハリーポッター')
   - new Author('J.K.ローリング')
   - new ISBN('9784915512377')
2. 重複チェック
3. Bookエンティティを作る
4. リポジトリで保存

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ステップ4: Title.php がバリデーション
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
処理内容:
1. 空文字列じゃないかチェック → OK
2. 200文字以内かチェック → OK
3. 前後の空白を削除
4. Titleオブジェクト完成！

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ステップ5: Book.php でエンティティ作成
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
処理内容:
1. 値オブジェクトを組み合わせる
2. 初期状態を「利用可能」に設定
3. Bookオブジェクト完成！

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ステップ6: EloquentBookRepository.php で保存
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
処理内容:
1. BookエンティティをEloquentモデルに変換
2. データベースに保存
   INSERT INTO books (title, author, isbn, is_available)
   VALUES ('ハリーポッター', 'J.K.ローリング', '9784915512377', true)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ステップ7: 結果を返す
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
逆順で結果が返っていく:
リポジトリ → ユースケース → コントローラー → ブラウザ
```

#### 最終結果

```json
{
  "success": true,
  "message": "本が正常に登録されました",
  "data": {
    "id": 1,
    "title": "ハリーポッター",
    "author": "J.K.ローリング",
    "isbn": "9784915512377"
  }
}
```

---

## ファイル間の繋がり

### 🔗 繋がり方の種類

#### 1. use文での繋がり

```php
// BookController.php の上部
use App\Application\Book\UseCase\RegisterBookUseCase;
// ↑ これで RegisterBookUseCase を使えるようになる

// RegisterBookUseCase.php の上部
use App\Domain\Book\ValueObject\Title;
use App\Domain\Book\Entity\Book;
// ↑ これで Title や Book を使えるようになる
```

#### 2. コンストラクタでの繋がり

```php
// BookController.php
public function __construct(RegisterBookUseCase $useCase) {
    $this->useCase = $useCase;  // ← Laravelが自動で渡してくれる
}

// RegisterBookUseCase.php
public function __construct(BookRepositoryInterface $repo) {
    $this->repository = $repo;  // ← Laravelが自動で渡してくれる
}
```

#### 3. AppServiceProvider.php での設定

```php
// app/Providers/AppServiceProvider.php
$this->app->bind(
    BookRepositoryInterface::class,     // ← インターフェース
    EloquentBookRepository::class       // ← 実際の実装
);
// ↑ 「BookRepositoryInterfaceが必要な時は
//    EloquentBookRepositoryを使って」という設定
```

### 📊 処理の流れ図

```
1️⃣ BookController.php が呼ばれる
┌─────────────────────────────────────┐
│ app/Http/Controllers/BookController.php │
│                                         │
│ public function store($request) {       │
│   $command = new RegisterBookCommand(); │ ← コマンド作成
│   $this->useCase->execute($command);   │ ← ユースケース呼び出し
│ }                                       │
└─────────────────────────────────────┘
                    ↓ $command を渡す

2️⃣ RegisterBookUseCase.php が呼ばれる
┌─────────────────────────────────────────┐
│ app/Application/Book/UseCase/            │
│ RegisterBookUseCase.php                  │
│                                         │
│ public function execute($command) {     │
│   $title = new Title($command->title); │ ← 値オブジェクト作成
│   $book = new Book($title, ...);       │ ← エンティティ作成
│   $this->repository->save($book);      │ ← リポジトリ呼び出し
│ }                                       │
└─────────────────────────────────────────┘
         ↓ new Title()          ↓ $this->repository->save()

3️⃣ Title.php が呼ばれる        4️⃣ EloquentBookRepository.php が呼ばれる
┌─────────────────────┐    ┌─────────────────────────────────┐
│ app/Domain/Book/     │    │ app/Infrastructure/Book/        │
│ ValueObject/Title.php│    │ Repository/                     │
│                     │    │ EloquentBookRepository.php      │
│ public function     │    │                                 │
│ __construct($value) {│    │ public function save($book) {   │
│   if (empty($value)) │    │   $model = new BookModel();    │
│     throw Error;    │    │   $model->title = $book->...;  │
│   $this->value =    │    │   $model->save();              │
│     $value;         │    │ }                               │
│ }                   │    │                                 │
└─────────────────────┘    └─────────────────────────────────┘
         ↓ バリデーション              ↓ データベース保存

5️⃣ Book.php が呼ばれる
┌─────────────────────────────────────┐
│ app/Domain/Book/Entity/Book.php     │
│                                     │
│ public function __construct(        │
│   $id, $title, $author, $isbn     │
│ ) {                                 │
│   $this->id = $id;                 │
│   $this->title = $title;           │
│   $this->isAvailable = true;       │ ← 初期状態設定
│ }                                   │
└─────────────────────────────────────┘
```

---

## バリデーションの使い分け

### 📝 FormRequest vs 値オブジェクト

#### FormRequest（従来のLaravelバリデーション）

**場所**: `app/Http/Requests/`
**層**: プレゼンテーション層（HTTP層）
**責任**: HTTPリクエストの形式チェック

```php
class RegisterBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'author' => 'required|string|max:100', 
            'isbn' => 'required|string|regex:/^[0-9]{10}$|^[0-9]{13}$/',
        ];
    }
}
```

**チェック内容:**
- ✓ 必須項目が送られてきたか
- ✓ データ型は正しいか（文字列、数値など）
- ✓ 文字数制限
- ✓ 正規表現パターン

**特徴:**
- ✓ Laravelフレームワークに依存
- ✓ HTTPリクエストでしか使えない
- ✓ 自動でエラーレスポンスを返す
- ✗ ビジネスルールはチェックできない
- ✗ 他のプロジェクトで再利用できない

#### 値オブジェクト（DDDのバリデーション）

**場所**: `app/Domain/Book/ValueObject/`
**層**: ドメイン層（ビジネス層）
**責任**: ビジネスルールの検証

```php
class Title
{
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

**チェック内容:**
- ✓ ビジネス的に正しい値か
- ✓ ドメインの制約を満たすか
- ✓ 値の意味が正しいか

**特徴:**
- ✓ フレームワークに依存しない
- ✓ どこからでも使える（CLI、バッチ処理など）
- ✓ ビジネスルールを表現できる
- ✓ 他のプロジェクトで再利用できる
- ✗ 自動エラーレスポンスはない

### 🎯 推奨される構成

**両方使う！**

```
HTTPリクエスト
  ↓
FormRequest（形式チェック）
  ↓
Controller
  ↓
UseCase
  ↓
値オブジェクト（ビジネスルールチェック）
  ↓
Entity
```

**メリット:**
- ✓ 二重チェックで安全
- ✓ 責任が明確
- ✓ どちらも再利用可能

---

## インターフェースと依存性逆転

### 🔌 現実世界の例：電化製品とコンセント

#### 問題

あなたは掃除機を使いたいです。でも、家によって電源の形が違ったらどうしますか？

#### 悪い例：掃除機が特定の家に依存

```
┌─────────────────┐
│ 田中家専用掃除機     │
│ ・田中家でしか使えない │
│ ・佐藤家では使えない   │
└─────────────────┘
        ↓ 直接接続
┌─────────────────┐
│ 田中家の電源コンセント │
└─────────────────┘
```

**問題点:**
- 掃除機が特定の家に依存している
- 他の家では使えない
- 家を変えるたびに掃除機も変える必要がある

#### 良い例：標準コンセント（インターフェース）

```
┌─────────────────┐
│ どこでも使える掃除機   │
│ ・田中家でも使える     │
│ ・佐藤家でも使える     │
└─────────────────┘
        ↓ 標準プラグで接続
┌─────────────────┐
│ 標準コンセント         │ ← これが「インターフェース」
│ （どの家でも同じ形）   │
└─────────────────┘
        ↓ 各家で実装
┌─────────────────┐   ┌─────────────────┐
│ 田中家の電源システム   │   │ 佐藤家の電源システム   │
│ （東京電力）           │   │ （関西電力）           │
└─────────────────┘   └─────────────────┘
```

**利点:**
- ✓ 掃除機はどの家でも使える
- ✓ 電力会社が変わっても掃除機は変更不要
- ✓ 新しい家でもすぐに使える

### 💻 プログラムでの実装

#### 悪い例：直接依存

```php
class BadRegisterBookUseCase 
{
    private MySQLBookRepository $repository;  // ← MySQLに直接依存！
    
    public function __construct(MySQLBookRepository $repository) 
    {
        $this->repository = $repository;
    }
}
```

**問題点:**
1. ユースケースがMySQLに「べったり依存」
2. データベースを変更するとユースケースも変更が必要
3. テストが困難（MySQLがないとテストできない）
4. 柔軟性がない

#### 良い例：インターフェースを使用

```php
// 1. まず「約束事」を決める（インターフェース）
interface BookRepositoryInterface 
{
    public function save($title, $author, $isbn): void;
}

// 2. ユースケースは「約束事」に依存する
class GoodRegisterBookUseCase 
{
    private BookRepositoryInterface $repository;  // ← インターフェースに依存！
    
    public function __construct(BookRepositoryInterface $repository) 
    {
        $this->repository = $repository;
    }
}

// 3. MySQL用の実装
class MySQLRepository implements BookRepositoryInterface 
{
    public function save($title, $author, $isbn): void 
    {
        echo "MySQLに保存: $title by $author\n";
    }
}

// 4. PostgreSQL用の実装
class PostgreSQLRepository implements BookRepositoryInterface 
{
    public function save($title, $author, $isbn): void 
    {
        echo "PostgreSQLに保存: $title by $author\n";
    }
}

// 5. テスト用の実装（モック）
class MockRepository implements BookRepositoryInterface 
{
    public function save($title, $author, $isbn): void 
    {
        echo "テスト用保存: $title by $author\n";
    }
}
```

### 🎯 依存性注入（Dependency Injection）

#### 問題

インターフェースと実装があっても、「どの実装を使うか」を決める必要がある

#### 解決策：外部から注入する

```php
// AppServiceProvider.php で設定
$this->app->bind(
    BookRepositoryInterface::class,  // インターフェース
    EloquentBookRepository::class    // 実装
);

// Laravelが自動で注入
public function __construct(RegisterBookUseCase $useCase) {
    // Laravelが自動で以下を実行：
    // 1. BookRepositoryInterfaceが必要
    // 2. 設定を見る → EloquentBookRepositoryを使う
    // 3. EloquentBookRepositoryを作成
    // 4. RegisterBookUseCaseに渡す
    // 5. コントローラーに渡す
}
```

#### 環境別の設定例

```php
// 本番環境
$this->app->bind(
    BookRepositoryInterface::class,
    EloquentBookRepository::class  // MySQL使用
);

// テスト環境
$this->app->bind(
    BookRepositoryInterface::class,
    InMemoryBookRepository::class  // メモリ使用
);

// 開発環境
$this->app->bind(
    BookRepositoryInterface::class,
    FileBookRepository::class      // ファイル使用
);
```

### 🎉 インターフェースの利点

- ✓ 設定ファイルを変更するだけで実装を切り替え可能
- ✓ コードを変更せずに環境別の設定が可能
- ✓ テスト時は自動でテスト用実装を使用
- ✓ 新しい実装を追加しても既存コードは変更不要

---

## 実践的な使い方

### 🎮 実際に動かして確認

#### 手順

1. ブラウザで `http://localhost:8080` を開く
2. 以下の情報を入力:
   - タイトル: デバッグ本
   - 著者: テスト著者
   - ISBN: 1234567890123
3. 「本を登録」ボタンを押す

#### 期待される結果

```json
{
  "success": true,
  "message": "本が正常に登録されました",
  "data": {
    "id": (自動生成されたID),
    "title": "デバッグ本",
    "author": "テスト著者",
    "isbn": "1234567890123"
  }
}
```

#### エラーテストも試してみよう

**テスト1: 空のタイトル**
- → Title.phpでエラー
- → 'タイトルは空にできません'

**テスト2: 同じISBNで再登録**
- → RegisterBookUseCase.phpでエラー
- → 'このISBNの本は既に登録されています'

### 🧪 テストの実行

#### ドメイン層のテスト

```bash
./vendor/bin/sail test tests/Unit/Domain/Book/Entity/BookTest.php
```

**結果:**
```
✓ 本を作成できる
✓ 本を貸し出しできる
✓ 貸し出し中の本は再度貸し出しできない
✓ 本を返却できる
✓ 利用可能な本は返却できない

Tests: 5 passed (11 assertions)
```

### 🎯 理解度チェック

**Q1: 「本を登録」ボタンを押すと、最初に呼ばれるファイルは？**
A1: BookController.php

**Q2: バリデーション（空文字チェック）はどのファイルで行われる？**
A2: Title.php, Author.php, ISBN.php（値オブジェクト）

**Q3: データベースに保存するのはどのファイル？**
A3: EloquentBookRepository.php

**Q4: ビジネスルール（重複チェック）はどのファイルで行われる？**
A4: RegisterBookUseCase.php

---

## まとめ

### 🎓 学習した内容

#### 1. クリーンアーキテクチャの基本構造

```
外側 ← 依存の向き ← 内側

┌─────────────────────────────────────┐
│ インフラ層 (Infrastructure)          │
│ - EloquentBookRepository            │
│ - BookModel                         │
│ - データベース、外部API              │
└─────────────────────────────────────┘
           ↑ 依存
┌─────────────────────────────────────┐
│ プレゼンテーション層                 │
│ - BookController                    │
│ - HTTPリクエスト/レスポンス          │
└─────────────────────────────────────┘
           ↑ 依存
┌─────────────────────────────────────┐
│ アプリケーション層                   │
│ - RegisterBookUseCase               │
│ - Command/Response                  │
└─────────────────────────────────────┘
           ↑ 依存
┌─────────────────────────────────────┐
│ ドメイン層 (Domain) ← 中心！         │
│ - Book (エンティティ)                │
│ - BookId, Title (値オブジェクト)     │
│ - BookRepositoryInterface           │
└─────────────────────────────────────┘
```

#### 2. 依存性逆転の原則

**従来の設計（悪い例）:**
```
コントローラー → サービス → リポジトリ実装 → データベース
```

**クリーンアーキテクチャ（良い例）:**
```
コントローラー → ユースケース → リポジトリインターフェース ← リポジトリ実装
                                      ↑                    ↓
                                 ドメイン層              インフラ層
```

#### 3. DDDの重要な概念

| 概念 | 特徴 | 例 | 役割 |
|---|---|---|---|
| **エンティティ** | 一意のIDを持つ、ライフサイクルがある | `Book` クラス | ビジネスルールを持つ |
| **値オブジェクト** | 値そのものを表現、不変、等価性で比較 | `Title`, `Author`, `ISBN`, `BookId` | バリデーションとドメインの表現力向上 |
| **リポジトリ** | データの永続化を抽象化 | `BookRepositoryInterface` | ドメインオブジェクトの保存・取得 |
| **ユースケース** | アプリケーションでできることを表現 | `RegisterBookUseCase` | ビジネスフローの制御 |

#### 4. 各層の責任

**ドメイン層**
```php
// ✅ やること
- ビジネスルールの実装
- エンティティと値オブジェクトの定義
- インターフェースの定義

// ❌ やらないこと
- データベースアクセス
- HTTPの処理
- フレームワーク依存の処理
```

**アプリケーション層**
```php
// ✅ やること
- ユースケースの実装
- ドメインオブジェクトの組み合わせ
- トランザクション制御

// ❌ やらないこと
- HTTPの詳細処理
- データベースの詳細
- ビジネスルールの実装
```

**プレゼンテーション層**
```php
// ✅ やること
- HTTPリクエストの受け取り
- レスポンスの返却
- 入力値の変換

// ❌ やらないこと
- ビジネスロジック
- データベースアクセス
- 複雑な計算
```

**インフラ層**
```php
// ✅ やること
- データベースアクセス
- 外部APIとの通信
- ファイルシステムアクセス

// ❌ やらないこと
- ビジネスルール
- アプリケーションフロー
```

### 🎯 このアーキテクチャの利点

#### 変更容易性
- データベースをMySQLからPostgreSQLに変更 → インフラ層のみ変更
- WebからCLIアプリに変更 → プレゼンテーション層のみ変更

#### テスト容易性
- ドメイン層 → フレームワーク不要でテスト
- ユースケース → モックで外部依存を排除

#### 理解しやすさ
- ビジネスルールがドメイン層に集約
- 各層の責任が明確

### 🚀 次のステップ

1. **集約 (Aggregate)** の概念を学ぶ
2. **ドメインサービス** を実装する
3. **ドメインイベント** を追加する
4. **CQRS** パターンを適用する
5. **バウンデッドコンテキスト** を設計する

### 🎉 おめでとうございます！

あなたは今、**実務レベルのクリーンアーキテクチャ + DDD**を理解し、実装できるようになりました！

- ✅ 値オブジェクトによるバリデーション
- ✅ エンティティによるビジネスルール
- ✅ 依存性逆転による柔軟性
- ✅ レイヤー分離による保守性

これらの知識を活かして、より良いソフトウェアを作っていきましょう！

---

## 参考資料

### 📚 推奨書籍

- 「クリーンアーキテクチャ」by ロバート・C・マーチン
- 「ドメイン駆動設計」by エリック・エヴァンス
- 「実践ドメイン駆動設計」by ヴァーン・ヴァーノン

### 🔗 関連リンク

- [Laravel公式ドキュメント](https://laravel.com/docs)
- [クリーンアーキテクチャ概要](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)

---

*このガイドは、中学生プログラマーでも理解できるように作成されました。質問や不明な点があれば、いつでもお聞きください！*
