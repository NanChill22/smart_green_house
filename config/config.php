<?php

// =====================================================================
//  DATABASE CONNECTION
// =====================================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smart_green_house');

// =====================================================================
//  BASE URL
// =====================================================================

// Definisikan base URL aplikasi Anda di sini.
// Pastikan diakhiri dengan garis miring (/).
// This points to the project root, as index.php is in the root.
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));


// =====================================================================
//  DATABASE HELPER
// =====================================================================

/**
 * Fungsi untuk membuat koneksi ke database menggunakan PDO.
 *
 * @return PDO
 */
function getDbConnection()
{
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Pada aplikasi production, log error ini dan tampilkan pesan umum.
        // Jangan pernah menampilkan detail error ke pengguna.
        die('Koneksi database gagal: ' . $e->getMessage());
    }
}

// =====================================================================
//  VIEW HELPER
// =====================================================================

/**
 * Fungsi untuk memuat view dengan layout header dan footer.
 *
 * @param string $view Nama file view di folder 'app/views/'
 * @param array $data Data yang akan diekstrak menjadi variabel di view
 * @return void
 */
function view($view, $data = [])
{
    // Ekstrak data agar bisa diakses sebagai variabel di dalam view
    extract($data);

    // Path ke file view
    $viewFile = __DIR__ . '/../app/views/' . $view . '.php';

    // Memuat header, view, dan footer
    require_once __DIR__ . '/../app/views/layout/header.php';
    
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        // Handle jika file view tidak ditemukan
        echo "View not found: {$view}.php";
    }
    
    require_once __DIR__ . '/../app/views/layout/footer.php';
}
