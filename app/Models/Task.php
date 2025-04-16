<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    public function task_categories(): BelongsToMany
    {
        return $this->belongsToMany(TaskCategory::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'deadline',
        'completed_at',
        'reminder_before_deadline',
        'reminder_before_hours',
        'reminder_daily',
        'reminder_daily_time',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
        'reminder_before_deadline' => 'boolean',
        'reminder_daily' => 'boolean',
    ];

}
