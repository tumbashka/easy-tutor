<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'is_default',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
