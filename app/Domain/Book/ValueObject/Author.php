<?php

namespace App\Domain\Book\ValueObject;

/**
 * 著者値オブジェクト
 */
class Author
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw new \InvalidArgumentException('著者名は空にできません');
        }

        if (mb_strlen($trimmed) > 100) {
            throw new \InvalidArgumentException('著者名は100文字以内で入力してください');
        }

        $this->value = $trimmed;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Author $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
