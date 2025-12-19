<?php

namespace Tests\Unit\Domain\Book\Entity;

use PHPUnit\Framework\TestCase;
use App\Domain\Book\Entity\Book;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\Title;
use App\Domain\Book\ValueObject\Author;
use App\Domain\Book\ValueObject\ISBN;

/**
 * 本エンティティのテスト
 *
 * ドメイン層のテストの特徴：
 * - フレームワークに依存しない
 * - データベースを使わない
 * - 純粋なビジネスロジックをテストする
 */
class BookTest extends TestCase
{
    public function test_本を作成できる()
    {
        // Arrange（準備）
        $id = new BookId(1);
        $title = new Title('クリーンアーキテクチャ');
        $author = new Author('ロバート・C・マーチン');
        $isbn = new ISBN('9784048930567');

        // Act（実行）
        $book = new Book($id, $title, $author, $isbn);

        // Assert（検証）
        $this->assertEquals(1, $book->getId()->getValue());
        $this->assertEquals('クリーンアーキテクチャ', $book->getTitle()->getValue());
        $this->assertEquals('ロバート・C・マーチン', $book->getAuthor()->getValue());
        $this->assertEquals('9784048930567', $book->getISBN()->getValue());
        $this->assertTrue($book->isAvailable());
    }

    public function test_本を貸し出しできる()
    {
        // Arrange
        $book = $this->createBook();

        // Act
        $book->lend();

        // Assert
        $this->assertFalse($book->isAvailable());
    }

    public function test_貸し出し中の本は再度貸し出しできない()
    {
        // Arrange
        $book = $this->createBook();
        $book->lend();

        // Act & Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('この本は既に貸し出し中です');
        $book->lend();
    }

    public function test_本を返却できる()
    {
        // Arrange
        $book = $this->createBook();
        $book->lend();

        // Act
        $book->return();

        // Assert
        $this->assertTrue($book->isAvailable());
    }

    public function test_利用可能な本は返却できない()
    {
        // Arrange
        $book = $this->createBook();

        // Act & Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('この本は既に返却済みです');
        $book->return();
    }

    private function createBook(): Book
    {
        return new Book(
            new BookId(1),
            new Title('テスト本'),
            new Author('テスト著者'),
            new ISBN('9784048930567')
        );
    }
}
