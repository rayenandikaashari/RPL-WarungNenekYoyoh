// asset/js/cart.js

function formatRupiahJS(number) {
  return "Rp" + new Intl.NumberFormat("id-ID").format(number);
}

async function updateCartQuantity(keranjangId, action, jumlah = null) {
  try {
    let payload = {
      keranjang_id: keranjangId,
      action: action,
    };

    if (action === "set" && jumlah !== null) {
      payload.jumlah = jumlah;
    }

    const response = await fetch("update_jumlah.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(payload),
    });

    if (!response.ok) {
      throw new Error("Network response was not ok: " + response.statusText);
    }

    const result = await response.json();

    if (result.success) {
      updateCartView(result);
    } else {
      alert("Gagal memperbarui keranjang: " + result.message);
      location.reload();
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Terjadi kesalahan jaringan.");
  }
}

function updateCartView(data) {
  const inputField = document.getElementById(`qty-${data.keranjangId}`);
  const itemDiv = document.getElementById(`item-${data.keranjangId}`);
  const totalSpan = document.getElementById("cart-total-price");
  const h1Title = document.querySelector(".page h1");

  // Ambil elemen yang mau disembunyikan/ditampilkan
  const cartTotalDiv = document.getElementById("cart-total-div");
  const checkoutBtn = document.getElementById("checkout-button-id");
  const cartFooterElement = document.getElementById("cart-footer-id"); // Ambil elemen cart-footer

  if (data.deleted) {
    if (itemDiv) itemDiv.remove();
  } else {
    if (inputField) inputField.value = data.newJumlah;
  }

  if (totalSpan) {
    totalSpan.textContent = formatRupiahJS(data.newTotal);
  }

  const cartItemsContainer = document.getElementById("cart-items");
  const currentItemCount =
    cartItemsContainer.querySelectorAll(".cart-item").length;

  if (h1Title) {
    h1Title.textContent = `${currentItemCount} Items In Cart`;
  }

  if (currentItemCount === 0) {
    if (!document.getElementById("empty-cart-message")) {
      cartItemsContainer.innerHTML = `
                <div class="empty-cart" id="empty-cart-message">
                    <p>Keranjang belanja Anda masih kosong.</p>
                    <a href="katalog.php">Mulai Belanja</a>
                </div>`;
    }
    // Saat kosong, sembunyikan bagian total dan checkout di dalam cart-footer
    if (cartTotalDiv) cartTotalDiv.style.display = "none";
    if (checkoutBtn) checkoutBtn.style.display = "none";
    // Anda bisa memilih untuk menyembunyikan seluruh cart-footer jika mau (kecuali back-to-menu)
    // atau biarkan tetap terlihat hanya dengan "Back to Menu"
    if (cartFooterElement) {
      // Jika Anda ingin menyembunyikan cart-footer jika kosong, kecuali link back-to-menu sudah ada di luar
      // Untuk sekarang, kita biarkan cart-footer terlihat, hanya kontennya yg diatur
      // cartFooterElement.style.opacity = '0';
      // cartFooterElement.style.zIndex = '-1';
    }
  } else {
    // Saat ada item, tampilkan kembali
    if (cartTotalDiv) cartTotalDiv.style.display = "flex";
    if (checkoutBtn) checkoutBtn.style.display = "block";
    if (cartFooterElement) {
      // cartFooterElement.style.opacity = '1';
      // cartFooterElement.style.zIndex = '999';
    }
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const cartContainer = document.getElementById("cart-items-container");

  if (cartContainer) {
    cartContainer.addEventListener("click", function (event) {
      const target = event.target;
      const button = target.closest(".quantity-button, .remove-item");
      if (!button) return;

      const keranjangId = button.getAttribute("data-id");
      if (!keranjangId) return;

      const inputField = document.getElementById(`qty-${keranjangId}`);
      let currentValue = inputField ? parseInt(inputField.value, 10) : 0;

      if (button.classList.contains("increase-qty")) {
        inputField.value = currentValue + 1;
        updateCartQuantity(keranjangId, "set", inputField.value);
      } else if (button.classList.contains("decrease-qty")) {
        if (currentValue > 1) {
          inputField.value = currentValue - 1;
          updateCartQuantity(keranjangId, "set", inputField.value);
        } else {
          if (confirm("Jumlah akan menjadi 0, hapus item ini?")) {
            updateCartQuantity(keranjangId, "remove");
          }
        }
      } else if (button.classList.contains("remove-item")) {
        event.preventDefault();
        if (confirm("Yakin ingin menghapus item ini?")) {
          updateCartQuantity(keranjangId, "remove");
        }
      }
    });

    cartContainer.addEventListener("change", function (event) {
      const target = event.target;
      if (target.classList.contains("quantity-input")) {
        const keranjangId = target.getAttribute("data-id");
        let newJumlah = parseInt(target.value, 10);

        if (isNaN(newJumlah) || newJumlah < 1) {
          if (
            confirm("Jumlah tidak valid. Apakah Anda ingin menghapus item ini?")
          ) {
            updateCartQuantity(keranjangId, "remove");
          } else {
            location.reload();
          }
          return;
        }
        updateCartQuantity(keranjangId, "set", newJumlah);
      }
    });
  }
});
