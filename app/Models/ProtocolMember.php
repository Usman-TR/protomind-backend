<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
