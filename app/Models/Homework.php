<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Homework extends Model
{
    use HasFactory;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

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
}
