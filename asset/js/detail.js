// Function to load product details from localStorage
function loadProductDetails() {
    const productJson = localStorage.getItem('currentProduct');
    
    if (productJson) {
        const product = JSON.parse(productJson);
        
        document.getElementById('product-detail-name').textContent = product.name;
        document.getElementById('product-detail-price').textContent = `Rp${product.price.toLocaleString()}`;
        document.getElementById('product-detail-description').textContent = product.description;
        
        // Set product image if available
        if (product.image) {
            document.getElementById('detail-image').src = product.image;
            document.getElementById('detail-image').alt = product.name;
        }
    } else {
        // Redirect back to catalog if no product is selected
        window.location.href = 'katalog.php';
    }
}

// Function to add current product to cart
function addToCart() {
    const productJson = localStorage.getItem('currentProduct');
    
    if (productJson) {
        const product = JSON.parse(productJson);
        
        // Get existing cart or initialize empty array
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        // Check if product already exists in cart
        const existingItemIndex = cart.findIndex(item => item.id === product.id);
        
        if (existingItemIndex !== -1) {
            // Increment quantity if product already in cart
            cart[existingItemIndex].quantity += 1;
        } else {
            // Add new product to cart with quantity 1
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                quantity: 1,
                image: product.image
            });
        }
        
        // Save updated cart back to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        alert(`${product.name} ditambahkan ke keranjang!`);
        window.location.href = 'katalog.php';
    }
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadProductDetails();
});