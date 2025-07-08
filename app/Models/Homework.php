<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Homework extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'description',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'timestamp',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    protected function scopeOrderByCompleted(Builder $query): void
    {
        $query->orderByRaw('CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END ASC, created_at DESC');
    }
}
