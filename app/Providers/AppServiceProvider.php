<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        URL::forceScheme('https');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        $this->app['request']->server->set('HTTPS','on'); // добавил, т.к. eloquent->paginate генерировал ссылки пагинации c http протоколом

        URL::forceScheme('https');

        setlocale(LC_ALL, 'ru_RU.utf8');
        
        Carbon::setLocale('ru');
    }
}
