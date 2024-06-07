<?php

namespace App\Models;

use App\Enums\ProtocolStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Protocol extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme',
        'agenda',
        'event_date',
        'secretary_id',
        'director_id',
        'status',
    ];

    protected $casts = [
        'status' => ProtocolStatusEnum::class,
    ];

    public function members(): HasMany
    {
        return $this->hasMany(ProtocolMember::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProtocolTask::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(ProtocolKeyword::class);
    }

    public function secretary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }
}
