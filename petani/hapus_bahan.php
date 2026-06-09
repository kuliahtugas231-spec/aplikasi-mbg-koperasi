<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Petani') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_bahan = $_GET['id'];
    $id_petani = $_SESSION['user_id'];

    try {
        $stmt = $db->prepare("DELETE FROM katalog_bahan WHERE id_bahan = :id AND id_user_petani = :petani");
        $stmt->execute([':id' => $id_bahan, ':petani' => $id_petani]);
        
        $_SESSION['success_msg'] = "Produk berhasil dihapus dari katalog.";
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Gagal menghapus produk.";
    }
}

header("Location: ../dashboard_petani.php");
exit;
?>
