<?php

namespace App\Models;

use App\DTO\Board\BoardFilterDTO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Board extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'name',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWhereNameLike(Builder $query, string $name): Builder
    {
        return $query->whereLike('name', "%{$name}%");
    }

    public function scopeWhereSubjectId(Builder $query, int $subject_id): Builder
    {
        return $query->where('subject_id', $subject_id);
    }

    public function scopeFilter(Builder $query, BoardFilterDTO $filterDTO): Builder
    {
        return $query->when($filterDTO->name, function (Builder $query, $name) {
            $this->scopeWhereNameLike($query, $name);
        })->when($filterDTO->subject_id, function (Builder $query, $subject_id) {
            $this->scopeWhereSubjectId($query, $subject_id);
        });
    }
}
