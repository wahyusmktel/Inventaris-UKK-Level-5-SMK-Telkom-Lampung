<?php
// File: proses_login.php
// Deskripsi: Memproses data login, mengecek ke database, dan membuat session

// 1. Mulai Session
// Session adalah 'penyimpanan' di sisi server untuk menyimpan data
// yang bisa diakses di berbagai halaman. Kita pakai untuk simpan status login.
session_start();

// 2. Hubungkan ke Database
// Panggil file koneksi.php yang sudah kita buat sebelumnya
include 'koneksi.php';

// 3. Ambil Data dari Form Login (login.php)
// Kita ambil data yang dikirim pakai metode 'POST'
// 'name="username"' akan jadi $_POST['username']
// 'name="password"' akan jadi $_POST['password']

// mysqli_real_escape_string adalah fungsi keamanan dasar
// untuk mencegah karakter 'aneh' (mencegah SQL Injection sederhana)
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

// 4. Buat Query SQL untuk Mencari User
// Kita cari di tabel 'tbl_user' di mana kolom 'username' cocok dengan yg diinput
$sql = "SELECT * FROM tbl_user WHERE username = '$username'";
$query = mysqli_query($koneksi, $sql);

// 5. Cek Apakah User Ditemukan
// mysqli_num_rows() menghitung jumlah baris hasil query.
// Jika > 0, berarti usernamenya ada.
if (mysqli_num_rows($query) > 0) {

    // User ditemukan, kita ambil datanya sebagai array
    $data_user = mysqli_fetch_assoc($query);

    // 6. Verifikasi Password
    // Kita gunakan password_verify() untuk membandingkan password yang diinput ($password)
    // dengan password HASH yang ada di database ($data_user['password']).
    // Ini JAUH LEBIH AMAN daripada membandingkan teks biasa.
    if (password_verify($password, $data_user['password'])) {

        // ---- LOGIN BERHASIL ----

        // 7. Simpan Data User ke dalam Session
        // Kita simpan data penting user ke session
        $_SESSION['id_user'] = $data_user['id_user'];
        $_SESSION['username'] = $data_user['username'];
        $_SESSION['nama_lengkap'] = $data_user['nama_lengkap'];
        $_SESSION['level'] = $data_user['level'];
        $_SESSION['status_login'] = true; // Ini penanda penting!

        // 8. Alihkan (Redirect) ke Halaman Dashboard
        // header() berfungsi untuk mengalihkan user ke halaman lain
        header("Location: index.php");
        exit; // Hentikan skrip setelah redirect

    } else {
        // ---- LOGIN GAGAL (Password Salah) ----
        // Alihkan kembali ke halaman login dengan pesan error
        header("Location: login.php?pesan=gagal_password");
        exit;
    }
} else {
    // ---- LOGIN GAGAL (Username tidak ditemukan) ----
    // Alihkan kembali ke halaman login dengan pesan error
    header("Location: login.php?pesan=gagal_username");
    exit;
}
