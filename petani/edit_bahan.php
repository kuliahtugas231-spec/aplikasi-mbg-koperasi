<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Petani') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ../dashboard_petani.php");
    exit;
}

$id_bahan = $_GET['id'];
$id_petani = $_SESSION['user_id'];

// Ambil data bahan yang akan diedit
$stmt = $db->prepare("SELECT * FROM katalog_bahan WHERE id_bahan = :id AND id_user_petani = :petani");
$stmt->execute([':id' => $id_bahan, ':petani' => $id_petani]);
$item = $stmt->fetch();

if (!$item) {
    header("Location: ../dashboard_petani.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_bahan = $_POST['nama_bahan'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];

    try {
        $update_stmt = $db->prepare("UPDATE katalog_bahan SET nama_bahan = :nama, deskripsi = :desc, harga = :harga, stok = :stok, satuan = :satuan WHERE id_bahan = :id AND id_user_petani = :petani");
        $update_stmt->execute([
            ':nama' => $nama_bahan,
            ':desc' => $deskripsi,
            ':harga' => $harga,
            ':stok' => $stok,
            ':satuan' => $satuan,
            ':id' => $id_bahan,
            ':petani' => $id_petani
        ]);

        $_SESSION['success_msg'] = "Produk berhasil diperbarui!";
        header("Location: ../dashboard_petani.php");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal memperbarui data: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Petani</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .form-container { background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); max-width: 600px; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="ph ph-plant"></i> Koperasi MBG</div>
        <ul class="sidebar-menu">
            <li><a href="../dashboard_petani.php" class="active"><i class="ph ph-squares-four"></i> Dashboard</a></li>
            <li><a href="tambah_bahan.php"><i class="ph ph-plus-circle"></i> Tambah Produk</a></li>
            <li><a href="riwayat.php"><i class="ph ph-clock-counter-clockwise"></i> Riwayat Pesanan</a></li>
            <li><a href="../auth/logout.php" style="color: #EF4444; margin-top: 2rem;"><i class="ph ph-sign-out"></i> Keluar</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="top-header" style="justify-content: flex-start; gap: 1rem;">
            <a href="../dashboard_petani.php" style="color: var(--text-muted); text-decoration: none; padding: 0.5rem; background: #F3F4F6; border-radius: 8px;"><i class="ph ph-arrow-left"></i></a>
            <h2 style="font-size: 1.25rem; font-weight: 600;">Edit Produk: <?= htmlspecialchars($item['nama_bahan']) ?></h2>
        </header>

        <div class="form-container">
            <?php if(isset($error)): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_bahan" class="form-control" value="<?= htmlspecialchars($item['nama_bahan']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi Singkat</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($item['deskripsi']) ?></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" value="<?= htmlspecialchars($item['harga']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($item['satuan']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Stok Tersedia Saat Ini</label>
                    <input type="number" name="stok" class="form-control" value="<?= htmlspecialchars($item['stok']) ?>" required>
                </div>
                <button type="submit" class="btn-primary" style="margin-top: 1rem;">Simpan Perubahan</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
