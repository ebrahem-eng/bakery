<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/login', [AuthController::class, 'loginPage'])->name('login.page');
Route::post('/login/check', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['admin.auth']], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Dashboard ──────────────────────────────────────────────────────
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
});