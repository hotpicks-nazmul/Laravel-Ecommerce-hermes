# E-Commerce Website Setup Guide

## Current Status
✅ Composer packages installed successfully (94 packages)
⚠️ Minor warning: OpenSSL loaded twice (not critical)

## Next Steps

### Step 1: Fix PHP Configuration (Optional - for OpenSSL warning)

1. Open `H:\Xampp\php\php.ini` with Notepad
2. Search for `extension=openssl`
3. If you find it twice, remove or comment out one by adding `;` at the beginning
4. Save the file

### Step 2: Run Composer Dump-Autoload

Open Command Prompt and run:

```cmd
cd /d "h:\Ecommerce Website Development Using Ai\Laravel Backend Frontend\ecommerce"
composer dump-autoload
```

If composer is not found, use the full path:

```cmd
H:\Xampp\php\php.exe -d extension=openssl -d extension=mbstring -d extension=gd "C:\ProgramData\ComposerSetup\bin\composer.phar" dump-autoload
```

### Step 3: Generate Application Key

```cmd
H:\Xampp\php\php.exe artisan key:generate
```

### Step 4: Create Storage Link

```cmd
H:\Xampp\php\php.exe artisan storage:link
```

### Step 5: Test the Application

1. Start XAMPP Apache and MySQL
2. Open browser and go to: `http://localhost/ecommerce/public`
3. You should see the installation wizard

## Deployment to cPanel

### Step 1: Prepare Files

1. Make sure all the above steps are completed
2. Zip the entire `ecommerce` folder including:
   - All files and folders
   - `vendor` folder (important!)
   - `.env` file

### Step 2: Upload to cPanel

1. Log into your cPanel
2. Go to File Manager
3. Navigate to `public_html` (or your domain folder)
4. Upload the zip file
5. Extract the zip file
6. Move all files from the extracted `ecommerce` folder to `public_html`

### Step 3: Set Folder Permissions

In cPanel File Manager, right-click and set permissions:

| Folder | Permission |
|--------|------------|
| storage | 755 or 775 |
| storage/framework | 755 or 775 |
| storage/framework/cache | 755 or 775 |
| storage/framework/sessions | 755 or 775 |
| storage/framework/views | 755 or 775 |
| storage/logs | 755 or 775 |
| storage/app | 755 or 775 |
| bootstrap/cache | 755 or 775 |
| public | 755 |

### Step 4: Create Database

1. Go to cPanel > MySQL Databases
2. Create a new database (e.g., `yourname_ecommerce`)
3. Create a MySQL user with password
4. Add user to database with ALL PRIVILEGES

### Step 5: Run Installation Wizard

1. Open your browser and go to your domain URL
2. The installation wizard will appear
3. Follow the 7-step process:
   - Step 1: Welcome
   - Step 2: Requirements Check
   - Step 3: Database Configuration (enter your MySQL details)
   - Step 4: Site Configuration (site name, admin email, etc.)
   - Step 5: Theme Selection
   - Step 6: Payment Gateway Setup
   - Step 7: Complete

### Step 6: Post-Installation

After installation is complete:

1. Delete the `install.lock` file if you want to reinstall
2. Access admin panel at: `yourdomain.com/admin`
3. Default admin credentials will be shown during installation

## Troubleshooting

### Blank Page / 500 Error

1. Check `storage/logs/laravel.log` for errors
2. Make sure all permissions are correct
3. Make sure `.env` file exists and has correct database info

### Database Connection Error

1. Verify database credentials in `.env` file
2. Make sure database user has all privileges
3. Check if database host is correct (usually `localhost`)

### Assets Not Loading

1. Run `php artisan storage:link`
2. Check if `public/storage` symlink exists
3. Check folder permissions

## Support

For issues or questions, check the following files:
- `PROJECT_BLUEPRINT.md` - Project overview
- `storage/logs/laravel.log` - Error logs
