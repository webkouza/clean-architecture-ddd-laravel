<?php

namespace App\Domain\Book\Entity;

use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\Title;
use App\Domain\Book\ValueObject\Author;
use App\Domain\Book\ValueObject\ISBN;

/**
 * 本エンティティ
 *
 * エンティティとは：
 * - 一意のIDを持つオブジェクト
 * - ライフサイクル（作成→更新→削除）を持つ
 * - ビジネスルールを持つ
 */
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

    // ゲッター
    public function getId(): BookId
    {
        return $this->id;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function getISBN(): ISBN
    {
        return $this->isbn;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }
}
