<?php
header('Content-Type: application/json');

// ambil data dari GET
$suhu       = $_GET['suhu'] ?? null;
$kelembaban = $_GET['kelembaban'] ?? null;
$soil       = $_GET['soil'] ?? null;
// var_dump($suhu, $kelembaban, $soil);die();ss
// validasi
if ($suhu === null || $kelembaban === null || $soil === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Parameter tidak lengkap. Kirim suhu, kelembaban, soil melalui GET."
    ]);
    exit;
}

// koneksi database
require_once __DIR__ . '/../../config/config.php';
// $database = new Database();
// $db = $database->getConnection();
$db = getDbConnection();

$query = "INSERT INTO sensor_data (suhu, kelembaban, kelembaban_tanah) VALUES (:suhu, :kelembaban, :kelembaban_tanah)";
$stmt = $db->prepare($query);
$stmt->bindParam(':suhu', $suhu);
$stmt->bindParam(':kelembaban', $kelembaban);
$stmt->bindParam(':kelembaban_tanah', $soil);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan data"]);
}
