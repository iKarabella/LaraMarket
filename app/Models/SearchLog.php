<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;

class SearchLog extends Model
{
    use Prunable;

    protected $fillable = [
        'search',     // строка поиска
        'driver',     // драйвер поиска
        'user_id',    // ID пользователя
        'session_id', // ID сессии
        'results'     // результаты поиска
    ];

    protected $casts = [
        'results' => 'array',
    ];
    
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(90));
    }
}
