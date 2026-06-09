<?php
// Konfigurasi Koneksi Database Menggunakan PDO
$host = 'localhost';
$username = 'root'; // XAMPP default
$password = '';     // XAMPP default tanpa password
$dbname = 'koperasi_mbg';

try {
    // Membuat instance PDO
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set PDO Error Mode ke Exception agar mudah mendeteksi error query
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode ke Associative Array
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Tangkap error jika koneksi gagal
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>
