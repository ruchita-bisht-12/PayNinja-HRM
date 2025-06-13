<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands;

return Application::configure(basePath: dirname(__DIR__))
    ->withCommands([
        Commands\MarkAbsentEmployees::class,
        Commands\MarkLeavesCommand::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\superadmin::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Schedule attendance commands
        $schedule->command('attendance:mark-leaves')
            ->dailyAt('19:00')
            ->timezone('Asia/Kolkata')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/schedule.log'));

        $schedule->command('attendance:mark-absent')
            ->dailyAt('19:00')
            ->timezone('Asia/Kolkata')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/schedule.log'));
    })
    ->create();
