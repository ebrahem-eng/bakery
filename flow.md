# Bakery Accounting System — Complete Testing Flow Guide

This document explains what data currently exists in your seeded database, how the core technical architecture of your bakery system operates, and exactly how you can test **every single feature and module** sequentially.

---

## 1. What Data is Currently Seeded?

The system has been completely wiped and re-seeded using `migrate:fresh --seed`. It currently contains:

- **1 Super Admin**: Email: `admin@example.com`, Password: `Password@123` (Granted explicit `Super Admin` role governing **all** module permissions).
- **Currencies**: Syrian Pounds (SYP — default), US Dollars (USD) with a configurable exchange rate.
- **Material Categories**: Flour (طحين), Diesel (مازوت), Yeast (خميرة), Salt (ملح).
- **2 Suppliers**: E.g., Flour Trader "Omar" and Diesel Distributor "Ahmad", each mapped to specific material categories.
- **2 Distributors**: Local wholesale market purchasers with seeded debt balances.
- **2 Workers**: Including explicit Daily Wages setup and seeded attendance shifts.
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
3. **Workers & Attendance**: When workers clock in (`WorkerShift`), they are evaluated based on their base wage. Any cash given to them (`WorkerTransaction` — advances/allowances) registers organically as an Expense inside the **Active Work Day**.
4. **General Expenses**: Operational fees (e.g., fuel, repairs, municipality taxes) are logged through the Expenses UI and are immediately grouped under the **Active Work Day**.
5. **Role-Based Access Control (RBAC)**: All of the UI components and API routes mentioned above are walled behind Spatie Laravel-Permissions. Your current Admin user bypasses this because it possesses the all-encompassing `Super Admin` role via the seeder.
6. **Activity Log**: Every create, update, and delete action across the entire system is tracked with full before/after data diffs, timestamped, and linked to the performing admin.

---

## 3. Step-by-Step Testing Flow

Follow these sequential steps to thoroughly test **every module** in the system.

### Step 1: Login & Dashboard
- **Go to**: `/admin/login`
- **Credentials**: `admin@example.com` / `Password@123`
- Check the **Dashboard** (لوحة التحكم):
  - Observe the KPI blocks at the top (Total Revenue, Total Expenses, Net Profit, Active Workers).
  - Scroll down to view the **Revenue vs Expenses Trend** chart, **Top Distributors** table, and **Expense Breakdown** pie chart.
  - Use the Period filter (Today / This Week / This Month / This Year) to see how all metrics recalculate dynamically.

### Step 2: Settings (الإعدادات)
- Navigate to **"Settings"** in the sidebar.
- **General Tab**: View/update the Bakery Name, address, phone number, and Default Language (Arabic/English). Click "Save Configuration".
- **Currencies Tab**: View SYP (default) and USD. Try editing the USD exchange rate. This rate propagates to every supply, expense, and distributor transaction in the system.
- **Categories Tab**: Confirm that default production material categories (Flour, Diesel, Yeast, Salt) are active. Try adding a new category (e.g., "Sugar") and verify it appears in the supply forms.

### Step 3: Work Days (أيام العمل)
- Navigate to **"Work Days"** in the sidebar.
- You will see 2 seeded work days: one **Closed** (past) and one **Active** (current).
- Click the **Action icon** next to the Active work day to open the **Settlement/Close Day View**.
- Examine how this page dynamically pulls in data from *every* module: Sales totals, Worker shift wages, Supply purchases, and Operational Expenses.
- **Do not close the day yet!** (We need it active for the next steps.)

### Step 4: Purchases & Suppliers (المشتريات والموردين)
This is a dropdown menu with two sub-pages:

#### 4a: Supply Records (سجلات التوريد)
- Click **"Supply Records"**.
- Click **"Register New Supply"** to open the batch invoice form.
- Select Supplier "Omar". Add a line for 500 KG of Flour. Note:
  - The currency selector and exchange rate fields.
  - Freight & Unloading fee sections.
- Submit the supply. Confirm you see it in the supply list with its total cost and payment status.

