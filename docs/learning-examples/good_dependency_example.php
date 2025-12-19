<?php

/**
 * ✅ 良い例：インターフェースを使用
 */

echo "=== ✅ 良い例：インターフェースを使用 ===\n\n";

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

    public function execute($title, $author, $isbn)
    {
        echo "本を登録します: $title\n";
        $this->repository->save($title, $author, $isbn);
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

// 6. ファイル用の実装
class FileRepository implements BookRepositoryInterface
{
    public function save($title, $author, $isbn): void
    {
        echo "ファイルに保存: $title by $author\n";
    }
}

echo "【MySQL版】\n";
$mysqlRepo = new MySQLRepository();
$useCase1 = new GoodRegisterBookUseCase($mysqlRepo);
$useCase1->execute("ハリーポッター", "J.K.ローリング", "123");

echo "\n【PostgreSQL版】※ユースケースは変更不要！\n";
$postgresRepo = new PostgreSQLRepository();
$useCase2 = new GoodRegisterBookUseCase($postgresRepo);
$useCase2->execute("クリーンアーキテクチャ", "ロバート・マーチン", "456");

echo "\n【テスト版】※データベース不要！\n";
$mockRepo = new MockRepository();
$useCase3 = new GoodRegisterBookUseCase($mockRepo);
$useCase3->execute("テスト本", "テスト著者", "789");

echo "\n【ファイル版】※新しい保存方法も簡単に追加！\n";
$fileRepo = new FileRepository();
$useCase4 = new GoodRegisterBookUseCase($fileRepo);
$useCase4->execute("設計本", "設計者", "999");

echo "\n=== 🎉 インターフェースの利点 ===\n";
echo "✓ ユースケースは一度書けば、どの実装でも使える\n";
echo "✓ データベースを変更してもユースケースは変更不要\n";
echo "✓ テスト用のモックを簡単に作れる\n";
echo "✓ 新しい保存方法を簡単に追加できる\n";
echo "✓ 複数の実装を同時に使える\n";
