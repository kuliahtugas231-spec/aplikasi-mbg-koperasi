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

    // Tandai pesanan batal
    $stmt1 = $db->prepare("UPDATE pesanan SET status_logistik = 'Pending', status_pembayaran = 'Pending' WHERE id_pesanan = ?");
    $stmt1->execute([$id_pesanan]);

    // Tandai escrow dibatalkan (pakai status yang ada)
    $stmt2 = $db->prepare("UPDATE escrow SET status = 'Ditahan' WHERE id_pesanan = ?");
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

