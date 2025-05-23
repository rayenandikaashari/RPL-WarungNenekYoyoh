let products = [];
let categories = [];

function redirectToSearchPage() {
  const query = document.getElementById("search-input").value.trim();
  window.location.href = `search.php?query=${encodeURIComponent(query)}`;
}

document.getElementById("search-bar").addEventListener("click", function () {
  window.location.href = "search.php";
});

function displayProducts(productList) {
  const productGrid = document.getElementById("product-grid");
  productGrid.innerHTML = "";
  productList.forEach((product) => {
    const productCard = document.createElement("div");
    productCard.classList.add("product-card");
    productCard.innerHTML = `
            <div class="product-image">
                <img src="${product.gambar_produk}" alt="${product.nama}">
            </div>
            <div class="product-name">${product.nama}</div>
            <div class="product-price">Rp${new Intl.NumberFormat(
              "id-ID"
            ).format(product.harga)}</div>
            <button class="add-button" onclick="goToDetail(${
              product.id
            })">+</button>
        `;
    productGrid.appendChild(productCard);
  });
}

function goToDetail(productId) {
  window.location.href = `detail.php?id=${productId}`;
}

function filterByCategory(category, element) {
  const categoryItems = document.querySelectorAll(".category-item");
  categoryItems.forEach((item) => item.classList.remove("active"));
  element.classList.add("active");

  const filteredProducts =
    category === "all"
      ? products
      : products.filter((product) => product.kategori === category);
  displayProducts(filteredProducts);
}

document.addEventListener("DOMContentLoaded", function () {
  products = productsFromPHP;
  categories = categoriesFromPHP;
  displayProducts(products);
});
