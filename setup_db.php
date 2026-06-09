<?php
$host = 'localhost';
$username = 'root';
$password = ''; 
$dbname = 'koperasi_mbg';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");

    // Drop existing tables
    $pdo->exec("DROP TABLE IF EXISTS escrow");
    $pdo->exec("DROP TABLE IF EXISTS pesanan");
    $pdo->exec("DROP TABLE IF EXISTS katalog_bahan");
    $pdo->exec("DROP TABLE IF EXISTS dompet");
    $pdo->exec("DROP TABLE IF EXISTS users");

    // 1. Table users
    $sql_users = "
    CREATE TABLE users (
        id_user INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('Petani', 'Koperasi', 'Dapur') NOT NULL,
        nama_lengkap VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_users);

    // 2. Table dompet
    $sql_dompet = "
    CREATE TABLE dompet (
        id_dompet INT AUTO_INCREMENT PRIMARY KEY,
        id_user INT NOT NULL,
        saldo DECIMAL(12,2) DEFAULT 0.00,
        status_dompet ENUM('Aktif', 'Ditangguhkan') DEFAULT 'Aktif',
        FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
    )";
    $pdo->exec($sql_dompet);

    // 3. Table katalog_bahan (Disesuaikan dengan logika aplikasi)
    $sql_katalog = "
    CREATE TABLE katalog_bahan (
        id_bahan INT AUTO_INCREMENT PRIMARY KEY,
        id_user_petani INT NOT NULL,
        nama_bahan VARCHAR(100) NOT NULL,
        deskripsi TEXT,
        harga DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        stok INT NOT NULL DEFAULT 0,
        satuan VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_user_petani) REFERENCES users(id_user) ON DELETE CASCADE
    )";
    $pdo->exec($sql_katalog);

    // 4. Table pesanan
    $sql_pesanan = "
    CREATE TABLE pesanan (
        id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
        id_produk INT NOT NULL,
        id_dapur INT NOT NULL,
        jumlah_beli INT NOT NULL,
        total_harga DECIMAL(12,2) NOT NULL,
        status_logistik ENUM('Pending', 'Verified', 'Selesai') DEFAULT 'Pending',
        status_pembayaran ENUM('Pending', 'Lunas') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_pesanan);

    // 5. Table escrow
    $sql_escrow = "
    CREATE TABLE escrow (
        id_escrow INT AUTO_INCREMENT PRIMARY KEY,
        id_pesanan INT NOT NULL,
        jumlah_dana DECIMAL(12,2) NOT NULL,
        status ENUM('Ditahan', 'Dicairkan') DEFAULT 'Ditahan',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_escrow);

    // 4. Insert dummy data (Password TEXT BIASA sesuai permintaan)
    $sql_insert = "INSERT INTO users (username, password, role, nama_lengkap) VALUES 
        ('petani1', 'synergy123', 'Petani', 'Kelompok Tani Makmur'),
        ('koperasi', 'synergy123', 'Koperasi', 'Koperasi MBG Pusat'),
        ('dapur1', 'synergy123', 'Dapur', 'Dapur Umum Sejahtera'),
        ('sk 3A', '....', 'Koperasi', 'Admin Koperasi Khusus')";
    
    $pdo->exec($sql_insert);

    // Otomatis buat dompet
    $pdo->exec("INSERT INTO dompet (id_user, saldo) SELECT id_user, 0 FROM users");

    // Insert dummy katalog
    $pdo->exec("INSERT INTO katalog_bahan (id_user_petani, nama_bahan, deskripsi, harga, stok, satuan) VALUES 
        (1, 'Telur Ayam Omega', 'Telur ayam berkualitas tinggi kaya omega 3', 2000.00, 500, 'Butir'),
        (1, 'Sayur Sawi Hijau', 'Sawi segar langsung panen', 10000.00, 100, 'Kg')");

    echo "<h2 style='color:green; font-family:sans-serif;'>Database Rapi Berhasil Dibuat!</h2>";
    echo "<p>Tabel <b>katalog_bahan, pesanan, & escrow</b> telah siap digunakan.</p>";
    echo "<a href='index.php'>Kembali ke Login</a>";

} catch (PDOException $e) {
    die("Error Setup Database: " . $e->getMessage());
}
?>
