<?php

class Petani
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDbConnection();
    }

    /**
     * Get all petani data with user information.
     *
     * @return array
     */
    public function findAll()
    {
        $stmt = $this->pdo->query(
            'SELECT p.*, u.nama, u.email, u.role FROM petani p JOIN users u ON p.user_id = u.id'
        );
        return $stmt->fetchAll();
    }

    /**
     * Find a single petani by their user ID.
     *
     * @param int $userId
     * @return mixed
     */
    public function findByUserId($userId)
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, u.nama, u.email, u.role FROM petani p JOIN users u ON p.user_id = u.id WHERE p.user_id = ?'
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Create a new petani profile.
     *
     * @param int $userId
     * @param string $alamat
     * @param string $noHp
     * @return bool
     */
    public function create($userId, $alamat, $noHp)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO petani (user_id, alamat, no_hp) VALUES (?, ?, ?)'
        );
        return $stmt->execute([$userId, $alamat, $noHp]);
    }

    /**
     * Update a petani's profile.
     *
     * @param int $userId
     * @param string $alamat
     * @param string $noHp
     * @return bool
     */
    public function update($userId, $alamat, $noHp)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE petani SET alamat = ?, no_hp = ? WHERE user_id = ?'
        );
        return $stmt->execute([$alamat, $noHp, $userId]);
    }
    
    /**
     * Delete a petani profile.
     *
     * @param int $userId
     * @return bool
     */
    public function delete($userId)
    {
        $stmt = $this->pdo->prepare('DELETE FROM petani WHERE user_id = ?');
        return $stmt->execute([$userId]);
    }
}
