<?php

namespace App\Models;

use App\Enums\ChatType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    protected $fillable = [
        'type',
        'admin_id',
        'name',
    ];

    protected $casts = [
        'type' => ChatType::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['user_name', 'accepted'])->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getAvatarUrlAttribute()
    {
        switch ($this->type) {
            case ChatType::Personal:
                return $this->users->firstWhere('id', '!=', auth()->id())->avatar_url;
            default:
                return '';
        }
    }

    public function getNameAttribute()
    {
        return $this->name ?? $this->users->first(fn($user) => $user->id !== auth()->id())->name;
    }

}
