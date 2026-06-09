<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Koperasi') {
    header("Location: index.php");
    exit;
}

// Ambil data untuk Metrics
$stmt_metrics = $db->query("SELECT 
    COUNT(CASE WHEN status_logistik = 'Pending' THEN 1 END) as pending,
    COUNT(CASE WHEN status_logistik = 'Verified' THEN 1 END) as verified,
    COALESCE(SUM(total_harga), 0) as total_cashflow 
    FROM pesanan");
$metrics = $stmt_metrics->fetch();

// Ambil data untuk Grafik (Contoh: Pesanan 7 hari terakhir)
try {
    $stmt_chart = $db->query("SELECT DATE(created_at) as tgl, SUM(total_harga) as total FROM pesanan GROUP BY DATE(created_at) LIMIT 7");
    $chart_data = $stmt_chart->fetchAll();
} catch (PDOException $e) {
    // Jika kolom created_at belum ada, grafik dikosongkan agar dashboard tidak blank putih
    $chart_data = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Koperasi - Koperasi MBG</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="ph ph-buildings"></i> Koperasi MBG</div>
        <ul class="sidebar-menu">
    <li><a href="dashboard_koperasi.php" class="active"><i class="ph ph-squares-four"></i> Overview</a></li>
    <li><a href="verifikasi_pesanan.php"><i class="ph ph-check-circle"></i> Verifikasi Pesanan</a></li>
    <li><a href="mutasi_escrow.php"><i class="ph ph-wallet"></i> Mutasi Escrow</a></li>
    <li><a href="riwayat_pencairan_dana.php"><i class="ph ph-arrow-circle-down"></i> Pencairan Dana</a></li>
    <li><a href="auth/logout.php" style="color: #EF4444; margin-top: 2rem;"><i class="ph ph-sign-out"></i> Keluar</a></li>
    </ul>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 600;">Panel Admin Koperasi (Agregator & Validator)</h2>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Pantau kualitas bahan, logistik, dan arus uang (Tahap 1)</p>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Pesanan Pending</div>
                <div class="stat-value" style="color: #F59E0B;"><?= $metrics['pending'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Pesanan Verified</div>
                <div class="stat-value" style="color: #10B981;"><?= $metrics['verified'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total Perputaran Uang</div>
                <div class="stat-value" style="color: #6366F1;">Rp <?= number_format($metrics['total_cashflow'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>

        <div class="table-container" style="margin-top: 2rem; padding: 1.5rem;">
            <h3>Tren Transaksi</h3>
            <canvas id="transactionChart" height="100"></canvas>
        </div>
    </main>
</div>

<script>
const ctx = document.getElementById('transactionChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($chart_data, 'tgl')) ?>,
        datasets: [{
            label: 'Total Transaksi (Rp)',
            data: <?= json_encode(array_column($chart_data, 'total')) ?>,
            borderColor: '#6366F1',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(99, 102, 241, 0.1)'
        }]
    }
});
</script>
</body>
</html>
