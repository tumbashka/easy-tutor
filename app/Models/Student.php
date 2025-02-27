<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Student extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson_times(): HasMany
    {
        return $this->hasMany(LessonTime::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    protected $fillable = [
        'user_id',
        'name',
        'class',
        'note',
        'price',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'class' => 'integer',
        'price' => 'integer',
    ];

    public function updateLessons(): void
    {
        $futureLessons = $this->getFutureLessons();
        $futureLessons->each(function ($lesson) {
            $lesson->price = getLessonPrice($lesson->start, $lesson->end, $this->price);
            $lesson->student_name = $this->name;
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
