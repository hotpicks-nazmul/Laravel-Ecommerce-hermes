@extends('themes.general.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>
    
    <div id="checkoutContent">
        <!-- Content will be loaded here -->
    </div>
</div>

<script>
let checkoutItems = [];
let checkoutSubtotal = 0;
let checkoutDelivery = 0;
let checkoutTotal = 0;

async function loadCheckoutData() {
    try {
        const response = await fetch('/api/cart/items', {
            credentials: 'same-origin'
        });
        const data = await response.json();
        
        checkoutItems = data.items || [];
        checkoutSubtotal = parseFloat(data.subtotal) || 0;
        checkoutDelivery = parseFloat(data.delivery) || 0;
        checkoutTotal = parseFloat(data.total) || 0;
        
        if (checkoutItems.length === 0) {
            window.location.href = '{{ route("cart.index") }}';
            return;
        }
        
        renderCheckoutPage();
    } catch (error) {
        console.error('Error loading checkout:', error);
    }
}

function renderCheckoutPage() {
    const checkoutContent = document.getElementById('checkoutContent');
    
    let itemsHtml = checkoutItems.map(item => {
        const imageUrl = item.image || 'https://via.placeholder.com/80';
        return `
            <div class="flex items-center space-x-4 bg-white p-4 rounded-lg shadow-sm mb-4">
                <img src="${imageUrl}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-800">${item.name}</h4>
                    <p class="text-halal-green font-bold">৳${parseFloat(item.price).toLocaleString()}</p>
                    <div class="flex items-center space-x-2 mt-2">
                        <button onclick="updateCheckoutItem(${item.product_id}, ${item.quantity - 1})" class="w-7 h-7 bg-gray-100 rounded flex items-center justify-center hover:bg-gray-200">
                            <i class="bi bi-dash text-sm"></i>
                        </button>
                        <span class="font-medium">${item.quantity}</span>
                        <button onclick="updateCheckoutItem(${item.product_id}, ${item.quantity + 1})" class="w-7 h-7 bg-gray-100 rounded flex items-center justify-center hover:bg-gray-200">
                            <i class="bi bi-plus text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-800">৳${(item.price * item.quantity).toLocaleString()}</p>
                    <button onclick="removeCheckoutItem(${item.product_id})" class="text-red-500 hover:text-red-700 text-sm mt-1">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    checkoutContent.innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <!-- Order Items -->
                <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                    <h3 class="text-lg font-bold mb-4">Order Items</h3>
                    ${itemsHtml}
                </div>
                
                <!-- Billing Details -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold mb-4">Billing Details</h3>
                    <form id="checkoutForm" onsubmit="processCheckout(event)">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                <input type="text" name="billing_first_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                <input type="text" name="billing_last_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="billing_email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                <input type="tel" name="billing_phone" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                                <input type="text" name="billing_address" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                <input type="text" name="billing_city" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                                <input type="text" name="billing_state" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postcode *</label>
                                <input type="text" name="billing_postcode" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                                <select name="billing_country" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green">
                                    <option value="Bangladesh">Bangladesh</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Order Notes</label>
                                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-halal-green" placeholder="Notes about your order, e.g. special notes for delivery"></textarea>
                            </div>
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="mt-6">
                            <h4 class="font-medium mb-3">Payment Method</h4>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="cod" checked class="mr-3">
                                    <span>Cash on Delivery</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Terms -->
                        <div class="mt-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="terms" required class="mr-2">
                                <span class="text-sm text-gray-600">I agree to the <a href="{{ route('pages.terms') }}" class="text-halal-green hover:underline">Terms and Conditions</a></span>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="bg-white p-6 rounded-lg shadow-sm h-fit sticky top-24">
                <h3 class="text-lg font-bold mb-4">Order Summary</h3>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span id="checkoutSubtotal">৳${checkoutSubtotal.toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Delivery</span>
                        <span id="checkoutDelivery" class="${checkoutDelivery === 0 ? 'text-halal-green' : ''}">${checkoutDelivery === 0 ? 'Free' : '৳' + checkoutDelivery}</span>
                    </div>
                    <hr>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span id="checkoutTotal" class="text-halal-green">৳${checkoutTotal.toLocaleString()}</span>
                    </div>
                </div>
                ${checkoutDelivery === 0 ? '<p class="text-halal-green text-sm mb-4"><i class="bi bi-truck mr-1"></i>You have free delivery!</p>' : ''}
                <button type="submit" form="checkoutForm" class="w-full bg-halal-green text-white py-3 rounded-lg font-medium hover:bg-halal-dark transition-colors">
                    Place Order
                </button>
                <a href="{{ route('cart.index') }}" class="block w-full text-center py-3 text-halal-green hover:underline mt-2">
                    <i class="bi bi-arrow-left mr-1"></i> Back to Cart
                </a>
            </div>
        </div>
    `;
}

async function updateCheckoutItem(productId, quantity) {
    if (quantity < 1) {
        removeCheckoutItem(productId);
        return;
    }
    
    try {
        const response = await fetch('/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin',
            body: JSON.stringify({ product_id: productId, quantity: quantity })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload checkout data
            await loadCheckoutData();
            
            // Also update the sidebar if available
            if (typeof loadCart === 'function') {
                loadCart();
            }
            
            showCheckoutNotification('Cart updated!', 'success');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
    }
}

async function removeCheckoutItem(productId) {
    try {
        const response = await fetch('/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin',
            body: JSON.stringify({ product_id: productId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload checkout data
            await loadCheckoutData();
            
            // Also update the sidebar if available
            if (typeof loadCart === 'function') {
                loadCart();
            }
            
            showCheckoutNotification('Item removed', 'success');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
    }
}

async function processCheckout(event) {
    event.preventDefault();
    
    const form = document.getElementById('checkoutForm');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('/checkout/process', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = data.redirect || '{{ route("home") }}';
        } else {
            showCheckoutNotification(data.message || 'Checkout failed', 'error');
        }
    } catch (error) {
        console.error('Error processing checkout:', error);
        showCheckoutNotification('An error occurred. Please try again.', 'error');
    }
}

function showCheckoutNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white flex items-center space-x-2`;
    notification.innerHTML = `
        <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', loadCheckoutData);
</script>
@endsection
