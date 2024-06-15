<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="ProtocolMember",
 *     type="object",
 *     title="Участник протокола",
 *     description="Модель участника протокола",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID участника протокола"
 *     ),
 *     @OA\Property(
 *         property="protocol_id",
 *         type="integer",
 *         description="ID протокола"
 *     ),
 *     @OA\Property(
 *         property="member_id",
 *         type="integer",
 *         description="ID участника (пользователя)"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время создания участника протокола"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время последнего обновления участника протокола"
 *     )
 * )
 */
class ProtocolMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol_id',
        'member_id',
    ];

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(Protocol::class);
    }

    // Связь с моделью User
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
