<?php
session_start();
require_once 'config/koneksi.php';

// Pastikan user adalah Koperasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Koperasi') {
    header("Location: index.php");
    exit;
}

// Mengambil data pesanan dengan status Pending
$stmt = $db->query("SELECT p.*, k.nama_bahan, u.nama_lengkap as nama_dapur 
                    FROM pesanan p 
                    JOIN katalog_bahan k ON p.id_produk = k.id_bahan 
                    JOIN users u ON p.id_dapur = u.id_user 
                    WHERE p.status_logistik = 'Menunggu Verifikasi'
                    ORDER BY p.id_pesanan ASC");
$pesanan_masuk = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pesanan - Koperasi MBG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .badge-pill { border-radius: 50rem !important; padding: 0.5em 1em; }
        .main-content { background: #f8fafc; }
    </style>
</head>
<body class="bg-light">

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="ph ph-buildings"></i> Koperasi MBG</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_koperasi.php"><i class="ph ph-squares-four"></i> Overview</a></li>
            <li><a href="verifikasi_pesanan.php" class="active"><i class="ph ph-check-circle"></i> Verifikasi Pesanan</a></li>
            <li><a href="mutasi_escrow.php"><i class="ph ph-wallet"></i> Mutasi Escrow</a></li>
            <li><a href="riwayat_pencairan_dana.php"><i class="ph ph-arrow-circle-down"></i> Pencairan Dana</a></li>
            <li><a href="auth/logout.php" style="color: #EF4444; margin-top: 2rem;"><i class="ph ph-sign-out"></i> Keluar</a></li>
        </ul>
    </aside>

    <main class="main-content container-fluid py-4">
        <header class="mb-4">
            <h2 class="fw-bold">Verifikasi Pesanan Masuk</h2>
            <p class="text-muted">Tinjau dan validasi pesanan dari Dapur Umum sebelum diteruskan ke Petani.</p>
        </header>
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-4 py-3">Dapur</th>
                            <th class="px-4 py-3">Bahan Pangan</th>
                            <th class="px-4 py-3 text-center">Jumlah</th>
                            <th class="px-4 py-3">Total Harga</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($pesanan_masuk) > 0): ?>
                            <?php foreach($pesanan_masuk as $p): ?>
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold"><?= htmlspecialchars($p['nama_dapur']) ?></div>
                                    <small class="text-muted text-uppercase" style="font-size: 10px;">ID: #<?= $p['id_pesanan'] ?></small>
                                </td>
                                <td class="px-4"><?= htmlspecialchars($p['nama_bahan']) ?></td>
                                <td class="px-4 text-center"><?= $p['jumlah_beli'] ?></td>
                                <td class="px-4 fw-bold text-primary">Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
                                <td class="px-4">
                                    <span class="badge badge-pill bg-warning text-dark"><?= $p['status_logistik'] ?></span>
                                </td>
                                <td class="px-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="proses_verifikasi.php?id=<?= $p['id_pesanan'] ?>" 
                                           class="btn btn-success btn-sm rounded-3 px-3 shadow-sm">
                                           <i class="ph ph-check me-1"></i> Verifikasi
                                        </a>
                                        <a href="proses_batal_pesanan.php?id=<?= $p['id_pesanan'] ?>" 
                                           onclick="return confirm('Tolak pesanan ini?');"
                                           class="btn btn-outline-danger btn-sm rounded-3 px-3">
                                           <i class="ph ph-x me-1"></i> Tolak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="ph ph-tray fs-1 mb-2 opacity-25"></i>
                                    <p class="mb-0">Tidak ada pesanan pending saat ini.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>