<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class TelegramReminder extends Model
{
    use HasFactory;

    protected $attributes = [
        'is_enabled' => true,
        'before_lesson_minutes' => 60,
        'homework_reminder_time' => '09:00',
    ];

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
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    protected function homeworkReminderTime(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                return (new Carbon($value))->format('H:i');
            },
        );
    }
}
