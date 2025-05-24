// asset/js/cart.js

// Fungsi untuk format Rupiah
function formatRupiahJS(number) {
    return 'Rp' + new Intl.NumberFormat('id-ID').format(number);
}

// Fungsi untuk mengirim update ke server
async function updateCartQuantity(keranjangId, action) {
    try {
        const response = await fetch('update_jumlah.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                keranjang_id: keranjangId, 
                action: action 
            })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const result = await response.json();

        if (result.success) {
            updateCartView(result); // Update tampilan jika sukses
        } else {
            alert('Gagal memperbarui keranjang: ' + result.message);
        }

    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan jaringan.');
    }
}

// Fungsi untuk update tampilan di halaman
function updateCartView(data) {
    const qtySpan = document.getElementById(`qty-${data.keranjangId}`);
    const itemDiv = document.getElementById(`item-${data.keranjangId}`);
    const totalSpan = document.getElementById('cart-total-price');
    const h1Title = document.querySelector('.page h1'); 
    
    // Ambil elemen yang mau disembunyikan/ditampilkan
    const cartTotalDiv = document.getElementById('cart-total-div');
    const checkoutBtn = document.getElementById('checkout-button-id');

    if (data.deleted) {
        if (itemDiv) itemDiv.remove(); // Hapus item dari tampilan
    } else {
        if (qtySpan) qtySpan.textContent = data.newJumlah; // Update jumlah
    }

    if (totalSpan) {
        totalSpan.textContent = formatRupiahJS(data.newTotal); // Update total
    }
    
    const cartItemsContainer = document.getElementById('cart-items'); 
    const currentItemCount = cartItemsContainer.querySelectorAll('.cart-item').length;

    // Update H1 Title
    if (h1Title) {
        h1Title.textContent = `${currentItemCount} Items In Cart`;
    }

    // Cek jika keranjang jadi kosong
    if (currentItemCount === 0) {
        // Tampilkan pesan keranjang kosong
        cartItemsContainer.innerHTML = `
            <div class="empty-cart" id="empty-cart-message">
                <p>Keranjang belanja Anda masih kosong.</p>
                <a href="katalog.php">Kembali Belanja</a>
            </div>`;
        // Sembunyikan Total dan Checkout
        if(cartTotalDiv) cartTotalDiv.style.display = 'none'; 
        if(checkoutBtn) checkoutBtn.style.display = 'none'; 

    } else {
        // Tampilkan Total dan Checkout
        if(cartTotalDiv) cartTotalDiv.style.display = 'flex'; // Atau 'block', sesuaikan CSS
        if(checkoutBtn) checkoutBtn.style.display = 'block'; 
    }
}

// Tambahkan event listener saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const cartContainer = document.getElementById('cart-items-container'); 

    if (cartContainer) {
        cartContainer.addEventListener('click', function(event) {
            const target = event.target;
            const button = target.closest('.quantity-button, .remove-item'); 

            if (!button) return; // Keluar jika bukan tombol + / - / X

            const keranjangId = button.getAttribute('data-id');

            if (!keranjangId) return; 

            if (button.classList.contains('increase-qty')) {
                updateCartQuantity(keranjangId, 'increase');
            } 
            else if (button.classList.contains('decrease-qty')) {
                updateCartQuantity(keranjangId, 'decrease');
            } 
            else if (button.classList.contains('remove-item')) {
                event.preventDefault(); 
                if (confirm('Yakin ingin menghapus item ini?')) {
                    updateCartQuantity(keranjangId, 'remove');
                }
            }
        });
    } 
});