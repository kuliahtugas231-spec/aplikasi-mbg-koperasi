<?php
session_start();
require_once 'config/koneksi.php';

// Pastikan hanya admin Koperasi yang akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Koperasi') {
    die("Akses ditolak!");
}

// Ambil data untuk Metrics Escrow
$stmt_metrics = $db->query("SELECT 
    SUM(CASE WHEN status = 'Ditahan' THEN jumlah_dana ELSE 0 END) as total_hold,
    SUM(CASE WHEN status = 'Dicairkan' THEN jumlah_dana ELSE 0 END) as total_cair,
    COUNT(*) as total_transaksi
    FROM escrow");
$metrics_escrow = $stmt_metrics->fetch();

$tab = $_GET['tab'] ?? 'Awaiting';

$stmt = $db->prepare("SELECT e.*, p.status_logistik FROM escrow e 
                      JOIN pesanan p ON e.id_pesanan = p.id_pesanan 
                      WHERE (CASE WHEN ? = 'Awaiting' THEN p.status_logistik = 'Pending' 
                                  WHEN ? = 'Verified' THEN p.status_logistik = 'Verified' 
                                  ELSE e.status = 'Dicairkan' END) ORDER BY e.created_at DESC");
$stmt->execute([$tab, $tab]);
$mutasi = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mutasi Escrow - Koperasi MBG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .nav-pills .nav-link { color: var(--text-muted); font-weight: 500; }
        .nav-pills .nav-link.active { background-color: var(--primary); color: white; }
        .table-hold { background-color: #fff9db !important; } /* Kuning muda */
        .table-cair { background-color: #e6fffa !important; } /* Hijau muda */
        .badge-pill { border-radius: 50rem; padding: 0.5em 1em; }
    </style>
</head>
<body class="bg-light">

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="ph ph-buildings"></i> Koperasi MBG</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_koperasi.php"><i class="ph ph-squares-four"></i> Overview</a></li>
            <li><a href="verifikasi_pesanan.php"><i class="ph ph-check-circle"></i> Verifikasi Pesanan</a></li>
            <li><a href="mutasi_escrow.php" class="active"><i class="ph ph-wallet"></i> Mutasi Escrow</a></li>
            <li><a href="auth/logout.php" style="color: #EF4444; margin-top: 2rem;"><i class="ph ph-sign-out"></i> Keluar</a></li>
        </ul>
    </aside>

    <main class="main-content container-fluid py-4">
        <header class="top-header mb-4">
            <h2 class="h4 fw-bold mb-0">Laporan Mutasi Escrow</h2>
            <p class="text-muted small">Kelola arus kas keluar masuk di rekening penampung.</p>
        </header>

        <!-- Card Metrics -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card p-3 border-0 shadow-sm bg-white rounded-4">
                    <div class="stat-title text-muted small">Dana Tertahan (Hold)</div>
                    <div class="stat-value h4 fw-bold text-warning">Rp <?= number_format($metrics_escrow['total_hold'], 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-3 border-0 shadow-sm bg-white rounded-4">
                    <div class="stat-title text-muted small">Total Dana Dicairkan</div>
                    <div class="stat-value h4 fw-bold text-success">Rp <?= number_format($metrics_escrow['total_cair'], 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-3 border-0 shadow-sm bg-white rounded-4">
                    <div class="stat-title text-muted small">Total Transaksi</div>
                    <div class="stat-value h4 fw-bold text-primary"><?= $metrics_escrow['total_transaksi'] ?></div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm">
            <li class="nav-item"><a class="nav-link <?= $tab == 'Awaiting' ? 'active' : '' ?>" href="?tab=Awaiting">Belum Diverifikasi</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab == 'Verified' ? 'active' : '' ?>" href="?tab=Verified">Verified (Logistik)</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab == 'Dicairkan' ? 'active' : '' ?>" href="?tab=Dicairkan">Selesai / Dicairkan</a></li>
        </ul>

        <!-- Table Mutasi -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">ID Pesanan</th>
                            <th class="px-4 py-3">Jumlah Dana</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-end">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($mutasi) > 0): ?>
                            <?php foreach($mutasi as $row): 
                                $rowClass = ($row['status'] == 'Dicairkan') ? 'table-cair' : 'table-hold';
                            ?>
                            <tr class="<?= $rowClass ?>">
                                <td class="px-4 fw-bold">#<?= $row['id_pesanan'] ?></td>
                                <td class="px-4 fw-bold text-primary">Rp <?= number_format($row['jumlah_dana'], 0, ',', '.') ?></td>
                                <td class="px-4">
                                    <span class="badge badge-pill <?= $row['status'] == 'Dicairkan' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="px-4 text-end text-muted small"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">Tidak ada mutasi pada kategori ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>