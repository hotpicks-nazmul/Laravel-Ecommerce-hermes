---
name: admin-frontend-integration
description: Critical rule that any admin panel functionality affecting frontend display must have corresponding frontend implementation.
---

# Admin Panel and Frontend Integration Rule

When implementing any admin panel functionality that affects the frontend display or user experience, ALWAYS implement the corresponding frontend adjustments as well.

**This includes but is not limited to:**

1. Product-related features (attributes, colors, variants) – Must be displayed on product detail page and work with cart/checkout
2. Category/Brand management – Must be reflected in frontend filters and navigation
3. Banner/Slider management – Must display correctly on homepage
4. Settings changes – Must reflect in frontend layout, colors, logos, etc.
5. SEO/Meta settings – Must be applied to frontend pages
6. Payment/Shipping settings – Must work with frontend checkout process

**Rule:** `Admin Panel Functionality = Backend + Frontend Implementation`

Always ask: "Does this admin feature need frontend display or interaction?" If yes, implement both sides.