# OWASP Top 10 Security Audit Report
**Project:** Laravel Ecommerce Project  
**Date:** 2026-05-18  
**Scope:** Full OWASP Top 10 (2021) assessment  
**Auditor:** Security Subagent

---

## 1. BROKEN ACCESS CONTROL (A01)

### Checks performed:
- Route middleware enforcement (auth, admin, permission guards)
- Controllers scoping queries to authenticated user
- Gates/policies implementation

### Results:

| Check | Status | Details |
|-------|--------|---------|
| Auth middleware on protected routes | **PASS** | `auth`, `admin`, `super_admin`, `staff` middleware properly applied |
| Admin routes guarded | **PASS** | `web.php` line 318: `middleware(['auth', 'admin', ...CheckSubmenuPermission::class, 'granular_permission'])` |
| User order scoping | **PASS** | `OrderController::index()` line 16: `->where('user_id', auth()->id())` |
| User order ownership check | **PASS** | `OrderController::show()` line 29: `if ($order->user_id !== auth()->id())` |
| Permission middleware | **PASS** | `CheckPermission.php` — checks super_admin, admin bypass, staff permissions |
| Granular permissions | **PASS** | `CheckGranularPermission.php` — CRUD-level permissions with action mapping |
| Staff middleware restricts warehouse | **PASS** | `StaffMiddleware.php` line 42: checks warehouse_id assignment |

### Verdict: **PASS** ✅
No critical broken access control issues found. All protected routes have appropriate middleware. User data scoping is implemented correctly.

---

## 2. CRYPTOGRAPHIC FAILURES (A02)

### Checks performed:
- Password hashing algorithm and rounds
- APP_KEY existence and format
- Session encryption
- Sensitive data exposure

### Results:

| Check | Status | Details |
|-------|--------|---------|
| Password hashing with bcrypt | **PASS** | `Hash::make()` used consistently, `BCRYPT_ROUNDS=12` (`.env` line 16) |
| APP_KEY is set | **PASS** | `.env` line 3: `APP_KEY=base64:m+KpE3FtogMMt8F6B1QumPqgRPXWXLoOfFCuytJHHQw=` |
| Session cookie `secure` flag | **FAIL** ⚠️ | `config/session.php` line 32: `'secure' => env('SESSION_SECURE_COOKIE')` — `SESSION_SECURE_COOKIE` not set in `.env`, default is null (cookies sent over HTTP) |
| Session encryption | **FAIL** ⚠️ | `.env` line 32: `SESSION_ENCRYPT=false` — session data stored in plaintext (files) |

### FAIL Item 1: Session Secure Cookie Not Enforced
- **File:** `config/session.php:32`
- **Severity:** MEDIUM
- **Detail:** `'secure' => env('SESSION_SECURE_COOKIE')` — The env variable `SESSION_SECURE_COOKIE` is NOT defined in `.env`, so Laravel defaults it to null/false. Session cookies are sent over unencrypted HTTP connections.
- **Fix:** Set `SESSION_SECURE_COOKIE=true` in `.env` when using HTTPS.

### FAIL Item 2: Session Encryption Disabled
- **File:** `.env:32`
- **Severity:** LOW (file-based sessions only readable by server)
- **Detail:** `SESSION_ENCRYPT=false` — Session data is stored as plaintext in files. If an attacker gains filesystem access, session data (including flash data, cart contents) is readable.
- **Fix:** Set `SESSION_ENCRYPT=true` in `.env`.

### Verdict: **PASS** (with minor issues) ✅
Password hashing is strong. APP_KEY is properly set. Low-severity session configuration issues noted.

---

## 3. INJECTION (A03)

### Checks performed:
- Raw SQL queries (`DB::raw`, `DB::select`, `DB::statement`)
- Mass assignment protection (`$fillable` / `$guarded`)
- XSS in Blade templates (`{!! !!}` usage)
- User input in raw methods

### Results:

