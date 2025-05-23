<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Warung Sembako Nenek Yoyoh</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Payment Page -->
        <div class="page active" id="payment-page">
            <div class="product-detail-header">
                <a href="cart.php" class="back-button">←</a>
            </div>
            <br>
            <h1 class="payment-title">Pilih Metode Pembayaran</h1>
            <br>
            <!-- Tombol Bayar Menggunakan Cash -->
            <div class="payment-options">
                <button class="cash-button" onclick="payWithCash()">💵 Bayar Menggunakan Cash</button>
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

    <script src="asset/js/payment.js"></script>
</body>
</html>