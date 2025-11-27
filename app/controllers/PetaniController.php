<?php

class PetaniController
{
    private $petaniModel;
    private $userModel;

    public function __construct()
    {
        $this->petaniModel = new Petani();
        $this->userModel = new User();
        $this->checkAuthAndRole();
    }
    
    /**
     * Check if the user is authenticated and is an admin.
     */
    private function checkAuthAndRole()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        if ($_SESSION['user_role'] !== 'admin') {
            // Tampilkan pesan error atau redirect ke halaman lain
            http_response_code(403);
            die('Akses ditolak. Anda bukan admin.');
        }
    }

    /**
     * Show list of all petani.
     */
    public function index()
    {
        $petani = $this->petaniModel->findAll();
        $data = [
            'petani' => $petani,
            'title' => 'Manajemen Petani'
        ];
        view('petani/index', $data);
    }
    
    /**
     * Show detail of a petani.
     */
    public function detail($userId)
    {
        $petani = $this->petaniModel->findByUserId($userId);
        if (!$petani) {
            http_response_code(404);
            die('Petani tidak ditemukan.');
        }
        
        $data = [
            'petani' => $petani,
            'title' => 'Detail Petani'
        ];
        view('petani/detail', $data);
    }

    /**
     * Show form to create a new petani.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Petani Baru',
            'action' => BASE_URL . 'petani/store',
            'petani' => null,
            'errors' => [],
            'input' => []
        ];
        view('petani/form', $data);
    }

    /**
     * Store a new petani in the database.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $no_hp = $_POST['no_hp'] ?? '';
            
            $errors = [];
            if (empty($nama) || empty($email) || empty($password)) {
                $errors[] = 'Nama, email, dan password wajib diisi.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Password minimal 8 karakter.';
            }
            if ($this->userModel->findByEmail($email)) {
                $errors[] = 'Email sudah terdaftar.';
            }

            if (empty($errors)) {
                // Buat user baru dengan role 'petani'
                $userId = $this->userModel->create($nama, $email, $password, 'petani');
                // Buat data petani
                $this->petaniModel->create($userId, $alamat, $no_hp);

                header('Location: ' . BASE_URL . 'petani');
                exit;
            } else {
                $data = [
                    'title' => 'Tambah Petani Baru',
                    'action' => BASE_URL . 'petani/store',
                    'petani' => null,
                    'errors' => $errors,
                    'input' => $_POST
                ];
                view('petani/form', $data);
            }
        }
    }
    
    /**
     * Show form to edit a petani.
     */
    public function edit($userId)
    {
        $petani = $this->petaniModel->findByUserId($userId);
        if (!$petani) {
            http_response_code(404);
            die('Petani tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Petani',
            'action' => BASE_URL . 'petani/update/' . $userId,
            'petani' => $petani,
            'errors' => [],
            'input' => $petani // Pre-fill form with existing data
        ];
        view('petani/form', $data);
    }

    /**
     * Update a petani in the database.
     */
    public function update($userId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama'] ?? '';
            $email = $_POST['email'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $no_hp = $_POST['no_hp'] ?? '';
            $password = $_POST['password'] ?? ''; // Untuk update password opsional
            $konfirmasi_password = $_POST['konfirmasi_password'] ?? ''; // Untuk konfirmasi password

            $currentUser = $this->userModel->findById($userId);
            $errors = [];

            if (empty($nama) || empty($email)) {
                $errors[] = 'Nama dan email wajib diisi.';
            }
            
            // Cek jika email diubah dan sudah ada yang pakai
            if ($email !== $currentUser['email'] && $this->userModel->findByEmail($email)) {
                $errors[] = 'Email sudah terdaftar.';
            }
            
            // Validasi password baru jika disediakan
            if (!empty($password)) {
                if (strlen($password) < 8) {
                    $errors[] = 'Password minimal 8 karakter.';
                }
                if ($password !== $konfirmasi_password) {
                    $errors[] = 'Konfirmasi password tidak cocok.';
                }
            }

            if (empty($errors)) {
                $this->userModel->update($userId, $nama, $email, 'petani');
                // Update password jika disediakan
                if (!empty($password)) {
                    // Hash password baru dan update
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->userModel->pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $stmt->execute([$hashedPassword, $userId]);
                }

                $this->petaniModel->update($userId, $alamat, $no_hp);

                header('Location: ' . BASE_URL . 'petani/detail/' . $userId);
                exit;
            } else {
                $petaniData = $this->petaniModel->findByUserId($userId);
                $data = [
                    'title' => 'Edit Petani',
                    'action' => BASE_URL . 'petani/update/' . $userId,
                    'petani' => $petaniData,
                    'errors' => $errors,
                    'input' => $_POST
                ];
                view('petani/form', $data);
            }
        }
    }

    /**
     * Delete a petani.
     */
    public function delete($userId)
    {
        // Di sini kita hanya menghapus user, dan data petani akan terhapus otomatis
        // karena ada ON DELETE CASCADE di foreign key.
        $this->userModel->delete($userId);
        header('Location: ' . BASE_URL . 'petani');
        exit;
    }
}
