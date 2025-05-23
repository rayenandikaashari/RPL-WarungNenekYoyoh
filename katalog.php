<?php
// Sertakan file koneksi
include 'koneksi.php';

// Ambil data produk (termasuk nama kategori)
$sql = "SELECT p.id, p.nama, p.harga, p.deskripsi, p.gambar_produk, p.is_popular, k.nama_kategori AS kategori
        FROM produk p
        JOIN kategori k ON p.kategori_id = k.id";
$result = $conn->query($sql);

$products_from_db = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products_from_db[] = $row;
    }
}

// Ambil data kategori untuk filter
$sql_kategori = "SELECT nama_kategori FROM kategori";
$result_kategori = $conn->query($sql_kategori);
$categories_from_db = [];
if ($result_kategori->num_rows > 0) {
    while ($row_kategori = $result_kategori->fetch_assoc()) {
        $categories_from_db[] = $row_kategori['nama_kategori'];
    }
}

// Ubah data PHP menjadi format JSON untuk JavaScript
$products_json = json_encode($products_from_db);
$categories_json = json_encode($categories_from_db);

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - Warung Sembako Nenek Yoyoh</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>
    <div class="container">
        <div class="page active">
            <h1>Menu</h1>
            <div class="search-bar">
                <span>🔍</span>
                <input type="text" placeholder="Mencari barang" id="search-input" oninput="searchProducts()">
            </div>

            <div class="category-list" id="category-list">
                <div class="category-item active" onclick="filterByCategory('all', this)">
                    <div class="category-icon">All</div>
                    <div>All</div>
                </div>
                <?php
                $decoded_categories = json_decode($categories_json);
                if (!empty($decoded_categories)) {
                    foreach ($decoded_categories as $category) {
                        echo '<div class="category-item" onclick="filterByCategory(\'' . $category . '\', this)">';
                        $icon = '';
                        switch ($category) {
                            case 'Beras': $icon = '🌾'; break;
                            case 'Minyak': $icon = '🧴'; break;
                            case 'Bumbu': $icon = '🧂'; break;
                            case 'Mie': $icon = '🍜'; break;
                            case 'Gas': $icon = '🔥'; break;
                            case 'Sabun': $icon = '🧼'; break;
                        }
                        echo '<div class="category-icon">' . $icon . '</div>';
                        echo '<div>' . $category . '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <h2>Popular</h2>
            <div class="product-grid" id="product-grid">
                </div>

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
        const categoriesFromPHP = <?php echo $categories_json; ?>;
    </script>
    <script src="asset/js/katalog.js"></script>
</body>
</html>