<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasFactory;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'student_id',
        'lesson_time_id',
        'user_id',
        'student_name',
        'date',
        'start',
        'end',
        'price',
        'is_paid',
        'note',
        'is_canceled',
    ];

    protected function casts(): array
    {
        return [
            'start' => 'datetime:H:i',
            'end' => 'datetime:H:i',
            'is_paid' => 'boolean',
            'is_canceled' => 'boolean',
        ];
    }
}
