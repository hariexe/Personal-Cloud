Personal Cloud Storage
adalah aplikasi penyimpanan awan pribadi yang memungkinkan pengguna untuk mengunggah, mengunduh, dan mengelola file mereka sendiri. Aplikasi ini mendukung autentikasi pengguna dan menyediakan ruang penyimpanan yang terisolasi untuk setiap pengguna.

Fitur
Autentikasi Pengguna: Pengguna harus masuk untuk mengakses dan mengelola file mereka.


Unggah File: Pengguna dapat mengunggah file ke ruang penyimpanan pribadi mereka.
Unduh File: Pengguna dapat mengunduh file yang telah mereka unggah.
Hapus File: Pengguna dapat menghapus file yang tidak lagi mereka butuhkan.
Tampilan Penyimpanan: Pengguna dapat melihat berapa banyak ruang penyimpanan yang telah mereka gunakan.

Prasyarat
Web Server: Apache/Nginx
PHP: Versi 7.4 atau lebih baru
MySQL: Versi 5.7 atau lebih baru
Git: Versi 2.0 atau lebih baru

Instalasi
Langkah 1: Klon Repositori
```
git clone https://github.com/hariexe/Personal-Cloud.git
cd Personal-Cloud
```

Langkah 2: Konfigurasi Database
Buat database MySQL baru:
```
CREATE DATABASE drive;
```

Import skema database:
```
mysql -u root -p drive < database/schema.sql
```

Konfigurasikan koneksi database di config.php:
```
// config.php
<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'drive_user');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'drive');
?>
```
Langkah 3: Setel Izin Direktori
Pastikan direktori upload/ dapat ditulisi oleh server web:

```
sudo chown -R www-data:www-data upload/
sudo chmod -R 755 upload/
```
Langkah 4: Jalankan Aplikasi
Buka browser dan akses aplikasi melalui URL yang sesuai (misalnya, http://localhost/personal-cloud-storage).

Penggunaan
Login: Masuk dengan akun pengguna.
Unggah File: Pilih file untuk diunggah dan klik "Unggah".
Lihat dan Kelola File: Lihat daftar file yang telah diunggah. Klik tombol unduh untuk mengunduh file atau tombol hapus untuk menghapus file.

Mengatasi Masalah

File Tidak Terunggah
Pastikan direktori upload/ memiliki izin yang benar.
Periksa log kesalahan di upload.log untuk detail lebih lanjut.

Tidak Bisa Mengunduh atau Menghapus File
Periksa koneksi database dan pastikan entri file ada di database.
Pastikan file yang diunduh atau dihapus ada di direktori yang benar.

Lisensi
Proyek ini dilisensikan di bawah lisensi MIT - lihat file LICENSE untuk detail lebih lanjut.

Kontak
Untuk pertanyaan lebih lanjut, Anda dapat menghubungi kami di alghuroba313@protonmail.com.
