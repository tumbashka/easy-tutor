<?php

namespace App\Models;

use App\Notifications\MyVerifyMail;
use App\Services\ScheduleService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Laravel\Sanctum\HasApiTokens;
use Symfony\Component\Mailer\Exception\UnexpectedResponseException;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['is_admin'];

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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'bool',
            'is_active' => 'bool',
            'is_enabled_task_reminders' => 'bool',
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function lessonTimes(): HasManyThrough
    {
        return $this->hasManyThrough(LessonTime::class, Student::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function count_payed_lessons(): int
    {
        return $this->lessons()->where('is_paid', true)->count();
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

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        } else {
            return asset('storage/avatars/default.png');
        }
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public static function getUserByTelegramID($telegram_id)
    {
        return self::query()->where('is_active', true)
            ->firstWhere('telegram_id', $telegram_id);
    }

    public function getTodayActualLessons()
    {
        return app(ScheduleService::class)->getActualLessonsOnDate(now());
    }

    #[\Override]
    public function sendEmailVerificationNotification(): void
    {
        try {
            $this->notify(new MyVerifyMail);
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
     */
    public function getLessonsOnDate(Carbon $date): Collection
    {
        return $this->lessons()
            ->where('date', $date->format('Y-m-d'))
            ->orderBy('start')
            ->get();
    }

    public function getAllLessonSlotsOnWeekDays(bool $allow_lessons = true): \Illuminate\Support\Collection
    {
        $lesson_times_on_days = $this->lessonTimes()
            ->with('student')
            ->get();

        $free_times_on_days = $this->freeTimes()
            ->get();

        if ($allow_lessons) {
            $all_lesson_slots_on_days = $lesson_times_on_days->merge($free_times_on_days);
        } else {
            $all_lesson_slots_on_days = $free_times_on_days;
        }

        return $all_lesson_slots_on_days->sortBy(['week_day', 'start'])->groupBy('week_day');
    }

    public function setAvatar($file): bool
    {
        $manager = ImageManager::gd(autoOrientation: true);
        $image = $manager->read($file)
            ->cover(300, 300) // Обрезка с сохранением пропорций
            ->encode(new WebpEncoder(80));

        if ($this->avatar) {
            Storage::delete($this->avatar);
        }

        $path = 'avatars/' . uniqid() . '.webp';
        Storage::put($path, $image);

        return $this->update(['avatar' => $path]);
    }

    public function get_telegram_url(): string
    {
        if (!$this->telegram_token) {
            $this->generate_telegram_token();
        }
        $bot_username = config('telegram.bots.mybot.username');

        return "https://t.me/{$bot_username}?start={$this->telegram_token}";
    }

    public function generate_telegram_token(): void
    {
        while (true) {
            $telegram_token = Str::random(64);
            $user = User::firstWhere('telegram_token', $telegram_token);
            if (!$user) {
                break;
            }
        }
        $this->telegram_token = $telegram_token;
        $this->update();
    }

    public function remember_user_telegram()
    {
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
