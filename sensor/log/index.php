<?php
// Set header to return JSON
header('Content-Type: application/json');

// Include the database configuration
require_once __DIR__ . '/../../config/config.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the raw POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Check if JSON is valid and contains the required keys
    if (json_last_error() === JSON_ERROR_NONE && isset($data['suhu'], $data['kelembaban'], $data['soil'])) {
        try {
            // Get database connection
            $pdo = getDbConnection();

            // Prepare SQL statement to prevent SQL injection
            $sql = "INSERT INTO sensor_logs (suhu, kelembaban, kelembaban_tanah) VALUES (:suhu, :kelembaban, :kelembaban_tanah)";
            $stmt = $pdo->prepare($sql);

            // Bind parameters and execute
            $stmt->execute([
                ':suhu' => $data['suhu'],
                ':kelembaban' => $data['kelembaban'],
                ':kelembaban_tanah' => $data['soil'] // Map 'soil' to 'kelembaban_tanah'
            ]);

            $response = ['status' => 'success', 'message' => 'Sensor data saved successfully.'];

        } catch (PDOException $e) {
            // In production, you would log this error, not expose it.
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid or incomplete JSON data. Required keys: suhu, kelembaban, soil.';
    }
} else {
    $response['message'] = 'Only POST requests are accepted.';
}

// Return the response
echo json_encode($response);
?>
