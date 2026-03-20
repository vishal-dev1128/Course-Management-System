# Installation Guide

Follow these steps to set up the EduManage Course Management System in your local development environment.

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- **XAMPP / WAMP / LAMP**: A local server environment with at least PHP 8.1.
- **Git**: (Optional) For cloning the repository.
- **Web Browser**: Modern version of Chrome, Firefox, or Edge.

---

## 🛠️ Step-by-Step Setup

### 1. Clone or Download the Project
Copy the project folder to your server's root directory:
- **XAMPP**: `C:\xampp\htdocs\CMS`
- **WAMP**: `C:\wamp\www\CMS`

### 2. Database Configuration
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a new database named `cms_db`.
3. Select the `cms_db` database and click the **Import** tab.
4. Choose the `config/cms_db.sql` file from the project folder and click **Import**.

### 3. Connect the Application
Open `config/db.php` and verify the settings:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cms_db');
define('DB_USER', 'root'); // Default XAMPP user
define('DB_PASS', '');     // Default XAMPP password is empty
```

### 4. Launching the Site
1. Start **Apache** and **MySQL** services in your XAMPP Control Panel.
2. Open your browser and navigate to `http://localhost/CMS`.

---

## 🔑 Default Login Credentials

Use these credentials to explore the different dashboards:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin@cms.com` | `admin_pass_2026` |
| **Instructor** | `vikram@cms.com` | `instructor123` |
| **Student** | `alice@cms.com` | `student123` |

## ❗ Troubleshooting

- **Database Connection Error**: Double-check `config/db.php` and ensure MySQL is running.
- **CSS Not Loading**: Ensure your browser has access to the internet (Tailwind is loaded via CDN for this version).
- **Session Issues**: Ensure your browser accepts cookies.
