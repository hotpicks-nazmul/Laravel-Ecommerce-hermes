---
name: admin-card-styling
description: Standard card styling pattern for admin panel using border-0 shadow-sm classes with white header and consistent spacing.
---

# Admin Card Styling

Use this standard card styling for all admin panel cards:

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-icon me-2"></i>Card Title</h6>
    </div>
    <div class="card-body">
        <!-- Content -->
    </div>
</div>

**Key Classes:**
- `border-0`: Removes the default card border
- `shadow-sm`: Adds a subtle shadow for depth
- `mb-3`: Adds margin-bottom between cards
- `bg-white`: Ensures header has white background
- `card-header`: Contains the title with icon
- `card-body`: Holds the main content

**Icon Placement:**
Always include an icon in the card header that matches the card's purpose. Use `me-2` for spacing between the icon and title text.