### FAIL Item 1: Stored XSS — Custom HTML Widget Content
- **File:** `resources/views/themes/general/partials/widgets/custom_html.blade.php:13`
- **Severity:** **HIGH**
- **Detail:** `{!! $content !!}` — The `$content` variable is rendered WITHOUT any escaping. This content comes from the database (admin-edited widgets) which could contain malicious JavaScript if an admin account is compromised or if widget content isn't sanitized on input.
- **Fix:** Use `{{ $content }}` for HTML-escaped output, or use `{!! Purifier::clean($content) !!}` if HTML is required.

### FAIL Item 2: Stored XSS — Blog Content Without Purifier Fallback
- **File:** `resources/views/themes/general/blogs/show.blade.php:759`
- **Severity:** **HIGH**
- **Detail:** `{!! class_exists('Purifier') ? Purifier::clean($blog->content) : $blog->content !!}` — If the `Purifier` package is not installed (class_exists returns false), the blog content is output RAW with NO escaping. This is a stored XSS vulnerability.
- **Fix:** Add a proper fallback: `{!! class_exists('Purifier') ? Purifier::clean($blog->content) : e($blog->content) !!}`

### FAIL Item 3: Stored XSS — Page Content Without Purifier Fallback
- **File:** `resources/views/themes/general/pages/show.blade.php:34`
- **Severity:** **HIGH**
- **Also in:** `privacy.blade.php:57`, `terms.blade.php:57`, `about.blade.php:63`, `faq.blade.php:65`
- **Detail:** Same pattern as blog content — raw output if Purifier not installed.
- **Fix:** Same as above — use `e()` as fallback.

### FAIL Item 4: Stored XSS — Newsletter Content
- **File:** `resources/views/admin/marketing/newsletters/preview.blade.php:37`
- **Severity:** **HIGH**
- **Detail:** `{!! $newsletter->content !!}` — Newsletter content rendered without any escaping. Stored XSS in admin panel.
- **Fix:** Escape output or sanitize with Purifier.

### FAIL Item 5: Stored XSS — Category Title in Home Page
- **File:** `resources/views/themes/general/home/index.blade.php:309`
- **Severity:** **MEDIUM**
- **Detail:** `{!! $categoryTitle !!}` — Category title rendered raw. If category name is editable by admin, this is XSS.
- **Fix:** Use `{{ $categoryTitle }}` (escaped).

### FAIL Item 6: Reflected XSS — Search Highlighting in Quotations
- **File:** `resources/views/admin/quotations/partials/table-rows.blade.php:17,29,37`
- **Severity:** **HIGH**
- **Detail:** `{!! preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark>$1</mark>', ...) !!}` — The `$search` variable comes from user input (URL parameter). Even though `preg_quote` escapes regex chars, the result is output in `{!! !!}` context. An attacker could inject HTML via search query.
- **Fix:** Apply `e()` to the result or use `{{ }}` with proper escaping.

### PASS Items:
- **PASS** ✅ No raw SQL concatenation with user input (`DB::raw()` used only for aggregate functions with hardcoded column names)
- **PASS** ✅ All models have `$fillable` defined — mass assignment protection active
- **PASS** ✅ `Request::validate()` used consistently on user input
- **PASS** ✅ Blade templates use `{{ }}` (escaped output) for all form values (`old()` calls)

### Verdict: **FAIL** ❌
Multiple XSS vulnerabilities found. The blog/page content fallback pattern and newsletter content are significant risks. The reflected XSS in quotations is also a concern.

---

## 4. INSECURE DESIGN (A04)

### Checks performed:
- Server-side price enforcement in checkout
- Client-input reliance for financial calculations
- Business logic validation

### Results:

### FAIL Item 1: Client-Supplied Price Accepted in Cart
- **File:** `app/Http/Controllers/CartController.php:143-145`
- **Severity:** **CRITICAL**
- **Detail:** 
  ```
  // Add custom price from frontend (already calculated with adjustments)
  if ($request->price) {
      $variantData['custom_price'] = $request->price;
  }
  ```
  The client can submit ANY price via the `price` POST parameter when adding to cart. This `custom_price` overrides `$product->final_price` in `app/Models/Cart.php:139`:
  ```
  $price = isset($variantData['custom_price']) ? $variantData['custom_price'] : $product->final_price;
  ```
  The `getSubtotal()` method in `Cart.php:260-271` uses this price directly from stored cart data without cross-referencing against the actual product price in the database.
