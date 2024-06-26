<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Filters\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;


/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="Пользователь",
 *     description="Модель пользователя",
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
 *         property="password",
 *         type="string",
 *         description="Пароль пользователя"
 *     ),
 *     @OA\Property(
 *         property="login",
 *         type="string",
 *         description="Логин пользователя"
 *     ),
 *     @OA\Property(
 *         property="department",
 *         type="string",
 *         description="Отдел пользователя"
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="boolean",
 *         description="Статус активности пользователя"
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

class User extends Authenticatable implements JWTSubject, HasMedia
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        HasRoles,
        Filterable,
        InteractsWithMedia;

    protected string $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'login',
        'department',
        'is_active',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function secretaries(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'manager_secretaries', 'manager_id', 'secretary_id');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'secretary_id');
    }

    public function protocols(): HasMany
    {
        return $this->hasMany(Protocol::class, 'secretary_id');
    }
}
