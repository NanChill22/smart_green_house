<?php

// Load dependencies
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/LaporanHarian.php';
require_once __DIR__ . '/../app/lib/fpdf/fpdf/fpdf.php';

// Cek autentikasi (opsional, tapi disarankan)
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die('Akses ditolak. Silakan login terlebih dahulu.');
}

// Instantiate model
$laporanModel = new LaporanHarian();
$reports = $laporanModel->findAll('ASC'); // Ambil data urut tanggal

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'Laporan Harian Smart Green House', 0, 1, 'C');
$pdf->Ln(10);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, 'Tanggal', 1, 0, 'C');
$pdf->Cell(40, 10, 'Suhu Rata-rata (C)', 1, 0, 'C');
$pdf->Cell(50, 10, 'Kelembaban Rata-rata (%)', 1, 0, 'C');
$pdf->Cell(35, 10, 'Durasi Pompa (menit)', 1, 0, 'C');
$pdf->Cell(35, 10, 'Durasi Kipas (menit)', 1, 1, 'C');

// Table Body
$pdf->SetFont('Arial', '', 10);
if (!empty($reports)) {
    foreach ($reports as $report) {
        $pdf->Cell(30, 10, date('d/m/Y', strtotime($report['tanggal'])), 1, 0, 'C');
        $pdf->Cell(40, 10, number_format($report['suhu_rata'], 1), 1, 0, 'C');
        $pdf->Cell(50, 10, number_format($report['kelembaban_rata'], 1), 1, 0, 'C');
        $pdf->Cell(35, 10, round($report['pompa_durasi'] / 60), 1, 0, 'C');
        $pdf->Cell(35, 10, round($report['kipas_durasi'] / 60), 1, 1, 'C');
    }
} else {
    $pdf->Cell(190, 10, 'Tidak ada data laporan harian.', 1, 1, 'C');
}

// Output PDF
$pdf->Output('D', 'laporan_harian_smart_green_house_' . date('Y-m-d') . '.pdf');
exit;