- **Impact:** An attacker could set any price (e.g., $0.01) for an expensive item, then proceed to checkout and purchase at that price.
- **Fix:** Remove the `custom_price` override. Always fetch the actual product price from the database during checkout processing. In `CartController::add()`, ignore `$request->price` and always use `$product->final_price`.

### PASS Items:
- **PASS** ✅ Checkout total computed server-side from cart subtotal + shipping + tax
- **PASS** ✅ Order create uses stored cart data (though the prices in cart are tainted)
- **PASS** ✅ Discount/coupon logic appears to be server-side (not examined in full detail)

### Verdict: **FAIL** ❌
**CRITICAL** finding: Price manipulation attack vector exists. Client-supplied prices are accepted and stored without verification.

---

## 5. SECURITY MISCONFIGURATION (A05)

### Checks performed:
- APP_DEBUG setting
- .env file exposure
- CORS configuration
- Error handling
- Storage permissions

### Results:

### FAIL Item 1: Debug Mode Enabled
- **File:** `.env:4`
- **Severity:** **HIGH**
- **Detail:** `APP_DEBUG=true` — Debug mode exposes full stack traces, environment variables, database queries, and file paths in error responses. In a production environment, this leaks sensitive information.
- **Fix:** Set `APP_DEBUG=false` in production.

### FAIL Item 2: Weak Database Password
- **File:** `.env:28`
- **Severity:** **MEDIUM**
- **Detail:** `DB_PASSWORD=abc123` — Trivial password for production database.
- **Fix:** Use a strong, randomly generated password (minimum 16 chars, mixed case, numbers, symbols).

### FAIL Item 3: Verbose Logging Level
- **File:** `.env:21`
- **Severity:** **LOW**
- **Detail:** `LOG_LEVEL=debug` — Debug level logging may include sensitive data (SQL queries, request data, stack traces) in log files.
- **Fix:** Set `LOG_LEVEL=warning` or `LOG_LEVEL=error` in production.

### FAIL Item 4: No CORS Configuration
- **File:** No `config/cors.php` file
- **Severity:** **MEDIUM**
- **Detail:** No CORS configuration found. While Laravel's defaults are restrictive (same-origin by default), this should be explicitly configured for the ecommerce application which likely has frontend JavaScript making API calls.
- **Fix:** Publish and configure `config/cors.php` with appropriate allowed origins, methods, and headers.

### PASS Items:
- **PASS** ✅ `.env` is not in public web root (located at project root, outside `public/`)
- **PASS** ✅ No exposed storage directories (storage is in the standard location)

### Verdict: **FAIL** ❌
Debug mode enabled in .env is the primary concern. Weak credentials and missing CORS config add to the risk.

---

## 6. VULNERABLE & OUTDATED COMPONENTS (A06)

### Checks performed:
- Composer dependencies (version check)
- Known vulnerability scanning

| Check | Status | Details |
|-------|--------|---------|
| Composer dependencies | **NOTE** | `composer.json` and `composer.lock` present but no full dependency audit was run. Recommend `composer audit` or using a SCA tool. |

### Verdict: **INCONCLUSIVE** ⚠️
Manual audit of composer vendor dependencies was not performed. Recommend running `composer audit` and `npm audit` (if applicable).

---

## 7. IDENTIFICATION & AUTHENTICATION FAILURES (A07)

### Checks performed:
- Password hashing strength
- Rate limiting on login endpoints
- Session management configuration
- Password reset implementation

### Results:

