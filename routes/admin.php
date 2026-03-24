<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/login', [AuthController::class, 'loginPage'])->name('login.page');
Route::post('/login/check', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['admin.auth']], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Localization Toggle ──────────────────────────────────────────
    Route::get('/lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'ar'])) {
            session()->put('locale', $locale);
        }
        return redirect()->back();
    })->name('setLang');

    // ── Dashboard ──────────────────────────────────────────────────────
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // ── Roles & Permissions ────────────────────────────────────────────
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);

    // ── Admins Management ──────────────────────────────────────────────
    Route::resource('manage_admins', \App\Http\Controllers\Admin\ManageAdminsController::class);

    // ── Work Days ──────────────────────────────────────────────────────
    Route::resource('work_days', \App\Http\Controllers\Admin\WorkDayController::class);
    Route::get('/work_days/{workDay}/close', [\App\Http\Controllers\Admin\WorkDayController::class, 'showCloseForm'])->name('work_days.showCloseForm');
    Route::post('/work_days/{workDay}/close', [\App\Http\Controllers\Admin\WorkDayController::class, 'close'])->name('work_days.close');

    // ── Suppliers ──────────────────────────────────────────────────────
    Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);

    // ── Supplies (Purchases) ───────────────────────────────────────────
    Route::resource('supplies', \App\Http\Controllers\Admin\SupplyController::class)->except(['edit', 'update', 'destroy']);

    // ── Workers (HR) ───────────────────────────────────────────────────
    Route::resource('workers', \App\Http\Controllers\Admin\WorkerController::class);
});