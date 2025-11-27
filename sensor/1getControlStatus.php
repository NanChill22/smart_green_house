<?php
header('Content-Type: application/json');

// Include the database configuration
require_once __DIR__ . '/../config/config.php';

$controlStatus = [
    'kipas1' => false,
    'kipas2' => false,
    'pompa' => false
];

try {
    // Get database connection
    $pdo = getDbConnection();

    // Fetch the latest device status (assuming one row in the table)
    $stmt = $pdo->query("SELECT pompa_status, kipas_status FROM device_status ORDER BY id DESC LIMIT 1");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($status) {
        // Map database values (0 or 1) to booleans for the JSON response
        $controlStatus['pompa'] = (bool)$status['pompa_status'];
        // Both fans are controlled by a single status
        $controlStatus['kipas1'] = (bool)$status['kipas_status'];
        $controlStatus['kipas2'] = (bool)$status['kipas_status'];
    }

} catch (PDOException $e) {
    // If there's a database error, we will return the default 'all off' status.
    // In a production environment, you should log this error.
    // error_log("GetControlStatus Error: " . $e->getMessage());
}

echo json_encode($controlStatus);
?>