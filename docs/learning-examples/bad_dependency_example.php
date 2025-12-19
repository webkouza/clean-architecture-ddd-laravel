<?php

/**
 * ❌ 悪い例：直接依存
 */

echo "=== ❌ 悪い例：直接依存 ===\n\n";

// MySQLに直接依存したユースケース
class BadRegisterBookUseCase
{
    private MySQLBookRepository $repository;  // ← MySQLに直接依存！

    public function __construct(MySQLBookRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($title, $author, $isbn)
    {
        echo "本を登録します: $title\n";
        $this->repository->save($title, $author, $isbn);
    }
}

// MySQL専用のリポジトリ
class MySQLBookRepository
{
    public function save($title, $author, $isbn)
    {
        echo "MySQLに保存: $title by $author\n";
    }
}

// 使用例
echo "【MySQLを使用】\n";
$mysqlRepo = new MySQLBookRepository();
$useCase = new BadRegisterBookUseCase($mysqlRepo);
$useCase->execute("ハリーポッター", "J.K.ローリング", "123");

echo "\n【問題発生！PostgreSQLに変更したい】\n";
echo "❌ BadRegisterBookUseCaseはMySQLにしか対応していない\n";
echo "❌ PostgreSQLを使うには、ユースケース自体を変更する必要がある\n";
echo "❌ テスト用のモック（偽物）も作れない\n\n";

echo "=== 😵 問題点まとめ ===\n";
echo "1. ユースケースがMySQLに「べったり依存」\n";
echo "2. データベースを変更するとユースケースも変更が必要\n";
echo "3. テストが困難（MySQLがないとテストできない）\n";
echo "4. 柔軟性がない\n";
