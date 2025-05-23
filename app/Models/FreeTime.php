<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeTime extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'user_id',
        'week_day',
        'start',
        'end',
        'status',
        'type',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'start' => 'datetime:H:i',
            'end' => 'datetime:H:i',
        ];
    }
}
