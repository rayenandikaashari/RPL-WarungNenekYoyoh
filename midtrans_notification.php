<?php
include 'koneksi.php'; // Koneksi database Anda
require_once dirname(__FILE__) . '/midtrans-php-master/Midtrans.php'; // Library Midtrans

// Set Server Key Anda di sini juga untuk verifikasi notifikasi
\Midtrans\Config::$serverKey = 'SB-Mid-server-pp1mAa3cCZQws473y8CSVmgW'; // GANTI DENGAN SERVER KEY ASLI ANDA
\Midtrans\Config::$isProduction = false; // Sesuaikan dengan environment

error_log("Midtrans Notification Received: " . file_get_contents('php://input')); // Log raw input

try {
    $notif = new \Midtrans\Notification();
} catch (Exception $e) {
    error_log('Midtrans Notification Exception: ' . $e->getMessage());
    http_response_code(400); // Bad request jika notifikasi tidak valid
    echo "Invalid notification format.";
    exit();
}

$transaction = $notif->transaction_status;
$type = $notif->payment_type;
$order_id_midtrans = $notif->order_id; // Ini adalah order_id yang kita kirim ke Midtrans
$fraud = $notif->fraud_status;

// Ekstrak ID Pembelian Asli dari order_id_midtrans
// Jika formatnya "WNY-IDDATABASE-TIMESTAMP"
$parts = explode('-', $order_id_midtrans);
$pembelianIdDatabase = null;
if (count($parts) >= 2 && strtoupper($parts[0]) === 'WNY') {
    $pembelianIdDatabase = (int)$parts[1];
}

if (!$pembelianIdDatabase) {
    error_log("Tidak bisa parse pembelianIdDatabase dari order_id: " . $order_id_midtrans);
    http_response_code(400);
    echo "Invalid order_id format from Midtrans.";
    exit();
}

$status_to_update = 'pending'; // Default

if ($transaction == 'capture') {
    // Untuk transaksi kartu kredit dengan tipe capture
    if ($type == 'credit_card') {
        if ($fraud == 'challenge') {
            // TODO: set transaction status on your database to 'challenge'
            $status_to_update = 'challenge';
        } else {
            // TODO: set transaction status on your database to 'success'
            $status_to_update = 'paid'; // atau 'success' atau 'settlement'
        }
    }
} else if ($transaction == 'settlement') {
    // TODO: set transaction status on your database to 'success'
    // Settlement berarti pembayaran sudah berhasil dan dana sudah masuk.
    $status_to_update = 'paid'; // atau 'success' atau 'completed'
} else if ($transaction == 'pending') {
    // TODO: set transaction status on your database to 'pending'
    $status_to_update = 'pending';
} else if ($transaction == 'deny') {
    // TODO: set transaction status on your database to 'denied'
    $status_to_update = 'denied';
} else if ($transaction == 'expire') {
    // TODO: set transaction status on your database to 'expire'
    $status_to_update = 'expired';
} else if ($transaction == 'cancel') {
    // TODO: set transaction status on your database to 'cancelled'
    $status_to_update = 'cancelled';
}

// --- Update Status di Database Anda ---
if ($status_to_update !== 'pending') { // Hanya update jika ada perubahan signifikan
    $sqlUpdateStatus = "UPDATE pembelian SET status_pembayaran = ?, metode_pembayaran = ? WHERE id = ?";
    // Anda perlu menambahkan kolom status_pembayaran dan metode_pembayaran di tabel `pembelian`
    // ALTER TABLE `pembelian` ADD `status_pembayaran` VARCHAR(50) DEFAULT 'pending';
    // ALTER TABLE `pembelian` ADD `metode_pembayaran` VARCHAR(50) DEFAULT NULL;
    
    $stmtUpdate = $conn->prepare($sqlUpdateStatus);
    if ($stmtUpdate) {
        $payment_type_db = "midtrans-" . $type;
        $stmtUpdate->bind_param("ssi", $status_to_update, $payment_type_db, $pembelianIdDatabase);
        if ($stmtUpdate->execute()) {
            error_log("Status pesanan ID: " . $pembelianIdDatabase . " diupdate menjadi: " . $status_to_update);
            // Di sini Anda bisa trigger email notifikasi ke pelanggan, dll.
        } else {
            error_log("Gagal update status pesanan ID: " . $pembelianIdDatabase . " Error: " . $stmtUpdate->error);
        }
        $stmtUpdate->close();
    } else {
        error_log("Gagal prepare statement untuk update status pesanan ID: " . $pembelianIdDatabase . " Error: " . $conn->error);
    }
}

$conn->close();
// Kirim respons 200 OK ke Midtrans agar mereka tahu notifikasi diterima
http_response_code(200);
echo "Notification processed.";
?>