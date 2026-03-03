# Paper Submission System

Simple Laravel application for paper submission with admin panel.

## Features

1. User Registration (name, email, password)
2. User Login
3. User Panel - View and manage submitted papers
4. Submit Paper Form (title, description, file upload)
5. Admin Panel - View all users and their papers

## System Requirements

- PHP 8.2+
- Composer
- MySQL/MariaDB
- Docker and Docker Compose (optional)

## Installation Steps

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd <project-folder>
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` file with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

### Step 5: Create Storage Link

```bash
php artisan storage:link
```

### Step 6: Create Admin User

```bash
php artisan db:seed --class=SuperAdminSeeder
```

This creates an admin account:
- Email: `admin@example.com`
- Password: `password`

### Step 7: Start the Application

```bash
php artisan serve
```

## Usage

### User Panel
- Visit: `http://localhost:8000`
- Register a new account or login
- Submit papers and view your submissions

### Admin Panel
- Visit: `http://localhost:8000/admin`
- Login with admin credentials
- View all users and papers
- Manage submissions

## File Upload

Papers can be uploaded in PDF, DOC, or DOCX format (max 10MB).
Files are stored in `storage/app/public/papers/`.

## Docker Setup (Optional)

If using Docker:

```bash
./run up
./run ssh
composer install
php artisan migrate
php artisan db:seed --class=SuperAdminSeeder
```
