// asset/js/katalog.js

let products = [];
let categories = [];

function redirectToSearchPage() {
    const query = document.getElementById('search-input').value.trim();
    window.location.href = `search.php?query=${encodeURIComponent(query)}`;
}

function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message); // Tampilkan pesan sukses
            // Anda bisa memperbarui tampilan keranjang di sini jika mau
        } else {
            alert(data.message); // Tampilkan pesan error
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        alert('Terjadi kesalahan saat menambahkan ke keranjang.');
    });
}

function displayProducts(productList) {
    const productGrid = document.getElementById('product-grid');
    productGrid.innerHTML = '';
    productList.forEach(product => {
        const productCard = document.createElement('div');
        productCard.classList.add('product-card');
        productCard.innerHTML = `
            <div class="product-image">
                <img src="${product.gambar_produk}" alt="${product.nama}">
            </div>
            <div class="product-name">${product.nama}</div>
            <div class="product-price">Rp${new Intl.NumberFormat('id-ID').format(product.harga)}</div>
            <p class="description">${product.deskripsi ? product.deskripsi.substring(0, 50) + '...' : ''}</p>
            <button class="add-button" onclick="addToCart(${product.id})">+</button>
        `;
        productGrid.appendChild(productCard);
    });
}

function filterByCategory(category, element) {
    const categoryItems = document.querySelectorAll('.category-item');
    categoryItems.forEach(item => item.classList.remove('active'));
    element.classList.add('active');

    const filteredProducts = category === 'all'
        ? products
        : products.filter(product => product.kategori === category);
    displayProducts(filteredProducts);
}

function searchProducts() {
    const query = document.getElementById('search-input').value.toLowerCase().trim();
    const filteredProducts = products.filter(product =>
        product.nama.toLowerCase().includes(query) ||
        product.kategori.toLowerCase().includes(query)
    );
    displayProducts(filteredProducts);
}

document.addEventListener('DOMContentLoaded', function() {
    products = productsFromPHP;
    categories = categoriesFromPHP;
    displayProducts(products.filter(p => p.is_popular));
});