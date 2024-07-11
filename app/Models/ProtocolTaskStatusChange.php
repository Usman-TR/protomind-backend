<?php

namespace App\Models;

use App\Http\Filters\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProtocolTaskStatusChange extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'protocol_task_id',
        'user_id',
        'status',
    ];

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(ProtocolTask::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
