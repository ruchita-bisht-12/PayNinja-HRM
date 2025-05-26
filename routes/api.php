<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AttendanceController as APIAttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Attendance routes
    Route::prefix('attendance')->group(function () {
        // Check in/out
        Route::post('/checkin', [APIAttendanceController::class, 'checkIn']);
        Route::post('/checkout', [APIAttendanceController::class, 'checkOut']);
        
        // Geolocation endpoints
        Route::get('/geolocation-settings', [APIAttendanceController::class, 'getGeolocationSettings']);
        Route::get('/check-location', [APIAttendanceController::class, 'checkLocation']);
    });
});
