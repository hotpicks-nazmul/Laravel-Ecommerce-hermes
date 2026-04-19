---
name: route-conflict-prevention
description: Prevent placeholder routes from conflicting with actual implementations by checking existing routes and removing placeholders before implementation.
---

# Route Conflict Prevention

**Problem:** Placeholder routes showing "under development" messages can conflict with actual implementations, causing users to see "under development" instead of the real feature.

**Example Issue:**
- Placeholder route: `admin/related-products` → Shows "under development" message
- Actual implementation: `admin/products/{product}/related` → Full functionality

**What Happens:**
User clicks a menu item linking to the placeholder route and sees "This feature is currently under development" instead of the actual implementation.

**Solution:**

1. Check for placeholder routes before implementing new features:
   php artisan route:list --name=feature-name

2. Remove or update placeholder routes when implementing the actual feature:
   - Delete placeholder routes from `routes/admin.php`
   - Ensure menu links point to the correct implementation URLs

3. Use consistent route naming:
   - Good: `admin.products.related` (nested under products)
   - Avoid: `admin.related-products` (separate top-level route)

4. Clear route cache after changes:
   php artisan route:clear

**Prevention Checklist:**
- Search for existing placeholder routes with similar names
- Remove any conflicting placeholder routes
- Update sidebar menu links to point to correct URLs
- Clear route cache after deployment