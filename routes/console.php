<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('modulkassa:update-products')->everyMinute();