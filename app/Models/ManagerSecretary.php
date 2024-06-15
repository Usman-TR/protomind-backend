<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ManagerSecretary",
 *     type="object",
 *     title="Связь менеджера и секретаря",
 *     description="Модель связи между менеджером и секретарем",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID связи"
 *     ),
 *     @OA\Property(
 *         property="manager_id",
 *         type="integer",
 *         description="ID менеджера"
 *     ),
 *     @OA\Property(
 *         property="secretary_id",
 *         type="integer",
 *         description="ID секретаря"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время создания связи"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время последнего обновления связи"
 *     )
 * )
 */
class ManagerSecretary extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'secretary_id',
    ];
}
