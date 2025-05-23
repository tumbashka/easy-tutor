<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    public $with = ['task_categories'];

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
        'reminder_daily_time' => 'datetime:H:i',
    ];

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

    protected function scopeSortByActuality(Builder $query): void
    {
        $query->orderByRaw(
            'CASE WHEN completed_at IS NOT NULL THEN 1
                                        ELSE 0
                                        END ASC,
                                    CASE
                                        WHEN deadline IS NULL THEN 1
                                        ELSE 0
                                    END ASC,
                                    deadline ASC, created_at DESC'
        );
    }

    protected function scopeWhereCategory(Builder $query, ?TaskCategory $category): void
    {
        $query->when($category, function (Builder $query, TaskCategory $category) {
            $query->whereHas('task_categories', function ($query) use ($category) {
                $query->where('task_categories.id', $category->id);
            });
        });
    }
}
