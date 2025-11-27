<?php

class DeviceStatus {
    private $conn;
    private $table_name = "device_status";

    public $id;
    public $pompa_status;
    public $kipas_status;
    public $last_updated;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read current device status
    public function read() {
        $query = "SELECT id, pompa_status, kipas_status, last_updated FROM " . $this->table_name . " LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id = $row['id'];
            $this->pompa_status = $row['pompa_status'];
            $this->kipas_status = $row['kipas_status'];
            $this->last_updated = $row['last_updated'];
        }

        return $stmt;
    }

    // Update pompa status
    public function updatePompaStatus($status) {
        $query = "UPDATE " . $this->table_name . " SET pompa_status = :status WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $status = htmlspecialchars(strip_tags($status));

        // bind new values
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update kipas status
    public function updateKipasStatus($status) {
        $query = "UPDATE " . $this->table_name . " SET kipas_status = :status WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $status = htmlspecialchars(strip_tags($status));

        // bind new values
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
