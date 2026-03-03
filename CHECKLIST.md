# Setup Checklist

## Pre-Installation

- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] MySQL/MariaDB running
- [ ] Database created

## Installation Steps

- [ ] Run `composer install`
- [ ] Copy `.env.example` to `.env`
- [ ] Update database credentials in `.env`
- [ ] Run `php artisan key:generate`
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan storage:link`
- [ ] Run `php artisan db:seed --class=SuperAdminSeeder`

## Verification

- [ ] Visit http://localhost:8000 - Should redirect to login
- [ ] Register a new user - Should work
- [ ] Login with new user - Should see papers page
- [ ] Submit a paper - Should upload successfully
- [ ] View submitted papers - Should see in list
- [ ] Visit http://localhost:8000/admin - Admin login page
- [ ] Login as admin (admin@example.com / password)
- [ ] Check Users resource - Should see all users
- [ ] Check Papers resource - Should see all papers

## Common Issues

### Storage Link Error
```bash
php artisan storage:link
```

### Permission Denied on Uploads
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Migration Error
```bash
php artisan migrate:fresh
php artisan db:seed --class=SuperAdminSeeder
```

### Composer Dependencies
```bash
composer update
composer dump-autoload
```

## File Structure

```
app/
├── Filament/
│   └── Resources/
│       ├── PaperResource.php
│       └── UserResource.php
├── Http/
│   └── Controllers/
│       ├── Auth/
│       │   ├── AuthenticatedSessionController.php
│       │   └── RegisteredUserController.php
│       └── PaperController.php
├── Models/
│   ├── Paper.php
│   └── User.php
└── Policies/
    └── PaperPolicy.php

resources/
└── views/
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── layouts/
    │   └── app.blade.php
    └── papers/
        ├── create.blade.php
        └── index.blade.php

routes/
├── auth.php
└── web.php
```

## Testing

### Test User Registration
1. Go to /register
2. Fill form with test data
3. Submit
4. Should redirect to papers page

### Test Paper Submission
1. Login as user
2. Click "Submit New Paper"
3. Fill form and upload file
4. Submit
5. Should see paper in list

### Test Admin Panel
1. Go to /admin
2. Login as admin
3. Check Users - should see all users
4. Check Papers - should see all papers
5. Try editing/deleting - should work

## Production Checklist

- [ ] Change admin password
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure proper mail settings
- [ ] Set up file backup for uploads
- [ ] Configure proper file storage (S3, etc.)
- [ ] Set up SSL certificate
- [ ] Configure proper database backups