### FAIL Item 1: No Rate Limiting on Login Routes
- **Files:** `routes/web.php:217,219,285-286,291-292,298-299`
- **Severity:** **HIGH**
- **Detail:** Login routes for users (`/login` POST), admin (`/admin/login` POST), super-admin (`/super-admin/login` POST), and staff (`/staff/login` POST) do NOT have `throttle` middleware applied. An attacker could perform unlimited brute-force login attempts.
- **Fix:** Add `->middleware('throttle:5,1')` to all POST login routes:
  - `web.php:217`: `Route::post('/login', ...)->middleware('throttle:5,1')`
  - `web.php:219`: `Route::post('/register', ...)->middleware('throttle:3,1')`
  - `admin` login routes: `->middleware('throttle:5,1')`
  - `super-admin` login routes: `->middleware('throttle:3,1')`
  - `staff` login routes: `->middleware('throttle:5,1')`

### FAIL Item 2: Weak Password Reset Token (No Expiry Enforcement in View)
- **File:** `routes/web.php:222`
- **Severity:** **LOW**
- **Detail:** Password reset route `/reset-password/{token}` does not have explicit token expiry validation beyond what Laravel's Password facade provides. The token is passed directly as a URL parameter.
- **Fix:** Ensure `config/auth.php` has appropriate `expire` value (default: 60 minutes).

### PASS Items:
- **PASS** ✅ Strong password hashing with bcrypt (12 rounds)
- **PASS** ✅ Session regeneration on login (`$request->session()->regenerate()`)
- **PASS** ✅ Session config: `http_only=true`, `same_site=lax`
- **PASS** ✅ Password validation: minimum 8 characters, confirmed
- **PASS** ✅ Session lifetime: 120 minutes (reasonable)

### Verdict: **FAIL** ❌
No rate limiting on any login endpoint is a significant authentication security gap, enabling brute-force attacks.

---

## 8. SOFTWARE & DATA INTEGRITY FAILURES (A08)

### Checks performed:
- Unsafe `exec()` calls with user-controllable input
- `unserialize()` usage
- File inclusion vulnerabilities

### Results:

| Check | Status | Details |
|-------|--------|---------|
| `unserialize()` usage | **PASS** | Not found anywhere in application code |
| `eval()` usage | **PASS** | Not found |
| `exec()` / `shell_exec()` usage | **WARNING** ⚠️ | Found in backup functionality (see below) |

### WARNING Item: exec() Used in Backup Operations
- **Files:**
  - `app/Http/Controllers/Admin/SystemController.php:231,252`
  - `app/Http/Controllers/Admin/SettingController.php:329`
- **Severity:** **LOW** (not exploitable as configured)
- **Detail:** `exec()` is used to run `mysqldump` and `pg_dump` for database backups. The commands use `config('database.connections.mysql.*')` values (hardcoded config), NOT user input. However, if an attacker gains admin access and modifies database config, this could be leveraged.
- **Fix:** Consider using Laravel's built-in backup packages (spatie/laravel-backup) instead.

### FAIL Item 3: PDO::exec() with Dynamic Database Name in Installer
- **File:** `app/Http/Controllers/Install/InstallController.php:70`
- **Severity:** **MEDIUM**
- **Detail:** `$connection->exec("CREATE DATABASE IF NOT EXISTS `{$request->db_name}` ...")` — The database name comes from user input during installation. This could allow SQL injection in the database name if not properly sanitized.
- **Risk:** Mitigated by the fact that the installer is only accessible before initial installation (protected by `CheckInstallation` middleware). After installation, the install routes redirect away.
- **Fix:** Validate database name with a regex pattern (letters, numbers, underscores only).

### Verdict: **PASS** (with warnings) ✅
No exploitable command injection found. The installer database creation has a theoretical injection vector that's protected by pre-install-only access.

---

## 9. SECURITY LOGGING & MONITORING FAILURES (A09)

### Checks performed:
- Failed login attempt logging
- Security event logging
- Audit trail presence

### Results:

### FAIL Item 1: Failed Login Attempts Not Logged
- **Files:**
  - `app/Http/Controllers/UserController.php:58-60`
  - `app/Http/Controllers/Admin/AuthController.php:67-69`
  - `app/Http/Controllers/Staff/AuthController.php:73-75`
