<?php
session_start();
require_once 'config/koneksi.php';

// Pastikan hanya Dapur yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Dapur') {
    header("Location: index.php");
    exit;
}

$id_dapur = $_SESSION['user_id'];

// Ambil parameter filter tab
$tab = $_GET['tab'] ?? 'proses';

$query = "SELECT p.*, k.nama_bahan FROM pesanan p JOIN katalog_bahan k ON p.id_produk = k.id_bahan WHERE p.id_dapur = ?";
$query .= ($tab === 'selesai') ? " AND p.status_logistik = 'Selesai'" : " AND p.status_logistik != 'Selesai'";
$query .= " ORDER BY p.id_pesanan DESC";

$stmt = $db->prepare($query); $stmt->execute([$id_dapur]); $daftar_pesanan = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Pesanan - Dapur Umum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .product-card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: 0.3s; margin-bottom: 1rem; }
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 12px; }
        .sticky-sidebar { position: sticky; top: 2rem; }
        .qty-control { max-width: 130px; }
        .badge-pill { border-radius: 50rem !important; padding: 0.5em 1em; }
    </style>
</head>
<body class="bg-light">

<div class="dashboard-container">
    <!-- Sidebar tetap sama sesuai file context sebelumnya -->

    <main class="main-content container-fluid py-4">
        <div class="row">
            <div class="col-lg-8">
                <h4 class="mb-4 fw-bold">Daftar Pesanan Dapur</h4>
                <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm">
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'proses' ? 'active' : '' ?>" href="?tab=proses">Sedang Diproses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'selesai' ? 'active' : '' ?>" href="?tab=selesai">Selesai</a>
                    </li>
                </ul>

                <?php if(count($daftar_pesanan) > 0): ?>
                    <?php $grand_total = 0; foreach($daftar_pesanan as $pesan): $grand_total += $pesan['total_harga']; ?>
                    <div class="card product-card p-3">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/80" class="product-img me-3" alt="Produk">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($pesan['nama_bahan']) ?></h6>
                                <small class="text-muted">ID Pesanan: #<?= $pesan['id_pesanan'] ?></small>
                                <div class="mt-2">
                                    <span class="badge badge-pill <?= $pesan['status_logistik'] == 'Pending' ? 'bg-warning' : ($pesan['status_logistik'] == 'Verified' ? 'bg-info' : 'bg-success') ?>">
                                        <?= $pesan['status_logistik'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">Rp <?= number_format($pesan['total_harga'], 0, ',', '.') ?></div>
                                <small class="text-muted"><?= $pesan['jumlah_beli'] ?> Unit</small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">Belum ada pesanan di kategori ini.</div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-sidebar">
                    <h5 class="fw-bold mb-4">Ringkasan Pesanan</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Belanja</span>
                        <span class="fw-bold">Rp <?= number_format($grand_total ?? 0, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Estimasi Pengiriman</span>
                        <span class="text-success fw-bold">Gratis</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h6 fw-bold">Total Pembayaran</span>
                        <span class="h6 fw-bold text-primary">Rp <?= number_format($grand_total ?? 0, 0, ',', '.') ?></span>
                    </div>
                    <button class="btn btn-primary w-100 py-3 rounded-3 fw-bold">Ajukan ke Koperasi</button>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>