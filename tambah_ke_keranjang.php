<?php
// tambah_ke_keranjang.php

// WAJIB: Mulai session di paling atas!
session_start(); 

header('Content-Type: application/json');
include 'koneksi.php'; // Sertakan koneksi database

// Dapatkan ID Sesi saat ini
$sessionId = session_id();

// Periksa apakah produk_id dikirim (melalui POST) dan valid
if (!isset($_POST['produk_id']) || !filter_var($_POST['produk_id'], FILTER_VALIDATE_INT)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'ID Produk tidak valid.']);
    exit;
}

$produkId = (int)$_POST['produk_id'];
$jumlahTambah = 1; // Selalu tambah 1 saat ini

// --- Cek apakah produk sudah ada di keranjang untuk sesi ini ---
$sqlCheck = "SELECT id, jumlah FROM keranjang WHERE session_id = ? AND produk_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("si", $sessionId, $produkId);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    // --- Jika SUDAH ADA: Update jumlah ---
    $row = $resultCheck->fetch_assoc();
    $keranjangId = $row['id'];
    $jumlahBaru = $row['jumlah'] + $jumlahTambah;

    $sqlUpdate = "UPDATE keranjang SET jumlah = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ii", $jumlahBaru, $keranjangId);

    if ($stmtUpdate->execute()) {
        echo json_encode(['success' => true, 'message' => 'Jumlah produk di keranjang diperbarui!']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui keranjang: ' . $stmtUpdate->error]);
    }
    $stmtUpdate->close();

} else {
    // --- Jika BELUM ADA: Insert baru ---
    $sqlInsert = "INSERT INTO keranjang (session_id, produk_id, jumlah) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("sii", $sessionId, $produkId, $jumlahTambah);

    if ($stmtInsert->execute()) {
        echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang!']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan ke keranjang: ' . $stmtInsert->error]);
    }
    $stmtInsert->close();
}

$stmtCheck->close();
$conn->close();

?>