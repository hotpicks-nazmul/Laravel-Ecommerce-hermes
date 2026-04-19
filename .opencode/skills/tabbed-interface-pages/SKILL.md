---
name: tabbed-interface-pages
description: Always render tabbed pages through the main index view that extends the admin layout, never return partial views directly.
---

# Tabbed Interface Pages

**Problem:** When creating admin pages with tabs, accessing a tab directly via URL may result in missing CSS/styles if the controller renders a partial view instead of the main page view.

**Solution:** Always render tabbed pages through the main index view that extends the admin layout.

**Correct Controller Implementation:**

public function index(Request $request)
{
    $tab = $request->get('tab', 'api-keys');
    
    if ($tab === 'webhooks') {
        return $this->webhooksIndex(); // Returns main index view
    }
    return $this->apiKeysIndex();
}

protected function webhooksIndex()
{
    $webhooks = Webhook::orderBy('created_at', 'desc')->paginate(10);
    $apiKeys = ApiKey::orderBy('created_at', 'desc')->paginate(10);
    
    // Always return the main index view with all variables
    return view('admin.settings.api-keys.index', [
        'apiKeys' => $apiKeys,
        'webhooks' => $webhooks,
        'activeTab' => 'webhooks',
    ]);
}

**View Implementation:**

@extends('admin.layouts.app')

@section('content')
@php
    $activeTab = $activeTab ?? 'api-keys';
    $apiKeys = $apiKeys ?? collect();
    $webhooks = $webhooks ?? collect();
@endphp

<div class="tab-content">
    @if($activeTab === 'api-keys')
        <!-- API Keys Tab Content -->
    @else
        <!-- Webhooks Tab Content -->
    @endif
</div>
@endsection

**Key Points:**
- Always use main layout view – Don't return partial views that don't extend the admin layout
- Pass all required variables – Even if empty, using default values
- Use query parameter – `?tab=xyz` to switch between tabs through the same controller method