-- =====================================================================
--  DATABASE: smart_green_house
-- =====================================================================

CREATE DATABASE IF NOT EXISTS smart_green_house
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE smart_green_house;

-- =====================================================================
--  TABLE: users (admin & petani)
-- =====================================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','petani') DEFAULT 'petani',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================================
--  TABLE: petani (biodata petani)
-- =====================================================================
CREATE TABLE petani (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    alamat TEXT,
    no_hp VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================================
--  TABLE: sensor_logs (log data sensor)
-- =====================================================================
CREATE TABLE sensor_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    suhu FLOAT,
    kelembaban FLOAT,        -- Kelembaban Udara
    kelembaban_tanah FLOAT,  -- Kelembaban Tanah
    pompa_status TINYINT(1) DEFAULT 0,
    kipas_status TINYINT(1) DEFAULT 0,
    mode VARCHAR(20) DEFAULT 'otomatis',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================================
--  TABLE: kontrol_logs (riwayat kontrol manual)
-- =====================================================================
CREATE TABLE kontrol_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    aksi VARCHAR(50),        -- contoh: pompa_on, kipas_off
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =====================================================================
--  TABLE: laporan_harian (rekap per hari)
-- =====================================================================
CREATE TABLE laporan_harian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    suhu_rata FLOAT,
    kelembaban_rata FLOAT,
    pompa_durasi INT DEFAULT 0,
    kipas_durasi INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
