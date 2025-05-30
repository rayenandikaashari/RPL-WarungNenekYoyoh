<?php
session_start();
include 'koneksi.php'; // Koneksi database Anda
// Pastikan path ke library Midtrans benar
require_once dirname(__FILE__) . '/midtrans-php-master/Midtrans.php'; 

$sessionId = session_id();
$grossAmount = 0; // Akan dihitung dari keranjang

// --- 1. Ambil Item Keranjang & Hitung Total ---
$sqlGetCart = "SELECT k.produk_id, k.jumlah, p.harga, p.nama AS nama_produk 
               FROM keranjang k
               JOIN produk p ON k.produk_id = p.id
               WHERE k.session_id = ?";
$stmtGetCart = $conn->prepare($sqlGetCart);
if ($stmtGetCart === false) { die("Error prepare get cart: " . $conn->error); }
$stmtGetCart->bind_param("s", $sessionId);
$stmtGetCart->execute();
$cartResult = $stmtGetCart->get_result();

$cartItems = [];
$item_details_for_midtrans = []; // Untuk Midtrans
if ($cartResult->num_rows > 0) {
    while ($row = $cartResult->fetch_assoc()) {
        $cartItems[] = $row;
        $subtotal = $row['harga'] * $row['jumlah'];
        $grossAmount += $subtotal;
        $item_details_for_midtrans[] = array(
            'id' => $row['produk_id'],
            'price' => (int)$row['harga'], // Midtrans butuh integer
            'quantity' => (int)$row['jumlah'],
            'name' => $row['nama_produk']
        );
    }
}
$stmtGetCart->close();

if (empty($cartItems) || $grossAmount <= 0) {
    header("Location: cart.php"); // Jika keranjang kosong, kembali
    exit;
}

// --- 2. Simpan Pesanan ke Database Anda DULU ---
$pembelianIdDatabase = null;
$conn->begin_transaction();
try {
    // Masukkan ke tabel 'pembelian'
    // Anda bisa menambahkan kolom status, misal 'pending'
    // Anda juga bisa menambahkan kolom metode_pembayaran jika mau (misal 'midtrans')
    $sqlInsertPembelian = "INSERT INTO pembelian (total, tanggal) VALUES (?, NOW())";
    $stmtPembelian = $conn->prepare($sqlInsertPembelian);
    if($stmtPembelian === false) throw new Exception("Prepare Gagal (Pembelian): " . $conn->error);
    $stmtPembelian->bind_param("i", $grossAmount); // Pastikan grossAmount adalah integer
    if(!$stmtPembelian->execute()) throw new Exception("Execute Gagal (Pembelian): " . $stmtPembelian->error);
    
    $pembelianIdDatabase = $conn->insert_id; // Ini akan jadi order_id untuk Midtrans
    if ($pembelianIdDatabase <= 0) throw new Exception("Gagal mendapatkan ID pembelian dari DB.");
    $stmtPembelian->close();

    // Masukkan ke tabel 'detail_pembelian'
    $sqlInsertDetail = "INSERT INTO detail_pembelian (pembelian_id, produk_id, jumlah, harga) VALUES (?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($sqlInsertDetail);
    if($stmtDetail === false) throw new Exception("Prepare Gagal (Detail): " . $conn->error);

    foreach ($cartItems as $item) {
        $stmtDetail->bind_param("iiii", $pembelianIdDatabase, $item['produk_id'], $item['jumlah'], $item['harga']);
        if(!$stmtDetail->execute()) throw new Exception("Execute Gagal (Detail): " . $stmtDetail->error);
    }
    $stmtDetail->close();

    // Kosongkan keranjang setelah pesanan tercatat
    $sqlDeleteKeranjang = "DELETE FROM keranjang WHERE session_id = ?";
    $stmtDelete = $conn->prepare($sqlDeleteKeranjang);
    if($stmtDelete === false) throw new Exception("Prepare Gagal (Delete Cart): " . $conn->error);
    $stmtDelete->bind_param("s", $sessionId);
    if(!$stmtDelete->execute()) throw new Exception("Execute Gagal (Delete Cart): " . $stmtDelete->error);
    $stmtDelete->close();

    $conn->commit(); // Simpan semua perubahan ke DB

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error Simpan Pesanan ke DB sebelum Midtrans: " . $e->getMessage());
    // Tutup koneksi sebelum die
    $conn->close();
    die("Terjadi kesalahan saat menyimpan pesanan Anda. Silakan coba lagi. " . $e->getMessage());
}
// $conn->close(); // Koneksi akan ditutup setelah getSnapToken atau di blok catch jika error

