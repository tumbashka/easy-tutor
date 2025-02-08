<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Collection\Collection;

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
        'price'
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

    public static function updateLessonsPriceOnStudentChanges(self $student): void
    {
        $futureLessons = self::getStudentFutureLessons($student);
        $futureLessons->each(function ($lesson) use ($student) {
            $lesson->price = getLessonPrice($lesson->start, $lesson->end, $student->price);
            $lesson->save();
        });
    }
    public static function updateLessonsNameOnStudentChanges(self $student): void
    {
        $futureLessons = self::getStudentFutureLessons($student);
        $futureLessons->each(function ($lesson) use ($student) {
            $lesson->student_name = $student->name;
            $lesson->save();
        });
    }

    public static function updateLessonTimeOnLessonTimeChanges(self $student, LessonTime $changedLessonTime): void
    {
        $futureLessons = Student::getStudentFutureLessons($student);
        $futureLessons->each(function ($lesson) use ($changedLessonTime){
            if($lesson->lesson_time_id == $changedLessonTime->id){
                $lesson->start = $changedLessonTime->start;
                $lesson->end = $changedLessonTime->end;
                $lesson->save();
            }
        });
    }
    public static function getStudentFutureLessons(self $student)
    {
        $fromTomorrowFutureLessons = $student->lessons()
            ->where('date', '>', now())
            ->where('user_id', auth()->user()->id)
            ->get();

        $todayFutureLessons = $student->lessons()
            ->where('date', now()->format('Y-m-d'))
            ->where('start', '>', now()->format('H:i:s'))
            ->where('user_id', auth()->user()->id)
            ->get();

        return $fromTomorrowFutureLessons->concat($todayFutureLessons);
    }
}