#### 4b: Manage Vendors (إدارة الموردين)
- Click **"Manage Vendors"**.
- View the list of suppliers. Click **"View Profile"** on "Omar".
- Inside his profile, verify:
  - **Contact Numbers** section.
  - **Supplying Categories** (Flour).
  - **Full Transaction History** — shows the supply you just created.
  - **Outstanding Balance** — see how much debt is owed vs. paid.
- Try clicking **"Receive Payment"** to issue a partial payment against his debt.

### Step 5: Warehouse (المستودع)
- Navigate to **"Warehouse"** in the sidebar.
- View the stock cards for each material category.
- Confirm that the 500 KG of Flour you just purchased in Step 4 has been added to the Available Stock.
- Check the recent delivery history and consumption logs.

### Step 6: Expenses & Drawings (المصاريف والسحوبات)
- Navigate to **"Expenses & Drawings"** in the sidebar.
- View the list of expenses already recorded for the Active Work Day.
- Click **"Add Expense/Draw"**:
  - Create a "Logistics/Patrol" (لوجستيات/دوريات) expense for 50,000 SYP.
  - Create a "Personal Drawing" (سحب شخصي) for 20 USD — observe how the system automatically converts it using the live exchange rate.
- Verify both expenses appear in the list with the correct amounts and categories.
- Delete one of the test expenses to verify the delete functionality works.

### Step 7: Distributors & Sales (الموزعون والمبيعات)
This is a dropdown menu with two sub-pages:

#### 7a: Active Distributions (التوزيعات)
- Click **"Active Distributions"**.
- Click **"Add Distribution"** to sell bread bundles.
- Select a Distributor (e.g., "Khaled"). Enter 200 Bundles, set the price per bundle, and optionally enter a partial down payment.
- Submit and verify the distribution appears in the list.

#### 7b: Manage Distributors (إدارة الموزعين)
- Click **"Manage Distributors"**.
- View the list. Click **"View Details"** on "Khaled".
- Inside his profile, verify:
  - **Contact Numbers** and debt currency configuration.
  - **Full Transaction History** — shows the sale you just created.
  - **Outstanding Balance** — see how much Khaled owes.
- Try issuing a **"Receive Payment"** (استلام دفعة) to clear part of his debt.
- Try logging a **"Return"** (مرتجع) — simulating Khaled returning unsold bread bundles.
- Create a new distributor from scratch using the "Add Distributor" button.

### Step 8: Workers HR (شؤون العمال)
This is a dropdown menu with two sub-pages:

#### 8a: Personnel Roster (قائمة الموظفين)
- Click **"Personnel Roster"**.
- View the worker profiles. Click **"View Details"** on a worker (e.g., "Samer").
- Inside Samer's profile:
  - View his **Base Daily Wage**, mobile contacts, and job title.
  - Check the **Advances & Allowances** section — try issuing a "Personal Advance" (سلفة) of 100,000 SYP.
  - Check the **Shift History** panel showing all his completed and active clock-ins.
- Try registering a new worker using **"Register Worker"**.

#### 8b: Attendance & Shifts (الحضور والورديات)
- Click **"Attendance (Shifts)"**.
- View the Active Work Day's attendance board.
- If a worker is clocked in, click **"Clock Out"**:
  - Enter the number of unsold bread bundles the worker returned.
  - Enter any physical cash the worker handed to the register.
  - Submit and observe the worker's shift being closed with calculated net wages.
- If no worker is clocked in, click **"Clock In"** on a worker to start their shift.

### Step 9: Accounts & Financial Reports (الحسابات)
- Navigate to **"Accounts"** in the sidebar.
- This page contains multiple financial views:
  - **Profit & Loss Statement**: Shows Revenue, COGS, Gross Profit, Operating Expenses, and Net Profit.
  - **Cash Flow Summary**: Cash In (from distributions) vs. Cash Out (expenses, wages, supplies).
  - **Transaction Ledger**: A chronological log of every single financial movement in the system.
