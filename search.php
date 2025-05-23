<?php
include 'koneksi.php';

$searchResults = [];
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $query = mysqli_real_escape_string($conn, $query);

    $sql = "SELECT p.id, p.nama, p.harga, p.deskripsi, p.gambar_produk, k.nama_kategori AS category
            FROM produk p
            JOIN kategori k ON p.kategori_id = k.id
            WHERE p.nama LIKE '%$query%' OR k.nama_kategori LIKE '%$query%'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
    }
}

// Kita akan mengirimkan hasil pencarian sebagai JSON jika ini adalah permintaan AJAX
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($searchResults);
    $conn->close();
    exit();
}

// Jika bukan AJAX request, kita akan menampilkan halaman HTML
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian - Warung Sembako Nenek Yoyoh</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>
    <div class="container">
        <div class="page active">
            <h1>Pencarian</h1>
            <div class="search-bar">
                <span>🔍</span>
                <input type="text" placeholder="Mencari barang" id="search-input" oninput="searchProducts()">
            </div>

            <div class="product-grid" id="product-grid">
                </div>

            <div class="nav-bar">
                <div class="nav-item">
                    <a href="katalog.php" style="text-decoration: none; color: inherit;">
                        <div class="nav-icon">🏠</div>
                        <div>Home</div>
                    </a>
                </div>
                <div class="nav-item active">
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
        const initialSearchResults = <?php echo json_encode($searchResults); ?>;
    </script>
    <script src="asset/js/search.js"></script>
</body>
</html>
<?php
$conn->close();
?>