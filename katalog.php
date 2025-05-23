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
        <!-- Menu Page -->
        <div class="page active">
            <h1>Menu</h1>
            <div class="search-bar" id="search-bar" onclick="redirectToSearchPage()">
            <span>🔍</span>
            <input type="text" placeholder="Mencari barang" id="search-input">
        </div>
            
            <div class="category-list">
    <div class="category-item active" onclick="filterByCategory('all', this)">
        <div class="category-icon">All</div>
        <div>All</div>
    </div>
    <div class="category-item" onclick="filterByCategory('Beras', this)">
        <div class="category-icon">🌾</div>
        <div>Beras</div>
    </div>
    <div class="category-item" onclick="filterByCategory('Minyak', this)">
        <div class="category-icon">🧴</div>
        <div>Minyak</div>
    </div>
    <div class="category-item" onclick="filterByCategory('Bumbu', this)">
        <div class="category-icon">🧂</div>
        <div>Bumbu</div>
    </div>
</div>
            
            <h2>Popular</h2>
            <div class="product-grid" id="product-grid">
                <!-- Products will be loaded here by JavaScript -->
            </div>
            
            <div class="nav-bar">
                <div class="nav-item active">
                    <div class="nav-icon">🏠</div>
                    <div>Home</div>
                </div>
                <div class="nav-item">
                    <a href="search.php" style="text-decoration: none; color: inherit;">
                        <div class="nav-icon">🔍</div>
                        <div>Search</div>
                    </a>
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
    <script src="asset/js/katalog.js"></script>
</body>
</html>