<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Warung Sembako Nenek Yoyoh</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Product Detail Page -->
        <div class="page product-detail active">
            <div class="product-detail-header">
                <a href="katalog.php" class="back-button">←</a>
            </div>
            
            <div class="product-detail-image" id="product-detail-image">
                <img src="/api/placeholder/200/200" alt="Product" id="detail-image">
            </div>
            
            <div class="product-detail-info">
                <h1 class="product-detail-name" id="product-detail-name"></h1>
                <div class="product-detail-price" id="product-detail-price"></div>
                <p class="product-detail-description" id="product-detail-description"></p>
                

                
                <button class="add-to-cart-button" onclick="addToCart()">Add to Cart</button>
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
    <script src="asset/js/detail.js"></script>
</body>
</html>