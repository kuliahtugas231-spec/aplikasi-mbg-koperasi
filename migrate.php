<?php
require_once 'config/koneksi.php';

try {
    $db->exec("ALTER TABLE pesanan MODIFY COLUMN status_logistik ENUM('Pending', 'Menunggu Verifikasi', 'Verified', 'Selesai') DEFAULT 'Pending'");
    echo "Migration successful.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
