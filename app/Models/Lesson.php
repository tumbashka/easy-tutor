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
        'is_paid' => 'boolean',
        'is_canceled' => 'boolean',
    ];
}
