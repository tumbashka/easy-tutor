<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    public function users(): BelongsToMany
    {
        return $this->BelongsToMany(User::class)->withPivot('is_default');
    }

    public function boards(): HasMany
    {
        return $this->hasMany(Board::class);
    }
}
