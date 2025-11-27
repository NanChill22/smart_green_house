<?php

 // Tambahkan ini

class SensorController
{
    private $pdo; // Tambahkan ini
    private $sensorModel;
    private $kontrolLogModel;
    private $deviceStatusModel; // Tambahkan ini

    public function __construct()
    {
        $this->pdo = getDbConnection(); // Inisialisasi koneksi DB
        $this->sensorModel = new Sensor();
        $this->kontrolLogModel = new KontrolLog();
        $this->deviceStatusModel = new DeviceStatus($this->pdo); // Teruskan koneksi DB ke model baru
    }

    /**
     * Endpoint to receive sensor data.
     * This would be called by the IoT device (e.g., ESP32).
     *
     * Data is expected in the POST body as JSON.
     * {
     *   "suhu": 25.5,
     *   "kelembaban": 60.2,
     *   "kelembaban_tanah": 70.0,
     *   "pompa_status": 0,
     *   "kipas_status": 1,
     *   "mode": "otomatis"
     * }
     */
    public function log()
    {
        // Hanya izinkan metode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Hanya metode POST yang diizinkan.']);
            return;
        }

        // Ambil data JSON dari body request
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Validasi data dari Arduino
        if (!isset($data['suhu']) || !isset($data['kelembaban']) || !isset($data['soil'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Data tidak lengkap. Field yang dibutuhkan: suhu, kelembaban, soil.']);
            return;
        }

        try {
            // Gunakan metode baru untuk menyimpan data sensor saja
            $this->sensorModel->createSensorReading(
                (float)$data['suhu'],
                (float)$data['kelembaban'],
                (float)$data['soil'] // Map 'soil' to 'kelembaban_tanah'
            );

            http_response_code(201); // Created
            echo json_encode(['success' => 'Log sensor dari perangkat berhasil disimpan.']);
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            // Sebaiknya log error ini di sisi server
            echo json_encode(['error' => 'Terjadi kesalahan saat menyimpan data sensor: ' . $e->getMessage()]);
        }
    }

    /**
     * Endpoint to get the current control status for ESP32 to poll.
     */
    public function getControlStatus()
    {
        header('Content-Type: application/json');

        $this->deviceStatusModel->read(); // Read the single row for device status

        if ($this->deviceStatusModel->id) {
            // Format to match Arduino's expectation
            echo json_encode([
                'kipas1' => (bool)$this->deviceStatusModel->kipas_status,
                'kipas2' => (bool)$this->deviceStatusModel->kipas_status, // Both fans controlled by one status
                'pompa'  => (bool)$this->deviceStatusModel->pompa_status
            ]);
        } else {
            // Provide a default off state if not found, to prevent Arduino errors
            echo json_encode([
                'kipas1' => false,
                'kipas2' => false,
                'pompa'  => false
            ]);
        }
        exit;
    }

    /**
     * Handle manual control commands from the web interface.
     * Updates the desired state in the device_status table.
     */
    public function updateControl() // Nama metode diubah dari kontrol() menjadi updateControl()
    {
        session_start();
        // Cek autentikasi
        if (!isset($_SESSION['user_id'])) {
             http_response_code(403);
             echo json_encode(['success' => false, 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
             return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $device = isset($_POST['device']) ? $_POST['device'] : '';
            $status = isset($_POST['status']) ? (int)$_POST['status'] : 0; // 0 or 1
            $userId = $_SESSION['user_id'];
            $logAksi = '';
            $logKeterangan = '';
            $updateSuccess = false;

            $this->deviceStatusModel->read(); // Ensure deviceStatusModel has the ID

            if (!$this->deviceStatusModel->id) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Device control status not initialized.']);
                exit;
            }

            if ($device === 'pompa') {
                $updateSuccess = $this->deviceStatusModel->updatePompaStatus($status);
                $logAksi = $status ? 'pompa_on' : 'pompa_off';
                $logKeterangan = $status ? 'Menyalakan pompa secara manual.' : 'Mematikan pompa secara manual.';
            } elseif ($device === 'kipas') {
                $updateSuccess = $this->deviceStatusModel->updateKipasStatus($status);
                $logAksi = $status ? 'kipas_on' : 'kipas_off';
                $logKeterangan = $status ? 'Menyalakan kipas secara manual.' : 'Mematikan kipas secara manual.';
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Perangkat tidak valid.']);
                exit;
            }

            if ($updateSuccess) {
                // Catat aksi ke dalam log kontrol
                $this->kontrolLogModel->create($userId, $logAksi, $logKeterangan);
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Perintah kontrol berhasil dikirim.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status perangkat.']);
            }
            exit;
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Hanya metode POST yang diizinkan.']);
            exit;
        }
    }

    /**
     * Endpoint to get the latest sensor data as JSON.
     */
    public function getLatest()
    {
        // Set header untuk memberitahu klien bahwa ini adalah JSON
        header('Content-Type: application/json');

        $latestData = $this->sensorModel->getLatestLog();

        if ($latestData) {
            echo json_encode(['success' => true, 'data' => $latestData]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No sensor data available.']);
        }
        exit;
    }

    /**
     * Endpoint to get the latest control history as JSON.
     */
    public function getControlHistory()
    {
        header('Content-Type: application/json');
        // session_start(); // Removed: session_start() is already called globally in index.php

        $history = $this->kontrolLogModel->findAll(10); // Ambil 10 log terakhir

        if ($history) {
            echo json_encode(['success' => true, 'data' => $history]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No control history available.']);
        }
        exit;
    }

    /**
     * Endpoint to get historical sensor data as JSON.
     * Optional parameter 'days' to specify how many days back to fetch.
     */
    public function getHistoricalData()
    {
        header('Content-Type: application/json');

        $days = isset($_GET['days']) ? (int)$_GET['days'] : 7; // Default to 7 days

        $historicalData = $this->sensorModel->getHistoricalData($days);

        if ($historicalData) {
            echo json_encode(['success' => true, 'data' => $historicalData]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No historical sensor data available.']);
        }
        exit;
    }
}
