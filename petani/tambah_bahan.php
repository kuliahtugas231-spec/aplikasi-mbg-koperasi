<?php
session_start();
require_once '../config/koneksi.php';

// Cek apakah user adalah Petani
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Petani') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_bahan = $_POST['nama_bahan'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];
    $id_petani = $_SESSION['user_id'];

    try {
        $stmt = $db->prepare("INSERT INTO katalog_bahan (id_user_petani, nama_bahan, deskripsi, harga, stok, satuan) VALUES (:id_petani, :nama, :desc, :harga, :stok, :satuan)");
        $stmt->execute([
            ':id_petani' => $id_petani,
            ':nama' => $nama_bahan,
            ':desc' => $deskripsi,
            ':harga' => $harga,
            ':stok' => $stok,
            ':satuan' => $satuan
        ]);

        $_SESSION['success_msg'] = "Produk berhasil ditambahkan!";
        header("Location: ../dashboard_petani.php");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal menyimpan data: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Petani</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            max-width: 600px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="ph ph-plant"></i> Koperasi MBG</div>
        <ul class="sidebar-menu">
            <li><a href="../dashboard_petani.php"><i class="ph ph-squares-four"></i> Dashboard</a></li>
            <li><a href="tambah_bahan.php" class="active"><i class="ph ph-plus-circle"></i> Tambah Produk</a></li>
            <li><a href="riwayat.php"><i class="ph ph-clock-counter-clockwise"></i> Riwayat Pesanan</a></li>
            <li><a href="../auth/logout.php" style="color: #EF4444; margin-top: 2rem;"><i class="ph ph-sign-out"></i> Keluar</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header" style="justify-content: flex-start; gap: 1rem;">
            <a href="../dashboard_petani.php" style="color: var(--text-muted); text-decoration: none; padding: 0.5rem; background: #F3F4F6; border-radius: 8px;"><i class="ph ph-arrow-left"></i></a>
            <h2 style="font-size: 1.25rem; font-weight: 600;">Tambah Produk Pangan Baru</h2>
        </header>

        <div class="form-container">
            <?php if(isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Produk (Contoh: Telur Ayam Negeri)</label>
                    <input type="text" name="nama_bahan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi Singkat</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan kualitas produk Anda..."></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stok & Satuan</label>
                        <div class="input-group">
                            <input type="number" name="stok" class="form-control" placeholder="0" required>
                            <select name="satuan" class="form-select" style="max-width: 100px;">
                                <option value="Kg">Kg</option>
                                <option value="Butir">Butir</option>
                                <option value="Ikat">Ikat</option>
                                <option value="Liter">Liter</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="margin-top: 1rem;">Simpan Produk</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
