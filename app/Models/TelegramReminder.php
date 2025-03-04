<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramReminder extends Model
{
    use HasFactory;
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    protected $fillable = [
        'student_id',
        'chat_id',
        'is_enabled',
        'before_lesson_minutes',
        'homework_reminder_time',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'chat_id' => 'integer',
            'before_lesson_minutes' => 'integer',
            'homework_reminder_time' => 'datetime:H:i'
        ];
    }
}
