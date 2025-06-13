<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/test-logging', function () {
    Log::info('Test log message from test route');
    
    // Test different log levels
    Log::emergency('Emergency test message');
    Log::alert('Alert test message');
    Log::critical('Critical test message');
    Log::error('Error test message');
    Log::warning('Warning test message');
    Log::notice('Notice test message');
    Log::info('Info test message');
    Log::debug('Debug test message');
    
    return response()->json(['message' => 'Test logs written. Check laravel.log']);
});
