<?php

namespace App\Infrastructure\Book\Repository;

use App\Domain\Book\Entity\Book;
use App\Domain\Book\Repository\BookRepositoryInterface;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\Title;
use App\Domain\Book\ValueObject\Author;
use App\Domain\Book\ValueObject\ISBN;
use App\Infrastructure\Book\Model\BookModel;

/**
 * Eloquent本リポジトリ実装
 *
 * インフラ層の実装：
 * - ドメインのインターフェースを実装する
 * - データベースの詳細を隠蔽する
 * - ドメインエンティティとEloquentモデルを変換する
 */
class EloquentBookRepository implements BookRepositoryInterface
{
    public function save(Book $book): void
    {
        $model = BookModel::find($book->getId()->getValue());

        if ($model === null) {
            // 新規作成
            $model = new BookModel();
            $model->id = $book->getId()->getValue();
        }

        // ドメインエンティティからEloquentモデルへ変換
        $model->title = $book->getTitle()->getValue();
        $model->author = $book->getAuthor()->getValue();
        $model->isbn = $book->getISBN()->getValue();
        $model->is_available = $book->isAvailable();

        $model->save();
    }

    public function findById(BookId $id): ?Book
    {
        $model = BookModel::find($id->getValue());

        if ($model === null) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function findByISBN(ISBN $isbn): ?Book
    {
        $model = BookModel::where('isbn', $isbn->getValue())->first();

        if ($model === null) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function findAvailableBooks(): array
    {
        $models = BookModel::where('is_available', true)->get();

        return $models->map(function ($model) {
            return $this->modelToEntity($model);
        })->toArray();
    }

    public function findAll(): array
    {
        $models = BookModel::all();

        return $models->map(function ($model) {
            return $this->modelToEntity($model);
        })->toArray();
    }

    public function delete(BookId $id): void
    {
        BookModel::destroy($id->getValue());
    }

    /**
     * Eloquentモデルからドメインエンティティへ変換
     */
    private function modelToEntity(BookModel $model): Book
    {
        return new Book(
            new BookId($model->id),
            new Title($model->title),
            new Author($model->author),
            new ISBN($model->isbn),
            $model->is_available
        );
    }
}
