<?php
session_start();
require_once 'config/koneksi.php';

// Pastikan hanya Dapur yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Dapur') {
    header("Location: index.php");
    exit;
}

$id_dapur = $_SESSION['user_id'];

// --- LOGIKA POTONG SALDO OTOMATIS (saat Ajukan ke Koperasi) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_ajuan'])) {
    $total_bayar = (float)$_POST['total_bayar'];

    try {
        $db->beginTransaction();

        // 1. Cek Saldo Dapur di tabel dompet
        $stmt_saldo = $db->prepare("SELECT saldo FROM dompet WHERE id_user = ? FOR UPDATE");
        $stmt_saldo->execute([$id_dapur]);
        $dompet = $stmt_saldo->fetch();

        if ($dompet && $dompet['saldo'] >= $total_bayar) {
            // 2. Kurangi Saldo Dapur
            $db->prepare("UPDATE dompet SET saldo = saldo - ? WHERE id_user = ?")->execute([$total_bayar, $id_dapur]);

            // 3. Masukkan data ke tabel escrow untuk semua pesanan Pending milik dapur ini
            $stmt_pending = $db->prepare("SELECT id_pesanan, total_harga FROM pesanan WHERE id_dapur = ? AND status_logistik = 'Pending'");
            $stmt_pending->execute([$id_dapur]);
            $pending_orders = $stmt_pending->fetchAll();

            $stmt_escrow = $db->prepare("INSERT INTO escrow (id_pesanan, jumlah_dana, status) VALUES (?, ?, 'Ditahan')");
            foreach ($pending_orders as $order) {
                $stmt_escrow->execute([$order['id_pesanan'], $order['total_harga']]);
            }

            // 4. Update status pesanan jadi 'Menunggu Verifikasi'
            $db->prepare("UPDATE pesanan SET status_logistik = 'Menunggu Verifikasi' WHERE id_dapur = ? AND status_logistik = 'Pending'")->execute([$id_dapur]);
            
            $db->commit();
            exit("sukses"); // Kirim respon sukses ke JavaScript
        } else {
            $db->rollBack();
            exit("gagal_saldo"); // Kirim respon gagal
        }
    } catch (Exception $e) {
        $db->rollBack();
        exit("error");
    }
}

// Ambil data untuk tampilan
$tab = $_GET['tab'] ?? 'proses';
$query = "SELECT p.*, k.nama_bahan FROM pesanan p JOIN katalog_bahan k ON p.id_produk = k.id_bahan WHERE p.id_dapur = ?";
$query .= ($tab === 'selesai') ? " AND p.status_logistik = 'Selesai'" : " AND p.status_logistik != 'Selesai'";
$query .= " ORDER BY p.id_pesanan DESC";

