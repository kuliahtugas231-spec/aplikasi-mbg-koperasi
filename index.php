<?php
session_start();
// Jika sudah login, redirect ke dashboard masing-masing
if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'Petani') header("Location: dashboard_petani.php");
    else if($_SESSION['role'] == 'Koperasi') header("Location: dashboard_koperasi.php");
    else header("Location: dashboard_dapur.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koperasi MBG (Synergy Team)</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div style="margin-bottom: 1.5rem;">
            <!-- Icon placeholder for Synergy Team -->
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="url(#gradient)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <defs>
                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#4F46E5" />
                        <stop offset="100%" stop-color="#8B5CF6" />
                    </linearGradient>
                </defs>
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
        </div>
        
        <h1>Koperasi MBG</h1>
        <p>Rembug Nusantara by Synergy Team</p>

        <?php
        if(isset($_SESSION['error_msg'])) {
            echo '<div class="alert alert-error">'.$_SESSION['error_msg'].'</div>';
            unset($_SESSION['error_msg']);
        }
        ?>

        <form action="auth/login_proses.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Contoh: petani1" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required autocomplete="off">
            </div>
            <button type="submit" class="btn-primary">Masuk ke Sistem</button>
        </form>


    </div>
</div>

</body>
</html>
