<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

$sessionId = session_id();
$input = json_decode(file_get_contents('php://input'), true);

$response = ['success' => false, 'message' => 'Input tidak valid.'];

if (isset($input['keranjang_id']) && isset($input['action'])) {
    
    $keranjangId = (int)$input['keranjang_id'];
    $action = $input['action'];

    $sqlGetCurrent = "SELECT k.jumlah, p.harga 
                      FROM keranjang k 
                      JOIN produk p ON k.produk_id = p.id
                      WHERE k.id = ? AND k.session_id = ?";
    $stmtGet = $conn->prepare($sqlGetCurrent);
    $stmtGet->bind_param("is", $keranjangId, $sessionId);
    $stmtGet->execute();
    $resultGet = $stmtGet->get_result();

    if ($resultGet->num_rows > 0) {
        $currentItem = $resultGet->fetch_assoc();
        $currentJumlah = $currentItem['jumlah'];
        $hargaProduk = $currentItem['harga'];
        $stmtGet->close();

        $newJumlah = $currentJumlah;
        $shouldDelete = false;

        if ($action === 'increase') {
            $newJumlah = $currentJumlah + 1;
        } elseif ($action === 'decrease') {
            $newJumlah = $currentJumlah - 1;
        } elseif ($action === 'set' && isset($input['jumlah'])) {
            $newJumlah = (int)$input['jumlah'];
        } elseif ($action === 'remove') {
             $shouldDelete = true;
        }

        if ($newJumlah <= 0 && !$shouldDelete) {
            $shouldDelete = true; 
        }
        
        if ($shouldDelete) {
            $sqlAction = "DELETE FROM keranjang WHERE id = ? AND session_id = ?";
            $stmtAction = $conn->prepare($sqlAction);
            $stmtAction->bind_param("is", $keranjangId, $sessionId);
            $newJumlah = 0;
        } else {
            $sqlAction = "UPDATE keranjang SET jumlah = ? WHERE id = ? AND session_id = ?";
            $stmtAction = $conn->prepare($sqlAction);
            $stmtAction->bind_param("iis", $newJumlah, $keranjangId, $sessionId);
        }

        if ($stmtAction->execute()) {
             $sqlTotal = "SELECT SUM(p.harga * k.jumlah) AS total 
                          FROM keranjang k 
                          JOIN produk p ON k.produk_id = p.id 
                          WHERE k.session_id = ?";
             $stmtTotal = $conn->prepare($sqlTotal);
             $stmtTotal->bind_param("s", $sessionId);
             $stmtTotal->execute();
             $resultTotal = $stmtTotal->get_result();
             $totalRow = $resultTotal->fetch_assoc();
             $newTotal = $totalRow['total'] ? (float)$totalRow['total'] : 0;
             $stmtTotal->close();

            $response = [
                'success' => true,
                'message' => 'Keranjang diperbarui.',
                'newJumlah' => $newJumlah,
                'newTotal' => $newTotal,
                'deleted' => $shouldDelete,
                'keranjangId' => $keranjangId
            ];
        } else {
            $response['message'] = 'Gagal memperbarui database: ' . $stmtAction->error;
        }
        $stmtAction->close();

    } else {
         $response['message'] = 'Item tidak ditemukan atau bukan milik Anda.';
         if(isset($stmtGet)) $stmtGet->close();
    }
}

$conn->close();
echo json_encode($response);
?>