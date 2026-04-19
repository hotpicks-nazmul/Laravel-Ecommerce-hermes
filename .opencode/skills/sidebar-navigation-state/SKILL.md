---
name: sidebar-navigation-state
description: Keep parent menu categories expanded when a child route is active using routeIs pattern and show class on collapse div.
---

# Sidebar Navigation State

**Problem:** When a user clicks on a submenu item, the parent menu category collapses, causing the user to lose track of where they are in the navigation hierarchy.

**Solution:** The sidebar menu should automatically expand and highlight the parent category when a child route is active.

**Implementation in `layouts/app.blade.php`:**

<div class="menu-category">
    <a class="menu-category-header {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" 
       data-bs-toggle="collapse" 
       href="#menuProducts" 
       role="button" 
       aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}">
        <div>
            <i class="bi bi-box menu-icon"></i>
            <span class="menu-category-title">Products</span>
        </div>
        <i class="bi bi-chevron-down arrow"></i>
    </a>
    <div class="collapse {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="menuProducts">
        <!-- Submenu items -->
    </div>
</div>

**Key Points:**
- `aria-expanded`: Set to `'true'` when any child route is active
- `.show` class: Add to collapse div to keep it expanded
- `.active` class: Add to header for visual feedback
- `request()->routeIs('admin.products.*')`: Matches all routes starting with `admin.products.`

**Route Naming Convention for Menu State:**
- Products: `admin.products.*` → `routeIs('admin.products.*')`
- Orders: `admin.orders.*` → `routeIs('admin.orders.*')`
- Categories: `admin.categories.*` → `routeIs('admin.categories.*')`
- Settings: `admin.settings.*` → `routeIs('admin.settings.*')`