<?php

class LaporanHarian
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDbConnection();
    }

    public function generateTodayReport()
    {
        $tanggal = date('Y-m-d');

        // ===============================
        // 1. RATA-RATA SENSOR
        // ===============================
        $stmt = $this->pdo->prepare("
            SELECT 
                AVG(suhu) AS suhu_rata,
                AVG(kelembaban) AS kelembaban_rata
            FROM sensor_data
            WHERE DATE(created_at) = ?
        ");
        $stmt->execute([$tanggal]);
        $sensor = $stmt->fetch();

        $suhuRata = $sensor['suhu_rata'] ?? 0;
        $kelembabanRata = $sensor['kelembaban_rata'] ?? 0;

        // ===============================
        // 2. DURASI ON POMPA & KIPAS
        // ===============================
        $stmt2 = $this->pdo->prepare("
            SELECT 
                SUM(CASE WHEN kipas_status = 1 THEN 1 ELSE 0 END) * 5 AS kipas_durasi,
                SUM(CASE WHEN pompa_status = 1 THEN 1 ELSE 0 END) * 5 AS pompa_durasi
            FROM device_status
            WHERE DATE(last_updated) = ?
        ");
        $stmt2->execute([$tanggal]);
        $dev = $stmt2->fetch();

        $pompaDurasi = $dev['pompa_durasi'] ?? 0;
        $kipasDurasi = $dev['kipas_durasi'] ?? 0;

        // ===============================
        // 3. INSERT / UPDATE LAPORAN HARIAN
        // ===============================
        return $this->createOrUpdate(
            $tanggal,
            round($suhuRata, 2),
            round($kelembabanRata, 2),
            $pompaDurasi,
            $kipasDurasi
        );
    }


    /**
     * Get all daily reports.
     *
     * @param string $order 'ASC' or 'DESC' for the date
     * @return array
     */
    public function findAll($order = 'DESC')
    {
        // Validate order direction
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        
        $stmt = $this->pdo->query("SELECT * FROM laporan_harian ORDER BY tanggal {$order}");
        return $stmt->fetchAll();
    }

    /**
     * Find a report by a specific date.
     *
     * @param string $tanggal (format Y-m-d)
     * @return mixed
     */
    public function findByDate($tanggal)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM laporan_harian WHERE tanggal = ?');
        $stmt->execute([$tanggal]);
        return $stmt->fetch();
    }

    /**
     * Create or update a daily report.
     * This would typically be run by a cron job at the end of the day.
     *
     * @param string $tanggal (format Y-m-d)
     * @param float $suhuRata
     * @param float $kelembabanRata
     * @param int $pompaDurasi (in seconds)
     * @param int $kipasDurasi (in seconds)
     * @return bool
     */
    public function createOrUpdate($tanggal, $suhuRata, $kelembabanRata, $pompaDurasi, $kipasDurasi)
    {
        $existing = $this->findByDate($tanggal);

        if ($existing) {
            // Update
            $stmt = $this->pdo->prepare(
                'UPDATE laporan_harian 
                 SET suhu_rata = ?, kelembaban_rata = ?, pompa_durasi = ?, kipas_durasi = ? 
                 WHERE tanggal = ?'
            );
            return $stmt->execute([$suhuRata, $kelembabanRata, $pompaDurasi, $kipasDurasi, $tanggal]);
        } else {
            // Insert
            $stmt = $this->pdo->prepare(
                'INSERT INTO laporan_harian (tanggal, suhu_rata, kelembaban_rata, pompa_durasi, kipas_durasi) 
                 VALUES (?, ?, ?, ?, ?)'
            );
            return $stmt->execute([$tanggal, $suhuRata, $kelembabanRata, $pompaDurasi, $kipasDurasi]);
        }
    }
}
