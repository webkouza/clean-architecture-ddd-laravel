<?php

namespace App\Application\Book\UseCase;

/**
 * 本登録レスポンス
 *
 * レスポンスとは：
 * - ユースケースからの出力データを表現する
 * - 外部への出力をカプセル化する
 */
class RegisterBookResponse
{
    private int $id;
    private string $title;
    private string $author;
    private string $isbn;

    public function __construct(int $id, string $title, string $author, string $isbn)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->isbn = $isbn;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getISBN(): string
    {
        return $this->isbn;
    }

    /**
     * 配列形式で返す（JSONレスポンス用）
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
        ];
    }
}
