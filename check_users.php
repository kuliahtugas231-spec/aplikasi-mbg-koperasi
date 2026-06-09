<?php
require 'config/koneksi.php';
$stmt = $db->query("SELECT * FROM users");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
