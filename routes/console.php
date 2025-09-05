<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('modulkassa:update-products')->dailyAt('03:00');
Schedule::command('modulkassa:get-sales')->everyTenMinutes();