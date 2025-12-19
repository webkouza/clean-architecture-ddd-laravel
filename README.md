# 🏗️ クリーンアーキテクチャ + DDD 学習プロジェクト

## 📚 概要

中学生プログラマーでも理解できるように、クリーンアーキテクチャとドメイン駆動設計（DDD）をLaravelで実装した学習用プロジェクトです。

## 🎯 学習内容

- **クリーンアーキテクチャ**の基本概念と実装
- **DDD（ドメイン駆動設計）**の重要な要素
- **値オブジェクト**によるバリデーション
- **エンティティ**によるビジネスルール
- **リポジトリパターン**とインターフェース
- **依存性逆転の原則**
- **ユースケース**による処理フロー制御

## 🏗️ アーキテクチャ構成

```
app/
├── Domain/           ← ビジネスの核心
│   └── Book/
│       ├── Entity/          ← エンティティ
│       ├── ValueObject/     ← 値オブジェクト
│       └── Repository/      ← リポジトリインターフェース
│
├── Application/      ← ユースケース
│   └── Book/UseCase/
│
├── Infrastructure/   ← 技術的詳細
│   └── Book/
│       ├── Model/
│       └── Repository/
│
└── Http/            ← 外部との接点
    └── Controllers/
```

## 🚀 セットアップ

### 前提条件

- Docker Desktop
- Git

### インストール手順

1. **リポジトリをクローン**
   ```bash
   git clone https://github.com/webkouza/clean-architecture-ddd-example.git
   cd clean-architecture-ddd-example
   ```

2. **Laravel Sailで環境構築**
   ```bash
   ./vendor/bin/sail up -d
   ```

3. **データベースマイグレーション**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

4. **アプリケーションにアクセス**
   ```
   http://localhost:8080
   ```

## 🎮 使い方

### 本の登録

1. ブラウザで `http://localhost:8080` を開く
2. 以下の情報を入力：
   - **タイトル**: 本のタイトル
   - **著者**: 著者名
   - **ISBN**: ISBN番号（10桁または13桁）
3. 「本を登録」ボタンをクリック

### テストの実行

```bash
# ドメイン層のテスト
./vendor/bin/sail test tests/Unit/Domain/Book/Entity/BookTest.php

# 全テストの実行
./vendor/bin/sail test
```

## 📖 学習ガイド

詳細な学習内容は以下のファイルを参照してください：

- **[完全学習ガイド](docs/COMPLETE_LEARNING_GUIDE.md)** - 全体的な学習内容
- **[アーキテクチャガイド](docs/ARCHITECTURE_GUIDE.md)** - アーキテクチャの詳細
- **[学習用サンプル](docs/learning-examples/)** - 実行可能なサンプルコード

## 🎯 実装例

### 値オブジェクト

```php
class Title
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('タイトルは空にできません');
        }
        
        $this->value = trim($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
```

### エンティティ

```php
class Book
{
    public function lend(): void
    {
        if (!$this->isAvailable) {
            throw new \DomainException('この本は既に貸し出し中です');
        }
        
        $this->isAvailable = false;
    }
}
```

### ユースケース

```php
class RegisterBookUseCase
{
    public function execute(RegisterBookCommand $command): RegisterBookResponse
    {
        // 1. 値オブジェクト作成（バリデーション）
        $title = new Title($command->getTitle());
        
        // 2. 重複チェック
        $existingBook = $this->repository->findByISBN($isbn);
        if ($existingBook !== null) {
            throw new \DomainException('このISBNの本は既に登録されています');
        }
        
        // 3. エンティティ作成・保存
        $book = new Book($bookId, $title, $author, $isbn);
        $this->repository->save($book);
        
        return new RegisterBookResponse(...);
    }
}
```

## 🎓 学習のポイント

### 1. 依存の向き

```
Http → Application → Domain ← Infrastructure
                      ↑
                   すべてがここに依存
```

### 2. 各層の責任

- **Domain**: ビジネスルール
- **Application**: ユースケース
- **Infrastructure**: 技術的詳細
- **Http**: 外部との接点

### 3. テスト容易性

- ドメイン層はフレームワーク不要でテスト可能
- インターフェースによりモックを使用可能

## 🔧 技術スタック

- **Laravel 12.x** - PHPフレームワーク
- **Laravel Sail** - Docker開発環境
- **MySQL** - データベース
- **Vue.js + Inertia.js** - フロントエンド
- **Tailwind CSS** - スタイリング
- **PHPUnit** - テストフレームワーク

## 📚 参考資料

- [クリーンアーキテクチャ](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html) by Robert C. Martin
- [Laravel公式ドキュメント](https://laravel.com/docs)
- [ドメイン駆動設計](https://www.amazon.co.jp/dp/4798121967) by Eric Evans

## 🤝 コントリビューション

学習用プロジェクトのため、改善提案やバグ報告は歓迎します！

## 📄 ライセンス

MIT License

---

**🎉 Happy Learning! クリーンアーキテクチャとDDDを楽しく学びましょう！**
