<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_GET['id'])) exit("Akses ditolak");

$id_pesanan = $_GET['id'];

try {
    $db->beginTransaction();

    // 1. Update status pesanan jadi Selesai
    $stmt = $db->prepare("UPDATE pesanan SET status_logistik = 'Selesai' WHERE id_pesanan = ?");
    $stmt->execute([$id_pesanan]);

    // 2. Tambahkan saldo ke petani (mengambil id_petani dari katalog_bahan berdasarkan id_produk pesanan)
    $stmt_saldo = $db->prepare("
        UPDATE dompet d
        JOIN katalog_bahan k ON d.id_user = k.id_user_petani
        JOIN pesanan p ON p.id_produk = k.id_bahan
        SET d.saldo = d.saldo + p.total_harga
        WHERE p.id_pesanan = ?");
    $stmt_saldo->execute([$id_pesanan]);

    // 2.5 Ubah status dana di escrow menjadi 'Dicairkan'
    $stmt_escrow = $db->prepare("UPDATE escrow SET status = 'Dicairkan' WHERE id_pesanan = ?");
    $stmt_escrow->execute([$id_pesanan]);

    // 3. Kurangi stok di katalog
    $stmt_stok = $db->prepare("
        UPDATE katalog_bahan k
        JOIN pesanan p ON p.id_produk = k.id_bahan
        SET k.stok = k.stok - p.jumlah_beli
        WHERE p.id_pesanan = ?");
    $stmt_stok->execute([$id_pesanan]);

    $db->commit();
    echo "<script>alert('Pesanan selesai, saldo petani bertambah, dan stok berkurang!'); window.location='riwayat.php';</script>";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error: " . $e->getMessage();
}
?>