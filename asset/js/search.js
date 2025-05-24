// asset/js/search.js

let products = []; // Akan diisi dengan hasil pencarian

// Function to save product data to localStorage when a product is clicked
function viewProduct(productId) {
    const product = products.find(p => p.id === productId);
    if (product) {
        localStorage.setItem('currentProduct', JSON.stringify(product));
        window.location.href = 'detail.php';
    }
}

// Function to display products
function displayProducts(productList) {
    const productGrid = document.getElementById('product-grid');
    productGrid.innerHTML = '';
    if (productList.length === 0) {
        productGrid.innerHTML = '<p>Tidak ada produk yang ditemukan.</p>';
        return;
    }
    productList.forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'product-card';
        productElement.innerHTML = `
            <div class="product-image">
                <img src="${product.gambar_produk}" alt="${product.nama}">
            </div>
            <div class="product-name">${product.nama}</div>
            <div class="product-price">Rp${new Intl.NumberFormat('id-ID').format(product.harga)}</div>
            <p class="description">${product.deskripsi ? product.deskripsi.substring(0, 50) + '...' : ''}</p>
            <button class="add-button" onclick="viewProduct(${product.id})">+</button>
        `;
        productGrid.appendChild(productElement);
    });
}

// Function to search products
function searchProducts() {
    const query = document.getElementById('search-input').value.toLowerCase().trim();

    fetch(window.location.href + `?query=${encodeURIComponent(query)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest' // Mengidentifikasi sebagai AJAX request
        }
    })
    .then(response => response.json())
    .then(data => {
        products = data;
        displayProducts(products);
    })
    .catch(error => {
        console.error('Error fetching search results:', error);
        document.getElementById('product-grid').innerHTML = '<p>Gagal melakukan pencarian.</p>';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    products = initialSearchResults; // Gunakan hasil pencarian awal dari PHP
    displayProducts(products);
    document.getElementById('search-input').focus();

    // Jika ada query di URL saat pertama kali dimuat, lakukan pencarian
    const urlParams = new URLSearchParams(window.location.search);
    const queryFromUrl = urlParams.get('query');
    if (queryFromUrl && initialSearchResults.length === 0) {
        document.getElementById('search-input').value = queryFromUrl;
        searchProducts();
    }
});