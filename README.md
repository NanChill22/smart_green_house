# Smart Green House Monitoring

Ini adalah aplikasi web sederhana untuk memonitoring dan mengontrol kondisi "Smart Green House". Aplikasi ini dibangun menggunakan PHP native tanpa framework, dengan struktur dasar MVC (Model-View-Controller).

## Fitur

- **Dashboard**: Menampilkan data sensor terkini (suhu, kelembaban) dan status aktuator (pompa, kipas).
- **Kontrol Manual**: Memungkinkan pengguna untuk menyalakan/mematikan pompa dan kipas secara manual.
- **Riwayat Kontrol**: Mencatat semua aksi kontrol manual yang dilakukan oleh pengguna.
- **Laporan Harian**: Menampilkan rekap data harian (rata-rata suhu/kelembaban, total durasi aktuator).
- **Manajemen Petani**: (Hanya untuk Admin) CRUD (Create, Read, Update, Delete) untuk data pengguna dengan peran "petani".
- **Ekspor Laporan**: Mengunduh laporan harian dalam format CSV.
- **API Sederhana**: Endpoint untuk menerima data dari perangkat IoT (misalnya ESP32).

## Struktur Proyek

```
/
├── app/
│   ├── controllers/  (Logika aplikasi)
│   ├── models/       (Interaksi database)
│   └── views/        (File presentasi/template)
├── config/
│   └── config.php    (Konfigurasi database dan base URL)
├── public/
│   ├── index.php     (Entry point/front controller)
│   └── assets/       (CSS, JS, gambar - jika ada)
├── scripts/
│   └── export.php    (Skrip untuk ekspor CSV)
├── sql/
│   └── schema.sql    (Skema database)
└── README.md
```

## Instalasi

1.  **Clone Repositori**:
    ```bash
    git clone <url-repositori-anda> smart_green_house
    cd smart_green_house
    ```

2.  **Database**:
    - Buat database baru di MySQL/MariaDB dengan nama `smart_green_house`.
    - Impor file `sql/schema.sql` untuk membuat tabel-tabel yang diperlukan.

3.  **Konfigurasi**:
    - Buka file `config/config.php`.
    - Sesuaikan kredensial database (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`) jika diperlukan.

    
    - Pastikan `BASE_URL` terdefinisi dengan benar sesuai dengan lingkungan pengembangan Anda (misalnya, `http://localhost/smart_green_house/public/`).

4.  **Web Server**:
    - Arahkan web server Anda (misalnya Apache, Nginx) ke direktori `public`. Konfigurasi ini memastikan bahwa semua permintaan diarahkan melalui `index.php`.
    - Contoh konfigurasi Virtual Host Apache:
      ```apache
      <VirtualHost *:80>
          ServerName smartgreen.local
          DocumentRoot "C:/path/to/your/project/smart_green_house/public"
          <Directory "C:/path/to/your/project/smart_green_house/public">
              AllowOverride All
              Require all granted
          </Directory>
      </VirtualHost>
      ```
    - Pastikan `mod_rewrite` diaktifkan.

5.  **Menjalankan Aplikasi**:
    - Buka browser dan akses URL yang telah Anda siapkan (misalnya, `http://smartgreen.local`).
    - Halaman login akan ditampilkan.

## Akun Awal

Untuk login pertama kali, Anda perlu membuat akun admin secara manual di database atau menggunakan fitur registrasi untuk membuat akun petani, lalu mengubah `role`-nya menjadi `admin` di tabel `users`.

```sql
-- Contoh membuat admin baru
INSERT INTO `users` (`nama`, `email`, `password`, `role`) 
VALUES ('Admin', 'admin@example.com', '$2y$10$your_hashed_password_here', 'admin');
```
Ganti `$2y$10$your_hashed_password_here` dengan hash password yang aman. Anda bisa membuatnya menggunakan skrip PHP: `echo password_hash('password_anda', PASSWORD_DEFAULT);`.

## Endpoint API Sensor

- **URL**: `BASE_URL/sensor/log`
- **Metode**: `POST`
- **Body (JSON)**:
  ```json
  {
    "suhu": 25.5,
    "kelembaban": 60.2,
    "pompa_status": 0,
    "kipas_status": 1,
    "mode": "otomatis"
  }
  ```
Endpoint ini digunakan oleh perangkat keras (misalnya ESP32) untuk mengirimkan data sensor ke aplikasi.
