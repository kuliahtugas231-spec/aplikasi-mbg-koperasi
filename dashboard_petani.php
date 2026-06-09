<?php
session_start();
require_once 'config/koneksi.php';

// Proteksi akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Petani') {
    header("Location: index.php");
    exit;
}

$id_petani = $_SESSION['user_id'];

try {
    // 1. Ambil data katalog dengan join agar lebih akurat
    $stmt = $db->prepare("SELECT id_bahan, id_user_petani, nama_bahan, deskripsi, harga, stok, satuan FROM katalog_bahan WHERE id_user_petani = :id ORDER BY id_bahan DESC");
    $stmt->execute([':id' => $id_petani]);
    $katalog = $stmt->fetchAll();

    // 2. Ambil data saldo dompet
    $stmt_dompet = $db->prepare("SELECT saldo FROM dompet WHERE id_user = :id");
    $stmt_dompet->execute([':id' => $id_petani]);
    $dompet = $stmt_dompet->fetch();
    $saldo = $dompet ? $dompet['saldo'] : 0;

    // 3. Statistik Pesanan (Gunakan alias untuk kejelasan)
    // Pesanan Baru = Status Verified (Siap dikirim petani)
    $stmt_baru = $db->prepare("SELECT COUNT(*) FROM pesanan p 
                               JOIN katalog_bahan k ON p.id_produk = k.id_bahan 
                               WHERE k.id_user_petani = ? AND p.status_logistik = 'Verified'");
    $stmt_baru->execute([$id_petani]);
    $jml_baru = $stmt_baru->fetchColumn();

    // Pesanan Selesai = Status Selesai
    $stmt_selesai = $db->prepare("SELECT COUNT(*) FROM pesanan p 
                                  JOIN katalog_bahan k ON p.id_produk = k.id_bahan 
                                  WHERE k.id_user_petani = ? AND p.status_logistik = 'Selesai'");
    $stmt_selesai->execute([$id_petani]);
    $jml_selesai = $stmt_selesai->fetchColumn();

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petani - Koperasi MBG</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="ph ph-plant"></i> Koperasi MBG</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_petani.php" class="active"><i class="ph ph-squares-four"></i> Dashboard</a></li>
            <li><a href="petani/tambah_bahan.php"><i class="ph ph-plus-circle"></i> Tambah Produk</a></li>
            <li><a href="petani/riwayat.php"><i class="ph ph-clock-counter-clockwise"></i> Riwayat Pesanan</a></li>
            <li><a href="auth/logout.php" style="color: #EF4444; margin-top: 2rem;"><i class="ph ph-sign-out"></i> Keluar</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div>
                <h2 style="font-size: 1.25rem;">Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h2>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; color: #666;">SALDO DOMPET</div>
                <div style="font-size: 1.125rem; font-weight: 700; color: #10B981;">Rp <?= number_format($saldo, 0, ',', '.') ?></div>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total Produk</div>
                <div class="stat-value"><?= count($katalog) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Pesanan Baru (Siap Kirim)</div>
                <div class="stat-value" style="color: #F59E0B;"><?= $jml_baru ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Pesanan Selesai</div>
                <div class="stat-value" style="color: #6366f1;"><?= $jml_selesai ?></div>
            </div>
        </div>

        <h3>Katalog Produk Saya</h3>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>No</th><th>Nama Produk</th><th>Harga</th><th>Satuan</th><th>Stok</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if(count($katalog) > 0): ?>
                        <?php $no = 1; foreach($katalog as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($item['nama_bahan']) ?></td>
                            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($item['satuan']) ?></td>
                            <td><span class="badge"><?= number_format($item['stok'], 0) ?></span></td>
                            <td>
                                <a href="petani/edit_bahan.php?id=<?= $item['id_bahan'] ?>" class="btn-icon"><i class="ph ph-pencil-simple"></i></a>
                                <a href="petani/hapus_bahan.php?id=<?= $item['id_bahan'] ?>" class="btn-icon" onclick="return confirm('Hapus produk?')"><i class="ph ph-trash" style="color:red;"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
<?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">Belum ada produk. Silakan tambah produk baru.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
