<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class LessonTime extends Model
{
    use HasFactory;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    protected $fillable = [
        'student_id',
        'week_day',
        'start',
        'end',
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
    protected function casts(): array
    {
        return [
            'start' => 'datetime:H:i',
            'end' => 'datetime:H:i',
        ];
    }
}
