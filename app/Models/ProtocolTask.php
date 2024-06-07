<?php

namespace App\Models;

use App\Enums\ProtocolTaskStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProtocolTask extends Model
{
    use HasFactory;

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
}
