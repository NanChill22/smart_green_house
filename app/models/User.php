<?php

class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDbConnection();
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function findById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get all users.
     *
     * @return array
     */
    public function findAll()
    {
        $stmt = $this->pdo->query('SELECT id, nama, email, role, created_at FROM users');
        return $stmt->fetchAll();
    }

    /**
     * Create a new user.
     *
     * @param string $nama
     * @param string $email
     * @param string $password
     * @param string $role
     * @return int The ID of the newly created user.
     */
    public function create($nama, $email, $password, $role = 'petani')
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$nama, $email, $hashedPassword, $role]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update a user.
     *
     * @param int $id
     * @param string $nama
     * @param string $email
     * @param string $role
     * @return bool
     */
    public function update($id, $nama, $email, $role)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?'
        );
        return $stmt->execute([$nama, $email, $role, $id]);
    }
    
    /**
     * Delete a user.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Update user's name.
     *
     * @param int $id
     * @param string $nama
     * @return bool
     */
    public function updateName($id, $nama)
    {
        $stmt = $this->pdo->prepare('UPDATE users SET nama = ? WHERE id = ?');
        return $stmt->execute([$nama, $id]);
    }

    /**
     * Update user's password.
     *
     * @param int $id
     * @param string $password (plain text)
     * @return bool
     */
    public function updatePassword($id, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        return $stmt->execute([$hashedPassword, $id]);
    }
}
