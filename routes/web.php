<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\NewReceipt;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Login bypass locally for easier development
Route::get('/bypass', function () {
    if (app()->environment("local")) {
        Auth::login(User::first());
    }
});

Route::get('/auth/redirect', [AuthController::class, 'redirect'])->name('login');
Route::get('/auth/callback', [AuthController::class, 'callback']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/receipts/add', NewReceipt::class)->name('receipts.create');
});
