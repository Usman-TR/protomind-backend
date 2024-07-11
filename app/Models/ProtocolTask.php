<?php

namespace App\Models;

use App\Enums\ProtocolTaskStatusEnum;
use App\Http\Filters\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="ProtocolTask",
 *     type="object",
 *     title="Задача протокола",
 *     description="Модель задачи протокола",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID задачи протокола"
 *     ),
 *     @OA\Property(
 *         property="protocol_id",
 *         type="integer",
 *         description="ID протокола"
 *     ),
 *     @OA\Property(
 *         property="responsible_id",
 *         type="integer",
 *         description="ID ответственного пользователя"
 *     ),
 *     @OA\Property(
 *         property="essence",
 *         type="string",
 *         description="Суть задачи",
 *         maxLength=65000
 *     ),
 *     @OA\Property(
 *         property="deadline",
 *         type="string",
 *         format="date",
 *         description="Срок выполнения задачи"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Статус задачи"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время создания задачи"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время последнего обновления задачи"
 *     )
 * )
 */
class ProtocolTask extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'protocol_id',
        'responsible_id',
        'essence',
        'deadline',
        'status',
    ];

    protected $casts = [
        'status' => ProtocolTaskStatusEnum::class
    ];

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(Protocol::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(ProtocolTaskStatusChange::class);
    }
}
