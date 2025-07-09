<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'week_day',
        'start',
        'end',
    ];

    protected function casts(): array
    {
        return [
            'start' => 'datetime:H:i',
            'end' => 'datetime:H:i',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}
