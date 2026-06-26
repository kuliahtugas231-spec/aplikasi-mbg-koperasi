<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Koperasi') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: verifikasi_pesanan.php");
    exit;
}

$id_pesanan = (int)$_GET['id'];

try {
    $db->beginTransaction();

    // Ambil detail pesanan untuk refund
    $stmt_detail = $db->prepare("SELECT id_dapur, total_harga FROM pesanan WHERE id_pesanan = ?");
    $stmt_detail->execute([$id_pesanan]);
    $pesanan = $stmt_detail->fetch();

    if ($pesanan) {
        // Refund saldo ke dompet dapur
        $stmt_refund = $db->prepare("UPDATE dompet SET saldo = saldo + ? WHERE id_user = ?");
        $stmt_refund->execute([$pesanan['total_harga'], $pesanan['id_dapur']]);
    }

    // Tandai pesanan kembali ke keranjang (Pending)
    $stmt1 = $db->prepare("UPDATE pesanan SET status_logistik = 'Pending', status_pembayaran = 'Pending' WHERE id_pesanan = ?");
    $stmt1->execute([$id_pesanan]);

    // Hapus data escrow karena transaksi dibatalkan (uang kembali ke dompet)
    $stmt2 = $db->prepare("DELETE FROM escrow WHERE id_pesanan = ?");
    $stmt2->execute([$id_pesanan]);

    $db->commit();

    echo "<script>alert('Pesanan berhasil dibatalkan.'); window.location='verifikasi_pesanan.php';</script>";
    exit;
} catch (Exception $e) {
    $db->rollBack();
    echo "<script>alert('Gagal: " . $e->getMessage() . "'); window.location='verifikasi_pesanan.php';</script>";
    exit;
}
?>