- Use the **Date Filter** (Today / This Week / This Month / All Time) to see data for different periods.
- Verify that the test supply (Step 4), expense (Step 6), and distribution (Step 7) all appear correctly in the ledger.

### Step 10: Admin Accounts (حسابات المسؤولين)
- Navigate to **"Admin Accounts"** in the sidebar.
- View the current list of administrators.
- Click **"Add Admin"**:
  - Fill in name, email, password, gender, age, address.
  - **Upload a Profile Picture** using the new file upload field.
  - Assign the "Super Admin" role.
  - Submit and verify the admin appears in the list.
- Click **"Edit"** on the new admin to verify you can update their info and change their profile picture (the current picture thumbnail is shown).

### Step 11: Activity Log (سجل النشاطات)
- Navigate to **"Activity Log"** in the sidebar.
- Verify that **every action** you performed in Steps 2-10 has been automatically logged:
  - Created supply → logged.
  - Created expense → logged.
  - Created distribution → logged.
  - Created admin → logged.
  - Updated settings → logged.
- Click on any log entry to view **full details**: old values, new values, the performing admin, and timestamp.
- Use the **filters** to narrow by Event Type (created/updated/deleted), Model, or specific admin.
- Test the **"Clear All Logs"** button to verify the bulk deletion works (with confirmation prompt).

### Step 12: Roles & Permissions (الأدوار والصلاحيات)
- Navigate to **"Roles & Permissions"** in the sidebar.
- View the existing `Super Admin` role and its full permission matrix.
- Click **"Create Role"**:
  - Name the role "Cashier".
  - Check **only**: `view dashboard`, `view expenses`, and `create expenses`.
  - Submit.
- Go back to **Admin Accounts** (Step 10), create a new admin, and assign them **only** the "Cashier" role.
- **Log out**, then log in as the new Cashier admin.
- Verify:
  - The **Sidebar** shows only Dashboard and Expenses links. All other modules (Accounts, Work Days, Suppliers, Distributors, Workers, Settings, Roles, Activity Log) are **completely hidden**.
  - Attempting to access a hidden route directly (e.g., `/admin/work_days`) returns **HTTP 403 Forbidden**.

### Step 13: Admin Profile & Navigation Bar
- Log back in as `admin@example.com`.
- Click on the **Profile Picture avatar** in the top-right navigation bar.
- A dropdown appears with:
  - **"System Preferences"** (تفضيلات النظام) — links to your profile settings.
  - **"Terminate Session"** (إنهاء الجلسة) — logs you out.
- Verify that when switching language to **Arabic**, the dropdown correctly flips to RTL alignment.
- Verify the profile picture you uploaded in Step 10 renders correctly in the circular avatar.

### Step 14: Close the Active Work Day
- Return to **Work Days** → Click Close Day on the Active work day.
- Fill out the daily **Raw Material Consumption** (how much Flour/Diesel you used today).
- Verify the dynamically calculated **Carry-Over Bundles** based on worker shift returns and distributor returns.
- Enter the **Carry-Over Cash** physical amount remaining in your drawer, and click **"Finalize Settlement"**.
- Navigate to **Accounts** and filter to today's date to verify the closed period's **Net Profit** calculation.

### Step 15: Language Switching (Arabic ↔ English)
- Throughout all steps, you can switch the system language between Arabic and English using the language toggle in the navigation bar or via Settings.
- Verify that:
  - All labels, buttons, and validation messages translate correctly.
  - The entire layout flips to **RTL** (right-to-left) when Arabic is active.
  - The Sidebar, Navigation dropdown, and all forms respect the RTL direction.

---

## 4. Dockerized Client Testing

For seamless client delivery and testing across different operating systems, the entire Bakery Accounting System has been dockerized.

- If you or your client wish to test the system without installing PHP, Composer, or MySQL locally, refer to the **[docker-tutorial.md](docker-tutorial.md)** file in the project root.
- It contains exactly three commands to spin up the App, Nginx, and MySQL, automatically seeding the database precisely as described in this flow document.
- The system will be accessible at `http://localhost:8080`.
