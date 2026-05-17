#!/usr/bin/env bash
#
# Build script for creating a cPanel-ready installable ZIP of the E-Commerce platform.
# Works entirely in a temp directory — your source project is NEVER modified.
#
# Usage: ./build.sh [--no-composer] [--output-dir=./dist]
#

set -euo pipefail

BOLD='\033[1m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RED='\033[0;31m'; CYAN='\033[0;36m'; NC='\033[0m'
log()  { echo -e "${GREEN}[✓]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
err()  { echo -e "${RED}[✗]${NC} $1"; exit 1; }
info() { echo -e "${CYAN}[i]${NC} $1"; }
header() { echo -e "\n${BOLD}━━━ $1 ━━━${NC}\n"; }

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUTPUT_DIR="${PROJECT_DIR}/dist"
BUILD_DIR="/tmp/ecommerce-build-$$"
COMPOSER_ARGS="--no-dev --no-interaction --prefer-dist --optimize-autoloader"
ZIP_NAME="ecommerce-installable.zip"
NO_COMPOSER=false

for arg in "$@"; do
    case $arg in
        --no-composer) NO_COMPOSER=true ;;
        --output-dir=*) OUTPUT_DIR="${arg#*=}" ;;
        *) warn "Unknown argument: $arg" ;;
    esac
done

cleanup() { rm -rf "$BUILD_DIR"; }
trap cleanup EXIT

# ─── Step 1: Validate ─────────────────────────────────────
header "Step 1/6: Validating Environment"

cd "$PROJECT_DIR"

if ! command -v php &>/dev/null; then
    err "PHP is required. Please install PHP 8.2+."
fi
PHP_VERSION=$(php -r "echo PHP_VERSION;")
info "PHP version: $PHP_VERSION"

if [ "$NO_COMPOSER" != "true" ] && [ ! -f "vendor/autoload.php" ]; then
    if ! command -v composer &>/dev/null && [ ! -f "composer.phar" ]; then
        err "Composer is required. Use --no-composer if vendor/ already exists."
    fi
fi
if ! command -v zip &>/dev/null; then
    err "'zip' command is required."
fi
for f in artisan composer.json public/index.php; do
    [ -f "$f" ] || err "Missing: $f"
done

log "Environment validation passed"

# ─── Step 2: Copy to build dir ────────────────────────────
header "Step 2/6: Copying Project to Build Directory"

rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR"

# Copy all files (excluding dev dirs) to build directory
rsync -a --exclude='.git/' --exclude='node_modules/' --exclude='vendor/bin/' \
    --exclude='vendor/*/tests/' --exclude='vendor/*/test/' --exclude='vendor/*/docs/' \
    --exclude='vendor/*/.git/' --exclude='*.md' --exclude='build.sh' --exclude='dist/' \
    --exclude='.opencode/' --exclude='tests/' \
    "$PROJECT_DIR/" "$BUILD_DIR/"

log "Project copied to $BUILD_DIR"

# ─── Step 3: Apply installer patches ──────────────────────
header "Step 3/6: Applying Installer Patches"

cd "$BUILD_DIR"

# --- Patch 1: Fix duplicate table creation migrations ---
for f in \
    "2026_03_07_000001_create_banners_table.php" \
    "2026_03_10_000001_create_blog_categories_table.php" \
    "2026_03_12_000003_create_activity_logs_table.php"; do
    file="database/migrations/$f"
    if [ -f "$file" ]; then
        # Insert Schema::dropIfExists before first Schema::create in the up() method
        php -r "
            \$c = file_get_contents('$file');
            \$table = '';
            if (preg_match(\"/Schema::create\\('([^']+)'/\", \$c, \$m)) \$table = \$m[1];
            if (\$table) {
                \$c = str_replace(
                    \"Schema::create('{\$table}'\",
                    \"Schema::dropIfExists('{\$table}');\\n        Schema::create('{\$table}'\",
                    \$c
                );
                file_put_contents('$file', \$c);
            }
        "
        log "Patched: $f"
    fi
done

# --- Patch 2: Remove duplicate log_name index ---
php -r "
    \$c = file_get_contents('database/migrations/2026_03_12_000003_create_activity_logs_table.php');
    \$c = preg_replace('/\\\$table->index\(.*log_name.*\);/', '', \$c);
    file_put_contents('database/migrations/2026_03_12_000003_create_activity_logs_table.php', \$c);
"

# --- Patch 3: Fix ENUM quoting in staff migration ---
php -r "
    \$c = file_get_contents('database/migrations/2026_03_12_000002_add_staff_fields_to_users_table.php');
    \$c = str_replace(
        \"explode(',', str_replace(\\\"'\\\"', '', \\\$matches[1]))\",
        \"explode(',', \\\$matches[1])\",
        \$c
    );
    file_put_contents('database/migrations/2026_03_12_000002_add_staff_fields_to_users_table.php', \$c);
"

# --- Patch 4: Fix role column migration (use raw SQL) ---
cat > "database/migrations/2026_03_14_114109_update_users_role_column_length.php" << 'MIGEOF'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) DEFAULT 'customer'");
    }
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) DEFAULT 'customer'");
    }
};
MIGEOF

# --- Patch 5: Fix typing status migration (remove after()) ---
php -r "
    \$c = file_get_contents('database/migrations/2026_03_08_120000_add_typing_status_to_chats_table.php');
    \$c = preg_replace('/->after\([^)]+\)/', '', \$c);
    file_put_contents('database/migrations/2026_03_08_120000_add_typing_status_to_chats_table.php', \$c);
