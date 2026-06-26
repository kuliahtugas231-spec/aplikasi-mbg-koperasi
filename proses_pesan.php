<?php
session_start();
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_dapur = $_SESSION['user_id'];
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah_beli'];
    $harga_satuan = $_POST['harga_satuan'];
    $total_harga = $jumlah * $harga_satuan;

    try {
        $db->beginTransaction();

        // MASUKKAN ke tabel pesanan (Status: Pending)
        $stmt = $db->prepare("INSERT INTO pesanan (id_produk, id_dapur, jumlah_beli, total_harga, status_logistik, status_pembayaran) 
                              VALUES (?, ?, ?, ?, 'Pending', 'Pending')");
        $stmt->execute([$id_produk, $id_dapur, $jumlah, $total_harga]);
        
        $db->commit();
        echo "<script>alert('Pesanan dimasukkan ke keranjang!'); window.location='dashboard_dapur.php';</script>";
        
    } catch (Exception $e) {
        $db->rollBack();
        echo "<script>alert('Gagal: " . $e->getMessage() . "'); window.location='dashboard_dapur.php';</script>";
    }
}
?>