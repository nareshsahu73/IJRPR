# Quick Installation Guide

## Agar Docker Use Kar Rahe Ho

```bash
# Start containers
./run up

# Container me enter karo
./run ssh

# Setup script run karo
chmod +x setup.sh
./setup.sh
```

## Agar Local Setup Kar Rahe Ho

```bash
# Setup script run karo
chmod +x setup.sh
./setup.sh
```

## Manual Setup (Agar script kaam nahi kare)

```bash
# 1. Dependencies install karo
composer install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Database credentials .env me update karo
# DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Migrations run karo
php artisan migrate

# 5. Storage link banao
php artisan storage:link

# 6. Admin user create karo
php artisan db:seed --class=SuperAdminSeeder

# 7. Server start karo
php artisan serve
```

## Access URLs

- **User Panel**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin

## Admin Login

- Email: `admin@example.com`
- Password: `password`

## Features

1. **User Registration** - Naye users register kar sakte hain
2. **User Login** - Email aur password se login
3. **Submit Paper** - Users apne papers submit kar sakte hain (PDF, DOC, DOCX)
4. **View Papers** - Apne submitted papers ki list dekh sakte hain
5. **Admin Panel** - Admin sab users aur unke papers dekh sakta hai

## Important Notes

- File upload limit: 10MB
- Supported formats: PDF, DOC, DOCX
- Admin ko manually `is_admin = 1` set karna padega database me (ya seeder use karo)
