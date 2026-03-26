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
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

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
    Route::get('warehouse', [\App\Http\Controllers\Admin\WarehouseController::class, 'index'])->name('warehouse.index');

    // ── Distributions (Sales) ──────────────────────────────────────────
    Route::resource('distributors', \App\Http\Controllers\Admin\DistributorController::class);
    Route::get('distributions', [\App\Http\Controllers\Admin\DistributionController::class, 'index'])->name('distributions.index');
    Route::post('distributions/store', [\App\Http\Controllers\Admin\DistributionController::class, 'storeDistribution'])->name('distributions.store');
    Route::post('distributions/return', [\App\Http\Controllers\Admin\DistributionController::class, 'storeReturn'])->name('distributions.return');
    Route::post('distributions/transaction', [\App\Http\Controllers\Admin\DistributionController::class, 'storeTransaction'])->name('distributions.transaction');

    // ── Expenses ───────────────────────────────────────────────────────
    Route::get('expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('expenses/store', [\App\Http\Controllers\Admin\ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'show'])->name('expenses.show');
    Route::delete('expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // ── Workers (HR & Attendance) ────────────────────────────────────────
    Route::resource('workers', \App\Http\Controllers\Admin\WorkerController::class);
    Route::get('attendance', [\App\Http\Controllers\Admin\WorkerAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance/clock-in', [\App\Http\Controllers\Admin\WorkerAttendanceController::class, 'clockIn'])->name('attendance.clock_in');
    Route::post('attendance/{shift}/clock-out', [\App\Http\Controllers\Admin\WorkerAttendanceController::class, 'clockOut'])->name('attendance.clock_out');
    Route::post('attendance/transaction', [\App\Http\Controllers\Admin\WorkerAttendanceController::class, 'storeTransaction'])->name('attendance.transaction');

    // ── Activity Log (Monitoring) ─────────────────────────────────────
    Route::middleware('permission:view activity log,admin')->group(function () {
        Route::get('activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('activity-log/{activity}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-log.show');
        Route::delete('activity-log/{activity}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'destroy'])->name('activity-log.destroy');
        Route::delete('activity-log-clear/all', [\App\Http\Controllers\Admin\ActivityLogController::class, 'clear'])->name('activity-log.clear');
    });

    // ── Settings ──────────────────────────────────────────────────────
    Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/bakery-info', [\App\Http\Controllers\Admin\SettingsController::class, 'updateBakeryInfo'])->name('settings.bakeryInfo');
    Route::post('settings/currencies', [\App\Http\Controllers\Admin\SettingsController::class, 'storeCurrency'])->name('settings.currencies.store');
    Route::put('settings/currencies/{currency}', [\App\Http\Controllers\Admin\SettingsController::class, 'updateCurrency'])->name('settings.currencies.update');
    Route::delete('settings/currencies/{currency}', [\App\Http\Controllers\Admin\SettingsController::class, 'destroyCurrency'])->name('settings.currencies.destroy');
    Route::post('settings/categories', [\App\Http\Controllers\Admin\SettingsController::class, 'storeCategory'])->name('settings.categories.store');
    Route::put('settings/categories/{category}', [\App\Http\Controllers\Admin\SettingsController::class, 'updateCategory'])->name('settings.categories.update');
    Route::delete('settings/categories/{category}', [\App\Http\Controllers\Admin\SettingsController::class, 'destroyCategory'])->name('settings.categories.destroy');
});