// Product data
const products = [
    { id: 1, name: 'Minyakita', price: 36000, category: 'Minyak', image: 'asset/images/minyak.png',
    description: 'Minyakita adalah minyak goreng kemasan bersubsidi yang jernih, sehat, terjangkau, dan cocok digunakan untuk menggoreng maupun menumis kebutuhan masakan sehari-hari.' },

    { id: 2, name: 'Beras', price: 15000, category: 'Beras', image: 'asset/images/Beras.png',
    description: 'Beras 5 kg adalah bahan pangan pokok yang bergizi, pulen, wangi, dan mudah dimasak untuk memenuhi kebutuhan konsumsi rumah tangga sehari-hari.' },

    { id: 3, name: 'Gas LPG 3kg', price: 20000, category: 'Gas', image: 'asset/images/Gas.png',
    description: 'Gas LPG 3 kg adalah bahan bakar memasak bersubsidi yang ringan, praktis, hemat, dan mudah digunakan untuk kebutuhan rumah tangga sehari-hari.' },

    { id: 4, name: 'Indomie', price: 3500, category: 'Mie', image: 'asset/images/Indomie.png',
    description: 'Indomie adalah mie instan favorit masyarakat Indonesia dengan berbagai varian rasa yang lezat dan mudah disiapkan untuk sajian cepat dan praktis.' },

    { id: 5, name: 'Mie Sedaap', price: 3000, category: 'Mie', image: 'asset/images/miesedaap.png',
    description: 'Mie Sedaap menawarkan rasa gurih dan kenyal yang nikmat, cocok untuk disantap kapan saja sebagai pilihan mie instan berkualitas.' },

    { id: 6, name: 'Supermie', price: 3000, category: 'Mie', image: 'asset/images/supermi.png',
    description: 'Supermie adalah mie instan klasik dengan cita rasa khas dan tekstur lembut, ideal untuk menu harian yang praktis dan mengenyangkan.' },

    { id: 7, name: 'Pop Mie', price: 6000, category: 'Mie', image: 'asset/images/popmie.png',
    description: 'Pop Mie adalah mie instan dalam cup yang praktis, cukup diseduh air panas dan siap disantap kapan saja, cocok untuk aktivitas sibuk.' },

    { id: 8, name: 'Sunlight', price: 5000, category: 'Sabun', image: 'asset/images/Sunlight.png',
    description: 'Sunlight adalah sabun pencuci piring dengan formula aktif penghilang lemak, membuat peralatan makan bersih, higienis, dan harum.' },

    { id: 9, name: 'Sasa', price: 2000, category: 'Bumbu', image: 'asset/images/sasa.png',
    description: 'Sasa adalah penyedap rasa MSG yang memberikan cita rasa gurih dan lezat pada berbagai masakan rumah tangga.' },
];

// Function to redirect to search page
function redirectToSearchPage() {
    const query = document.getElementById('search-input').textContent.trim();
    window.location.href = `search.php?query=${encodeURIComponent(query)}`;
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const query = urlParams.get('query');
    
    const searchInput = document.getElementById('search-input');
    if (query) {
        searchInput.textContent = query; // Isi search bar dengan query dari URL
        searchProducts(); // Jalankan pencarian
    }

    // Fokuskan ke search bar
    const searchBar = document.getElementById('search-bar');
    searchBar.scrollIntoView(); // Pastikan terlihat di layar
});

// Function to save product data to localStorage when a product is clicked
function viewProduct(productId) {
    const product = products.find(p => p.id === productId);
    
    if (product) {
        localStorage.setItem('currentProduct', JSON.stringify(product));
        window.location.href = 'detail.php';
    }
}

// Function to load products
function loadProducts(category = 'all') {
    const productGrid = document.getElementById('product-grid');
    productGrid.innerHTML = '';
    
    const filteredProducts = category === 'all' 
        ? products 
        : products.filter(product => product.category === category);
    
    filteredProducts.forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'product-card';
        productElement.innerHTML = `
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}">
            </div>
            <div class="product-name">${product.name}</div>
            <div class="product-price">Rp${product.price.toLocaleString()}</div>
            <button class="add-button" onclick="viewProduct(${product.id})">+</button>
        `;
        productGrid.appendChild(productElement);
    });
}

// Function to search products
function searchProducts() {
    const query = document.getElementById('search-input').value.toLowerCase().trim();
    
    if (query === '') {
        loadProducts();
        return;
    }
    
    const filteredProducts = products.filter(product => 
        product.name.toLowerCase().includes(query) || 
        product.category.toLowerCase().includes(query)
    );
    
    const productGrid = document.getElementById('product-grid');
    productGrid.innerHTML = '';
    
    filteredProducts.forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'product-card';
        productElement.innerHTML = `
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}">
            </div>
            <div class="product-name">${product.name}</div>
            <div class="product-price">Rp${product.price.toLocaleString()}</div>
            <button class="add-button" onclick="viewProduct(${product.id})">+</button>
        `;
        productGrid.appendChild(productElement);
    });
}

function filterByCategory(category, element) {
    // Filter produk berdasarkan kategori
    loadProducts(category);

    // Hapus kelas 'active' dari semua tombol kategori
    const categoryItems = document.querySelectorAll('.category-item');
    categoryItems.forEach(item => item.classList.remove('active'));

    // Tambahkan kelas 'active' ke tombol yang diklik
    element.classList.add('active');
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
});