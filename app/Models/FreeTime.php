<?php

namespace App\Models;

use App\Enums\FreeTimeStatus;
use App\Enums\FreeTimeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeTime extends Model
{
    use HasFactory;

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
            'status' => FreeTimeStatus::class,
            'type' => FreeTimeType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
