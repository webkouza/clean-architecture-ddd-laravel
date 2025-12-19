<?php

namespace App\Application\Book\UseCase;

use App\Domain\Book\Entity\Book;
use App\Domain\Book\Repository\BookRepositoryInterface;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\Title;
use App\Domain\Book\ValueObject\Author;
use App\Domain\Book\ValueObject\ISBN;

/**
 * 本登録ユースケース
 *
 * ユースケースとは：
 * - アプリケーションでできることを表現する
 * - ビジネスロジックの流れを制御する
 * - ドメインオブジェクトを組み合わせて処理を実行する
 */
class RegisterBookUseCase
{
    private BookRepositoryInterface $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * 本を登録する
     */
    public function execute(RegisterBookCommand $command): RegisterBookResponse
    {
        // 1. 値オブジェクトを作成（バリデーション含む）
        $title = new Title($command->getTitle());
        $author = new Author($command->getAuthor());
        $isbn = new ISBN($command->getISBN());

        // 2. 重複チェック
        $existingBook = $this->bookRepository->findByISBN($isbn);
        if ($existingBook !== null) {
            throw new \DomainException('このISBNの本は既に登録されています');
        }

        // 3. 新しいIDを生成（実際のアプリでは自動採番など）
        $bookId = new BookId($this->generateNewId());

        // 4. エンティティを作成
        $book = new Book($bookId, $title, $author, $isbn);

        // 5. 保存
        $this->bookRepository->save($book);

        // 6. レスポンスを返す
        return new RegisterBookResponse(
            $book->getId()->getValue(),
            $book->getTitle()->getValue(),
            $book->getAuthor()->getValue(),
            $book->getISBN()->getValue()
        );
    }

    /**
     * 新しいIDを生成（簡単な実装）
     */
    private function generateNewId(): int
    {
        // 実際のアプリでは、データベースの自動採番やUUIDなどを使用
        return time() + rand(1, 1000);
    }
}
