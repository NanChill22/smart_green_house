
-- =====================================================================
--  TABLE: device_status (current commanded status for ESP32)
-- =====================================================================
CREATE TABLE device_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pompa_status TINYINT(1) DEFAULT 0,
    kipas_status TINYINT(1) DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO device_status (pompa_status, kipas_status) VALUES (0, 0);
