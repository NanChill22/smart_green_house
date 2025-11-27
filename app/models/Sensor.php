<?php

class Sensor
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDbConnection();
    }

    /**
     * Get the latest sensor log.
     *
     * @return mixed
     */
    public function getLatestLog()
    {
        $stmt = $this->pdo->query('SELECT suhu, kelembaban, kelembaban_tanah, pompa_status, kipas_status, mode, created_at FROM sensor_logs ORDER BY created_at DESC LIMIT 1');
        return $stmt->fetch();
    }

    /**
     * Get all sensor logs, newest first.
     *
     * @param int $limit
     * @return array
     */
    public function getAllLogs($limit = 100)
    {
        $stmt = $this->pdo->prepare('SELECT suhu, kelembaban, kelembaban_tanah, pompa_status, kipas_status, mode, created_at FROM sensor_logs ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Create a new sensor log entry.
     * This would typically be called by the sensor hardware.
     *
     * @param float $suhu
     * @param float $kelembaban
     * @param float $kelembabanTanah
     * @param int $pompaStatus
     * @param int $kipasStatus
     * @param string $mode
     * @return bool
     */
    public function createLog($suhu, $kelembaban, $kelembabanTanah, $pompaStatus, $kipasStatus, $mode)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sensor_logs (suhu, kelembaban, kelembaban_tanah, pompa_status, kipas_status, mode) VALUES (?, ?, ?, ?, ?, ?)'
        );
        return $stmt->execute([$suhu, $kelembaban, $kelembabanTanah, $pompaStatus, $kipasStatus, $mode]);
    }

    /**
     * Create a new sensor log entry with only sensor readings.
     * Lets the database handle defaults for status and mode.
     *
     * @param float $suhu
     * @param float $kelembaban
     * @param float $kelembabanTanah
     * @return bool
     */
    public function createSensorReading($suhu, $kelembaban, $kelembabanTanah)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sensor_logs (suhu, kelembaban, kelembaban_tanah) VALUES (?, ?, ?)'
        );
        return $stmt->execute([$suhu, $kelembaban, $kelembabanTanah]);
    }
    
    
    /**
     * Updates the control status (pump, fan, mode).
     * This is for manual control.
     *
     * @param int $pompaStatus
     * @param int $kipasStatus
     * @param string $mode
     * @return bool
     */
    public function updateControlStatus($pompaStatus, $kipasStatus, $mode = 'manual')
    {
        // This function doesn't update a specific log, but rather would be used
        // to set the state for the hardware. For simplicity in this web app,
        // we'll insert a new log to reflect the manual change.
        $latest = $this->getLatestLog();
        $suhu = $latest['suhu'] ?? 0; // Use last known temperature
        $kelembaban = $latest['kelembaban'] ?? 0; // Use last known air humidity
        $kelembabanTanah = $latest['kelembaban_tanah'] ?? 0; // Use last known soil humidity

        return $this->createLog($suhu, $kelembaban, $kelembabanTanah, $pompaStatus, $kipasStatus, $mode);
    }

    /**
     * Get historical sensor data for a specified number of days.
     *
     * @param int $days Number of days to look back.
     * @return array
     */
    public function getHistoricalData($days = 7)
    {
        $stmt = $this->pdo->prepare('SELECT suhu, kelembaban, kelembaban_tanah, created_at FROM sensor_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) ORDER BY created_at ASC');
        $stmt->bindValue(1, $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
