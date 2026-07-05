# IT Job Portal – Setup Guide

## Requirements
- XAMPP (PHP 8.1+, MySQL, Apache)
- Composer (https://getcomposer.org/)

---

## Step 1: Copy project files

1. Copy the entire `job_portal` folder into:
   ```
   C:\xampp\htdocs\job_portal
   ```

---

## Step 2: Install PHPMailer via Composer

1. Open **CMD** or **PowerShell**
2. Navigate to your project folder:
   ```
   cd C:\xampp\htdocs\job_portal
   ```
3. Run:
   ```
   composer install
   ```
   This creates a `vendor/` folder with PHPMailer inside.

---

## Step 3: Import the database

1. Open **phpMyAdmin**: http://localhost/phpmyadmin
2. Click **New** → create database called `job` with charset `utf8mb4_unicode_ci`
3. Select the `job` database
4. Click **Import** → choose `job.sql` → click **Go**

---

## Step 4: Configure the project

Open `db_config.php` and update:

```php
define('SMTP_USER', 'your_gmail@gmail.com');
define('SMTP_PASS', 'your_app_password');  // Gmail App Password, NOT your real password
define('SMTP_FROM', 'your_gmail@gmail.com');
```

### How to get a Gmail App Password:
1. Go to https://myaccount.google.com/security
2. Enable 2-Step Verification
3. Go to "App Passwords" → generate one for "Mail"
4. Paste that 16-char password into `SMTP_PASS`

---

## Step 5: Run the project

1. Start **Apache** and **MySQL** in XAMPP Control Panel
2. Open: http://localhost/job_portal/
3. Admin login:
   - Email: `admin@jobportal.com`
   - Password: `Admin123!`

---

## Project Structure

```
job_portal/
├── index.php              ← Home page
├── jobs.php               ← Browse jobs
├── job.php                ← Single job detail
├── register.php           ← Register
├── login.php              ← Login
├── logout.php             ← Logout
├── activate.php           ← Email activation
├── forgot_password.php    ← Forgot password
├── reset_password.php     ← Reset password
├── profile.php            ← User profile
├── post_job.php           ← Company posts job
├── db_config.php          ← DB + SMTP config ← EDIT THIS
├── composer.json          ← Dependencies
├── job.sql                ← Database schema + seed
├── css/
│   └── style.css
├── js/
│   └── main.js
├── php/
│   ├── classes/
│   │   ├── User.php
│   │   ├── JobListing.php
│   │   ├── Category.php
│   │   └── Mailer.php
│   └── ajax/
│       ├── check_email.php
│       └── get_jobs.php
├── includes/
│   ├── auth.php
│   ├── header.php
│   └── footer.php
└── admin/
    ├── index.php          ← Admin dashboard
    ├── jobs.php           ← Manage jobs
    ├── users.php          ← Manage users
    └── categories.php     ← Manage categories
```

---

## User Roles

| Role      | What they can do |
|-----------|-----------------|
| Guest     | Browse jobs, register |
| User      | Browse jobs, edit profile, contact company |
| Company   | All user actions + post jobs |
| Admin     | Approve jobs, manage users & categories |

---

## Notes for submission

- All passwords hashed with **bcrypt** (PASSWORD_BCRYPT, cost 12)
- CSRF tokens on all forms
- XSS prevention with `htmlspecialchars()` everywhere
- SQL injection prevention with PDO prepared statements
- Responsive via Bootstrap 5
- Fetch API used for: live job search, email availability check
- PHPMailer used for: activation emails, password reset emails
