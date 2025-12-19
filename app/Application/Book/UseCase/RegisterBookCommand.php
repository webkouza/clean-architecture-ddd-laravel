<?php

namespace App\Application\Book\UseCase;

/**
 * 本登録コマンド
 *
 * コマンドとは：
 * - ユースケースへの入力データを表現する
 * - 外部からの入力をカプセル化する
 * - バリデーションは値オブジェクトで行う
 */
class RegisterBookCommand
{
    private string $title;
    private string $author;
    private string $isbn;

    public function __construct(string $title, string $author, string $isbn)
    {
        $this->title = $title;
        $this->author = $author;
        $this->isbn = $isbn;
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
}
