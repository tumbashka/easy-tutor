<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'chat_id',
        'text',
        'is_notified',
    ];

    protected function casts(): array
    {
        return [
            'is_notified' => 'boolean',
            'chat_id' => 'integer',
            'text' => 'string',
        ];
    }
}