- **Severity:** **HIGH**
- **Detail:** When login fails, the controllers return `back()->withErrors(...)` but do NOT log the failed attempt. Only successful logins are logged via `ActivityLog`. Failed brute-force attempts cannot be detected or investigated.
- **Fix:** Add logging for failed login attempts:
  ```php
  \Illuminate\Support\Facades\Log::warning('Failed login attempt', [
      'email' => $request->email,
      'ip' => $request->ip(),
      'user_agent' => $request->userAgent(),
  ]);
  ```

### FAIL Item 2: No Event-Driven Login Monitoring
- **File:** No `EventServiceProvider` found registered in `bootstrap/app.php`
- **Severity:** **MEDIUM**
- **Detail:** Laravel fires `Illuminate\Auth\Events\Failed` and `Illuminate\Auth\Events\Login` events, but there are no event listeners registered to handle them. This means there's no centralized security monitoring.
- **Fix:** Create listeners for `Failed` login events to log attempts, send alerts, and implement IP-based blocking.

### PASS Items:
- **PASS** ✅ Successful logins ARE logged via `ActivityLog` (adminLog/customerLog)
- **PASS** ✅ `ActivityLog` model exists and is used for audit trail
- **PASS** ✅ API key usage is logged (ValidateApiKey middleware)

### Verdict: **FAIL** ❌
Failed login attempts are not recorded, making it impossible to detect or investigate brute-force attacks.

---

## 10. SERVER-SIDE REQUEST FORGERY (SSRF) (A10)

### Checks performed:
- Outbound HTTP requests from application code
- URL fetching based on user input

### Results:

| Check | Status | Details |
|-------|--------|---------|
| System update checker HTTP request | **PASS** | `SystemController.php:83-92` — posts to `$updateServerUrl` from setting (no user input) |
| No file fetching from URLs | **PASS** | Image URLs appear to be local storage paths, not fetched remotely |
| No webhook proxies | **PASS** | Not found |

### Verdict: **PASS** ✅
No SSRF vectors identified.

---

## OVERALL SUMMARY

| OWASP Category | Verdict | Critical | High | Medium | Low |
|----------------|---------|----------|------|--------|-----|
| A01: Broken Access Control | **PASS** ✅ | 0 | 0 | 0 | 0 |
| A02: Cryptographic Failures | **PASS** ✅ | 0 | 0 | 1 | 1 |
| A03: Injection (XSS) | **FAIL** ❌ | 0 | 4 | 1 | 0 |
| A04: Insecure Design | **FAIL** ❌ | 1 | 0 | 0 | 0 |
| A05: Security Misconfiguration | **FAIL** ❌ | 0 | 1 | 2 | 1 |
| A06: Outdated Components | **INCONCLUSIVE** ⚠️ | — | — | — | — |
| A07: Auth Failures | **FAIL** ❌ | 0 | 1 | 0 | 1 |
| A08: Software Integrity | **PASS** ✅ | 0 | 0 | 1 | 1 |
| A09: Security Logging | **FAIL** ❌ | 0 | 2 | 1 | 0 |
| A10: SSRF | **PASS** ✅ | 0 | 0 | 0 | 0 |

### TOP PRIORITY FIXES:

1. **CRITICAL** — Price manipulation: Remove `custom_price` client override in `CartController.php:143-145` and always use server-side product prices.

2. **HIGH** — Stored XSS via Blog/Page content: Fix the `class_exists('Purifier')` fallback in all page and blog views to escape content when Purifier is not available.

3. **HIGH** — Stored XSS via Newsletter/Widget content: Escape `{!! $content !!}` outputs.

4. **HIGH** — Reflected XSS via search highlighting in quotations: Escape search term output.

5. **HIGH** — No rate limiting on login: Add `throttle` middleware to all login routes.

6. **HIGH** — Failed login not logged: Add logging to all failed authentication attempts.

7. **HIGH** — Debug mode enabled: Set `APP_DEBUG=false` in production `.env`.
