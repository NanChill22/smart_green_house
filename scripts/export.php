<?php

// Load dependencies
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/LaporanHarian.php';

// Cek autentikasi (opsional, tapi disarankan)
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die('Akses ditolak. Silakan login terlebih dahulu.');
}

// Instantiate model
$laporanModel = new LaporanHarian();
$reports = $laporanModel->findAll('ASC'); // Ambil data urut tanggal

// Set header untuk download file CSV
$filename = 'laporan_harian_smart_green_house_' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis header kolom
fputcsv($output, [
    'Tanggal',
    'Suhu Rata-rata (°C)',
    'Kelembaban Rata-rata (%)',
    'Durasi Pompa (detik)',
    'Durasi Kipas (detik)'
]);

// Tulis data baris per baris
if (!empty($reports)) {
    foreach ($reports as $report) {
        fputcsv($output, [
            $report['tanggal'],
            number_format($report['suhu_rata'], 2, ',', '.'),
            number_format($report['kelembaban_rata'], 2, ',', '.'),
            $report['pompa_durasi'],
            $report['kipas_durasi']
        ]);
    }
}

fclose($output);
exit;
