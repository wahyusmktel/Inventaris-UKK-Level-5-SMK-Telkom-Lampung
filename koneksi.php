<?php
// File: koneksi.php
// Deskripsi: Menghubungkan aplikasi ke database MySQL

// 1. Variabel untuk koneksi database
$db_host = "localhost"; // Biasanya "localhost" kalau di XAMPP
$db_user = "root";      // User default database (biasanya "root")
$db_pass = "";          // Password default database (biasanya kosong)
$db_name = "db_inventaris"; // Nama database yang sudah kita buat

// 2. Perintah untuk menghubungkan ke database
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// 3. Pengecekan Koneksi
// Jika koneksi GAGAL, hentikan program dan tampilkan pesan error
if (!$koneksi) {
    // die() berfungsi untuk menghentikan skrip PHP
    die("KoneKSI GAGAL: " . mysqli_connect_error());
}

// Jika skrip berlanjut (tidak mati/die), berarti koneksi BERHASIL.
// Kita tidak perlu menampilkan pesan "Koneksi Berhasil" di sini,
// karena file ini akan sering kita 'include' di file lain.
// Kalau ada pesannya, nanti malah mengganggu tampilan.
