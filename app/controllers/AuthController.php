<?php

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Show the login page.
     */
    public function login()
    {
        // Jika pengguna sudah login, arahkan ke dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }
        
        // Tampilkan halaman login
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Handle login form submission.
     */
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Login berhasil
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_role'] = $user['role'];
                
                // Arahkan ke dashboard
                header('Location: ' . BASE_URL . 'dashboard');
                exit;
            } else {
                // Login gagal
                $error = 'Email atau password salah.';
                $data = ['error' => $error];
                extract($data);
                require_once __DIR__ . '/../views/auth/login.php';
            }
        }
    }

    /**
     * Handle logout.
     */
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
    
    /**
     * Show the registration page.
     * For simplicity, registration is open. In a real app, you might restrict this.
     */
    public function register()
    {
        // Tampilkan halaman registrasi
        require_once __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Handle registration form submission.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
            $role = 'petani'; // Default role untuk registrasi

            $errors = [];

            if (empty($nama) || empty($email) || empty($password) || empty($konfirmasi_password)) {
                $errors[] = 'Semua field wajib diisi.';
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

            if (empty($errors)) {
                $userId = $this->userModel->create($nama, $email, $password, $role);
                // Redirect to login page with a success message
                header('Location: ' . BASE_URL . 'auth/login?status=registered');
                exit;
            } else {
                // Tampilkan kembali form dengan error
                $data = ['errors' => $errors, 'input' => $_POST];
                extract($data);
                require_once __DIR__ . '/../views/auth/register.php';
            }
        }
    }
}
