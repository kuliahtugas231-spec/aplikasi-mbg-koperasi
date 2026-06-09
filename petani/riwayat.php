<?php
session_start();
require_once '../config/koneksi.php'; // Sesuaikan path jika perlu

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Petani') {
    header("Location: ../index.php");
    exit;
}

$id_petani = $_SESSION['user_id'];

// Mengambil riwayat pesanan khusus untuk produk milik petani ini
$stmt = $db->prepare("SELECT p.*, k.nama_bahan, u.nama_lengkap as nama_dapur 
                      FROM pesanan p 
                      JOIN katalog_bahan k ON p.id_produk = k.id_bahan 
                      JOIN users u ON p.id_dapur = u.id_user 
                      WHERE k.id_user_petani = ? 
                      ORDER BY p.id_pesanan DESC");
$stmt->execute([$id_petani]);
$riwayat = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan - Koperasi MBG</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="ph ph-plant"></i> Koperasi MBG</div>
        <ul class="sidebar-menu">
            <li><a href="../dashboard_petani.php"><i class="ph ph-squares-four"></i> Dashboard</a></li>
            <li><a href="tambah_bahan.php"><i class="ph ph-plus-circle"></i> Tambah Produk</a></li>
            <li><a href="riwayat.php" class="active"><i class="ph ph-clock-counter-clockwise"></i> Riwayat Pesanan</a></li>
            <li><a href="../auth/logout.php" style="color: #EF4444; margin-top: 2rem;"><i class="ph ph-sign-out"></i> Keluar</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <h2 style="font-size: 1.25rem;">Riwayat Pesanan Masuk</h2>
        </header>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Dapur</th><th>Bahan</th><th>Total</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($riwayat as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['nama_dapur']) ?></td>
                    <td><?= htmlspecialchars($r['nama_bahan']) ?></td>
                    <td>Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                    <td>
                        <span class="badge"><?= $r['status_logistik'] ?></span>
                    </td>
                    <td>
                        <?php if($r['status_logistik'] == 'Verified'): ?>
                            <a href="proses_kirim.php?id=<?= $r['id_pesanan'] ?>" class="btn-primary" style="background:#10B981;">Konfirmasi Kirim</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>