<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_GET['id'])) exit("Akses ditolak");

$id_pesanan = $_GET['id'];

try {
    $db->beginTransaction();

    // 1. Ambil detail info pesanan (Termasuk id_dapur untuk memotong anggaran mereka)
    $stmt_detail = $db->prepare("
        SELECT p.id_pesanan, p.id_produk, p.id_dapur, p.total_harga, k.id_user_petani 
        FROM pesanan p
        JOIN katalog_bahan k ON p.id_produk = k.id_bahan
        WHERE p.id_pesanan = ?
    ");
    $stmt_detail->execute([$id_pesanan]);
    $pesanan = $stmt_detail->fetch(PDO::FETCH_ASSOC);

    if (!$pesanan) {
        throw new Exception("Data pesanan tidak ditemukan di database.");
    }

    $id_produk    = $pesanan['id_produk'];
    $id_dapur     = $pesanan['id_dapur']; // Mengambil ID Dapur yang memesan
    $total_harga  = $pesanan['total_harga'];
    $id_petani    = $pesanan['id_user_petani'];

    // 2. Update status pesanan menjadi Selesai
    $stmt = $db->prepare("UPDATE pesanan SET status_logistik = 'Selesai' WHERE id_pesanan = ?");
    $stmt->execute([$id_pesanan]);

    // 3. TAMBAHKAN SALDO KE PETANI
    $stmt_cek_dompet = $db->prepare("SELECT COUNT(*) FROM dompet WHERE id_user = ?");
    $stmt_cek_dompet->execute([$id_petani]);
    $punya_dompet = $stmt_cek_dompet->fetchColumn();

    if ($punya_dompet > 0) {
        $stmt_saldo = $db->prepare("UPDATE dompet SET saldo = saldo + ? WHERE id_user = ?");
        $stmt_saldo->execute([$total_harga, $id_petani]);
    } else {
        $stmt_saldo_baru = $db->prepare("INSERT INTO dompet (id_user, saldo, status_dompet) VALUES (?, ?, 'Aktif')");
        $stmt_saldo_baru->execute([$id_petani, $total_harga]);
    }

    // 4. (Dihapus: Saldo dapur sudah dipotong saat mengajukan pesanan ke koperasi, 
    // jadi tidak perlu dipotong lagi di sini untuk mencegah pemotongan ganda.)

    // 5. Ubah status dana di escrow menjadi 'Dicairkan'
    $stmt_escrow = $db->prepare("UPDATE escrow SET status = 'Dicairkan' WHERE id_pesanan = ?");
    $stmt_escrow->execute([$id_pesanan]);

    // Jika semua proses sukses, jalankan commit
    $db->commit();
    echo "<script>alert('Pesanan selesai! Saldo petani bertambah & Anggaran dapur berhasil dikurangi.'); window.location='riwayat.php';</script>";

} catch (Exception $e) {
    $db->rollback();
    echo "Gagal Memproses Transaksi. Kesalahan: " . $db->quote($e->getMessage());
}
?>