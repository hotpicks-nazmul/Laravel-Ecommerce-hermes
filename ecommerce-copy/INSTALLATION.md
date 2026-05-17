# Complete Installation Guide (No PHP/Composer on Local Computer)

## Option 1: Install XAMPP (Recommended - Free)

### Step 1: Install XAMPP
1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Choose PHP 8.2+ version
3. Install to `C:\xampp` (default)
4. During installation, select these components:
   - Apache
   - MySQL
   - PHP
   - phpMyAdmin

### Step 2: Install Composer
1. Download Composer-Setup.exe from: https://getcomposer.org/download/
2. Run the installer
3. When asked, select PHP from `C:\xampp\php\php.exe`
4. Complete installation

### Step 3: Prepare Project
1. Open Command Prompt
2. Navigate to project folder:
   ```cmd
   cd "h:\Ecommerce Website Development Using Ai\Laravel Backend Frontend\ecommerce"
   ```
3. Run:
   ```cmd
   composer install --no-dev --optimize-autoloader
   ```

### Step 4: Upload to Server
1. Zip the entire `ecommerce` folder (including `vendor` folder)
2. Upload to cPanel
3. Extract and follow the installation wizard

---

## Option 2: Use Online PHP Environment (Alternative)

### Use a Cloud IDE (Free Options):

1. **GitHub Codespaces** (Free tier available)
   - Go to GitHub.com
   - Create a new repository
   - Upload project files
   - Open in Codespaces
   - Run `composer install` in terminal
   - Download the complete project

2. **Gitpod** (Free tier available)
   - Go to Gitpod.io
   - Connect your GitHub account
   - Open your repository
   - Run `composer install` in terminal
   - Download the complete project

---

## Option 3: Pre-built Laravel Hosting (Easiest)

Some hosting providers offer pre-installed Laravel:

1. **Laravel Forge** (Paid)
2. **Cloudways** (Paid, but easy)
3. **Hostinger** (Budget-friendly with Laravel support)

---

## Option 4: Request Pre-built Package

If you cannot install PHP/Composer locally, I can provide you with:

1. **A list of required files** that you can download separately
2. **Instructions for manual installation** without Composer

---

## Quick Start with XAMPP (Detailed Steps)

### 1. Download and Install XAMPP
- URL: https://www.apachefriends.org/
- Version: PHP 8.2+
- Location: C:\xampp

### 2. Download and Install Composer
- URL: https://getcomposer.org/download/
- Select PHP: C:\xampp\php\php.exe

### 3. Open Command Prompt as Administrator
- Press Win+X, select "Command Prompt (Admin)"

### 4. Navigate to Project
```cmd
cd /d "h:\Ecommerce Website Development Using Ai\Laravel Backend Frontend\ecommerce"
```

### 5. Install Dependencies
```cmd
composer install --no-dev --optimize-autoloader
```

### 6. Create Required Folders
```cmd
mkdir storage\framework\sessions
mkdir storage\framework\views
mkdir storage\framework\cache
mkdir storage\logs
mkdir bootstrap\cache
mkdir public\uploads
```

### 7. Copy Environment File
```cmd
copy .env.example .env
```

### 8. Zip the Project
- Right-click on `ecommerce` folder
- Send to > Compressed (zipped) folder
- Include ALL folders and files

### 9. Upload to cPanel
1. Login to cPanel
2. Open File Manager
3. Go to public_html
4. Upload the zip file
5. Extract
6. Move files to public_html root

### 10. Set Permissions (via File Manager)
Right-click each folder > Change Permissions:
- storage: 755
- storage/framework: 755
- storage/logs: 755
- bootstrap/cache: 755
- public/uploads: 755

### 11. Create Database
1. cPanel > MySQL Databases
2. Create database
3. Create user
4. Add user to database

### 12. Run Installation
1. Visit your website URL
2. Follow the installation wizard
3. Enter database details
4. Create admin account
5. Select theme
6. Configure payment gateways

---

## Need Help?

If you're still having trouble:
1. Install XAMPP (it's free and easy)
2. Follow the steps above
3. The installation wizard will handle the rest

The installation wizard will:
- Check server requirements
- Create database tables
- Set up your admin account
- Configure your theme
- Set up payment gateways
