# Bakery Accounting System & Testing Flow Guide

This document explains what data currently exists in your seeded database, how the core technical architecture of your bakery system operates, and exactly how you can test each component sequentially.

## 1. What Data is Currently Seeded?

The system has been completely wiped and re-seeded using `migrate:fresh --seed`. It currently contains:

- **1 Super Admin**: Email: `admin@example.com`, Password: `Password@123` (Granted explicit `Super Admin` role governing 48 module permissions).
- **Currencies & Categories**: Default predefined records including Flour (طحين), Diesel (مازوت), Yeast (خميرة), Salt (ملح), Syrian Pounds (SYP), and US Dollars (USD).
- **2 Suppliers**: E.g., Flour Trader "Omar" and Diesel Distributor "Ahmad".
- **2 Distributors**: Local Market Purchasers.
- **2 Workers**: Including explicit Daily Wages setup.
- **2 Work Days**: 
  - One **"Settled" (Past) Work Day**: Has supplies, completed worker shifts, sales, and a recorded closing balance.
  - One **"Active" Work Day**: Represents "Today". It is currently accumulating active inventory, sales debts, active clocked-in workers, and operational expenses.

---

## 2. How the Components Are Connected

At the heart of the system is the **Active Work Day**. Think of the Work Day as a ledger. 
Virtually every financial or business transaction in the system **must** attach itself to the currently Active Work Day.

### The Ecosystem Architecture
1. **Warehouse & Supplies (Purchasing)**: When you buy FLOUR from a `Supplier`, a `Supply` record is created. It impacts the `Warehouse` stock, and the money paid (or left as debt) registers instantly as cash out for the **Active Work Day**.
2. **Distributions (Sales)**: When bread bundles are sold to a `Distributor`, a `Distribution` record is formed. It calculates the income (cash in) dynamically based on the bundles sold and registers the payment into the **Active Work Day**.
3. **Workers & Attendance**: When workers clock in (`WorkerShift`), they are evaluated based on their base wage. Any cash given to them (`WorkerTransaction` - advances) registers organically as an Expense inside the **Active Work Day**.
4. **General Expenses**: Operational fees (e.g., fuel, repairs, municipality taxes) are logged through the Expenses UI and are immediately grouped under the **Active Work Day**.
5. **Role-Based Access Control (RBAC)**: All of the UI components and API routes mentioned above are walled behind Spatie Laravel-Permissions. Your current Admin user bypasses this because it possesses the all-encompassing `Super Admin` role via the seeder.

---

## 3. Step-by-Step Testing Flow

Follow these sequential steps to thoroughly test the connected systems:

### Step 1: Login & Initial Check
- **Go to**: `/admin/login`
- **Credentials**: `admin@example.com` / `Password@123`
- Check the **Dashboard**: Observe the KPI blocks, Charts, and Top Distributor tables. Because the seeder placed real transactions inside the Active Work Day, these charts should already feature vivid data.

### Step 2: The Active Work Day
- Navigate to **"Work Days"** in the sidebar.
- Click the **"Action"** (Stop/Power icon) next to the currently `Active` work day to open the **Settlement/Close Day View**.
- Examine how this single page dynamically pulls in data from *every* active module in the system (Sales, Worker Advances, Supply Purchases, Operational Expenses).
- *Do not close it yet!*

### Step 3: Test Purchasing (Inventory Flow)
- Go to the **Supplies** tab.
- Click **"Register New Supply"**.
- Add a purchase from "Omar" for 500 KG of Flour. Note the currency modal and exchange rates.
- Go to the **Warehouse** tab. See how the new 500 KG instantly adds to the *Available Stock*.

### Step 4: Test Sales (Distributor Flow)
- Go to the **Distributions** tab.
- Add a Sale to "Khaled" for 200 Bundles.
- Go to the **Accounts -> Profit & Loss** page, and see your "Sales Income" increase dynamically based on that test entry.
- Go to the **Distributors -> Khaled's Details**, and look at his running financial ledger.

### Step 5: Test HR (Worker Shifting)
- Go to the **Attendance** tab.
- You will see "Samer" is already clocked in (`Active Shift`).
- Click "Clock Out", input the number of accumulated leftover bread bundles Samer brought back, and any cash he directly handed over to the cash register.
- Check **Workers -> Samer -> Details** to see his running balance change based on the processed shift.

### Step 6: Close the Day & Accounts Yield
- Return to **Work Days** -> Close Day.
- Fill out the daily *Raw Material Consumption* (How much Flour/Diesel you used today).
- Verify the dynamically calculated *Carry-Over Bundles* based on worker shift returns and distributor returns.
- Choose your drawer's *Carry-Over Cash* in SYP, and click **Finalize Settlement**.
- Finally, navigate to the **Accounts** tab on the sidebar and switch the date filter to observe the generated **Net Profit** logic for that specific closed Work Day scope.

---

## 4. Testing Roles & Security (Bonus)
- Go to **Roles & Permissions**.
- Create a new role named "Cashier" and check *only* the `view dashboard` and `create expenses` grid boxes.
- Go to **Administrators** and create a new Admin login. Assign them the "Cashier" role.
## 5. Dockerized Client Testing (New)
For seamless client delivery and testing across different operating systems, the entire Bakery Accounting System has been dockerized.

- If you or your client wish to test the system without installing PHP, Composer, or MySQL locally, refer to the **[docker-tutorial.md](docker-tutorial.md)** file in the project root.
- It contains exactly three commands to spin up the App, Nginx, and MySQL, automatically seeding the database precisely as described in this flow document.
