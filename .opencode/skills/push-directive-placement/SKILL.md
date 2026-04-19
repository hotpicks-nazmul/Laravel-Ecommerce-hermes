---
name: push-directive-placement
description: Always place @push directives after @section('content') to ensure CSS and JavaScript are properly rendered in the layout's @stack.
---

# @push Directive Placement

**Problem:** CSS and JavaScript may not load correctly, causing layout issues where headers appear at the bottom, sidebars overlap content, or form elements appear displaced.

**Root Cause:** In Laravel Blade, the `@push` directive must be placed AFTER the `@section` declaration where the content will be injected. If `@push` is placed BEFORE `@section('content')`, the stacked content may not be properly rendered in the layout's `@stack` directive.

**Incorrect Code:**

@extends('admin.layouts.app')
@section('title', 'Page Title')

@push('styles')  // ❌ WRONG - Before @section('content')
<style> .content-area { padding-bottom: 100px !important; } </style>
@endpush

@section('content')
<!-- Page content -->
@endsection

**Correct Code:**

@extends('admin.layouts.app')
@section('title', 'Page Title')

@section('content')
<!-- Page content -->
@endsection

@push('styles')  // ✅ CORRECT - After @section('content')
<style> .content-area { padding-bottom: 100px !important; } </style>
@endpush

@push('scripts')
<script> // JavaScript code here </script>
@endpush

**Best Practice:**
1. Always place `@push` after `@section`
2. Put `@push('styles')` and `@push('scripts')` AFTER your `@section('content')` ends
3. Group all `@push` directives at the end of the file, after all content sections