// --- 3. Konfigurasi Midtrans ---
\Midtrans\Config::$serverKey = 'SB-Mid-server-HP72oJ5I8BFKXF7-GkSvpGpI'; // GANTI DENGAN SERVER KEY ASLI ANDA
\Midtrans\Config::$isProduction = false; // Set true untuk produksi
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// --- 4. Siapkan Parameter untuk Midtrans ---
$params = array(
    'transaction_details' => array(
        'order_id' => "WNY-" . $pembelianIdDatabase . "-" . time(), // Order ID Unik dari DB Anda + timestamp
        'gross_amount' => (int)$grossAmount, // Total harga
    ),
    'item_details' => $item_details_for_midtrans, // Detail item dari keranjang
    'customer_details' => array(
        // Data dummy, ganti dengan data asli jika ada form input
        'first_name' => 'Pelanggan',
        'last_name' => 'WNY',
        'email' => 'pelanggan@example.com',
        'phone' => '08123456789',
        // 'billing_address' => array(...), // Opsional
        // 'shipping_address' => array(...), // Opsional
    ),
    // 'callbacks' => array( // Opsional, jika ingin Snap mengarahkan ke halaman Anda
    // 'finish' => "https://warungnenekyoyoh.com/payment_finish.php" 
    // )
);

$snapToken = null;
try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
} catch (Exception $e) {
    error_log("Midtrans Snap Token Error: " . $e->getMessage());
    // Handle error (misalnya tampilkan pesan error)
}

// Tutup koneksi setelah semua selesai
if ($conn) $conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Proses Pembayaran Midtrans</title>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="SB-Mid-client-NJ1mWHKDnXdKodIV"></script> 
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="asset/css/style.css"> <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f5f5f5; }
        .payment-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); text-align: center; }
        .error-message { color: red; }
    </style>
</head>
<body>
    <div class="payment-container">
        <?php if ($snapToken): ?>
            <h1>Menyiapkan Pembayaran...</h1>
            <p>Anda akan diarahkan ke halaman pembayaran Midtrans.</p>
            <?php else: ?>
            <h1 class="error-message">Gagal Memproses Pembayaran</h1>
            <p>Maaf, terjadi kesalahan saat menyiapkan pembayaran Anda. Silakan coba lagi atau hubungi dukungan.</p>
            <p><a href="cart.php">Kembali ke Keranjang</a></p>
        <?php endif; ?>
    </div>

    <script type="text/javascript">
      var snapToken = "<?php echo $snapToken; ?>";
      
      function proceedToPayment() {
        if (snapToken) {
          window.snap.pay(snapToken, {
            onSuccess: function(result){
              console.log('success');
              console.log(result);
              // Arahkan ke halaman yang memberi tahu bahwa pesanan sedang diproses,
              // dan akan dikonfirmasi setelah notifikasi server diterima.
              // Untuk sementara, kita arahkan ke thankyou.php dengan status pending
              window.location.href = 'thankyou.php?order_id=<?php echo "WNY-" . $pembelianIdDatabase; ?>&status_code=' + result.status_code + '&transaction_status=pending_webhook';
            },
            onPending: function(result){
              console.log('pending');
              console.log(result);
              // Arahkan ke halaman yang sama atau halaman status pending
              window.location.href = 'thankyou.php?order_id=<?php echo "WNY-" . $pembelianIdDatabase; ?>&status_code=' + result.status_code + '&transaction_status=' + result.transaction_status;
            },
            onError: function(result){
              console.log('error');
              console.log(result);
              alert("Pembayaran gagal atau dibatalkan.");
              window.location.href = 'payment.php'; // Kembali ke halaman pembayaran
            },
            onClose: function(){
              console.log('customer closed the popup without finishing the payment');
              alert('Anda menutup popup pembayaran.');
              // Bisa arahkan kembali ke payment.php atau cart.php
              // window.location.href = 'payment.php'; 
            }
          });
        } else {
          // Ini tidak akan terlihat jika PHP sudah menampilkan pesan error
          // alert('Snap Token tidak valid atau tidak ditemukan!');
        }
      }

      // Panggil fungsi secara otomatis jika snapToken ada
      if (snapToken) {
        proceedToPayment();
      }
    </script>
</body>
</html>