---
name: content-area-extra-padding
description: Prevent extra padding and double-wrapped content areas by relying on layout's existing content-area wrapper instead of adding custom wrappers.
---

# Content Area and Extra Padding

**Problem:** Admin pages may display with extra padding, margin, or double-wrapped content areas, causing inconsistent layout compared to other admin pages.

**Common symptoms:**
- Content appears lower than other pages
- Double padding on top/bottom
- Extra spacing around content

**Root Cause:** The issue occurs when pages include unnecessary wrappers that conflict with the layout's existing structure:
1. Duplicate `content-area` wrapper – Adding `<div class="content-area">` when the layout already provides it
2. Extra `container-fluid` wrapper – Adding `<div class="container-fluid">` unnecessarily
3. Extra padding classes – Adding `pt-4` or similar padding classes
4. Unnecessary custom CSS – Adding `padding-bottom: 100px` without floating buttons

**Correct Page Structure:**

@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Page Title</h4>
    <a href="..." class="btn btn-primary">Add New</a>
</div>
<div class="row">
    <div class="col-lg-8">
        <!-- Cards -->
    </div>
</div>
@endsection

**What NOT to Do:**

<!-- WRONG - Don't add these -->
@section('content')
<div class="content-area">           <!-- Layout already provides this -->
    <div class="container-fluid pt-4">  <!-- Extra wrapper + padding -->
        <!-- Content -->
    </div>
</div>
@endsection

**When to Use Custom Padding:**
Only add `padding-bottom: 100px` via `@push('styles')` when the page has floating save buttons.

**Key Points:**
- `.content-area`: Don't add – layout provides it
- `.container-fluid`: Don't add – not needed
- `pt-4` / `mt-4`: Don't add – layout provides spacing
- `padding-bottom: 100px`: Only with floating buttons