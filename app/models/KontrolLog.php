<?php

class KontrolLog
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDbConnection();
    }

    /**
     * Create a new control log entry.
     *
     * @param int $userId
     * @param string $aksi
     * @param string $keterangan
     * @return bool
     */
    public function create($userId, $aksi, $keterangan = '')
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO kontrol_logs (user_id, aksi, keterangan) VALUES (?, ?, ?)'
        );
        // User ID can be null if action is not performed by a logged-in user (e.g., system)
        $stmt->bindValue(1, $userId, $userId ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->execute([$userId, $aksi, $keterangan]);
        return true;
    }

    /**
     * Get all control logs with user information.
     *
     * @param int $limit
     * @return array
     */
    public function findAll($limit = 50)
    {
        $stmt = $this->pdo->prepare(
            'SELECT k.*, u.nama 
             FROM kontrol_logs k 
             LEFT JOIN users u ON k.user_id = u.id 
             ORDER BY k.created_at DESC 
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
