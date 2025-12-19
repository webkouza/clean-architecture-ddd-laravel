<?php

namespace App\Domain\Book\ValueObject;

/**
 * ISBN値オブジェクト
 */
class ISBN
{
    private string $value;

    public function __construct(string $value)
    {
        $cleaned = preg_replace('/[^0-9X]/', '', strtoupper($value));

        if (!$this->isValidISBN($cleaned)) {
            throw new \InvalidArgumentException('有効なISBNを入力してください');
        }

        $this->value = $cleaned;
    }

    private function isValidISBN(string $isbn): bool
    {
        // ISBN-10またはISBN-13の簡単なバリデーション
        return (strlen($isbn) === 10 || strlen($isbn) === 13) &&
               preg_match('/^[0-9]{9}[0-9X]$|^[0-9]{13}$/', $isbn);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ISBN $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
