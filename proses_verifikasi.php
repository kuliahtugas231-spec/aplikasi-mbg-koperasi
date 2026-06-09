<?php
session_start();
require_once 'config/koneksi.php';

// Pastikan yang akses adalah Koperasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Koperasi') {
    die("Akses ditolak!");
}

if (isset($_GET['id'])) {
    $id_pesanan = $_GET['id'];

    try {
        // Mulai transaksi database agar data konsisten
        $db->beginTransaction();

        // 1. Ubah status pesanan menjadi 'Verified', pembayaran tetap ditahan di escrow
        $stmt1 = $db->prepare("UPDATE pesanan SET status_logistik = 'Verified' WHERE id_pesanan = ?");
        $stmt1->execute([$id_pesanan]);

        // Catatan: Escrow tetap berstatus 'Ditahan' sampai Petani mengonfirmasi Kirim Barang
        $db->commit();

        echo "<script>alert('Pesanan berhasil diverifikasi!'); window.location='verifikasi_pesanan.php';</script>";
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        echo "<script>alert('Gagal: " . $e->getMessage() . "'); window.location='verifikasi_pesanan.php';</script>";
        exit;
    }
}
?>
