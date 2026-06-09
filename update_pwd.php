<?php
require 'config/koneksi.php';
$db->exec("UPDATE users SET password = 'synergy123'");
echo "Updated!";
?>
