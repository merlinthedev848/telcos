<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('telecomos:dunning-run')->hourly()->withoutOverlapping();
Schedule::command('telecomos:paywall-sync')->everyFifteenMinutes()->withoutOverlapping();
Schedule::command('telecomos:automations-run')->dailyAt('02:00')->withoutOverlapping();
