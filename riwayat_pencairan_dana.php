<?php
session_start();
require_once 'config/koneksi.php';

// Pastikan user adalah Koperasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Koperasi') {
    header("Location: index.php");
    exit;
}

// Ambil ringkasan total dana yang sudah dicairkan
try {
    $stmt_total = $db->prepare("SELECT COALESCE(SUM(jumlah_dana), 0) AS total_dana FROM escrow WHERE status = 'Dicairkan'");
    $stmt_total->execute();
    $total_dana = $stmt_total->fetchColumn();
} catch (Exception $e) {
    $total_dana = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pencairan Dana - Koperasi MBG</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="ph ph-buildings"></i> Koperasi MBG</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_koperasi.php" ><i class="ph ph-squares-four"></i> Overview</a></li>
            <li><a href="verifikasi_pesanan.php"><i class="ph ph-check-circle"></i> Verifikasi Pesanan</a></li>
            <li><a href="mutasi_escrow.php" ><i class="ph ph-wallet"></i> Mutasi Escrow</a></li>
            <li><a href="riwayat_pencairan_dana.php" class="active"><i class="ph ph-arrow-circle-down"></i> Pencairan Dana</a></li>
            <li><a href="auth/logout.php" style="color: #EF4444; margin-top: 2rem;"><i class="ph ph-sign-out"></i> Keluar</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 600;">Riwayat Pencairan Dana</h2>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Ringkasan pencairan yang sudah selesai.</p>
            </div>
        </header>

        <div class="stats-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); margin-bottom: 1.25rem;">
            <?php
                // Metrics tambahan (Card Metrics)
                $total_pending = 0;
                $total_terverifikasi_kg = 0;

                try {
                    // Total Pesanan Pending
                    $stmt_pending = $db->query("SELECT COUNT(*) FROM pesanan WHERE status_logistik = 'Menunggu Verifikasi'");
                    $total_pending = (int)$stmt_pending->fetchColumn();

                    // Total Bahan Pangan Terverifikasi (jumlah komoditas yang sudah Verified)
                    // Kolom jumlah pada pesanan diasumsikan dalam satuan kilogram (kg)
                    $stmt_ver = $db->query("SELECT COALESCE(SUM(jumlah_beli), 0) FROM pesanan WHERE status_logistik = 'Verified'");
                    $total_terverifikasi_kg = (float)$stmt_ver->fetchColumn();
                } catch (Exception $e) {
                    $total_pending = 0;
                    $total_terverifikasi_kg = 0;
                }
            ?>

            <div class="stat-card">
                <div class="stat-title">Total Pesanan Pending</div>
                <div class="stat-value" style="color:#F59E0B;"><?= $total_pending ?></div>

            </div>

            <div class="stat-card">
                <div class="stat-title">Total Bahan Terverifikasi (kg)</div>
                <div class="stat-value" style="color:#3B82F6;"><?= number_format($total_terverifikasi_kg, 2, '.', ',') ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-title">Total Dicairkan</div>
                <div class="stat-value" style="color:#10B981;">Rp <?= number_format((int)$total_dana, 0, ',', '.') ?></div>
            </div>
        </div>


        <div class="table-container">
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #f3f4f6; text-align:left;">
                        <th style="padding:1rem;">No</th>
                        <th style="padding:1rem;">No. Ref Keuangan</th>
                        <th style="padding:1rem;">No. Pesanan</th>
                        <th style="padding:1rem;">Penerima (Petani)</th>
                        <th style="padding:1rem;">Jumlah Dana</th>
                        <th style="padding:1rem;">Status</th>
                        <th style="padding:1rem;">Waktu Cair</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    try {
                        // JOIN untuk mendapatkan nama petani penerima dana
                        $stmt = $db->prepare("
                            SELECT e.id_escrow, e.id_pesanan, e.jumlah_dana, e.status, e.created_at, u.nama_lengkap as nama_petani
                            FROM escrow e
                            JOIN pesanan p ON e.id_pesanan = p.id_pesanan
                            JOIN katalog_bahan k ON p.id_produk = k.id_bahan
                            JOIN users u ON k.id_user_petani = u.id_user
                            WHERE e.status = 'Dicairkan' 
                            ORDER BY e.created_at DESC");
                        $stmt->execute();
                        $rows = $stmt->fetchAll();
                    } catch (Exception $e) {
                        $rows = [];
                    }
                ?>
                <?php if (count($rows) > 0): ?>
                    <?php $no = 1; foreach ($rows as $r): ?>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding:1rem;"><?= $no++ ?></td>
                            <td style="padding:1rem; font-family: monospace; color: var(--text-muted);">ESC-<?= $r['id_escrow'] ?></td>
                            <td style="padding:1rem; font-weight: 500;">ORD-<?= $r['id_pesanan'] ?></td>
                            <td style="padding:1rem;"><?= htmlspecialchars($r['nama_petani']) ?></td>
                            <td style="padding:1rem;">Rp <?= number_format((int)$r['jumlah_dana'], 0, ',', '.') ?></td>
                            <td style="padding:1rem;"><span class="badge badge-success" style="background: #D1FAE5; color: #065F46;">Selesai</span></td>
                            <td style="padding:1rem; font-size: 0.85rem; color: var(--text-muted);"><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="padding:2rem; text-align:center; color:#9ca3af;">Belum ada riwayat pencairan dana yang diselesaikan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
