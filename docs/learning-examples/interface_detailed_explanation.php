<?php

/**
 * 🔍 BookRepositoryInterfaceの詳細解説
 */

echo "=== 🔍 BookRepositoryInterface の詳細解説 ===\n\n";

echo "【1. インターフェースとは何か】\n";
echo "インターフェース = 「約束事」「契約書」\n";
echo "\n";
echo "例えば、BookRepositoryInterfaceは以下を約束している：\n";
echo "  ✓ save(Book \$book): void メソッドを持つ\n";
echo "  ✓ findById(BookId \$id): ?Book メソッドを持つ\n";
echo "  ✓ findByISBN(ISBN \$isbn): ?Book メソッドを持つ\n";
echo "  ✓ その他のメソッドも持つ\n";
echo "\n";
echo "この約束を守れば、どんな実装でもOK！\n\n";

echo "【2. なぜドメイン層に置くのか】\n";
echo "\n";
echo "❌ 悪い配置：インフラ層にインターフェース\n";
echo "Domain → Application → Infrastructure\n";
echo "                           ↑\n";
echo "                    ここにインターフェース\n";
echo "\n";
echo "問題：ドメイン層がインフラ層に依存してしまう\n";
echo "\n";
echo "✅ 良い配置：ドメイン層にインターフェース\n";
echo "Domain ← Application ← Infrastructure\n";
echo "  ↑                        ↓\n";
echo "ここにインターフェース    実装がここ\n";
echo "\n";
echo "利点：インフラ層がドメイン層に依存する（依存性逆転）\n\n";

echo "【3. 実装例の比較】\n\n";

// インターフェース（約束事）
interface BookRepositoryInterface
{
    public function save($book): void;
    public function findById($id);
}

// MySQL実装
class MySQLBookRepository implements BookRepositoryInterface
{
    public function save($book): void
    {
        echo "MySQLのテーブルに INSERT文で保存\n";
    }

    public function findById($id)
    {
        echo "MySQLのテーブルから SELECT文で検索\n";
        return "MySQL検索結果";
    }
}

// PostgreSQL実装
class PostgreSQLBookRepository implements BookRepositoryInterface
{
    public function save($book): void
    {
        echo "PostgreSQLのテーブルに INSERT文で保存\n";
    }

    public function findById($id)
    {
        echo "PostgreSQLのテーブルから SELECT文で検索\n";
        return "PostgreSQL検索結果";
    }
}

// ファイル実装
class FileBookRepository implements BookRepositoryInterface
{
    public function save($book): void
    {
        echo "JSONファイルに保存\n";
    }

    public function findById($id)
    {
        echo "JSONファイルから検索\n";
        return "ファイル検索結果";
    }
}

// メモリ実装（テスト用）
class InMemoryBookRepository implements BookRepositoryInterface
{
    private $books = [];

    public function save($book): void
    {
        echo "メモリ（配列）に保存\n";
        $this->books[] = $book;
    }

    public function findById($id)
    {
        echo "メモリ（配列）から検索\n";
        return "メモリ検索結果";
    }
}

echo "【実際に使ってみる】\n\n";

// ユースケース（どの実装でも使える）
class BookUseCase
{
    private BookRepositoryInterface $repository;

    public function __construct(BookRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function registerBook($book)
    {
        echo "本を登録中...\n";
        $this->repository->save($book);
        echo "登録完了！\n\n";
    }

    public function findBook($id)
    {
        echo "本を検索中...\n";
        $result = $this->repository->findById($id);
        echo "検索結果: $result\n\n";
    }
}

echo "【MySQL版】\n";
$mysqlRepo = new MySQLBookRepository();
$useCase1 = new BookUseCase($mysqlRepo);
$useCase1->registerBook("ハリーポッター");
$useCase1->findBook(1);

echo "【PostgreSQL版】\n";
$postgresRepo = new PostgreSQLBookRepository();
$useCase2 = new BookUseCase($postgresRepo);
$useCase2->registerBook("クリーンアーキテクチャ");
$useCase2->findBook(2);

echo "【ファイル版】\n";
$fileRepo = new FileBookRepository();
$useCase3 = new BookUseCase($fileRepo);
$useCase3->registerBook("DDD本");
$useCase3->findBook(3);

echo "【テスト版】\n";
$memoryRepo = new InMemoryBookRepository();
$useCase4 = new BookUseCase($memoryRepo);
$useCase4->registerBook("テスト本");
$useCase4->findBook(4);

echo "=== 🎯 重要なポイント ===\n";
echo "1. BookUseCaseは一度書けば、どの実装でも使える\n";
echo "2. 新しい保存方法を追加してもBookUseCaseは変更不要\n";
echo "3. テスト時はInMemoryRepositoryを使えばDB不要\n";
echo "4. 本番はMySQLRepository、開発はFileRepositoryなど使い分け可能\n";
