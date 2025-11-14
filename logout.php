<?php
// File: logout.php
// Deskripsi: Menghancurkan session (logout) dan mengalihkan ke halaman login

// 1. Mulai session
session_start();

// 2. Hancurkan semua data session yang tersimpan
session_unset();    // Hapus semua variabel session
session_destroy();  // Hancurkan session-nya

// 3. Alihkan (redirect) ke halaman login.php
// Kita beri pesan 'logout' agar bisa ditampilkan di halaman login (jika mau)
header("Location: login.php?pesan=logout");
exit;
