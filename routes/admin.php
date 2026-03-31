<?php

use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DistributionController;
use App\Http\Controllers\Admin\DistributorController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ManageAdminsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SupplyController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\WorkDayController;
use App\Http\Controllers\Admin\WorkerAttendanceController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\WorkerWageController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard,admin')->name('dashboard');

    // ── Accounts ──────────────────────────────────────────────────────
    Route::get('/accounts', [AccountsController::class, 'index'])
        ->middleware('permission:view accounts,admin')->name('accounts.index');
    Route::get('/accounts/debts', [AccountsController::class, 'debts'])
        ->middleware('permission:view accounts,admin')->name('accounts.debts');

    // ── Roles & Permissions ────────────────────────────────────────────
    Route::resource('roles', RoleController::class)->middleware('permission:view roles,admin');

    // ── Admins Management ──────────────────────────────────────────────
    Route::resource('manage_admins', ManageAdminsController::class)->middleware('permission:view admins,admin');

    // ── Work Days ──────────────────────────────────────────────────────
    Route::group(['middleware' => ['permission:view work days,admin']], function () {
        Route::resource('work_days', WorkDayController::class);
        Route::get('/work_days/{workDay}/close', [WorkDayController::class, 'showCloseForm'])->name('work_days.showCloseForm');
        Route::post('/work_days/{workDay}/close', [WorkDayController::class, 'close'])->name('work_days.close');
    });

    // ── Suppliers ──────────────────────────────────────────────────────
    Route::resource('suppliers', SupplierController::class)->middleware('permission:view suppliers,admin');

    // ── Supplies (Purchases) ───────────────────────────────────────────
    Route::group(['middleware' => ['permission:view supplies,admin']], function () {
        Route::resource('supplies', SupplyController::class)->except(['edit', 'update', 'destroy']);
        Route::post('supplies/{supply}/pay', [SupplyController::class, 'registerPayment'])->name('supplies.pay');
    });

    // ── Warehouse ──────────────────────────────────────────────────────
    Route::get('warehouse', [WarehouseController::class, 'index'])
        ->middleware('permission:view warehouse,admin')->name('warehouse.index');

    // ── Distributions (Sales) ──────────────────────────────────────────
    Route::resource('distributors', DistributorController::class)->middleware('permission:view distributors,admin');
    Route::post('distributors/{distributor}/transaction', [DistributorController::class, 'storeTransaction'])->name('distributors.transaction.store')->middleware('permission:view distributors,admin');
    Route::get('distributors/{distributor}/transaction/create', [DistributorController::class, 'createTransaction'])->name('distributors.transaction.create')->middleware('permission:view distributors,admin');

    Route::group(['middleware' => ['permission:view distributions,admin']], function () {
        Route::get('distributions', [DistributionController::class, 'index'])->name('distributions.index');
        Route::post('distributions/store', [DistributionController::class, 'storeDistribution'])->name('distributions.store');
        Route::post('distributions/return', [DistributionController::class, 'storeReturn'])->name('distributions.return');
        Route::post('distributions/transaction', [DistributionController::class, 'storeTransaction'])->name('distributions.transaction');
    });

    // ── Expenses ───────────────────────────────────────────────────────
    Route::group(['middleware' => ['permission:view expenses,admin']], function () {
        Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('expenses/store', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
        Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });

    // ── Workers (HR & Attendance) ────────────────────────────────────────
    Route::resource('workers', WorkerController::class)->middleware('permission:view workers,admin');

    Route::group(['middleware' => ['permission:view presence,admin']], function () {
        Route::get('attendance/presence', [WorkerAttendanceController::class, 'presence'])->name('attendance.presence');
        Route::post('attendance/mark-attendance', [WorkerAttendanceController::class, 'markAttendance'])->name('attendance.mark_attendance');
        Route::post('attendance/bulk-mark-attendance', [WorkerAttendanceController::class, 'bulkMarkAttendance'])->name('attendance.bulk_mark_attendance');
        Route::post('attendance/{attendance}/mark-departure', [WorkerAttendanceController::class, 'markDeparture'])->name('attendance.mark_departure');
        Route::post('attendance/bulk-mark-departure', [WorkerAttendanceController::class, 'bulkMarkDeparture'])->name('attendance.bulk_mark_departure');
    });

    Route::group(['middleware' => ['permission:view attendance,admin']], function () {
        Route::get('attendance', [WorkerAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance/clock-in', [WorkerAttendanceController::class, 'clockIn'])->name('attendance.clock_in');
        Route::post('attendance/{shift}/clock-out', [WorkerAttendanceController::class, 'clockOut'])->name('attendance.clock_out');
        Route::post('attendance/transaction', [WorkerAttendanceController::class, 'storeTransaction'])->name('attendance.transaction');
    });
        
    Route::group(['middleware' => ['permission:view wages,admin']], function () {
        Route::get('wages', [WorkerWageController::class, 'index'])->name('wages.index');
        Route::post('wages/store', [WorkerWageController::class, 'store'])->name('wages.store');
    });

    // ── Activity Log (Monitoring) ─────────────────────────────────────
    Route::middleware('permission:view activity log,admin')->group(function () {
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('activity-log/{activity}', [ActivityLogController::class, 'show'])->name('activity-log.show');
        Route::delete('activity-log/{activity}', [ActivityLogController::class, 'destroy'])->name('activity-log.destroy');
        Route::delete('activity-log-clear/all', [ActivityLogController::class, 'clear'])->name('activity-log.clear');
    });

    // ── Settings ──────────────────────────────────────────────────────
    Route::group(['middleware' => ['permission:manage settings,admin']], function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings/bakery-info', [SettingsController::class, 'updateBakeryInfo'])->name('settings.bakeryInfo');
        Route::post('settings/currencies', [SettingsController::class, 'storeCurrency'])->name('settings.currencies.store');
        Route::put('settings/currencies/{currency}', [SettingsController::class, 'updateCurrency'])->name('settings.currencies.update');
        Route::delete('settings/currencies/{currency}', [SettingsController::class, 'destroyCurrency'])->name('settings.currencies.destroy');
        Route::post('settings/categories', [SettingsController::class, 'storeCategory'])->name('settings.categories.store');
        Route::put('settings/categories/{category}', [SettingsController::class, 'updateCategory'])->name('settings.categories.update');
        Route::delete('settings/categories/{category}', [SettingsController::class, 'destroyCategory'])->name('settings.categories.destroy');
    });
});
