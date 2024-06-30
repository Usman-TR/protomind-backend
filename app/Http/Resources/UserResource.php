<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     title="Пользователь",
 *     description="Ресурс пользователя",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID пользователя"
 *     ),
 *     @OA\Property(
 *         property="full_name",
 *         type="string",
 *         description="Полное имя пользователя"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Электронная почта пользователя"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         description="Роль пользователя"
 *     ),
 *     @OA\Property(
 *         property="department",
 *         type="string",
 *         description="Отдел пользователя"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время создания пользователя"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата и время последнего обновления пользователя"
 *     ),
 *     @OA\Property(
 *           property="avatar",
 *           type="string",
 *           format="binary",
 *           nullable=true,
 *           description="Avatar image of the user"
 *       )
 * )
 */

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "full_name" => $this->full_name,
            "email" => $this->email,
            "role" => $this->getRoleNames()->first(),
            "avatar" => $this->getFirstMedia('avatar')?->getUrl() ?? Storage::url("avatars/default_user_avatar.jpg"),
            "department" => $this->department,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
