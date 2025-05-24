// asset/js/detail.js (Versi baru)

let currentProductData = null;

function getProductIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

async function loadProductDetails() {
    const productId = getProductIdFromUrl();

    if (!productId) {
        console.error("ID Produk tidak ditemukan di URL.");
        document.getElementById('product-detail-name').textContent = "Produk tidak ditemukan.";
        document.getElementById('add-to-cart-btn').style.display = 'none';
        return; 
    }

    try {
        const response = await fetch(`get_produk_detail.php?id=${productId}`);

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'Gagal memuat produk.' }));
            console.error("Error:", errorData.error);
            document.getElementById('product-detail-name').textContent = errorData.error || "Gagal memuat produk.";
            document.getElementById('add-to-cart-btn').style.display = 'none';
            return;
        }

        const product = await response.json();
        currentProductData = product; // Simpan data produk

        document.getElementById('product-detail-name').textContent = product.nama;
        document.getElementById('product-detail-price').textContent = `Rp${Number(product.harga).toLocaleString('id-ID')}`;
        document.getElementById('product-detail-description').textContent = product.deskripsi;
        
        const detailImage = document.getElementById('detail-image');
        detailImage.src = product.gambar_produk ? product.gambar_produk : 'asset/images/placeholder.png';
        detailImage.alt = product.nama;

        setupAddToCartButton();

    } catch (error) {
        console.error("Terjadi kesalahan saat fetch:", error);
        document.getElementById('product-detail-name').textContent = "Terjadi kesalahan jaringan.";
        document.getElementById('add-to-cart-btn').style.display = 'none';
    }
}

function setupAddToCartButton() {
    const button = document.getElementById('add-to-cart-btn');
    button.onclick = function() {
        // Panggil fungsi addToCartDatabase
        addToCartDatabase(); 
    };
}

// FUNGSI BARU: Mengirim data ke server (database)
async function addToCartDatabase() {
    if (!currentProductData || !currentProductData.id) {
        alert("Gagal menambahkan, data produk tidak ada. Coba muat ulang.");
        return;
    }

    const productId = currentProductData.id;

    try {
        // Siapkan data untuk dikirim (gunakan FormData atau URLSearchParams)
        const formData = new URLSearchParams();
        formData.append('produk_id', productId);

        // Kirim permintaan POST ke PHP
        const response = await fetch('tambah_ke_keranjang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString() // Kirim data
        });

        const result = await response.json(); // Baca respons JSON dari PHP

        if (response.ok && result.success) {
            // Jika sukses, tampilkan notif dan redirect
            alert(result.message); // Tampilkan notifikasi dari PHP
            window.location.href = 'katalog.php'; // Redirect ke katalog
        } else {
            // Jika gagal, tampilkan pesan error
            alert("Gagal menambahkan ke keranjang: " + (result.message || "Terjadi kesalahan server."));
        }

    } catch (error) {
        console.error("Error saat menambahkan ke keranjang:", error);
        alert("Terjadi kesalahan jaringan saat mencoba menambahkan ke keranjang.");
    }
}

document.addEventListener('DOMContentLoaded', loadProductDetails);