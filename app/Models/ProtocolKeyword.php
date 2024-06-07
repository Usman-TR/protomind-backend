<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtocolKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol_id',
        'title',
        'phrase',
        'created_at',
        'updated_at',
    ];
}
