---
name: admin-search-filter
description: Live search and filter system with AJAX updates, debounced input, loading spinner, and URL synchronization for admin listing pages.
---

# Admin Search and Filter Functionality

**Filter Form Structure:**

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" 
                               placeholder="Name, SKU..." value="{{ request('search') }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm"></div>
                        </span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Category</label>
                    <select name="category" id="filterCategory" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-8">
                    <a href="{{ route('admin.items.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

**Live Search JavaScript:**

let searchTimeout;
const searchInput = document.getElementById('liveSearch');
const searchSpinner = document.getElementById('searchSpinner');

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchSpinner.style.display = 'block';
    searchTimeout = setTimeout(() => {
        performLiveSearch(this.value.trim());
    }, 300);
});

function performLiveSearch(searchTerm) {
    const params = new URLSearchParams();
    if (searchTerm) params.set('search', searchTerm);
    fetch(`${window.location.pathname}?${params.toString()}&ajax=1`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        searchSpinner.style.display = 'none';
        if (data.html) document.querySelector('#tableBody').innerHTML = data.html;
        window.history.pushState({}, '', `${window.location.pathname}?${params.toString()}`);
    });
}

**Key Features:**
- Debounced search with 300ms delay
- Loading spinner during AJAX requests
- URL updates without page reload
- Multiple filters support
- AJAX table updates