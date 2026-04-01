<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkDay;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\Distributor;
use App\Models\Distribution;
use App\Models\Worker;
use App\Models\WorkerShift;
use App\Models\WorkerTransaction;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Admin;
use App\Models\Consumption;
use Carbon\Carbon;

class SystemTestingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('email', 'admin@example.com')->first();
        $baseCurrency = Currency::where('is_default', true)->first();
        $usdCurrency = Currency::where('code', 'USD')->first();

        // 1. Create Suppliers
        $supplier1 = Supplier::create([
            'first_name' => 'Omar',
            'last_name' => 'Flour Trader',
            'title' => 'Al-Aman Mills',
        ]);

        $supplier2 = Supplier::create([
            'first_name' => 'Ahmad',
            'last_name' => 'Fuel Distribution',
            'title' => 'Sadcop Agent',
        ]);

        // 2. Create Distributors
        $distributor1 = Distributor::create([
            'first_name' => 'Khaled',
            'last_name' => 'Supermarket Chains',
            'title' => 'Al-Huda Markets',
            'preferred_currency_id' => $baseCurrency->id,
        ]);

        $distributor2 = Distributor::create([
            'first_name' => 'Youssef',
            'last_name' => 'Local Groceries',
            'preferred_currency_id' => $baseCurrency->id,
        ]);

        // 3. Create Workers
        $worker1 = Worker::create([
            'first_name' => 'Samer',
            'last_name' => 'Bakery Hand',
            'title' => 'Head Baker',
            'daily_wage' => 150000,
            'currency_id' => $baseCurrency->id,
            'exchange_rate' => 1.0,
        ]);

        $worker2 = Worker::create([
            'first_name' => 'Rami',
            'last_name' => 'Delivery',
            'title' => 'Driver',
            'daily_wage' => 100000,
            'currency_id' => $baseCurrency->id,
            'exchange_rate' => 1.0,
        ]);

        // 4. Create a past "Settled" Work Day
        // $pastDay = WorkDay::create([
        //     'start_time' => Carbon::now()->subDays(2)->setHour(6)->setMinute(0),
        //     'end_time' => Carbon::now()->subDays(2)->setHour(18)->setMinute(0),
        //     'status' => 'closed',
        //     'closed_by' => $admin->id,
        //     'carried_over_bundles' => 50, // bundles left at end of day
        //     'carried_over_currency_id' => $baseCurrency->id,
        //     'carried_over_money' => 200000, // Cash left inside the drawer
        // ]);

        // Add Past Supplies
        // $flourCategory = Category::where('name', 'like', '%طحين%')->orWhere('name', 'Flour')->first();
        // if ($flourCategory) {
        //     Supply::create([
        //         'work_day_id' => $pastDay->id,
        //         'supplier_id' => $supplier1->id,
        //         'category_id' => $flourCategory->id,
        //         'admin_id' => $admin->id,
        //         'quantity' => 2000, // 2 tons
        //         'unit_price' => 5000,
        //         'total_cost' => 10000000,
        //         'paid_amount' => 5000000, // Half paid
        //         'currency_id' => $baseCurrency->id,
        //         'exchange_rate' => 1,
        //     ]);
        // }

        // Past Work Shifts
        // $shift1 = WorkerShift::create([
        //     'worker_id' => $worker1->id,
        //     'work_day_id' => $pastDay->id,
        //     'admin_id' => $admin->id,
        //     'check_in' => Carbon::now()->subDays(2)->setHour(6),
        //     'check_out' => Carbon::now()->subDays(2)->setHour(18),
        //     'snapshot_daily_wage' => 150000,
        //     'snapshot_currency_id' => $baseCurrency->id,
        //     'snapshot_exchange_rate' => 1,
        //     'bundles_received' => 2000,
        //     'bundles_returned' => 150,
        //     'cash_collected' => 5000000,
        //     'cash_currency_id' => $baseCurrency->id,
        //     'cash_exchange_rate' => 1,
        // ]);

        // 5. Create the "Active" Work Day
        // $activeDay = WorkDay::create([
        //     'start_time' => Carbon::yesterday()->setHour(22)->setMinute(0),
        //     'status' => 'active',
        // ]);

        // Add Active Supplies
        // $dieselCategory = Category::where('name', 'like', '%مازوت%')->orWhere('name', 'Diesel')->first();
        // if ($dieselCategory) {
        //     Supply::create([
        //         'work_day_id' => $activeDay->id,
        //         'supplier_id' => $supplier2->id,
        //         'category_id' => $dieselCategory->id,
        //         'admin_id' => $admin->id,
        //         'quantity' => 1000, // 1000 Liters
        //         'unit_price' => 12000,
        //         'total_cost' => 12000000,
        //         'paid_amount' => 12000000, // Fully Paid
        //         'currency_id' => $baseCurrency->id,
        //         'exchange_rate' => 1,
        //     ]);
        // }

        // Active Distributions (Sales)
        // Distribution::create([
        //     'work_day_id' => $activeDay->id,
        //     'distributor_id' => $distributor1->id,
        //     'created_by' => $admin->id,
        //     'price_per_bundle' => 3500,
        //     'bundle_count' => 1000,
        //     'total_price' => 3500000,
        //     'amount_paid' => 2000000, // Left 1.5M debt
        //     'currency_id' => $baseCurrency->id,
        //     'exchange_rate' => 1,
        // ]);

        // Distribution::create([
        //     'work_day_id' => $activeDay->id,
        //     'distributor_id' => $distributor2->id,
        //     'created_by' => $admin->id,
        //     'price_per_bundle' => 3500,
        //     'bundle_count' => 500,
        //     'total_price' => 1750000,
        //     'amount_paid' => 1750000, // Fully Paid
        //     'currency_id' => $baseCurrency->id,
        //     'exchange_rate' => 1,
        // ]);

        // Active Work Shifts (Currently clocked in)
        // WorkerShift::create([
        //     'worker_id' => $worker1->id,
        //     'work_day_id' => $activeDay->id,
        //     'admin_id' => $admin->id,
        //     'check_in' => Carbon::now()->subHours(4),
        //     'snapshot_daily_wage' => 150000,
        //     'snapshot_currency_id' => $baseCurrency->id,
        //     'snapshot_exchange_rate' => 1,
        //     'bundles_received' => 1800,
        //     'bundles_returned' => 0,
        //     'cash_collected' => 0,
        // ]);

        // Worker Advance Payment Today
        // WorkerTransaction::create([
        //     'worker_id' => $worker1->id,
        //     'work_day_id' => $activeDay->id,
        //     'admin_id' => $admin->id,
        //     'type' => 'advance',
        //     'amount' => 50000,
        //     'currency_id' => $baseCurrency->id,
        //     'exchange_rate' => 1,
        //     'notes' => 'Mid-shift advance for meals.',
        // ]);

        // Expenses for Active Day
        // Expense::create([
        //     'work_day_id' => $activeDay->id,
        //     'admin_id' => $admin->id,
        //     'category' => 'operating',
        //     'title' => 'Generator Maintenance',
        //     'amount' => 150000,
        //     'currency_id' => $baseCurrency->id,
        //     'exchange_rate' => 1,
        //     'notes' => 'Fixed the oil leak.',
        // ]);

        // Expense::create([
        //     'work_day_id' => $activeDay->id,
        //     'admin_id' => $admin->id,
        //     'category' => 'logistics',
        //     'title' => 'Patrol Fees',
        //     'amount' => 50000,
        //     'currency_id' => $baseCurrency->id,
        //     'exchange_rate' => 1,
        // ]);
    }
}
