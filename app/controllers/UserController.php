<?php

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->checkAuthAndRole();
    }
    
    /**
     * Memeriksa apakah pengguna diautentikasi dan merupakan admin.
     */
    private function checkAuthAndRole()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        if ($_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die('Akses ditolak. Anda bukan admin.');
        }
    }

    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index()
    {
        $users = $this->userModel->findAll();
        $data = [
            'users' => $users,
            'title' => 'Manajemen Pengguna'
        ];
        view('user/index', $data);
    }
    
    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Pengguna Baru',
            'action' => BASE_URL . 'user/store',
            'user' => null,
            'errors' => [],
            'input' => []
        ];
        view('user/form', $data);
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
            $role = $_POST['role'] ?? 'petani';
            
            $errors = [];
            if (empty($nama) || empty($email) || empty($password)) {
                $errors[] = 'Nama, email, dan password wajib diisi.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Password minimal 8 karakter.';
            }
            if ($password !== $konfirmasi_password) {
                $errors[] = 'Konfirmasi password tidak cocok.';
            }
            if ($this->userModel->findByEmail($email)) {
                $errors[] = 'Email sudah terdaftar.';
            }
            if (!in_array($role, ['admin', 'petani'])) {
                $errors[] = 'Role tidak valid.';
            }

            if (empty($errors)) {
                $this->userModel->create($nama, $email, $password, $role);
                header('Location: ' . BASE_URL . 'user');
                exit;
            } else {
                $data = [
                    'title' => 'Tambah Pengguna Baru',
                    'action' => BASE_URL . 'user/store',
                    'user' => null,
                    'errors' => $errors,
                    'input' => $_POST
                ];
                view('user/form', $data);
            }
        }
    }
    
    /**
     * Menampilkan form untuk mengedit pengguna.
     */
    public function edit($id)
    {
        $user = $this->userModel->findById($id);
        if (!$user) {
            http_response_code(404);
            die('Pengguna tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Pengguna',
            'action' => BASE_URL . 'user/update/' . $id,
            'user' => $user,
            'errors' => [],
            'input' => $user
        ];
        view('user/form', $data);
    }

    /**
     * Memperbarui pengguna di database.
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'petani';
            $password = $_POST['password'] ?? '';
            $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

            $currentUser = $this->userModel->findById($id);
            $errors = [];

            if (empty($nama) || empty($email)) {
                $errors[] = 'Nama dan email wajib diisi.';
            }
            if ($email !== $currentUser['email'] && $this->userModel->findByEmail($email)) {
                $errors[] = 'Email sudah terdaftar.';
            }
            if (!in_array($role, ['admin', 'petani'])) {
                $errors[] = 'Role tidak valid.';
            }

            if (!empty($password)) {
                if (strlen($password) < 8) {
                    $errors[] = 'Password baru minimal 8 karakter.';
                }
                if ($password !== $konfirmasi_password) {
                    $errors[] = 'Konfirmasi password tidak cocok.';
                }
            }

            if (empty($errors)) {
                $this->userModel->update($id, $nama, $email, $role);
                if (!empty($password)) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = getDbConnection()->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $stmt->execute([$hashedPassword, $id]);
                }
                header('Location: ' . BASE_URL . 'user');
                exit;
            } else {
                $data = [
                    'title' => 'Edit Pengguna',
                    'action' => BASE_URL . 'user/update/' . $id,
                    'user' => $currentUser,
                    'errors' => $errors,
                    'input' => $_POST
                ];
                view('user/form', $data);
            }
        }
    }

    /**
     * Menghapus pengguna.
     */
    public function delete($id)
    {
        // Pencegahan agar admin tidak bisa menghapus akunnya sendiri
        if ($id == $_SESSION['user_id']) {
            die('Anda tidak bisa menghapus akun Anda sendiri.');
        }
        $this->userModel->delete($id);
        header('Location: ' . BASE_URL . 'user');
        exit;
    }
}
