<?php
session_start();
require_once 'config/koneksi.php';

// Proteksi halaman hanya untuk Dapur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Dapur') {
    header("Location: index.php");
    exit;
}

// Ambil katalog bahan yang stoknya > 0
$stmt = $db->query("
    SELECT k.*, u.nama_lengkap as nama_petani 
    FROM katalog_bahan k 
    JOIN users u ON k.id_user_petani = u.id_user 
    WHERE k.stok > 0 
    ORDER BY k.id_bahan DESC
");
$katalog = $stmt->fetchAll();

// Ambil saldo dompet
$id_dapur = $_SESSION['user_id'];
$stmt_dompet = $db->prepare("SELECT saldo FROM dompet WHERE id_user = :id");
$stmt_dompet->execute([':id' => $id_dapur]);
$dompet = $stmt_dompet->fetch();
$saldo = $dompet ? $dompet['saldo'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Dapur - Koperasi MBG</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .pesan-form { display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem; }
        /* Perubahan di bagian ini: Lebar ditambah jadi 90px dan angka rata tengah */
        .input-jumlah { width: 90px; padding: 0.4rem; border: 1px solid #ddd; border-radius: 4px; text-align: center; }
        .qty-btn { 
            background: #f3f4f6; border: 1px solid #ddd; padding: 0.4rem 0.7rem; 
            cursor: pointer; border-radius: 4px; font-weight: bold; transition: 0.2s;
        }
        .qty-btn:hover { background: #e5e7eb; }
    </style>
    <script>
        function changeQty(btn, delta) {
            const input = btn.parentElement.querySelector('input[type="number"]');
            const newVal = parseInt(input.value) + delta;
            if (newVal >= parseInt(input.min) && newVal <= parseInt(input.max)) input.value = newVal;
        }
    </script>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="ph ph-cooking-pot"></i> Koperasi MBG</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_dapur.php" class="active"><i class="ph ph-squares-four"></i> Katalog Pangan</a></li>
            <li><a href="keranjang_pesanan.php"><i class="ph ph-shopping-cart"></i> Keranjang Pesanan</a></li>
            <li><a href="auth/logout.php" style="color: #EF4444; margin-top: 2rem;"><i class="ph ph-sign-out"></i> Keluar</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div>
                <h2 style="font-size: 1.25rem;">Dapur Umum: <?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h2>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; color: #666;">SALDO (ESCROW)</div>
                <div style="font-size: 1.125rem; font-weight: 700; color: #6366f1;">Rp <?= number_format($saldo, 0, ',', '.') ?></div>
            </div>
        </header>

        <h3>Katalog Pangan Tersedia</h3>
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
            <?php foreach($katalog as $item): ?>
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <strong><?= htmlspecialchars($item['nama_bahan']) ?></strong>
                        <div style="font-size: 0.8rem;"><i class="ph ph-farm"></i> <?= htmlspecialchars($item['nama_petani']) ?></div>
                    </div>
                    <span class="badge"><?= $item['stok'] ?> <?= $item['satuan'] ?></span>
                </div>
                <p style="font-size: 0.875rem; color: #666; margin: 1rem 0;"><?= htmlspecialchars($item['deskripsi']) ?></p>
                
                <div style="font-weight: 700;">Rp <?= number_format($item['harga'], 0, ',', '.') ?>/<?= $item['satuan'] ?></div>
                
                <form action="proses_pesan.php" method="POST" class="pesan-form">
                    <input type="hidden" name="id_produk" value="<?= $item['id_bahan'] ?>">
                    <input type="hidden" name="harga_satuan" value="<?= $item['harga'] ?>">
                    
                    <button type="button" class="qty-btn" onclick="changeQty(this, -1)">-</button>
                    
                    <!-- PERBAIKAN: Atribut 'readonly' telah dihapus agar bisa diketik manual skala besar -->
                    <input type="number" name="jumlah_beli" value="1" min="1" max="<?= $item['stok'] ?>" class="input-jumlah">
                    
                    <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                    <button type="submit" class="btn-primary" style="padding: 0.4rem 1rem;">Pesan</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>