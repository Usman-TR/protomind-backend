<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Keyword",
 *     type="object",
 *     title="Ключевое слово",
 *     description="Модель ключевого слова",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID ключевого слова"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Заголовок ключевого слова"
 *     ),
 *     @OA\Property(
 *         property="phrase",
 *         type="string",
 *         description="Фраза ключевого слова"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время создания ключевого слова"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время последнего обновления ключевого слова"
 *     )
 * )
 */
class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'phrase',
        'created_at',
        'updated_at',
    ];
}
