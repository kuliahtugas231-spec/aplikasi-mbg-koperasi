<?php
session_start();
require_once 'config/koneksi.php';

// Ambil ID petani dari session
$id_petani = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT p.*, k.nama_bahan, u.nama_lengkap as nama_dapur 
                      FROM pesanan p 
                      JOIN katalog_bahan k ON p.id_produk = k.id_bahan 
                      JOIN users u ON p.id_dapur = u.id_user 
                      WHERE k.id_user_petani = ? 
                      ORDER BY p.id_pesanan DESC");
$stmt->execute([$id_petani]);
$riwayat = $stmt->fetchAll();
?>

<!-- Tampilkan dalam tabel -->
<table>
    <thead>
        <tr><th>Dapur</th><th>Produk</th><th>Jumlah</th><th>Total Harga</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php foreach($riwayat as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['nama_dapur']) ?></td>
            <td><?= htmlspecialchars($r['nama_bahan']) ?></td>
            <td><?= $r['jumlah_beli'] ?></td>
            <td>Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
            <td><span class="badge"><?= $r['status_logistik'] ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>