"

# --- Patch 6: Fix CheckInstallation middleware ---
cat > "app/Http/Middleware/CheckInstallation.php" << 'MWEOF'
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CheckInstallation
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('install.*') || $request->is('install/*') || $request->is('install')) {
            if (File::exists(storage_path('framework/install.lock'))) {
                return redirect()->route('home');
            }
            return $next($request);
        }

        if (File::exists(storage_path('framework/install.lock'))) {
            return $next($request);
        }

        if (File::exists(base_path('.env'))) {
            try {
                DB::connection()->getPdo();
                if (Schema::hasTable('users')) {
                    return $next($request);
                }
            } catch (\Exception $e) {}
        }
        return redirect()->route('install.welcome');
    }
}
MWEOF

# --- Patch 7-9: Fix middleware install route checks (use PHP to avoid sed escaping issues) ---
for mid_file in "LanguageMiddleware.php" "ThemeMiddleware.php" "SeoRedirectMiddleware.php"; do
    php -r "
        \$c = file_get_contents('app/Http/Middleware/$mid_file');
        \$c = str_replace(
            \"if (\\\$request->is('install/*')) {\",
            \"if (\\\$request->routeIs('install.*') || \\\$request->is('install/*')) {\",
            \$c
        );
        file_put_contents('app/Http/Middleware/$mid_file', \$c);
    "
done

# --- Patch 10: Fix InstallController ($request->db_url typo) ---
php -r "
    \$c = file_get_contents('app/Http/Controllers/Install/InstallController.php');
    \$c = str_replace(\"'APP_URL' => \\\$request->db_url,\", \"'APP_URL' => \\\$request->site_url,\", \$c);
    file_put_contents('app/Http/Controllers/Install/InstallController.php', \$c);
"

# --- Patch 11: Fix env.example (already copied from source, keep as is) ---

log "All installer patches applied"

log "All installer patches applied"

# ─── Step 4: Install Composer Dependencies ────────────────
header "Step 4/6: Installing Composer Dependencies"

if [ "$NO_COMPOSER" == "true" ]; then
    # Copy vendor from source project
    cp -a "$PROJECT_DIR/vendor" "$BUILD_DIR/vendor"
    log "Copied vendor from source (--no-composer flag set)"
elif [ -d "$PROJECT_DIR/vendor" ]; then
    cp -a "$PROJECT_DIR/vendor" "$BUILD_DIR/vendor"
    if command -v composer &>/dev/null; then
        cd "$BUILD_DIR" && composer install $COMPOSER_ARGS 2>/dev/null && cd "$PROJECT_DIR" || true
    fi
    log "Composer dependencies set up"
fi

# ─── Step 5: Create .env and storage ──────────────────────
header "Step 5/6: Creating Environment & Storage"

APP_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")

cd "$BUILD_DIR"

if [ -f ".env.example" ]; then
    cp .env.example .env
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
    sed -i 's|^APP_ENV=.*|APP_ENV=production|' .env
    sed -i 's|^APP_DEBUG=.*|APP_DEBUG=false|' .env
    sed -i 's|^LOG_LEVEL=.*|LOG_LEVEL=error|' .env
fi

for dir in storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs; do
    mkdir -p "$dir" && touch "$dir/.gitkeep"
done
mkdir -p public/uploads bootstrap/cache

log "Environment and storage prepared"

# ─── Step 6: Build ZIP ────────────────────────────────────
header "Step 6/6: Creating Installable ZIP"

rm -rf "$OUTPUT_DIR"
mkdir -p "$OUTPUT_DIR"

TEMP_EXCLUDE=$(mktemp)
cat > "$TEMP_EXCLUDE" << 'EXCLUDES'
.git/
.gitignore
.github/
build.sh
dist/
node_modules/
tests/
test/
.phpunit.cache/
.editorconfig
.styleci.yml
.symfony/
vagrant/
docker/
docker-compose.yml
storage/framework/install.lock
*.md
.env.local.backup
EXCLUDES

info "Creating ZIP archive (this may take a moment)..."
zip -r -q "$OUTPUT_DIR/$ZIP_NAME" . -x@"$TEMP_EXCLUDE" -x "vendor/bin/*" -x "vendor/*/tests/*" \
    -x "vendor/*/test/*" -x "vendor/*/docs/*" -x "vendor/*/.git/*" -x "analyze_migrations.php" 2>/dev/null
rm -f "$TEMP_EXCLUDE"

ZIP_SIZE=$(du -h "$OUTPUT_DIR/$ZIP_NAME" | cut -f1)
log "ZIP created successfully!"
info "Location: ${OUTPUT_DIR}/${ZIP_NAME}"
info "Size: ${ZIP_SIZE}"

# Cleanup build dir
rm -rf "$BUILD_DIR"

# ─── Summary ──────────────────────────────────────────────
header "Build Complete!"
echo -e "  ${BOLD}Output:${NC}  ${OUTPUT_DIR}/${ZIP_NAME} (${ZIP_SIZE})"
echo ""
echo -e "  ${BOLD}Deployment:${NC}"
echo -e "  1. Upload ZIP to cPanel → File Manager"
echo -e "  2. Extract it"
echo -e "  3. Point subdomain to ${CYAN}<extracted>/public${NC}"
echo -e "  4. Visit URL → ${BOLD}7-step installer${NC} appears"
echo ""
echo -e "  ${GREEN}Your local project was NOT modified.${NC}"
