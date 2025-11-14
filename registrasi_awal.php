<?php
// File: registrasi_awal.php
// (Hapus file ini setelah berhasil dipakai!)

include 'koneksi.php';

// --- Data Admin Pertama ---
$username_admin = 'admin';
$password_admin_plain = 'admin123'; // Password yg gampang diingat
$nama_admin = 'Administrator Web';
$level_admin = 'admin';
// -------------------------

// HASH passwordnya! Ini bagian penting.
$password_admin_hashed = password_hash($password_admin_plain, PASSWORD_DEFAULT);

// Buat query untuk memasukkan data ke database
$query = "INSERT INTO tbl_user (username, password, nama_lengkap, level) 
          VALUES (
            '$username_admin', 
            '$password_admin_hashed', 
            '$nama_admin', 
            '$level_admin'
          )";

// Eksekusi query
if (mysqli_query($koneksi, $query)) {
    echo "<h1>User Admin BERHASIL dibuat!</h1>";
    echo "<p>Silakan login di <strong>login.php</strong> dengan data:</p>";
    echo "<p>Username: <strong>$username_admin</strong></p>";
    echo "<p>Password: <strong>$password_admin_plain</strong></p>";
    echo "<hr>";
    echo "<h2><strong style='color:red;'>PENTING: SEGERA HAPUS FILE INI (registrasi_awal.php)!!</strong></h2>";
} else {
    echo "ERROR: Gagal membuat user. " . mysqli_error($koneksi);
}
