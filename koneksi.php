<?php

declare(strict_types=1);

$host     = "localhost";
$user     = "root";
$password = "";
$database = "db_recycle"; // sesuaikan dengan nama database Anda

// Aktifkan pelaporan error mysqli agar mudah dideteksi saat pengembangan
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Koneksi database gagal: " . $e->getMessage());
    http_response_code(500);
    exit("Koneksi ke database gagal.");
}