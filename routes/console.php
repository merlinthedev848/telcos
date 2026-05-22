<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('telecomos:dunning-run')->hourly()->withoutOverlapping();
Schedule::command('telecomos:paywall-sync')->everyFifteenMinutes()->withoutOverlapping();
