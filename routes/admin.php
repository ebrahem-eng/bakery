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
    Route::get('/dashboard', function () {
        $activeWorkDay = \App\Models\WorkDay::where('status', 'active')
            ->with(['distributions', 'supplies', 'expenses', 'workerShifts', 'workerTransactions', 'distributorReturns'])
            ->first();
            
        $lastDays = \App\Models\WorkDay::where('status', 'closed')
            ->orderBy('id', 'desc')->take(5)->get();
            
        $totalWorkers = \App\Models\Worker::count();
        $totalDistributors = \App\Models\Distributor::count();

        $todaySales = 0;
        $todayExpenses = 0;
        $todayBundlesSold = 0;
        
        if ($activeWorkDay) {
            $todaySales = $activeWorkDay->distributions->sum('total_price') - $activeWorkDay->distributorReturns->sum('total_refund');
            $todayBundlesSold = $activeWorkDay->distributions->sum('bundle_count');
            
            $todayExpenses += $activeWorkDay->supplies->sum('total_cost') + $activeWorkDay->supplies->sum('unloading_fee');
            $todayExpenses += $activeWorkDay->workerShifts->sum('snapshot_daily_wage');
            $todayExpenses += $activeWorkDay->workerTransactions->where('type', 'allowance')->sum('amount');
            $todayExpenses -= $activeWorkDay->workerTransactions->where('type', 'deduction')->sum('amount');
            $todayExpenses += $activeWorkDay->expenses->sum('amount');
        }
        $defaultCurrency = \App\Models\Currency::where('is_default', true)->first();

        return view('Admin.dashboard', compact('activeWorkDay', 'lastDays', 'totalWorkers', 'totalDistributors', 'todaySales', 'todayExpenses', 'todayBundlesSold', 'defaultCurrency'));
    })->name('dashboard');

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
    Route::middleware('permission:view activity log')->group(function () {
        Route::get('activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('activity-log/{activity}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-log.show');
        Route::delete('activity-log/{activity}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'destroy'])->name('activity-log.destroy');
        Route::delete('activity-log-clear/all', [\App\Http\Controllers\Admin\ActivityLogController::class, 'clear'])->name('activity-log.clear');
    });
});