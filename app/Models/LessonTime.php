<?php

namespace App\Models;

use App\Events\LessonTime\LessonTimeAdded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LessonTime extends Model
{
    use HasFactory;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

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

    public function updateLessons(): void
    {
        $futureLessons = $this->getFutureLessons();
        $futureLessons->each(function ($lesson) {
            $lesson->start = $this->start;
            $lesson->end = $this->end;
            $lesson->price = getLessonPrice($this->start, $this->end, $lesson->student->price);
            $lesson->save();
        });
    }

    public function getFutureLessons()
    {
        $fromTomorrowFutureLessons = $this->lessons()
            ->where('date', '>', now())
            ->where('user_id', auth()->user()->id)
            ->get();

        $todayFutureLessons = $this->lessons()
            ->where('date', now()->format('Y-m-d'))
            ->where('start', '>', now()->format('H:i:s'))
            ->where('user_id', auth()->user()->id)
            ->get();

        return $fromTomorrowFutureLessons->concat($todayFutureLessons);
    }
}
