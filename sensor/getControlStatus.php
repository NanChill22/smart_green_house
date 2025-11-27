<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

$response = [
    'mode' => 'auto',        // sementara auto, bisa ambil dari DB kalau sudah ada
    'batas_suhu' => 30,      // ambil dari DB jika sudah ada
    'batas_soil' => 40,      // ambil dari DB jika sudah ada
    'kipas' => 0,
    'pompa' => 0
];

try {
    $pdo = getDbConnection();

    $stmt = $pdo->query("SELECT * FROM control_settings ORDER BY id DESC LIMIT 1");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($status) {
        $response['kipas'] = (int)$status['kipas'];
        $response['pompa'] = (int)$status['pompa'];
    }

} catch (PDOException $e) {
    // error_log("ERR: " . $e->getMessage());
}

echo json_encode($response);
