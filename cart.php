<?php
session_start(); 
include 'koneksi.php';

$sessionId = session_id();
$cartItems = [];
$totalPrice = 0;

// Fungsi format Rupiah
function formatRupiah($number) {
    return 'Rp' . number_format($number, 0, ',', '.');
}

$sql = "SELECT k.id AS keranjang_id, k.jumlah, 
               p.id AS produk_id, p.nama, p.harga, p.gambar_produk
        FROM keranjang k
        JOIN produk p ON k.produk_id = p.id
        WHERE k.session_id = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $sessionId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $row['subtotal'] = $row['harga'] * $row['jumlah'];
        $cartItems[] = $row;
        $totalPrice += $row['subtotal'];
    }
    $stmt->close();
} else {
    error_log("Gagal menyiapkan query cart.php: " . $conn->error);
    echo "Terjadi kesalahan saat memuat keranjang."; 
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Warung Sembako Nenek Yoyoh</title>
    <link rel="stylesheet" href="asset/css/style.css"> 
</head>
<body>
    <div class="container">
        <div class="page active">
            <h1><?php echo count($cartItems); ?> Items In Cart</h1> <br>
            
            <div id="cart-items-container">
                <div id="cart-items">
                    <?php if (!empty($cartItems)): ?>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item" id="item-<?php echo $item['keranjang_id']; ?>">
                                <div class="cart-item-image">
                                    <img src="<?php echo htmlspecialchars($item['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>">
                                </div>
                                <div class="cart-item-info">
                                    <div class="cart-item-name"><?php echo htmlspecialchars($item['nama']); ?></div>
                                    <div class="cart-item-price"><?php echo formatRupiah($item['harga']); ?></div>
                                    <div class="quantity-control">
                                        <button class="quantity-button decrease-qty" data-id="<?php echo $item['keranjang_id']; ?>">-</button>
                                        <input type="number" 
                                               class="quantity-input" 
                                               id="qty-<?php echo $item['keranjang_id']; ?>" 
                                               value="<?php echo $item['jumlah']; ?>" 
                                               min="1" 
                                               data-id="<?php echo $item['keranjang_id']; ?>">
                                        <button class="quantity-button increase-qty" data-id="<?php echo $item['keranjang_id']; ?>">+</button>
                                    </div>
                                    </div>
                                <button class="remove-button remove-item" data-id="<?php echo $item['keranjang_id']; ?>">×</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                         <div class="empty-cart" id="empty-cart-message">
                            <p>Keranjang belanja Anda masih kosong.</p>
                            <a href="katalog.php">Mulai Belanja</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div> </div> <div class="cart-footer" id="cart-footer-id"> 
            <div class="cart-total" id="cart-total-div" style="<?php echo empty($cartItems) ? 'display: none;' : 'display: flex;'; ?>">
                <span>Total:</span>
                <span class="total-price" id="cart-total-price"><?php echo formatRupiah($totalPrice); ?></span>
            </div>
            <a href="payment.php" class="checkout-button" id="checkout-button-id" style="<?php echo empty($cartItems) ? 'display: none;' : 'display: block;'; ?>">Checkout</a>
            <a href="katalog.php" class="back-to-menu">Back to Menu</a>
        </div>

        <div class="nav-bar">
            <div class="nav-item">
                <a href="katalog.php">
                    <div class="nav-icon">🏠</div>
                    <div>Home</div>
                </a>
            </div>
            <div class="nav-item">
                <a href="search.php">
                    <div class="nav-icon">🔍</div>
                    <div>Search</div>
                </a>
            </div>
            <div class="nav-item active">
                <a href="cart.php">
                    <div class="nav-icon">🛒</div>
                    <div>Cart</div>
                </a>
            </div>
        </div>

    </div> <script src="asset/js/cart.js"></script> 
</body>
</html>