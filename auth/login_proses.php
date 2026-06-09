<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // TWEAK: Mengubah pengecekan dari password_verify menjadi perbandingan biasa (===)
        if ($user && $password === $user['password']) {
            // Set session
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

            // Redirect berdasarkan role (Sudah pas dengan huruf kapital sesuai phpMyAdmin kamu)
            if ($user['role'] == 'Petani') {
                header("Location: ../dashboard_petani.php");
            } else if ($user['role'] == 'Koperasi') {
                header("Location: ../dashboard_koperasi.php");
            } else if ($user['role'] == 'Dapur') {
                header("Location: ../dashboard_dapur.php");
            }
            exit;
        } else {
            $_SESSION['error_msg'] = "Username atau password salah!";
            header("Location: ../index.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Terjadi kesalahan sistem.";
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>