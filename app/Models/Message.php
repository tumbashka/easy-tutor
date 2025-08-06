<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'user_name',
        'text',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    public function isRead(): bool
    {
        return (bool)$this->reads()->where('user_id', '!=', auth()->id())->count();
    }

    public function isReadByUser(?User $user = null): bool
    {
        if(is_null($user)) {
            $user = auth()->user();
        }

        return (bool)$this->reads()->where('user_id', $user->id)->count();
    }
}
