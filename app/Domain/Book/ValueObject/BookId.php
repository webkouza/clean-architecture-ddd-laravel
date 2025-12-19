<?php

namespace App\Domain\Book\ValueObject;

/**
 * 本ID値オブジェクト
 *
 * 値オブジェクトとは：
 * - 値そのものを表現する
 * - 不変（一度作ったら変更できない）
 * - 等価性で比較する（IDが同じなら同じオブジェクト）
 */
class BookId
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('本IDは1以上の数値である必要があります');
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * 等価性の比較
     */
    public function equals(BookId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
