<?php
// scripts/generate_daily_report.php

// Set timezone to avoid date/time issues
date_default_timezone_set('Asia/Jakarta');

// Include config
require_once __DIR__ . '/../config/config.php';

echo "Memulai proses pembuatan laporan harian...\n";

try {
    $pdo = getDbConnection();
    
    // Tentukan tanggal yang akan diproses (kemarin)
    $targetDate = date('Y-m-d', strtotime('-1 day'));
    echo "Tanggal target untuk laporan: $targetDate\n";

    // 1. Periksa apakah laporan untuk tanggal tersebut sudah ada
    $checkSql = "SELECT id FROM laporan_harian WHERE tanggal = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$targetDate]);
    
    if ($checkStmt->fetch()) {
        echo "Laporan untuk tanggal $targetDate sudah ada. Proses dibatalkan untuk menghindari duplikat.\n";
        exit;
    }

    // 2. Hitung data agregat dari sensor_logs
    // Durasi dihitung dengan asumsi setiap log mewakili interval 5 detik (sesuai delay di ESP32)
    $aggSql = "
        SELECT
            AVG(suhu) as suhu_rata,
            AVG(kelembaban) as kelembaban_rata,
            (SUM(kipas_status) * 5) as kipas_durasi_detik,
            (SUM(pompa_status) * 5) as pompa_durasi_detik
        FROM
            sensor_logs
        WHERE
            DATE(created_at) = ?
    ";
    
    $aggStmt = $pdo->prepare($aggSql);
    $aggStmt->execute([$targetDate]);
    $reportData = $aggStmt->fetch(PDO::FETCH_ASSOC);

    // Periksa apakah ada data untuk diproses
    if (!$reportData || is_null($reportData['suhu_rata'])) {
        echo "Tidak ada data sensor yang ditemukan untuk tanggal $targetDate. Tidak ada laporan yang dibuat.\n";
        exit;
    }

    // 3. Masukkan data agregat ke tabel laporan_harian
    $insertSql = "
        INSERT INTO laporan_harian (tanggal, suhu_rata, kelembaban_rata, kipas_durasi, pompa_durasi)
        VALUES (:tanggal, :suhu_rata, :kelembaban_rata, :kipas_durasi, :pompa_durasi)
    ";
    
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([
        ':tanggal' => $targetDate,
        ':suhu_rata' => $reportData['suhu_rata'],
        ':kelembaban_rata' => $reportData['kelembaban_rata'],
        ':kipas_durasi' => $reportData['kipas_durasi_detik'], // Durasi dalam detik
        ':pompa_durasi' => $reportData['pompa_durasi_detik']  // Durasi dalam detik
    ]);

    echo "Laporan harian untuk tanggal $targetDate berhasil dibuat!\n";
    echo " - Suhu Rata-rata: " . number_format($reportData['suhu_rata'], 2) . " °C\n";
    echo " - Kelembaban Rata-rata: " . number_format($reportData['kelembaban_rata'], 2) . " %\n";
    echo " - Durasi Kipas: " . $reportData['kipas_durasi_detik'] . " detik\n";
    echo " - Durasi Pompa: " . $reportData['pompa_durasi_detik'] . " detik\n";

} catch (PDOException $e) {
    die("Koneksi atau query database gagal: " . $e->getMessage() . "\n");
}

?>
