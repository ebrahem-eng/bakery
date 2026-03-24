# Bakery Accounting System - Project Memory

## Project Overview
A comprehensive management and accounting system built for a 24/7 bakery operation. The system aims to meticulously track all financial records, distributions, supply purchases, daily wages, and expenses to eliminate financial inaccuracies. 

## Key Business Rules & Workflows

1. **Work Days**: 
   - A logical time period rather than a strict 24-hour calendar day. It can span multiple dates.
   - Every financial activity (sales, expenses, worker advances, wages) must fall under a specific active Work Day.
   - Work Day closing requires reconciling worker wages, consumed materials (flour, yeast, diesel, salt), and recording all expenses.
   - Work Days can be marked as a holiday with a provided reason.

2. **Shifts & Carry-over Logic**: 
   - Each Work Day includes multiple shifts managed by different employees. 
   - Shifts track how many bread bundles were given to the employee, how much money was collected, and how many unsold bundles are returned.
   - **Critical**: Any unsold bundles left at the very end of a Work Day must carry over to the next Work Day. The financial proceeds from these carried-over bundles go to the *new* Work Day's balance, but are reconciled on paper with the *previous* Work Day's end state to ensure no lost units.

3. **Multi-Currency System**: 
   - System relies heavily on 3 main currencies: USD, Old SYP, New SYP.
   - **Crucial Rule**: Every payment logged (be it for distributors, suppliers, or employees) must record the applied Exchange Rate at the time of the transaction, ensuring zero inflation/deflation discrepancy later.

4. **Suppliers & Categories**:
   - Seeded Categories: Flour, Diesel, Yeast, Salt. Admins can only mark Active/Inactive.
   - Each category has unique variables upon creation (e.g. Flour: payload unloading cost logic, Yeast: box vs kilo pricing, Diesel: liters). 
   - **Unloading Fees Decision**: If unloading fees apply to the bakery, they are automatically logged as an operational expense under the heading "Transporting Supplies" (نقل وتنزيل).

5. **Drawings / Expenses (سحوبات/مصاريف)**: 
   - Categorized strictly into: Employees (Advances), Personal (owner), Operational, Supply/Patrol (التموين/دوريات).

## Technical Requirements & Guidelines

- **Architecture Strategy (Clean Code)**: The app applies modular feature separation. Each logic block will have its own dedicated directory and Controller under the Admin namespace (e.g., `SupplierController`, `WorkerController`). 
- **System Users**: System is completely closed off. **Only Admins** use the system through the existing `admin` guard. No other entities log in.
- **Localization**: UI must support full dynamic Arabic and English toggling with a central button.
- **Theming**: UI must have a Light Mode and Dark Mode toggle.
- **Git State**: After every successfully accomplished logical step, changes must be committed and pushed to the repository.
- **UI & Views**: ALL necessary views must be explicitly built. The Dashboard will include a highly comprehensive financial statistics report with robust multi-filtering and rigorous accuracy.
- **Permissions**: **Ultra-granular capability**. Not just CRUD models; every distinct UI element, button, and sub-feature must be controllable via permissions mapping.
- **Memory Appending Rule**: Once a step finishes, log it here without removing past logs. This serves as our sequential historical trail.

## Existing Infrastructure
- Admin model, Admin authentication guard, empty View layout/sidebar, Login View, and empty Dashboard View are currently available. 

---

## Execution Log & State Tracking

### Log: [2026-03-24] - Architecture Refinement & Logging Setup
- **Status**: The AI Assistant successfully gathered the user's feedback regarding UI views, comprehensive dashboards, UI-level granular permissions, strict admin-only execution, and unloading transport expenses. 
- **Action**: Overwrote the core `implementation_plan.md` handling these directives and initialized the append-only constraint protocol for this `memory.md` file. 
- **Next Horizon**: Awaiting final 'go-ahead' to formally enter Execution Mode and begin structuring Database Migrations.
