<?php

class DashboardController
{
    private $sensorModel;
    private $laporanHarianModel;

    public function __construct()
    {
        $this->sensorModel = new Sensor();
        $this->laporanHarianModel = new LaporanHarian();
        $this->checkAuth();
    }
    
    /**
     * Check if the user is authenticated.
     */
    private function checkAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    /**
     * Show the main dashboard.
     */
    public function index()
    {
        $latestSensorData = $this->sensorModel->getLatestLog();
        $historicalData = $this->sensorModel->getHistoricalData(7); // Get 7 days of historical data
        $dailyReports = $this->laporanHarianModel->findAll();
        
        $data = [
            'latestSensorData' => $latestSensorData,
            'historicalData' => $historicalData, // Pass historical data to the view
            'dailyReports' => $dailyReports,
            'title' => 'Dashboard'
        ];

        view('dashboard', $data);
    }
    
    /**
     * Show the daily reports page.
     */
    public function laporanHarian()
    {
        $reports = $this->laporanHarianModel->findAll('DESC');
        $data = [
            'reports' => $reports,
            'title' => 'Laporan Harian'
        ];
        view('laporan_harian', $data);
    }

    /**
     * Show the sensor control page.
     */
    public function kontrolSensor()
    {
        $latestSensorData = $this->sensorModel->getLatestLog();
        $kontrolLogModel = new KontrolLog();
        $history = $kontrolLogModel->findAll(10); // Ambil 10 log terakhir

        $data = [
            'latestSensorData' => $latestSensorData,
            'history' => $history,
            'title' => 'Kontrol Sensor & Aktuator'
        ];
        view('kontrol_sensor', $data);
    }

    /**
     * Show the user profile page.
     */
    public function profile()
    {
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        $data = [
            'user' => $user,
            'title' => 'Ubah Profile'
        ];
        view('profile', $data);
    }

    /**
     * Handle profile update form submission.
     */
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $userId = $_SESSION['user_id'];
            
            $nama = $_POST['nama'] ?? '';
            $password = $_POST['password'] ?? '';
            $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
            
            $errors = [];

            if (empty($nama)) {
                $errors[] = 'Nama tidak boleh kosong.';
            }

            if (!empty($password)) {
                if (strlen($password) < 8) {
                    $errors[] = 'Password minimal 8 karakter.';
                }
                if ($password !== $konfirmasi_password) {
                    $errors[] = 'Konfirmasi password tidak cocok.';
                }
            }

            if (empty($errors)) {
                // Update nama
                $userModel->updateName($userId, $nama);
                $_SESSION['user_nama'] = $nama; // Update session

                // Update password jika diisi
                if (!empty($password)) {
                    $userModel->updatePassword($userId, $password);
                }

                header('Location: ' . BASE_URL . 'dashboard/profile?status=success');
                exit;
            } else {
                // Tampilkan kembali form dengan error
                $user = $userModel->findById($userId);
                $data = [
                    'user' => $user,
                    'title' => 'Ubah Profile',
                    'errors' => $errors
                ];
                view('profile', $data);
            }
        }
    }

    /**
     * Handle account deletion.
     */
    public function deleteAccount()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $userId = $_SESSION['user_id'];

            $userModel->delete($userId);
            
            // Logout dan hancurkan sesi
            session_unset();
            session_destroy();

            header('Location: ' . BASE_URL . 'auth/login?status=deleted');
            exit;
        }
    }
}
