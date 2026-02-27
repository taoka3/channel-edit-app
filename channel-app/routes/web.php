<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Channels
    Route::get('/channels', [\App\Http\Controllers\ChannelController::class, 'index'])->name('channels.index');
    Route::post('/channels/sync', [\App\Http\Controllers\ChannelController::class, 'sync'])->name('channels.sync');
    Route::put('/channels/{channel}', [\App\Http\Controllers\ChannelController::class, 'update'])->name('channels.update');
    Route::delete('/channels/{channel}', [\App\Http\Controllers\ChannelController::class, 'destroy'])->name('channels.destroy');

    // Categories
    Route::resource('categories', \App\Http\Controllers\CategoryController::class)->except(['show']);
});
