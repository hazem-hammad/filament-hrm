<div align="center">

# Filament HRM

This is the base Laravel core prepared for internal projects at **IntCore**. It includes preconfigured modules like Admin Panel, Dashboard, User Roles & Permissions, Notification Center, Static Pages, and common APIs.

---

</div>
	
## 🚀 Getting Started

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/hazem-hammad/filament-hrm.git
```

### 2️⃣ Build and Start Docker Containers

```bash
docker compose up --build -d
```

💡 **Note:** The first time you start the project, it will:

-   Install dependencies (`composer install`)
-   Set up the database (`php artisan migrate --seed`)
-   Create super admin (`php artisan shield:super-admin`)
-   Run background services

📌 **If you see a `502 Bad Gateway` error,** wait for a few seconds until everything initializes.

---

### 3️⃣ Verify Services

Check if all containers are running:

## 📌 **Available Services & URLs**

### 🖥️ **Project (Main Application)**

-   **URL:** [http://localhost:8080](http://localhost:8080)
-   **Admin Panel URL:** [http://localhost:8080/admin/login](http://localhost:8080/admin/login) [email: **admin@example.com**, password: **admin123**]

### 🛠️ **PHPMyAdmin (Database Management)**

-   **URL:** [http://localhost:8081](http://localhost:8081)  
    _(Manage MySQL database)_

### 📧 **Mailpit (Email Testing)**

-   **URL:** [http://localhost:8082](http://localhost:8082)  
    _(Test emails sent by the application)_

### 🔥 **RedisInsight (Redis Viewer)**

-   **URL:** [http://localhost:8083](http://localhost:8083)  
    _(View cached data in Redis)_
-   **Setup Instructions:**

    -   Click **"Create Database"** to view cached data.
    -   **Host:** `redis`
    -   **Port:** `6379`
    -   **User:** `default`
    -   **Password:** _(leave empty)_

---

## 📦 Modules Included

**Dashboard**

-   Install Filament
-   Admin Module
-   User Module
-   Static Pages Module
-   Roles and Permissions Module
-   Faq Module
-   Notification Center Module
-   Slider Module

**APIs**

-   Get Configurations
-   Device Token
-   Upload Files
-   Auth
-   Register
-   Login (Phone, Password)
-   Social Login
-   Reset Password
-   Send OTP
-   Verify OTP
-   Logout
-   Get Profile
-   Update Profile
-   Change Password
-   Change Phone
-   Notifications
-   Get FAQ
-   Static Pages
-   Get Sliders
-   Delete Account

**Code**

-   Custom Logging
-   Custom Cache Helpers
-   FCM Helpers
-   Filters by Pipeline
-   Payments (Just use gateway class and enjoy)
-   Payfort Gateway
-   MyFatoorah Gateway
-   Activity Logs

**Packages**

-   Filament
-   Filament Google Map
-   Spatie Media Library
-   Spatie Activity Logs
-   Filament Shield (roles and permissions)
-   Laravel Notification Channels (fcm as a driver)
-   Telescope
-   Sanctum

## 🎉 Enjoy and Happy Coding! 🚀🔥
