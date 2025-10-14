<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('modulkassa:update-products')->dailyAt('03:00');
Schedule::command('modulkassa:get-sales')->everyTenMinutes();
Schedule::command('model:prune')->daily();

//TODO cd /var/www/laramarket && vendor/bin/sail php artisan schedule:run >> /dev/null 2>&1