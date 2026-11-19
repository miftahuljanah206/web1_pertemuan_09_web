<?php
$host = 'localhost';
$db   = 'web1_pertemuan09';
$user = 'root';
$pass = ''; // Sesuaikan jika MySQL Anda memakai password.

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    exit("Koneksi database gagal. Pastikan MySQL aktif dan database web1_pertemuan09 sudah dibuat. Detail: " .
         htmlspecialchars($e->getMessage()));
}
