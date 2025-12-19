<?php

require_once 'vendor/autoload.php';

use App\Domain\Book\Entity\Book;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\Title;
use App\Domain\Book\ValueObject\Author;
use App\Domain\Book\ValueObject\ISBN;

echo "=== 📚 本を作ってみよう ===\n";

try {
    // 1. 値オブジェクトを作る
    $id = new BookId(1);
    $title = new Title("ハリー・ポッター");
    $author = new Author("J.K.ローリング");
    $isbn = new ISBN("9784915512377");

    echo "✅ 値オブジェクト作成成功！\n";
    echo "   タイトル: {$title}\n";
    echo "   著者: {$author}\n";
    echo "   ISBN: {$isbn}\n\n";

    // 2. 本エンティティを作る
    $book = new Book($id, $title, $author, $isbn);
    echo "✅ 本エンティティ作成成功！\n";
    echo "   利用可能: " . ($book->isAvailable() ? "はい" : "いいえ") . "\n\n";

    // 3. 本を貸し出してみる
    echo "=== 📖 本を貸し出してみよう ===\n";
    $book->lend();
    echo "✅ 貸し出し成功！\n";
    echo "   利用可能: " . ($book->isAvailable() ? "はい" : "いいえ") . "\n\n";

    // 4. もう一度貸し出そうとする（エラーになるはず）
    echo "=== ❌ もう一度貸し出そうとする ===\n";
    try {
        $book->lend();
    } catch (DomainException $e) {
        echo "✅ 期待通りエラー: {$e->getMessage()}\n\n";
    }

    // 5. 本を返却する
    echo "=== 📚 本を返却してみよう ===\n";
    $book->return();
    echo "✅ 返却成功！\n";
    echo "   利用可能: " . ($book->isAvailable() ? "はい" : "いいえ") . "\n\n";

} catch (Exception $e) {
    echo "❌ エラー: {$e->getMessage()}\n";
}

echo "=== 🚫 不正な値でエラーテスト ===\n";

try {
    $badTitle = new Title("");  // 空のタイトル
} catch (InvalidArgumentException $e) {
    echo "✅ 期待通りエラー: {$e->getMessage()}\n";
}

try {
    $badId = new BookId(0);  // 0以下のID
} catch (InvalidArgumentException $e) {
    echo "✅ 期待通りエラー: {$e->getMessage()}\n";
}

echo "\n🎉 テスト完了！\n";