$stmt = $db->prepare($query); 
$stmt->execute([$id_dapur]); 
$daftar_pesanan = $stmt->fetchAll();
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
        .badge-pill { border-radius: 50rem !important; padding: 0.5em 1em; }
        
        @media print {
            .sidebar, .nav-pills, .sticky-sidebar, .btn { display: none !important; }
            .dashboard-container { display: block !important; padding: 0 !important; }
            .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; background: white !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-light">

<div class="dashboard-container">
    <main class="main-content container-fluid py-4">
        <div class="row">
            <div class="col-lg-8">
                <?php if($tab !== 'selesai'): ?>
                <h4 class="mb-4 fw-bold">Daftar Pesanan Dapur</h4>
                <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm">
                    <li class="nav-item"><a class="nav-link <?= $tab == 'proses' ? 'active' : '' ?>" href="?tab=proses">Sedang Diproses</a></li>
                    <li class="nav-item"><a class="nav-link <?= $tab == 'selesai' ? 'active' : '' ?>" href="?tab=selesai">Selesai</a></li>
                </ul>

                <?php if(count($daftar_pesanan) > 0): ?>
                    <?php 
                    $grand_total = 0; 
                    foreach($daftar_pesanan as $pesan): 
                        // Hanya jumlahkan yang berstatus Pending untuk diajukan ke koperasi
                        if ($pesan['status_logistik'] === 'Pending') {
                            $grand_total += $pesan['total_harga']; 
                        }
                    ?>
                    <div class="card product-card p-3">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/80" class="product-img me-3" alt="Produk">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($pesan['nama_bahan']) ?></h6>
                                <small class="text-muted">ID Pesanan: #<?= $pesan['id_pesanan'] ?></small>
                                <div class="mt-2">
                                    <span class="badge badge-pill <?= $pesan['status_logistik'] == 'Pending' ? 'bg-warning text-dark' : 'bg-info' ?>">
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
                    <h5 class="fw-bold mb-4">Ringkasan Ajuan Baru</h5>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h6 fw-bold">Total (Draft)</span>
                        <span class="h6 fw-bold text-primary" id="total_display">Rp <?= number_format($grand_total ?? 0, 0, ',', '.') ?></span>
                    </div>
                    
                    <?php if(isset($grand_total) && $grand_total > 0): ?>
                        <button type="button" class="btn btn-primary w-100 py-3 rounded-3 fw-bold" onclick="ajukanDanPotong(<?= $grand_total ?>)">
                            Ajukan ke Koperasi
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary w-100 py-3 rounded-3 fw-bold" disabled>Tidak ada ajuan baru</button>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php else: ?>
            
            <!-- TAMPILAN LAPORAN PENGELUARAN DAPUR (TAB SELESAI) -->
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0 fw-bold">Laporan Pengeluaran Dapur</h4>
                    <div>
                        <button class="btn btn-outline-primary rounded-pill px-4" onclick="window.print()">
                            <i class="ph ph-printer me-2"></i> Cetak Laporan
                        </button>
                        <a href="?tab=proses" class="btn btn-light rounded-pill px-4 ms-2">Kembali</a>
                    </div>
                </div>

                <?php 
                $total_pengeluaran = 0;
                foreach($daftar_pesanan as $pesan) { $total_pengeluaran += $pesan['total_harga']; }
                ?>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                            <h6 class="text-muted">Total Pengeluaran Selesai</h6>
                            <h3 class="fw-bold text-success mb-0">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                            <h6 class="text-muted">Jumlah Transaksi</h6>
                            <h3 class="fw-bold text-primary mb-0"><?= count($daftar_pesanan) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">ID Pesanan</th>
                                    <th class="px-4 py-3">Produk</th>
                                    <th class="px-4 py-3 text-center">Jumlah</th>
                                    <th class="px-4 py-3">Total Harga</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($daftar_pesanan) > 0): ?>
                                    <?php foreach($daftar_pesanan as $pesan): ?>
                                    <tr>
                                        <td class="px-4 text-muted small"><?= date('d M Y, H:i', strtotime($pesan['created_at'])) ?></td>
                                        <td class="px-4 fw-bold">#<?= $pesan['id_pesanan'] ?></td>
                                        <td class="px-4"><?= htmlspecialchars($pesan['nama_bahan']) ?></td>
                                        <td class="px-4 text-center"><?= $pesan['jumlah_beli'] ?> Unit</td>
                                        <td class="px-4 fw-bold text-primary">Rp <?= number_format($pesan['total_harga'], 0, ',', '.') ?></td>
                                        <td class="px-4">
                                            <span class="badge badge-pill bg-success">Selesai</span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada pesanan yang selesai.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
function ajukanDanPotong(total) {
    if (confirm('Apakah Anda yakin ingin mengajukan pesanan ini ke Koperasi? Saldo Anda akan otomatis terpotong.')) {
        // Kirim data ke file ini sendiri via AJAX (Fetch API)
        fetch('keranjang_pesanan.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'proses_ajuan=true&total_bayar=' + total
        })
        .then(response => response.text())
        .then(result => {
            if (result === "sukses") {
                alert('Pesanan telah diajukan dan saldo berhasil dipotong!');
                window.location.reload(); // Refresh halaman agar status berubah
            } else {
                alert('Gagal: Saldo Anda tidak cukup!');
            }
        });
    }
}
</script>
</body>
</html>