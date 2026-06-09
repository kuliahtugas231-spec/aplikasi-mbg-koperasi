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

        // 1. CEK SALDO Dapur (Apakah cukup?)
        $stmt_cek = $db->prepare("SELECT saldo FROM dompet WHERE id_user = ? FOR UPDATE");
        $stmt_cek->execute([$id_dapur]);
        $saldo = $stmt_cek->fetchColumn();

        if ($saldo < $total_harga) {
            throw new Exception("Saldo Anda tidak cukup (Saldo: Rp " . number_format($saldo) . ")");
        }

        // 2. POTONG SALDO Dapur
        $stmt_potong = $db->prepare("UPDATE dompet SET saldo = saldo - ? WHERE id_user = ?");
        $stmt_potong->execute([$total_harga, $id_dapur]);

        // 3. MASUKKAN ke tabel pesanan
        $stmt = $db->prepare("INSERT INTO pesanan (id_produk, id_dapur, jumlah_beli, total_harga, status_logistik, status_pembayaran) 
                              VALUES (?, ?, ?, ?, 'Pending', 'Pending')");
        $stmt->execute([$id_produk, $id_dapur, $jumlah, $total_harga]);
        
        $id_pesanan_baru = $db->lastInsertId();

        // 4. MASUKKAN ke tabel escrow
        $stmt_escrow = $db->prepare("INSERT INTO escrow (id_pesanan, jumlah_dana, status) 
                                     VALUES (?, ?, 'Ditahan')");
        $stmt_escrow->execute([$id_pesanan_baru, $total_harga]);

        $db->commit();
        echo "<script>alert('Pesanan berhasil! Saldo telah dipotong.'); window.location='dashboard_dapur.php';</script>";
        
    } catch (Exception $e) {
        $db->rollBack();
        echo "<script>alert('Gagal: " . $e->getMessage() . "'); window.location='dashboard_dapur.php';</script>";
    }
}
?>