<?php

require_once 'vendor/autoload.php';

use App\Domain\Book\Entity\Book;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\Title;
use App\Domain\Book\ValueObject\Author;
use App\Domain\Book\ValueObject\ISBN;

echo "=== 📚 Bookエンティティのテスト ===\n\n";

// 本を作る
$book = new Book(
    new BookId(1),                          // ID: 1
    new Title("ハリーポッター"),              // タイトル
    new Author("J.K.ローリング"),            // 著者
    new ISBN("9784915512377")               // ISBN
);

echo "📖 本を作成しました\n";
echo "   ID: " . $book->getId()->getValue() . "\n";
echo "   タイトル: " . $book->getTitle()->getValue() . "\n";
echo "   著者: " . $book->getAuthor()->getValue() . "\n";
echo "   ISBN: " . $book->getISBN()->getValue() . "\n";
echo "   利用可能: " . ($book->isAvailable() ? "はい" : "いいえ") . "\n\n";

// 本を貸し出す
echo "=== 📖 本を貸し出してみる ===\n";
$book->lend();
echo "✅ 貸し出し成功\n";
echo "   利用可能: " . ($book->isAvailable() ? "はい" : "いいえ") . "\n\n";

// もう一度貸し出そうとする（エラーになるはず）
echo "=== ❌ もう一度貸し出そうとする ===\n";
try {
    $book->lend();
    echo "✅ 貸し出し成功\n";
} catch (DomainException $e) {
    echo "❌ 期待通りエラー: " . $e->getMessage() . "\n\n";
}

// 本を返却する
echo "=== 📚 本を返却する ===\n";
$book->return();
echo "✅ 返却成功\n";
echo "   利用可能: " . ($book->isAvailable() ? "はい" : "いいえ") . "\n\n";

echo "=== 🎯 エンティティの利点 ===\n";
echo "1. 現実世界のモノ（本）をプログラムで表現\n";
echo "2. ビジネスルール（貸し出し中は再度貸し出せない）を持つ\n";
echo "3. IDで識別できる\n";
echo "4. 状態を持つ（利用可能/貸し出し中）\n";
