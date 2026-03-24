<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/login', [AuthController::class, 'loginPage'])->name('login.page');
Route::post('/login/check', [AuthController::class, 'login'])->name('login');

// ── Localization Toggle (Global for Admin) ───────────────────────
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('setLang');

Route::group(['middleware' => ['admin.auth']], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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

    // ── Workers (HR & Attendance) ────────────────────────────────────────
    Route::resource('workers', \App\Http\Controllers\Admin\WorkerController::class);
    Route::get('attendance', [\App\Http\Controllers\Admin\WorkerAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance/clock-in', [\App\Http\Controllers\Admin\WorkerAttendanceController::class, 'clockIn'])->name('attendance.clock_in');
    Route::post('attendance/{shift}/clock-out', [\App\Http\Controllers\Admin\WorkerAttendanceController::class, 'clockOut'])->name('attendance.clock_out');
    Route::post('attendance/transaction', [\App\Http\Controllers\Admin\WorkerAttendanceController::class, 'storeTransaction'])->name('attendance.transaction');
});