<?php

use App\Http\Controllers\LeaveRecordController;
use App\Http\Controllers\TimeRecordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Clock route to TimeRecordController
    Route::post('/clock', [TimeRecordController::class, 'handleClock']);

    // Add leave route to LeaveRecordController
    Route::post('/leave', [LeaveRecordController::class, 'addLeave']);

});
