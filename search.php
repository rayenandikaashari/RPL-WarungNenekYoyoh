<?php
include 'koneksi.php';

$sql = "SELECT p.id, p.nama, p.harga, p.gambar_produk, k.nama_kategori AS kategori
        FROM produk p
        JOIN kategori k ON p.kategori_id = k.id";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$products_json = json_encode($products);
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian - Warung Nenek Yoyoh</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>
    <div class="container">
        <div class="page active">
            <h1>Pencarian</h1>
            <div class="search-bar" onclick="focusSearch()">
                <span onclick="event.stopPropagation(); window.location.href='search.php'">🔍</span>
                <input type="text" placeholder="Mencari barang" id="search-input" autofocus oninput="searchProducts()">
            </div>

            <div class="product-grid" id="product-grid"></div>

            <div class="nav-bar">
                <div class="nav-item">
                    <a href="katalog.php" style="text-decoration: none; color: inherit;">
                        <div class="nav-icon">🏠</div>
                        <div>Home</div>
                    </a>
                </div>
                <div class="nav-item">
                    <div class="nav-icon">🔍</div>
                    <div>Search</div>
                </div>
                <div class="nav-item">
                    <a href="cart.php" style="text-decoration: none; color: inherit;">
                        <div class="nav-icon">🛒</div>
                        <div>Cart</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const productsFromPHP = <?php echo $products_json; ?>;

        function focusSearch() {
            document.getElementById('search-input').focus();
        }

        function searchProducts() {
            const query = document.getElementById('search-input').value.toLowerCase().trim();
            const filtered = productsFromPHP.filter(p =>
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
            displayProducts(productsFromPHP);
        });
    </script>
</body>
</html>
