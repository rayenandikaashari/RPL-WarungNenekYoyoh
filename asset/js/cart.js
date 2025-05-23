// Function to load cart items from localStorage
function loadCartItems() {
    const cartJson = localStorage.getItem('cart');
    
    if (cartJson) {
        const cart = JSON.parse(cartJson);
        const cartItemsContainer = document.getElementById('cart-items');
        cartItemsContainer.innerHTML = '';
        
        // Update cart items count
        document.querySelector('h1').textContent = `${cart.length} Items In Cart`;
        
        // If cart is empty
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p>Keranjang Anda kosong. <a href="katalog.php">Kembali belanja</a></p>';
            document.querySelector('.cart-total').style.display = 'none';
            document.querySelector('.checkout-button').style.display = 'none';
            return;
        }
        
        // Display cart items
        let totalPrice = 0;
        
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            totalPrice += itemTotal;
            
            const cartItemElement = document.createElement('div');
            cartItemElement.className = 'cart-item';
            cartItemElement.innerHTML = `
                <div class="cart-item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">Rp${item.price.toLocaleString()}</div>
                    <div class="quantity-control">
                        <button class="quantity-button" onclick="decreaseQuantity(${index})">-</button>
                        <span class="quantity">${item.quantity}</span>
                        <button class="quantity-button" onclick="increaseQuantity(${index})">+</button>
                    </div>
                </div>
                <button class="remove-button" onclick="removeItem(${index})">×</button>
            `;
            cartItemsContainer.appendChild(cartItemElement);
        });

        // Update total price
        document.querySelector('.total-price').textContent = `Rp. ${totalPrice.toLocaleString()}`;
    } else {
        // Empty cart message
        document.querySelector('h1').textContent = '0 Items In Cart';
        document.getElementById('cart-items').innerHTML = '<p>Keranjang Anda kosong. <a href="katalog.php">Kembali belanja</a></p>';
        document.querySelector('.cart-total').style.display = 'none';
        document.querySelector('.checkout-button').style.display = 'none';
    }
}

// Function to increase item quantity
function increaseQuantity(index) {
    const cartJson = localStorage.getItem('cart');
    
    if (cartJson) {
        const cart = JSON.parse(cartJson);
        cart[index].quantity += 1;
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCartItems();
    }
}

// Function to decrease item quantity
function decreaseQuantity(index) {
    const cartJson = localStorage.getItem('cart');
    
    if (cartJson) {
        const cart = JSON.parse(cartJson);
        
        if (cart[index].quantity > 1) {
            cart[index].quantity -= 1;
        } else {
            removeItem(index);
            return;
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCartItems();
    }
}

// Function to remove item from cart
function removeItem(index) {
    const cartJson = localStorage.getItem('cart');
    
    if (cartJson) {
        const cart = JSON.parse(cartJson);
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCartItems();
    }
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();
});