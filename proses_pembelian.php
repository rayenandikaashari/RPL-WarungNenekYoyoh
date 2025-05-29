<?php
session_start();
include 'koneksi.php';

$sessionId = session_id();
$totalPrice = isset($_POST['total_price']) ? (int)$_POST['total_price'] : 0;
$paymentMethod = $_POST['payment_method'] ?? 'unknown'; // Kita tetap simpan metodenya, mungkin berguna nanti

//  Ambil semua item dari keranjang
$sqlGetCart = "SELECT produk_id, jumlah, harga 
               FROM keranjang k
               JOIN produk p ON k.produk_id = p.id
               WHERE k.session_id = ?";
$stmtGetCart = $conn->prepare($sqlGetCart);

if ($stmtGetCart === false) {
    error_log("Prepare failed (GetCart): " . $conn->error);
    $conn->close(); 
    die("Terjadi kesalahan #1. Silakan coba lagi.");
}

$stmtGetCart->bind_param("s", $sessionId);
$stmtGetCart->execute();
$cartResult = $stmtGetCart->get_result();

$cartItems = [];
while ($row = $cartResult->fetch_assoc()) {
    $cartItems[] = $row;
}
$stmtGetCart->close();

if (empty($cartItems)) {
    $conn->close(); 
    header("Location: katalog.php");
    exit;
}

//  Mulai Transaksi
$conn->begin_transaction();

try {
    //  Masukkan ke 'pembelian'
    $sqlInsertPembelian = "INSERT INTO pembelian (total) VALUES (?)";
    $stmtPembelian = $conn->prepare($sqlInsertPembelian);
    if($stmtPembelian === false) throw new Exception("Prepare Gagal (Pembelian): " . $conn->error);
    $stmtPembelian->bind_param("i", $totalPrice);
    if(!$stmtPembelian->execute()) throw new Exception("Execute Gagal (Pembelian): " . $stmtPembelian->error);
    
    $pembelianId = $conn->insert_id;
    if ($pembelianId <= 0) throw new Exception("Gagal mendapatkan ID pembelian.");
    $stmtPembelian->close();

    //  Masukkan ke 'detail_pembelian'
    $sqlInsertDetail = "INSERT INTO detail_pembelian (pembelian_id, produk_id, jumlah, harga) VALUES (?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($sqlInsertDetail);
    if($stmtDetail === false) throw new Exception("Prepare Gagal (Detail): " . $conn->error);

    foreach ($cartItems as $item) {
        $stmtDetail->bind_param("iiii", $pembelianId, $item['produk_id'], $item['jumlah'], $item['harga']);
        if(!$stmtDetail->execute()) throw new Exception("Execute Gagal (Detail): " . $stmtDetail->error);
    }
    $stmtDetail->close();

    //  Hapus dari 'keranjang'
    $sqlDeleteKeranjang = "DELETE FROM keranjang WHERE session_id = ?";
    $stmtDelete = $conn->prepare($sqlDeleteKeranjang);
    if($stmtDelete === false) throw new Exception("Prepare Gagal (Delete): " . $conn->error);
    $stmtDelete->bind_param("s", $sessionId);
    if(!$stmtDelete->execute()) throw new Exception("Execute Gagal (Delete): " . $stmtDelete->error);
    $stmtDelete->close();

    //  Commit
    $conn->commit();

    //  Tutup koneksi SEBELUM redirect
    $conn->close(); 
    
    // SELALU Arahkan ke halaman terima kasih
    header("Location: thankyou.php");
    exit; // Hentikan skrip setelah redirect

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error Proses Pembelian: " . $e->getMessage());
    $conn->close(); 
    die("Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi.");
}

?>