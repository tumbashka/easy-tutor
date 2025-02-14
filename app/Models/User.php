<?php

namespace App\Models;

use App\Notifications\MyVerifyMail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use mysql_xdevapi\Exception;
use Symfony\Component\Mailer\Exception\UnexpectedResponseException;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

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
    public function freeTimes(): HasMany
    {
        return $this->hasMany(FreeTime::class);
    }
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token'
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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'bool',
        'is_active' => 'bool',
    ];

    public function sendEmailVerificationNotification()
    {
        try {
            $this->notify(new MyVerifyMail());
        } catch (UnexpectedResponseException $e) {
            Auth::logout();
            return abort(450);
        }

    }
}
