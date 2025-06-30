<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint',
        'p256dh',
        'auth',
        'user_id',
        'user_agent',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'p256dh',
        'auth',
    ];

    /**
     * Связь с пользователем
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получение краткой информации об endpoint
     */
    public function getEndpointShortAttribute(): string
    {
        return substr($this->endpoint, 0, 50) . '...';
    }

    /**
     * Проверка принадлежности подписки текущему пользователю
     */
    public function belongsToUser(?int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Scope для получения подписок пользователя
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope для получения активных подписок
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('endpoint');
    }

}