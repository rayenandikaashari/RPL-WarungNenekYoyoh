<?php
session_start(); 
include 'koneksi.php';

$sessionId = session_id();
$totalPrice = 0;

// Fungsi helper untuk format Rupiah
function formatRupiah($number) {
    return 'Rp' . number_format($number, 0, ',', '.');
}

// Ambil total harga dari keranjang
$sql = "SELECT SUM(p.harga * k.jumlah) AS total 
        FROM keranjang k
        JOIN produk p ON k.produk_id = p.id
        WHERE k.session_id = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $sessionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalPrice = $row['total'] ? (float)$row['total'] : 0;
    $stmt->close();
} else {
    error_log("Gagal mengambil total payment: " . $conn->error);
    die("Terjadi kesalahan.");
}

if ($totalPrice <= 0) {
    header("Location: cart.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Warung Sembako Nenek Yoyoh</title>
    <link rel="stylesheet" href="asset/css/style.css"> 
    <style>
        .payment-section { padding: 0 15px; margin-bottom: 25px; }
        .payment-section h2 { font-size: 18px; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;}
        .qr-info { text-align: center; margin-bottom: 15px; }
        hr { margin: 30px 15px; border: 0; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="page active" id="payment-page">
            <div class="product-detail-header">
                <a href="cart.php" class="back-button">←</a>
            </div>
            <br>
            <h1 class="payment-title">Pilih Metode Pembayaran</h1>
            <br>

            <div class="cart-total" style="padding: 0 15px; margin-bottom: 20px;">
                <span>Total Bayar:</span>
                <span class="total-price"><?php echo formatRupiah($totalPrice); ?></span>
            </div>

            <div class="payment-section">
                <h2>Bayar Tunai (Cash)</h2>
                <p>Bayar langsung saat barang diterima/diambil.</p>
                <form action="proses_pembelian.php" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
                    <input type="hidden" name="payment_method" value="cash">
                    <button type="submit" class="cash-button">💵 Konfirmasi Pesanan (Cash)</button>
                </form>
            </div>

            <hr> <div class="payment-section">
                <h2>Bayar dengan QRIS</h2>
                <div class="qr-info">
                    <p>Scan kode di bawah ini:</p>
                    <div class="qr-container" style="min-height: 220px; margin-top: 10px; margin-bottom: 10px;">
                        <img src="asset/images/qris_placeholder.png" alt="QRIS Code" class="qr-image">
                    </div>
                </div>
                <form action="proses_pembelian.php" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
                    <input type="hidden" name="payment_method" value="qris">
                    <button type="submit" class="checkout-button">✅ Saya Sudah Bayar (QRIS)</button> 
                </form>
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
                <div class="nav-item">
                    <a href="cart.php">
                        <div class="nav-icon">🛒</div>
                        <div>Cart</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>