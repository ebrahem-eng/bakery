# Bakery Management System - Docker Setup & Tutorial

This guide will walk you through setting up and running the Bakery Management System on your local machine using Docker. This ensures a consistent environment regardless of your operating system.

---

## 1. Prerequisites

Before you begin, ensure you have the following installed on your machine:

1.  **Docker Desktop**: Download and install it from [Docker's official website](https://www.docker.com/products/docker-desktop/).
    -   *Windows*: Ensure WSL 2 is enabled.
    -   *Mac*: Support for both Intel and Apple Silicon (M1/M2/M3) is included.
    -   *Linux*: Follow the instructions for your specific distribution.

2.  **Git (Optional)**: If you are cloning the repository. Otherwise, just extract the zip file provided.

---

## 2. Quick Start (Running the Project)

### Step 1: Prepare the Environment File
Copy the example environment file to create your own `.env` file:
```bash
cp .env.example .env
```
*(On Windows PowerHell: `copy .env.example .env`)*

### Step 2: Build and Start the Containers
Open your terminal inside the project folder and run:
```bash
docker-compose up -d --build
```
This command will:
-   Download the necessary images (PHP, MySQL, Nginx).
-   Compile the frontend assets (Vite/Tailwind).
-   Build the application container.
-   Start all services in the background (`-d`).

### Step 3: Initialize the Application
Once the containers are running, you need to set up the database and encryption keys. Run these commands sequentially:

1.  **Install Composer Dependencies**:
    ```bash
    docker-compose exec app composer install
    ```
2.  **Generate Application Key**:
    ```bash
    docker-compose exec app php artisan key:generate
    ```
3.  **Run Migrations and Seed Data**:
    ```bash
    docker-compose exec app php artisan migrate:fresh --seed
    ```
    *Note: This will populate the system with realistic test data (Suppliers, Workers, Distributors, and an Admin account).*

---

## 3. Accessing the System

-   **Dashboard / Front Panel**: [http://localhost:8080](http://localhost:8080)
-   **Admin Panel**: [http://localhost:8080/admin/login](http://localhost:8080/admin/login)

### Default Admin Credentials
-   **Email**: `admin@example.com`
-   **Password**: `Password@123`

---

## 4. Useful Commands

| Action | Command |
| :--- | :--- |
| **Stop the system** | `docker-compose stop` |
| **Start the system** | `docker-compose start` |
| **View logs** | `docker-compose logs -f app` |
| **Restart a single service** | `docker-compose restart app` |
| **Enter terminal inside app** | `docker-compose exec app sh` |
| **Remove all containers** | `docker-compose down -v` *(Warning: This deletes the database data)* |

---

## 5. Troubleshooting

-   **Port Conflict**: If port `8080` or `33060` is already in use, you can change them in the `docker-compose.yml` file under the `ports` section of the `web` or `db` service.
-   **Database Connection Refused**: When the containers first start, MySQL may take a few seconds to initialize. If the migration fails, wait 10 seconds and try the migration command again.
-   **Permissions**: On Linux/Mac, if you encounter permission errors with the `storage` folder, run:
    ```bash
    docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
    ```

---

*Happy Testing! If you encounter any issues, please contact the development team.*
