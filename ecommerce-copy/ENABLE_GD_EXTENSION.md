# How to Enable GD Extension in XAMPP

## Step 1: Open PHP Configuration

1. Open File Explorer
2. Navigate to: `H:\Xampp\php\`
3. Find the file `php.ini`
4. Open it with Notepad (Right-click > Open with > Notepad)

## Step 2: Enable Required Extensions

Find these lines and remove the semicolon `;` from the beginning:

**Before:**
```ini
;extension=curl
;extension=fileinfo
;extension=gd
;extension=intl
;extension=mbstring
;extension=openssl
;extension=pdo_mysql
```

**After:**
```ini
extension=curl
extension=fileinfo
extension=gd
extension=intl
extension=mbstring
extension=openssl
extension=pdo_mysql
```

## Step 3: Save and Close

1. Press `Ctrl + S` to save
2. Close Notepad

## Step 4: Run Composer Install

Open Command Prompt and run:

```cmd
cd /d "h:\Ecommerce Website Development Using Ai\Laravel Backend Frontend\ecommerce"
composer install --no-dev --optimize-autoloader
```

## Troubleshooting

If you still get errors, try running:

```cmd
composer install --no-dev --optimize-autoloader --ignore-platform-reqs
```

This will ignore platform requirements and install the dependencies.
