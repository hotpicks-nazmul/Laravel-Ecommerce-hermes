@extends('themes.general.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>
    
    <div id="cartPageContent">
        <!-- Cart items will be loaded here -->
    </div>
</div>

<script>
// Cart page specific functions
let cartPageItems = [];
const checkoutUrl = "{{ route('checkout.index') }}";
const productsUrl = "{{ route('products.index') }}";

async function loadCartPageData() {
    try {
        const response = await fetch('/api/cart/items', {
            credentials: 'same-origin'
        });
        const data = await response.json();
        
        cartPageItems = data.items || [];
        renderCartPage();
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

function renderCartPage() {
    const cartPageContent = document.getElementById('cartPageContent');
    
    if (cartPageItems.length === 0) {
        cartPageContent.innerHTML = `
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-cart-x text-4xl text-gray-400"></i>
                </div>
                <h4 class="text-gray-600 font-medium mb-2">Your cart is empty</h4>
                <p class="text-gray-400 text-sm mb-4">Add some products to get started!</p>
                <a href="${productsUrl}" class="inline-block bg-halal-green text-white px-6 py-2 rounded-full hover:bg-halal-dark transition-colors">
                    Start Shopping
                </a>
            </div>
        `;
        return;
    }
    
    let subtotal = 0;
    let itemsHtml = cartPageItems.map(item => {
        subtotal += item.price * item.quantity;
        const imageUrl = item.image || 'https://placehold.co/80';
        return `
            <div class="flex items-center space-x-4 bg-white p-4 rounded-lg shadow-sm mb-4" id="cart-item-${item.product_id}">
                <img src="${imageUrl}" alt="${item.name}" class="w-20 h-20 object-cover rounded-lg">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-800">${item.name}</h4>
                    <p class="text-halal-green font-bold">৳${parseFloat(item.price).toLocaleString()}</p>
                    <div class="flex items-center space-x-2 mt-2">
                        <button onclick="updateCartPageItem(${item.product_id}, ${item.quantity - 1})" class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center hover:bg-gray-200">
                            <i class="bi bi-dash"></i>
                        </button>
                        <span class="font-medium" id="qty-${item.product_id}">${item.quantity}</span>
                        <button onclick="updateCartPageItem(${item.product_id}, ${item.quantity + 1})" class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center hover:bg-gray-200">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-800">৳${(item.price * item.quantity).toLocaleString()}</p>
                    <button onclick="removeCartPageItem(${item.product_id})" class="text-red-500 hover:text-red-700 mt-2">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    const delivery = subtotal >= 500 ? 0 : 60;
    const total = subtotal + delivery;
    
    cartPageContent.innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                ${itemsHtml}
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm h-fit">
                <h3 class="text-lg font-bold mb-4">Order Summary</h3>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span id="pageSubtotal">৳${subtotal.toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Delivery</span>
                        <span id="pageDelivery" class="${delivery === 0 ? 'text-halal-green' : ''}">${delivery === 0 ? 'Free' : '৳' + delivery}</span>
                    </div>
                    <hr>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span id="pageTotal" class="text-halal-green">৳${total.toLocaleString()}</span>
                    </div>
                </div>
                ${delivery === 0 ? '<p class="text-halal-green text-sm mb-4"><i class="bi bi-truck mr-1"></i>You\'ve got free delivery!</p>' : ''}
                <a href="${checkoutUrl}" class="block w-full bg-halal-green text-white text-center py-3 rounded-lg font-medium hover:bg-halal-dark transition-colors">
                    Proceed to Checkout
                </a>
                <a href="${productsUrl}" class="block w-full text-center py-3 text-halal-green hover:underline mt-2">
                    Continue Shopping
                </a>
            </div>
        </div>
    `;
}

async function updateCartPageItem(productId, quantity) {
    if (quantity < 1) {
        removeCartPageItem(productId);
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
            // Update local items array
            const itemIndex = cartPageItems.findIndex(item => item.product_id == productId);
            if (itemIndex !== -1) {
                cartPageItems[itemIndex].quantity = quantity;
            }
            
            // Re-render the page
            renderCartPage();
            
            // Also update the sidebar if available
            if (typeof loadCart === 'function') {
                loadCart();
            }
            
            showCartPageNotification('Cart updated!', 'success');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
    }
}

async function removeCartPageItem(productId) {
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
            // Remove from local items array
            cartPageItems = cartPageItems.filter(item => item.product_id != productId);
            
            // Re-render the page
            renderCartPage();
            
            // Also update the sidebar if available
            if (typeof loadCart === 'function') {
                loadCart();
            }
            
            showCartPageNotification('Item removed from cart', 'success');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
    }
}

function showCartPageNotification(message, type) {
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

document.addEventListener('DOMContentLoaded', loadCartPageData);
</script>
@endsection
