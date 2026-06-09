<?php
$pdo = new PDO("mysql:host=localhost;dbname=koperasi_mbg", "root", "");
$stmt = $pdo->query("DESCRIBE users");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $pdo->query("DESCRIBE dompet");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
?>
