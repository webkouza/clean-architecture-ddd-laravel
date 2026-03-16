# リファクタリングメモ

## 2026-03-16 Fat コントローラー → サービス移行

### 課題

Fat コントローラーのロジックをサービスに移動する際、リファクタリングが失敗した場合にメソッド単位で元に戻せる方法が必要。

---

### 検討した方法

#### 1. トレイト（現状）

旧ロジックをトレイトに逃がしている状態。

**デメリット**
- どこに何があるか分散して追いにくい
- 移行期間が終わったら削除が必要

---

#### 2. Git ブランチ戦略（一番確実）

```bash
# リファクタリング前にブランチを切る
git checkout -b refactor/move-to-service

# メソッドごとにコミット
git add app/Services/BookService.php
git commit -m "feat: BookService に createBook メソッドを移動"

git add app/Http/Controllers/BookController.php
git commit -m "refactor: BookController から createBook を削除"
```

メソッド単位でコミットしておけば、特定のコミットだけ戻せる。

```bash
# 特定のコミットだけ元に戻す
git revert <commit-hash>

# 特定ファイルの特定コミット時点に戻す
git checkout <commit-hash> -- app/Http/Controllers/BookController.php
```

---

#### 3. Feature Flag パターン（採用）

同じコントローラーファイルには残したくないので、別ファイルに切り出す構成。

**ディレクトリ構成**

```
app/Http/Controllers/
├── BookController.php          ← 新しいサービスを使う
└── Legacy/
    └── BookControllerLegacy.php  ← 旧ロジックを退避
```

**BookControllerLegacy.php（旧ロジックを退避）**

```php
namespace App\Http\Controllers\Legacy;

class BookControllerLegacy
{
    public function create(Request $request)
    {
        // 旧コード
    }

    public function update(Request $request, int $id)
    {
        // 旧コード
    }
}
```

**BookController.php（スッキリ）**

```php
namespace App\Http\Controllers;

use App\Http\Controllers\Legacy\BookControllerLegacy;
use App\Services\BookService;

class BookController extends Controller
{
    public function __construct(
        private BookService $bookService,
        private BookControllerLegacy $legacy,
    ) {}

    public function store(Request $request)
    {
        if (config('features.use_book_service')) {
            return $this->bookService->create($request);
        }

        return $this->legacy->create($request);
    }
}
```

**config/features.php**

```php
return [
    'use_book_service' => env('USE_BOOK_SERVICE', true),
];
```

**.env**

```env
USE_BOOK_SERVICE=true
```

**問題が起きた時の戻し方**

```env
# .env を 1 行変えるだけで即座に旧ロジックに戻る
USE_BOOK_SERVICE=false
```

```bash
php artisan config:clear
```

---

#### 4. Decorator パターン

```php
interface BookServiceInterface
{
    public function create(array $data): Book;
}

class BookService implements BookServiceInterface
{
    public function create(array $data): Book { ... }
}

class LegacyBookService implements BookServiceInterface
{
    public function create(array $data): Book {
        // 元のコントローラーのロジック
    }
}
```

`AppServiceProvider` のバインドを変えるだけで切り替えられる。

```php
// 新しい実装
$this->app->bind(BookServiceInterface::class, BookService::class);

// 問題が起きたら 1 行変えるだけで戻せる
$this->app->bind(BookServiceInterface::class, LegacyBookService::class);
```

---

### まとめ

| 状況 | おすすめ |
|------|---------|
| 開発中のリファクタリング | Git ブランチ + メソッド単位コミット |
| 本番環境で段階的に移行 | Feature Flag |
| チーム開発で安全に切り替えたい | Decorator パターン |

**採用方針**: Feature Flag パターン（旧ロジックは `Legacy/` ディレクトリに退避）

---

## 2026-03-16 コントローラーを汚さない Feature Flag

### 課題

- リファクタリング済みのコントローラーに旧ロジックを混在させたくない
- Git は メンバーにとって敷居が高い

### 採用方針

**コントローラーは一切触らず、サービスの中だけで切り替える。**

**ディレクトリ構成**

```
app/Services/
├── BookService.php           ← フラグで切り替えるだけ
└── Legacy/
    └── LegacyBookService.php ← 旧ロジックをここに退避
```

**LegacyBookService.php（旧ロジックを退避）**

```php
namespace App\Services\Legacy;

class LegacyBookService
{
    public function create(array $data): Book
    {
        // 元の旧ロジック
    }
}
```

**BookService.php（コントローラーは一切変更不要）**

```php
namespace App\Services;

use App\Services\Legacy\LegacyBookService;

class BookService
{
    public function __construct(
        private LegacyBookService $legacy,
    ) {}

    public function create(array $data): Book
    {
        if (config('features.use_new_create')) {
            return $this->newCreate($data);  // 新ロジック
        }

        return $this->legacy->create($data); // 旧ロジック
    }

    private function newCreate(array $data): Book
    {
        // 新しいロジック
    }
}
```

**config/features.php**

```php
return [
    'use_new_create' => env('USE_NEW_CREATE', true),
];
```

**.env で切り替えるだけ**

```env
# 新ロジックを使う（デフォルト）
USE_NEW_CREATE=true

# 問題が起きたら false に変えるだけで即座に戻る
USE_NEW_CREATE=false
```

```bash
php artisan config:clear
```

### ポイント

- コントローラーは `$this->bookService->create()` を呼ぶだけで変更ゼロ
- 旧ロジックは `Legacy/` に退避するので本体が汚れない
- `.env` の 1 行を変えるだけで戻せるのでメンバーでも簡単
- `Legacy/` ディレクトリは移行完了後に丸ごと削除するだけ
