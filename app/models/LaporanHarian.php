<?php

class LaporanHarian
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDbConnection();
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
