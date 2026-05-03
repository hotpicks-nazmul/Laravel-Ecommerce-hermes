@extends('themes.general.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

    @if($cart->isEmpty())
    <div class="text-center py-12">
        <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class="bi bi-cart-x text-4xl text-gray-400"></i>
        </div>
        <h4 class="text-gray-600 font-medium mb-2">Your cart is empty</h4>
        <a href="{{ route('cart.index') }}" class="inline-block bg-halal-green text-white px-6 py-2 rounded-full hover:bg-halal-dark transition-colors">Go to Cart</a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            @if($lastOrder && auth()->check())
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-halal-green">
                <h3 class="text-lg font-bold mb-3"><i class="bi bi-clock-history text-halal-green me-2"></i>Previously Used Address</h3>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <p class="font-medium">{{ $lastOrder->billing_full_name }}</p>
                    <p class="text-gray-600">{{ $lastOrder->billing_address }}, {{ $lastOrder->billing_city }}, {{ $lastOrder->billing_state }} - {{ $lastOrder->billing_postcode }}</p>
                    <p class="text-gray-600">{{ $lastOrder->billing_country }}</p>
                    <p class="text-gray-600">{{ $lastOrder->billing_phone }} | {{ $lastOrder->billing_email }}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="usePreviousAddress()" class="bg-halal-green text-white px-4 py-2 rounded-lg text-sm hover:bg-halal-dark transition-colors">
                        <i class="bi bi-check2 me-1"></i> Use This Address
                    </button>
                    <button onclick="showNewAddressForm()" class="border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                        <i class="bi bi-pencil me-1"></i> Enter New Address
                    </button>
                </div>
            </div>
            @endif

            <div id="addressFormSection" class="{{ $lastOrder && auth()->check() ? 'hidden' : '' }}" style="{{ $lastOrder && auth()->check() ? 'display: none;' : '' }}">
                <div class="bg-white p-6 rounded-lg shadow-sm mb-6 billing-card">
                    <h3 class="text-lg font-bold mb-4">Billing Details</h3>
                    <form id="checkoutForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                <input type="text" name="billing_first_name" id="billing_first_name" value="{{ $user->first_name ?? '' }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                <input type="text" name="billing_last_name" id="billing_last_name" value="{{ $user->last_name ?? '' }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="billing_email" id="billing_email" value="{{ $user->email ?? '' }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                <input type="tel" name="billing_phone" id="billing_phone" value="{{ $user->phone ?? '' }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                                <input type="text" name="billing_address" id="billing_address" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>

                            @if($checkoutMode === 'international')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                                <select name="billing_country" id="billing_country" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green" onchange="loadCities()">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                            <input type="hidden" name="billing_country" id="billing_country" value="{{ $defaultCountryName }}">
                            @endif

                            <div class="searchable-select-wrapper">
                                <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                <div class="searchable-select" data-select-id="billing_city_id">
                                    <input type="text" class="searchable-select-input w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green" placeholder="Search city..." autocomplete="off">
                                    <input type="hidden" name="billing_city" id="billing_city">
                                    <select name="billing_city_id" id="billing_city_id" required class="hidden-select" onchange="onCityChange()">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="searchable-select-dropdown hidden">
                                        @foreach($cities as $city)
                                            <div class="searchable-select-option" data-value="{{ $city->id }}">{{ $city->name }}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <span class="error-message" id="error-billing_city_id"></span>
                            </div>

                            <div class="searchable-select-wrapper">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Area *</label>
                                <div class="searchable-select" data-select-id="billing_area_id">
                                    <input type="text" class="searchable-select-input w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green bg-gray-100" placeholder="Select a city first" autocomplete="off" disabled>
                                    <select name="billing_area_id" id="billing_area_id" class="hidden-select" onchange="onAreaChange()" required>
                                        <option value="">Select Area</option>
                                    </select>
                                    <div class="searchable-select-dropdown hidden">
                                        <div class="searchable-select-option text-muted" style="cursor:default;color:#9ca3af;">Select a city first</div>
                                    </div>
                                </div>
                                <span class="error-message" id="error-billing_area_id"></span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                                <input type="text" name="billing_state" id="billing_state" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green" onchange="updateShippingOptions()">
                                <span class="error-message" id="error-billing_state"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postcode *</label>
                                <input type="text" name="billing_postcode" id="billing_postcode" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                                <span class="error-message" id="error-billing_postcode"></span>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Order Notes</label>
                                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green" placeholder="Notes about your order, e.g. special notes for delivery"></textarea>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-medium mb-3">Shipping Method</h4>
                            <div id="shippingOptionsContainer" class="space-y-2">
                                @php
                                    $defaultDelivery = $cart->getSubtotal() >= 500 ? 0 : 60;
                                @endphp
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $defaultDelivery === 0 ? 'border-halal-green bg-green-50' : '' }}">
                                    <input type="radio" name="shipping_method" value="home_delivery" checked
                                        class="mr-3" onchange="selectShippingMethod('home_delivery', {{ $defaultDelivery }})">
                                    <div class="flex-1">
                                        <div class="font-medium">Home Delivery</div>
                                        <div class="text-sm text-gray-500">3-5 business days</div>
                                    </div>
                                    <div class="font-medium {{ $defaultDelivery === 0 ? 'text-halal-green' : '' }}">
                                        {{ $defaultDelivery === 0 ? 'Free' : '৳' . $defaultDelivery }}
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-medium mb-3">Payment Method</h4>
                            <div class="space-y-2">
                                @forelse($paymentGateways as $gateway)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $loop->first ? 'border-halal-green bg-green-50' : '' }}">
                                    <input type="radio" name="payment_method" value="{{ $gateway->slug }}" {{ $loop->first ? 'checked' : '' }} class="mr-3">
                                    @if($gateway->logo)
                                    <img src="{{ Storage::url($gateway->logo) }}" alt="{{ $gateway->name }}" class="w-8 h-8 object-contain mr-2">
                                    @else
                                    <i class="bi bi-credit-card mr-2"></i>
                                    @endif
                                    <span>{{ $gateway->name }}</span>
                                    @if($gateway->test_mode)
                                    <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Test</span>
                                    @endif
                                </label>
                                @empty
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="cod" checked class="mr-3">
                                    <span>Cash on Delivery</span>
                                </label>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="terms" id="terms" required class="mr-2">
                                <span class="text-sm text-gray-600">I agree to the <a href="{{ route('terms') }}" class="text-halal-green hover:underline">Terms and Conditions</a></span>
                            </label>
                            <span class="error-message" id="error-terms"></span>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <h3 class="text-lg font-bold mb-4">Order Items</h3>
                @foreach($cart->items as $item)
                <div class="flex items-center space-x-4 bg-white p-4 rounded-lg shadow-sm mb-4" id="checkout-item-{{ $item['product_id'] }}">
                    @php
                        $imageUrl = isset($item['image'])
                            ? (str_starts_with($item['image'], 'http') ? $item['image']
                            : (str_starts_with($item['image'], '/storage/') ? $item['image']
                            : (str_starts_with($item['image'], '/uploads/') ? asset($item['image'])
                            : asset('storage/' . $item['image']))))
                            : 'https://placehold.co/80';
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">{{ $item['name'] }}</h4>
                        <p class="text-halal-green font-bold">৳{{ number_format($item['price'], 2) }}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <button onclick="updateCheckoutItem({{ $item['product_id'] }}, {{ $item['quantity'] - 1 }})" class="w-7 h-7 bg-gray-100 rounded flex items-center justify-center hover:bg-gray-200">
                                <i class="bi bi-dash text-sm"></i>
                            </button>
                            <span class="font-medium" id="qty-{{ $item['product_id'] }}">{{ $item['quantity'] }}</span>
                            <button onclick="updateCheckoutItem({{ $item['product_id'] }}, {{ $item['quantity'] + 1 }})" class="w-7 h-7 bg-gray-100 rounded flex items-center justify-center hover:bg-gray-200">
                                <i class="bi bi-plus text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                        <button onclick="removeCheckoutItem({{ $item['product_id'] }})" class="text-red-500 hover:text-red-700 text-sm mt-1">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm h-fit sticky top-24">
            <h3 class="text-lg font-bold mb-4">Order Summary</h3>
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span id="checkoutSubtotal">৳{{ number_format($cart->getSubtotal(), 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Delivery</span>
                    <span id="checkoutDelivery" class="{{ $defaultDelivery === 0 ? 'text-halal-green' : '' }}">{{ $defaultDelivery === 0 ? 'Free' : '৳' . $defaultDelivery }}</span>
                </div>
                <hr>
                <div class="flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span id="checkoutTotal" class="text-halal-green">৳{{ number_format($cart->getSubtotal() + $defaultDelivery, 2) }}</span>
                </div>
            </div>
            @if($defaultDelivery === 0)
            <div id="freeDeliveryMessage" class="text-halal-green text-sm mb-4">
                <i class="bi bi-truck mr-1"></i>You have free delivery!
            </div>
            @endif
            <button type="button" id="placeOrderBtn" onclick="processCheckout(event)" class="w-full bg-halal-green text-white py-3 rounded-lg font-medium hover:bg-halal-dark transition-colors">
                Place Order
            </button>
            <a href="{{ route('cart.index') }}" class="block w-full text-center py-3 text-halal-green hover:underline mt-2">
                <i class="bi bi-arrow-left mr-1"></i> Back to Cart
            </a>
        </div>
    </div>
    @endif
</div>

<style>
.searchable-select { position: relative; z-index: 50; }
.searchable-select.open { z-index: 999; }
.searchable-select .hidden-select { position: absolute; left: -9999px; opacity: 0; height: 0; width: 0; pointer-events: none; }
.searchable-select-dropdown {
    position: absolute; top: 100%; left: 0; right: 0; z-index: 1000;
    max-height: 220px; overflow-y: auto;
    background: #fff; border: 1px solid #d1d5db; border-radius: 0 0 8px 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15); margin-top: 2px;
}
.searchable-select-dropdown.hidden { display: none; }
.searchable-select-option {
    padding: 8px 14px; cursor: pointer; font-size: 14px;
    border-bottom: 1px solid #f3f4f6; transition: background 0.15s;
}
.searchable-select-option:last-child { border-bottom: none; }
.searchable-select-option:hover, .searchable-select-option.highlighted { background: #dcfce7; color: #166534; }
.searchable-select-option.selected { background: #22c55e; color: #fff; }
.searchable-select-input:focus { border-color: #22c55e !important; box-shadow: 0 0 0 2px rgba(34,197,94,0.2) !important; }
.searchable-select-input::placeholder { color: #9ca3af; }
.billing-card { overflow: visible !important; }
.billing-card .grid { overflow: visible !important; }
.error-message { display: none; color: #dc2626; font-size: 0.8rem; margin-top: 4px; }
.error-message.visible { display: block; }
.is-invalid { border-color: #dc2626 !important; }
.is-invalid:focus { box-shadow: 0 0 0 2px rgba(220,38,38,0.2) !important; }
</style>

<script>
let checkoutSubtotal = {{ $cart->getSubtotal() }};
let checkoutDelivery = {{ $defaultDelivery }};
let checkoutTotal = checkoutSubtotal + checkoutDelivery;
let selectedShippingMethod = 'home_delivery';

const checkoutMode = '{{ $checkoutMode }}';
const defaultCountry = '{{ $defaultCountryName }}';

function initSearchableSelect(container) {
    const input = container.querySelector('.searchable-select-input');
    const dropdown = container.querySelector('.searchable-select-dropdown');
    const select = container.querySelector('select');
    const hiddenInput = container.querySelector('input[type="hidden"]');

    function filterOptions(query) {
        const options = dropdown.querySelectorAll('.searchable-select-option');
        const q = query.toLowerCase().trim();
        options.forEach(opt => {
            opt.style.display = !q || opt.textContent.toLowerCase().includes(q) ? 'block' : 'none';
        });
    }

    function setSelectOptions(opts) {
        select.innerHTML = '';
        dropdown.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select';
        select.appendChild(placeholder);
        if (opts.length === 0) {
            const msg = document.createElement('div');
            msg.className = 'searchable-select-option text-muted';
            msg.textContent = 'No areas available. Select a city first.';
            msg.style.cursor = 'default';
            msg.style.color = '#9ca3af';
            dropdown.appendChild(msg);
        } else {
            opts.forEach(o => {
                const opt = document.createElement('option');
                opt.value = o.value;
                opt.textContent = o.text;
                select.appendChild(opt);
                const div = document.createElement('div');
                div.className = 'searchable-select-option';
                div.dataset.value = o.value;
                div.textContent = o.text;
                div.addEventListener('click', () => selectOption(o.value, o.text));
                dropdown.appendChild(div);
            });
        }
        select.value = '';
        input.value = '';
        input.placeholder = opts.length > 0 ? 'Search area...' : 'Select a city first';
    }

    function selectOption(value, text) {
        select.value = value;
        input.value = text;
        closeDropdown();
        select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function highlightNext() {
        const visible = [...dropdown.querySelectorAll('.searchable-select-option')].filter(o => o.style.display !== 'none');
        const idx = visible.findIndex(o => o.classList.contains('highlighted'));
        visible.forEach(o => o.classList.remove('highlighted'));
        if (idx < visible.length - 1) visible[idx + 1]?.classList.add('highlighted');
        else visible[0]?.classList.add('highlighted');
    }

    function highlightPrev() {
        const visible = [...dropdown.querySelectorAll('.searchable-select-option')].filter(o => o.style.display !== 'none');
        const idx = visible.findIndex(o => o.classList.contains('highlighted'));
        visible.forEach(o => o.classList.remove('highlighted'));
        if (idx > 0) visible[idx - 1]?.classList.add('highlighted');
        else visible[visible.length - 1]?.classList.add('highlighted');
    }

    function selectHighlighted() {
        const hl = dropdown.querySelector('.searchable-select-option.highlighted');
        if (hl) selectOption(hl.dataset.value, hl.textContent);
    }

    // Clear all options and rebuild from select
    function rebuildOptions() {
        dropdown.innerHTML = '';
        [...select.options].forEach(opt => {
            if (!opt.value) return;
            const div = document.createElement('div');
            div.className = 'searchable-select-option';
            div.dataset.value = opt.value;
            div.textContent = opt.text;
            div.addEventListener('click', () => selectOption(opt.value, opt.text));
            dropdown.appendChild(div);
        });
        filterOptions(input.value);
    }

    function openDropdown() {
        var hasOptions = dropdown.querySelectorAll('.searchable-select-option[data-value]').length > 0;
        if (!hasOptions) {
            rebuildOptions();
        }
        dropdown.classList.remove('hidden');
        container.classList.add('open');
        filterOptions(input.value);
    }

    function closeDropdown() {
        if (!input.value.trim() && select.value) {
            select.value = '';
            if (hiddenInput) hiddenInput.value = '';
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
        dropdown.classList.add('hidden');
        container.classList.remove('open');
    }

    input.addEventListener('focus', openDropdown);

    input.addEventListener('input', () => {
        dropdown.classList.remove('hidden');
        filterOptions(input.value);
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown') { e.preventDefault(); highlightNext(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); highlightPrev(); }
        else if (e.key === 'Enter') { e.preventDefault(); selectHighlighted(); }
        else if (e.key === 'Escape') { closeDropdown(); input.blur(); }
    });

    document.addEventListener('click', (e) => {
        if (!container.contains(e.target)) closeDropdown();
    });

    // Attach click handlers to pre-rendered options
    dropdown.querySelectorAll('.searchable-select-option[data-value]').forEach(opt => {
        opt.addEventListener('click', () => selectOption(opt.dataset.value, opt.textContent));
    });

    // If select already has a value, show it
    if (select.value) {
        const selectedOpt = [...select.options].find(o => o.value === select.value);
        if (selectedOpt) input.value = selectedOpt.text;
    }

    // Expose methods for dynamic options
    container.rebuildOptions = rebuildOptions;
    container.setSelectOptions = setSelectOptions;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.searchable-select').forEach(initSearchableSelect);
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        runCheckout();
    });
});

async function loadCities() {
    const country = document.getElementById('billing_country')?.value || defaultCountry;
    try {
        const response = await fetch(`/checkout/get-cities?country=${encodeURIComponent(country)}`);
        const data = await response.json();
        const cityContainer = document.querySelector('.searchable-select[data-select-id="billing_city_id"]');
        const cityOpts = [];
        if (data.success && data.cities) {
            data.cities.forEach(c => cityOpts.push({ value: c.id, text: c.name }));
        }
        cityContainer.setSelectOptions(cityOpts);

        const areaContainer = document.querySelector('.searchable-select[data-select-id="billing_area_id"]');
        const areaInput = areaContainer.querySelector('.searchable-select-input');
        areaInput.value = '';
        areaInput.disabled = true;
        areaInput.classList.add('bg-gray-100');
        areaInput.placeholder = 'Select a city first';
        areaContainer.setSelectOptions([]);
    } catch (e) { console.error('Error loading cities:', e); }
}

async function onCityChange() {
    const citySelect = document.getElementById('billing_city_id');
    var cityErrorEl = document.getElementById('error-billing_city_id');
    if (cityErrorEl) { cityErrorEl.textContent = ''; cityErrorEl.classList.remove('visible'); }
    citySelect.classList.remove('is-invalid');

    const cityId = citySelect.value;
    const cityName = citySelect.options[citySelect.selectedIndex]?.text || '';
    document.getElementById('billing_city').value = cityName;

    const areaContainer = document.querySelector('.searchable-select[data-select-id="billing_area_id"]');
    const areaInput = areaContainer.querySelector('.searchable-select-input');
    areaInput.value = '';
    var areaErrorEl = document.getElementById('error-billing_area_id');
    if (areaErrorEl) { areaErrorEl.textContent = ''; areaErrorEl.classList.remove('visible'); }
    areaContainer.querySelector('select')?.classList.remove('is-invalid');
    areaContainer.setSelectOptions([]);

    if (cityId) {
        areaInput.disabled = false;
        areaInput.classList.remove('bg-gray-100');
        try {
            const response = await fetch(`/checkout/get-areas?city_id=${cityId}`);
            const data = await response.json();
            const opts = [];
            if (data.success && data.areas) {
                data.areas.forEach(a => opts.push({ value: a.id, text: a.name }));
            }
            areaContainer.setSelectOptions(opts);
        } catch (e) { console.error('Error loading areas:', e); }
    } else {
        areaInput.disabled = true;
        areaInput.classList.add('bg-gray-100');
        areaInput.placeholder = 'Select a city first';
    }
    updateShippingOptions();
}

function onAreaChange() {
    updateShippingOptions();
}

async function usePreviousAddress() {
    document.getElementById('addressFormSection').style.display = 'none';
    @if($lastOrder)
    const prevCity = '{{ addslashes($lastOrder->billing_city) }}';
    const prevCountry = '{{ addslashes($lastOrder->billing_country) }}';

    document.getElementById('billing_first_name').value = '{{ addslashes($lastOrder->billing_first_name) }}';
    document.getElementById('billing_last_name').value = '{{ addslashes($lastOrder->billing_last_name) }}';
    document.getElementById('billing_email').value = '{{ addslashes($lastOrder->billing_email) }}';
    document.getElementById('billing_phone').value = '{{ addslashes($lastOrder->billing_phone) }}';
    document.getElementById('billing_address').value = '{{ addslashes($lastOrder->billing_address) }}';
    document.getElementById('billing_city').value = prevCity;
    document.getElementById('billing_state').value = '{{ addslashes($lastOrder->billing_state) }}';
    document.getElementById('billing_postcode').value = '{{ addslashes($lastOrder->billing_postcode) }}';

    const countryEl = document.getElementById('billing_country');
    if (countryEl && countryEl.tagName === 'SELECT') {
        countryEl.value = prevCountry;
        await loadCities();
    }

    const cityContainer = document.querySelector('.searchable-select[data-select-id="billing_city_id"]');
    if (cityContainer) {
        const citySelect = cityContainer.querySelector('select');
        for (let opt of citySelect.options) {
            if (opt.text.toLowerCase() === prevCity.toLowerCase()) {
                const input = cityContainer.querySelector('.searchable-select-input');
                input.value = opt.text;
                citySelect.value = opt.value;
                citySelect.dispatchEvent(new Event('change'));
                break;
            }
        }
    }
    @endif
}

function showNewAddressForm() {
    document.getElementById('addressFormSection').style.display = 'block';
    document.getElementById('addressFormSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function updateShippingOptions() {
    const citySelect = document.getElementById('billing_city_id');
    const areaSelect = document.getElementById('billing_area_id');
    const cityId = citySelect?.value || '';
    const areaId = areaSelect?.value || '';

    try {
        const response = await fetch(`/checkout/shipping-options?city=${encodeURIComponent(cityId)}&area=${encodeURIComponent(areaId)}&subtotal=${checkoutSubtotal}`, {
            credentials: 'same-origin'
        });
        const data = await response.json();
        if (data.success && data.options) {
            renderShippingOptions(data.options);
        }
    } catch (error) {
        console.error('Error loading shipping options:', error);
    }
}

function renderShippingOptions(options) {
    const container = document.getElementById('shippingOptionsContainer');
    if (!container) return;
    let html = options.map(option => {
        const isSelected = option.id === selectedShippingMethod;
        return `
            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 ${isSelected ? 'border-halal-green bg-green-50' : ''}">
                <input type="radio" name="shipping_method" value="${option.id}" ${isSelected ? 'checked' : ''}
                    class="mr-3" onchange="selectShippingMethod('${option.id}', ${option.cost})">
                <div class="flex-1">
                    <div class="font-medium">${option.name}</div>
                    <div class="text-sm text-gray-500">${option.estimated_days || ''}</div>
                </div>
                <div class="font-medium ${option.cost === 0 ? 'text-halal-green' : ''}">
                    ${option.cost === 0 ? 'Free' : '৳' + option.cost.toLocaleString()}
                </div>
            </label>
        `;
    }).join('');
    container.innerHTML = html;
}

function selectShippingMethod(method, cost) {
    selectedShippingMethod = method;
    checkoutDelivery = cost;
    checkoutTotal = checkoutSubtotal + checkoutDelivery;
    updateOrderSummary();
    document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
        const label = radio.closest('label');
        if (radio.value === method) {
            label.classList.add('border-halal-green', 'bg-green-50');
            label.classList.remove('border-gray-300');
        } else {
            label.classList.remove('border-halal-green', 'bg-green-50');
            label.classList.add('border-gray-300');
        }
    });
}

function updateOrderSummary() {
    const subtotalEl = document.getElementById('checkoutSubtotal');
    const deliveryEl = document.getElementById('checkoutDelivery');
    const totalEl = document.getElementById('checkoutTotal');
    const freeDeliveryMsg = document.getElementById('freeDeliveryMessage');
    if (subtotalEl) subtotalEl.textContent = '৳' + checkoutSubtotal.toLocaleString();
    if (deliveryEl) {
        deliveryEl.textContent = checkoutDelivery === 0 ? 'Free' : '৳' + checkoutDelivery.toLocaleString();
        deliveryEl.className = checkoutDelivery === 0 ? 'text-halal-green' : '';
    }
    if (totalEl) totalEl.textContent = '৳' + checkoutTotal.toLocaleString();
    if (freeDeliveryMsg) freeDeliveryMsg.style.display = checkoutDelivery === 0 ? 'block' : 'none';
}

async function updateCheckoutItem(productId, quantity) {
    if (quantity < 1) { removeCheckoutItem(productId); return; }
    try {
        const response = await fetch('/cart/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            credentials: 'same-origin',
            body: JSON.stringify({ product_id: productId, quantity: quantity })
        });
        const data = await response.json();
        if (data.success) window.location.reload();
    } catch (error) { console.error('Error updating cart:', error); }
}

async function removeCheckoutItem(productId) {
    try {
        const response = await fetch('/cart/remove', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            credentials: 'same-origin',
            body: JSON.stringify({ product_id: productId })
        });
        const data = await response.json();
        if (data.success) window.location.reload();
    } catch (error) { console.error('Error removing from cart:', error); }
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(function(el) {
        el.textContent = '';
        el.classList.remove('visible');
    });
    document.querySelectorAll('.is-invalid').forEach(function(el) {
        el.classList.remove('is-invalid');
    });
}

function showError(fieldId, message) {
    var el = document.getElementById(fieldId);
    if (el) { el.classList.add('is-invalid'); }
    var errorEl = document.getElementById('error-' + fieldId);
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.add('visible');
    }
    showCheckoutNotification(message, 'error');
    if (el) { try { el.focus(); } catch(e) {} }
}

function showServerErrors(errors) {
    for (var field in errors) {
        if (errors.hasOwnProperty(field) && errors[field].length > 0) {
            showError(field, errors[field][0]);
        }
    }
}

async function runCheckout() {
    clearErrors();

    var hasError = false;
    var cityEl = document.getElementById('billing_city_id');
    if (!cityEl?.value) { showError('billing_city_id', 'Please select a city.'); hasError = true; }

    var areaEl = document.getElementById('billing_area_id');
    if (!areaEl?.value) { showError('billing_area_id', 'Please select an area.'); hasError = true; }

    var stateEl = document.getElementById('billing_state');
    if (!stateEl?.value?.trim()) { showError('billing_state', 'Please enter your state.'); hasError = true; }

    var postcodeEl = document.getElementById('billing_postcode');
    if (!postcodeEl?.value?.trim()) { showError('billing_postcode', 'Please enter your postcode.'); hasError = true; }

    var termsEl = document.getElementById('terms');
    if (!termsEl?.checked) { showError('terms', 'Please agree to the terms and conditions.'); hasError = true; }

    if (hasError) return;

    try {
        var form = document.getElementById('checkoutForm');
        var formData = new FormData(form);
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        var response = await fetch('/checkout/process', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '' },
            credentials: 'same-origin',
            body: formData
        });
        var data = await response.json();
        if (response.ok && data.success) {
            window.location.href = data.redirect || '{{ route("home") }}';
        } else if (data.errors) {
            showServerErrors(data.errors);
        } else {
            showCheckoutNotification(data.message || 'Checkout failed. Please try again.', 'error');
        }
    } catch (error) {
        console.error('Error processing checkout:', error);
        showCheckoutNotification('An error occurred. Please try again.', 'error');
    }
}

function processCheckout(event) {
    runCheckout();
}

function showCheckoutNotification(message, type) {
    var notif = document.getElementById('checkout-notif');
    if (notif) notif.remove();
    notif = document.createElement('div');
    notif.id = 'checkout-notif';
    notif.className = 'fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white flex items-center space-x-2';
    notif.style.cssText = 'z-index:99999;position:fixed;bottom:20px;right:20px;padding:12px 24px;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,0.3);color:#fff;font-weight:500;';
    notif.style.backgroundColor = type === 'success' ? '#22c55e' : '#ef4444';
    notif.innerHTML = '<span>' + message + '</span>';
    document.body.appendChild(notif);
    setTimeout(function(){ notif.remove(); }, 4000);
}
</script>
@endsection
