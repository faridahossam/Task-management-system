
# 🛠️ Laravel Project Setup Guide

A step-by-step guide to setting up and running the Laravel application

---

## 📦 Requirements

Ensure the following are installed on your system:

- PHP >= 8.1
- Composer
- MySQL or other supported DBMS
- Laravel CLI (optional): `composer global require laravel/installer`

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/faridahossam/Task-management-system.git
cd Task-management-system
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Copy Environment File

```bash
cp .env.example .env
```

### 4. Configure Environment Variables

Edit `.env` and update the following based on your local setup:

```env
APP_NAME="LaravelApp"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=secret
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

---

## 🧱 Database Setup

### 1. Create Your Database

Make sure your database defined in `.env` exists.

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Run Seeders

To populate the database with demo or default data:

```bash
php artisan db:seed
```

You can also run a specific seeder:

```bash
php artisan db:seed --class=UserSeeder
```

---

## 🛡️ Spatie Package Configuration

Spatie packages are already included in `composer.json` and installed via `composer install`.

### Spatie Laravel Permission

The package is already published and migrations are included.

Roles and permissions are typically set up in:
```
database/seeders/PermissionRoleSeeder.php

---

## 🌐 Running the Application

```bash
php artisan serve
```

Visit: [http://localhost:8000](http://localhost:8000)

---

## 📁 Project Structure Highlights

- `app/Models` – Eloquent models
- `app/Http/Controllers` – Application controllers
- `database/seeders` – All seeders
- `routes/api.php` – API routes
- `bootstrap/app.php` - Middleware and routes configuration

---
