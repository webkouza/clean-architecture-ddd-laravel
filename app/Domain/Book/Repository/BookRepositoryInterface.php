<?php

namespace App\Domain\Book\Repository;

use App\Domain\Book\Entity\Book;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\ISBN;

/**
 * 本リポジトリインターフェース
 *
 * インターフェースをドメイン層に置く理由：
 * - ドメイン層がインフラ層に依存しないようにするため
 * - テストしやすくするため（モックを作りやすい）
 * - データベースの種類を変更しても影響を受けないため
 */
interface BookRepositoryInterface
{
    /**
     * 本を保存する
     */
    public function save(Book $book): void;

    /**
     * IDで本を検索する
     */
    public function findById(BookId $id): ?Book;

    /**
     * ISBNで本を検索する
     */
    public function findByISBN(ISBN $isbn): ?Book;

    /**
     * 利用可能な本の一覧を取得する
     */
    public function findAvailableBooks(): array;

    /**
     * 全ての本の一覧を取得する
     */
    public function findAll(): array;

    /**
     * 本を削除する
     */
    public function delete(BookId $id): void;
}
