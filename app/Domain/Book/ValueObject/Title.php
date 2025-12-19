<?php

namespace App\Domain\Book\ValueObject;

/**
 * タイトル値オブジェクト
 */
class Title
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw new \InvalidArgumentException('タイトルは空にできません');
        }

        if (mb_strlen($trimmed) > 200) {
            throw new \InvalidArgumentException('タイトルは200文字以内で入力してください');
        }

        $this->value = $trimmed;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Title $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
