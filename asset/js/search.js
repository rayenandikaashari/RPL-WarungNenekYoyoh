let products = productsFromPHP;

function searchProducts() {
    const query = document.getElementById('search-input').value.toLowerCase().trim();
    const filtered = products.filter(p =>
        p.nama.toLowerCase().includes(query) ||
        p.kategori.toLowerCase().includes(query)
    );
    displayProducts(filtered);
}

function displayProducts(productList) {
    const container = document.getElementById('product-grid');
    container.innerHTML = '';
    productList.forEach(product => {
        const card = document.createElement('div');
        card.classList.add('product-card');
        card.innerHTML = `
            <div class="product-image">
                <img src="${product.gambar_produk}" alt="${product.nama}">
            </div>
            <div class="product-name">${product.nama}</div>
            <div class="product-price">Rp${new Intl.NumberFormat('id-ID').format(product.harga)}</div>
            <button class="add-button" onclick="location.href='detail.php?id=${product.id}'">+</button>
        `;
        container.appendChild(card);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    displayProducts(products);
});
