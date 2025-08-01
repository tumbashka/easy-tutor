<?php

namespace App\Models;

use App\Enums\Roles;
use App\Notifications\MyVerifyMail;
use App\Services\ImageService;
use App\Services\LessonService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Symfony\Component\Mailer\Exception\UnexpectedResponseException;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [
        'role',
        'is_active',
    ];

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'about',
        'telegram_username',
        'telegram_id',
        'telegram_token',
        'phone',
        'password',
        'remember_token',
        'is_enabled_task_reminders',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Roles::class,
            'is_active' => 'bool',
            'is_enabled_task_reminders' => 'bool',
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'user_id');
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(Student::class, 'account_id');
    }

    public function lessonTimes(): HasManyThrough
    {
        return $this->hasManyThrough(LessonTime::class, Student::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function freeTimes(): HasMany
    {
        return $this->hasMany(FreeTime::class);
    }

    public function taskCategories(): HasMany
    {
        return $this->hasMany(TaskCategory::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function getCountPayedLessonsAttribute(): int
    {
        return $this->lessons()->where('is_paid', true)->count();
    }

    public function getAvatarUrlAttribute(): string
    {
        return app(ImageService::class)->getImageURL($this->id);
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role == Roles::Admin;
    }

    public static function getUserByTelegramID($telegram_id)
    {
        return self::query()->where('is_active', true)
            ->firstWhere('telegram_id', $telegram_id);
    }

    public function getTodayActualLessons()
    {
        return app(LessonService::class)->getActualLessonsOnDate(now());
    }

    #[\Override]
    public function sendEmailVerificationNotification(): void
    {
        try {
            dispatch(function () {
                $this->notify(new MyVerifyMail);
            })->afterResponse();
        } catch (UnexpectedResponseException $e) {
            Auth::logout();

            abort(450);
        }
    }

    /**
     * Получить время занятия всех учеников за конкретный день недели.
     */
    public function getWeekDayLessonTimes(int $week_day_id): Collection
    {
        return $this->lessonTimes()
            ->where('week_day', $week_day_id)
            ->orderBy('start')
            ->get();
    }

    /**
     * Получить занятия за конкретную дату.
     * @return Collection<Lesson>
     */
    public function getLessonsOnDate(Carbon $date): Collection
    {
        return $this->lessons()
            ->where('date', $date->format('Y-m-d'))
            ->orderBy('start')
            ->get();
    }

    public function getAllLessonSlotsOnWeekDays(bool $allowLessons = true): \Illuminate\Support\Collection
    {
        $lessonTimesOnDays = $this->lessonTimes()
            ->with('student')
            ->get();

        $freeTimesOnDays = $this->freeTimes()
            ->get();

        if ($allowLessons) {
            $allLessonSlotsOnDays = $lessonTimesOnDays->merge($freeTimesOnDays);
        } else {
            $allLessonSlotsOnDays = $freeTimesOnDays;
        }

        return $allLessonSlotsOnDays->sortBy(['week_day', 'start'])->groupBy('week_day');
    }

    public function getConnectToTelegramUrlAttribute(): string
    {
        if (!$this->telegram_token) {
            $this->updateConnectToTelegramToken();
        }
        $bot_username = config('telegram.bots.mybot.username');

        return "https://t.me/{$bot_username}?start={$this->telegram_token}";
    }

    public function updateConnectToTelegramToken(): void
    {
        while (true) {
            $telegram_token = Str::random(64);
            $user = self::firstWhere('telegram_token', $telegram_token);
            if (!$user) {
                break;
            }
        }
        $this->telegram_token = $telegram_token;
        $this->update();
    }

    public function studentsOnClasses(): Collection
    {
        return $this->students()
            ->orderBy('name')
            ->with('lesson_times')
            ->get()
            ->groupBy('class')
            ->sortKeys();
    }
}
