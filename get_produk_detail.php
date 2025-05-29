<?php

header('Content-Type: application/json'); 
include 'koneksi.php'; 

// Periksa apakah ID produk ada di URL dan merupakan angka
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    http_response_code(400); // Bad request
    echo json_encode(['error' => 'ID produk tidak valid.']);
    exit;
}

$productId = (int)$_GET['id'];

// Ambil kolom yang diperlukan (id, nama, harga, deskripsi, gambar_produk)
$sql = "SELECT id, nama, harga, deskripsi, gambar_produk FROM produk WHERE id = ?";
$stmt = $conn->prepare($sql);

// Periksa jika prepare gagal
if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error); // Log error (cek log server Anda)
    echo json_encode(['error' => 'Gagal menyiapkan query ke database.']);
    exit;
}

// Bind parameter ID ke placeholder (?)
$stmt->bind_param("i", $productId);

// Eksekusi query
if (!$stmt->execute()) {
    http_response_code(500); // Internal Server Error
    error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error); // Log error
    echo json_encode(['error' => 'Gagal mengeksekusi query.']);
    exit;
}

// Dapatkan hasil
$result = $stmt->get_result();

// Periksa apakah produk ditemukan
if ($result->num_rows > 0) {
    // Ambil data produk sebagai array asosiatif
    $product = $result->fetch_assoc();
    
    // Kirim data sebagai JSON
    echo json_encode($product);
} else {
    // Jika produk tidak ditemukan
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Produk dengan ID tersebut tidak ditemukan.']);
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();

?>