<?php

/**
 * 🤔 インターフェースの必要性を理解しよう
 */

// ❌ 悪い例：具体的なクラスに依存
class BadBookService
{
    private MySQLBookRepository $repository;  // MySQLに依存！

    public function __construct(MySQLBookRepository $repository)
    {
        $this->repository = $repository;
    }

    public function registerBook($title, $author, $isbn)
    {
        // 本を作って保存
        $book = new Book($title, $author, $isbn);
        $this->repository->save($book);
    }
}

class MySQLBookRepository
{
    public function save($book)
    {
        echo "MySQLに保存しました\n";
    }
}

// 問題：PostgreSQLに変更したくなったら？
// → BadBookServiceも変更が必要！

echo "=== ❌ 悪い例の問題点 ===\n";
echo "1. MySQLに依存している\n";
echo "2. データベースを変更したらサービスも変更が必要\n";
echo "3. テスト時にMySQLが必要\n\n";

// ✅ 良い例：インターフェースに依存
interface BookRepositoryInterface
{
    public function save($book): void;
}

class GoodBookService
{
    private BookRepositoryInterface $repository;  // インターフェースに依存！

    public function __construct(BookRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function registerBook($title, $author, $isbn)
    {
        // 本を作って保存
        $book = ['title' => $title, 'author' => $author, 'isbn' => $isbn];
        $this->repository->save($book);
    }
}

// MySQL実装
class MySQLRepository implements BookRepositoryInterface
{
    public function save($book): void
    {
        echo "MySQLに保存: " . $book['title'] . "\n";
    }
}

// PostgreSQL実装
class PostgreSQLRepository implements BookRepositoryInterface
{
    public function save($book): void
    {
        echo "PostgreSQLに保存: " . $book['title'] . "\n";
    }
}

// テスト用実装
class MockRepository implements BookRepositoryInterface
{
    public function save($book): void
    {
        echo "テスト用保存: " . $book['title'] . "\n";
    }
}

echo "=== ✅ 良い例：柔軟性 ===\n";

// MySQL版
$mysqlRepo = new MySQLRepository();
$service1 = new GoodBookService($mysqlRepo);
$service1->registerBook("ハリーポッター", "J.K.ローリング", "123");

// PostgreSQL版（サービスは変更不要！）
$postgresRepo = new PostgreSQLRepository();
$service2 = new GoodBookService($postgresRepo);
$service2->registerBook("クリーンアーキテクチャ", "ロバート・マーチン", "456");

// テスト版（データベース不要！）
$mockRepo = new MockRepository();
$service3 = new GoodBookService($mockRepo);
$service3->registerBook("テスト本", "テスト著者", "789");

echo "\n=== 🎯 インターフェースの利点 ===\n";
echo "1. データベースを変更してもサービスは変更不要\n";
echo "2. テスト時にモック（偽物）を使える\n";
echo "3. 複数の実装を切り替えられる\n";
