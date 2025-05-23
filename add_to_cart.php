<?php
session_start(); // Jika Anda menggunakan session

include 'koneksi.php';

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $jumlah = 1; // Kita mulai dengan menambahkan 1 item

    // Periksa apakah produk sudah ada di keranjang (tanpa user_id)
    $stmt_check = $conn->prepare("SELECT id FROM keranjang WHERE produk_id = ?");
    $stmt_check->bind_param("i", $product_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Jika sudah ada, tambahkan jumlahnya
        $stmt_update = $conn->prepare("UPDATE keranjang SET jumlah = jumlah + ? WHERE produk_id = ?");
        $stmt_update->bind_param("ii", $jumlah, $product_id);
        if ($stmt_update->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Jumlah di keranjang diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui keranjang.']);
        }
        $stmt_update->close();
    } else {
        // Jika belum ada, masukkan produk baru ke keranjang
        $stmt_insert = $conn->prepare("INSERT INTO keranjang (produk_id, jumlah) VALUES (?, ?)");
        $stmt_insert->bind_param("ii", $product_id, $jumlah);
        if ($stmt_insert->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan ke keranjang.']);
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID produk tidak valid.']);
}

$conn->close();
?>