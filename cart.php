<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Warung Sembako Nenek Yoyoh</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Cart Page -->
        <div class="page active">
            <h1>2 Items In Cart</h1> <br>
            
            <div id="cart-items-container">
    <div id="cart-items">
        <!-- Cart items will be loaded here by JavaScript -->
        <div class="cart-item">
            <div class="cart-item-image">
                <img src="/api/placeholder/80/80" alt="Minyakita">
            </div>
            <div class="cart-item-info">
                <div class="cart-item-name">Minyakita</div>
                <div class="cart-item-price">Rp36.000</div>
                <div class="quantity-control">
                    <button class="quantity-button">-</button>
                    <span class="quantity">2</span>
                    <button class="quantity-button">+</button>
                </div>
            </div>
            <button class="remove-button">×</button>
        </div>
        <div class="cart-item">
            <div class="cart-item-image">
                <img src="/api/placeholder/80/80" alt="Beras">
            </div>
            <div class="cart-item-info">
                <div class="cart-item-name">Beras</div>
                <div class="cart-item-price">Rp15.000</div>
                <div class="quantity-control">
                    <button class="quantity-button">-</button>
                    <span class="quantity">1</span>
                    <button class="quantity-button">+</button>
                </div>
            </div>
            <button class="remove-button">×</button>
        </div>
    </div>
</div>

<!-- Footer Section -->
<div class="cart-footer">
    <div class="cart-total">
        <span>Total:</span>
        <span class="total-price">Rp. 51.000</span>
    </div>
    <a href="payment.php" class="checkout-button">Checkout</a>
    <a href="katalog.php" class="back-to-menu">Back to Menu</a>
            
            <div class="nav-bar">
                <div class="nav-item">
                    <a href="katalog.php" style="text-decoration: none; color: inherit;">
                        <div class="nav-icon">🏠</div>
                        <div>Home</div>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="search.php" style="text-decoration: none; color: inherit;">
                    <div class="nav-icon">🔍</div>
                    <div>Search</div>
                    </a>
                </div>
                <div class="nav-item active">
                    <div class="nav-icon">🛒</div>
                    <div>Cart</div>
                </div>
            </div>
        </div>
    </div>
    <script src="asset/js/cart.js"></script>
</body>
</html>