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
        'start' => 'datetime:H:i',
        'end' => 'datetime:H:i',
    ];

    public static function getAllLessonSlotsOnWeekDays(User $user)
    {
        $lesson_times_on_days = $user
            ->lessonTimes()
            ->with('student')
            ->get()
            ->sortBy('week_day')
            ->groupBy('week_day')
            ->toArray();

        $free_times_on_days = $user->freeTimes()->get()->sortBy('week_day')
            ->groupBy('week_day')
            ->toArray();

        $all_lesson_slots_on_days = [];
        foreach ($lesson_times_on_days as $week_day => $lesson_times_on_day) {
            $additional = $free_times_on_days[$week_day] ?? [];
            $all_lesson_slots_on_days[$week_day] = array_merge($lesson_times_on_day, $additional);
        }

        $all_lesson_slots_on_days = array_map(function ($all_lesson_slots_on_day) {
            usort($all_lesson_slots_on_day, function ($a, $b) {
                return $a['start'] <=> $b['start'];
            });
            return $all_lesson_slots_on_day;
        }, $all_lesson_slots_on_days);
        return $all_lesson_slots_on_days;
    }
}
