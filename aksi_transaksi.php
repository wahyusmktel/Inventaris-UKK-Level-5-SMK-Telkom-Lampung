<?php
// File: aksi_transaksi.php
// Deskripsi: File ini hanya memproses penambahan data transaksi baru.
// Tidak ada HTML di sini.

// 1. Mulai Session dan Cek Login
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    die("Akses dilarang! Silakan login terlebih dahulu.");
}

// 2. Hubungkan ke Database
include 'koneksi.php';

// ===================================================================
// KONDISI: JIKA AKSI-NYA ADALAH 'TAMBAH' (dari transaksi.php)
// ===================================================================
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {

    // 3. Ambil data dari form
    // (Kita pakai 'intval' untuk pastikan 'jumlah' adalah angka)
    $id_barang       = mysqli_real_escape_string($koneksi, $_POST['id_barang']);
    $id_user         = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $jenis_transaksi = mysqli_real_escape_string($koneksi, $_POST['jenis_transaksi']);
    $jumlah          = intval($_POST['jumlah']); // Pastikan ini angka
    $keterangan      = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

    // 4. Validasi sederhana
    // Pastikan 'jumlah' lebih dari 0
    if ($jumlah <= 0) {
        // Jika jumlahnya 0 atau minus, tendang balik
        header("Location: transaksi.php?pesan=tambah_gagal");
        exit;
    }

    // CATATAN:
    // Kita sengaja TIDAK memvalidasi stok saat 'KELUAR'.
    // Ini adalah 'penyederhanaan' agar kodenya mudah dipahami.
    // Membiarkan stok minus itu wajar di sistem inventaris sederhana.

    // 5. Buat Query SQL untuk Insert (Tambah)
    // Kolom 'tgl_transaksi' tidak perlu diisi, karena di database
    // sudah kita set 'DEFAULT CURRENT_TIMESTAMP' (otomatis terisi)
    $query = "INSERT INTO tbl_transaksi 
                (id_barang, id_user, jenis_transaksi, jumlah, keterangan) 
              VALUES 
                ('$id_barang', '$id_user', '$jenis_transaksi', '$jumlah', '$keterangan')";

    // 6. Eksekusi Query
    $sql = mysqli_query($koneksi, $query);

    // 7. Cek Hasil Query
    if ($sql) {
        // Jika berhasil, alihkan kembali ke transaksi.php
        header("Location: transaksi.php?pesan=tambah_sukses");
    } else {
        // Jika gagal, alihkan dengan pesan error
        header("Location: transaksi.php?pesan=tambah_gagal");
    }
} else {
    // Jika file ini diakses langsung tanpa 'aksi', kembalikan ke halaman dashboard
    header("Location: index.php");
}
