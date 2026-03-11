@php
use App\Models\Currency;
use App\Models\Setting;

// Check if frontend currency switcher is enabled
$frontendCurrencySwitcher = Setting::get('frontend_currency_switcher', 0);

// Get active currencies
$currencies = Currency::active()->get();
$defaultCurrency = Currency::getDefault();

// Get current currency from session or use default
$currentCurrencyCode = session('currency_code', $defaultCurrency?->code ?? 'USD');
$currentCurrency = Currency::where('code', $currentCurrencyCode)->first() ?? $defaultCurrency;
@endphp

@if($frontendCurrencySwitcher && $currencies->count() > 1)
<div class="relative">
    <button class="flex items-center space-x-1 text-sm hover:text-halal-gold transition-colors" type="button" id="currencyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-currency-exchange"></i>
        <span>{{ $currentCurrency?->code ?? 'USD' }}</span>
        <i class="bi bi-chevron-down text-xs"></i>
    </button>
    
    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-sm" aria-labelledby="currencyDropdown">
        @foreach($currencies as $currency)
            <li>
                <a class="dropdown-item {{ $currency->code === $currentCurrencyCode ? 'active bg-primary text-white' : '' }}" 
                   href="{{ route('currency.switch', $currency->code) }}">
                    <span class="me-2">{{ $currency->symbol }}</span>
                    {{ $currency->name }} ({{ $currency->code }})
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endif
