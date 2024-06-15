<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @OA\Schema(
 *     schema="Meeting",
 *     type="object",
 *     title="Совещание",
 *     description="Модель совещания",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID совещания"
 *     ),
 *     @OA\Property(
 *         property="secretary_id",
 *         type="integer",
 *         description="ID секретаря"
 *     ),
 *     @OA\Property(
 *         property="theme",
 *         type="string",
 *         description="Тема совещания"
 *     ),
 *     @OA\Property(
 *         property="link",
 *         type="string",
 *         description="Ссылка на совещание"
 *     ),
 *     @OA\Property(
 *         property="event_date",
 *         type="string",
 *         format="date",
 *         description="Дата совещания"
 *     ),
 *     @OA\Property(
 *         property="event_start_time",
 *         type="string",
 *         format="date-time",
 *         description="Время начала совещания"
 *     ),
 *     @OA\Property(
 *         property="event_end_time",
 *         type="string",
 *         format="date-time",
 *         description="Время окончания совещания"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время создания совещания"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время последнего обновления совещания"
 *     )
 * )
 */
class Meeting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'secretary_id',
        'theme',
        'link',
        'event_date',
        'event_start_time',
        'event_end_time',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(MeetingMember::class);
    }
}
