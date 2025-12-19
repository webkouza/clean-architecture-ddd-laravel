<?php

namespace App\Infrastructure\Book\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * 本Eloquentモデル
 *
 * Eloquentモデルの役割：
 * - データベースとのやり取りを担当
 * - ビジネスロジックは持たない
 * - インフラ層に配置する
 */
class BookModel extends Model
{
    protected $table = 'books';

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public $timestamps = true;
}
