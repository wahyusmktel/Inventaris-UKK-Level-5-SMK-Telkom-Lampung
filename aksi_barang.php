<?php
// File: aksi_barang.php
// Deskripsi: File ini berfungsi sebagai 'otak' untuk memproses
// semua aksi CRUD (Tambah, Edit, Hapus) untuk data barang.
// File ini tidak memiliki tampilan HTML.

// 1. Mulai Session dan Cek Login
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    // Jika belum login, hentikan proses
    die("Akses dilarang! Silakan login terlebih dahulu.");
}

// 2. Hubungkan ke Database
include 'koneksi.php';

// ===================================================================
// KONDISI 1: JIKA AKSI-NYA ADALAH 'TAMBAH' (dari barang.php)
// ===================================================================
// Kita cek apakah ada data 'aksi' yang dikirim via POST
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {

    // 3. Ambil data dari form
    // Kita gunakan mysqli_real_escape_string untuk keamanan dasar (mencegah SQL Injection)
    $kode_barang = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);

    // 4. Buat Query SQL untuk Insert (Tambah)
    $query = "INSERT INTO tbl_barang (kode_barang, nama_barang, satuan) 
              VALUES ('$kode_barang', '$nama_barang', '$satuan')";

    // 5. Eksekusi Query
    $sql = mysqli_query($koneksi, $query);

    // 6. Cek Hasil Query
    if ($sql) {
        // Jika berhasil, alihkan (redirect) kembali ke barang.php
        // Kita kirim 'pesan' lewat URL untuk ditampilkan sebagai alert sukses
        header("Location: barang.php?pesan=tambah_sukses");
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}

// ===================================================================
// KONDISI 2: JIKA AKSI-NYA ADALAH 'EDIT' (dari edit_barang.php)
// ===================================================================
// Kita cek apakah ada data 'aksi' yang dikirim via POST
else if (isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {

    // 3. Ambil data dari form (termasuk ID barangnya!)
    $id_barang = mysqli_real_escape_string($koneksi, $_POST['id_barang']);
    $kode_barang = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);

    // 4. Buat Query SQL untuk Update (Edit)
    $query = "UPDATE tbl_barang 
              SET 
                  kode_barang = '$kode_barang', 
                  nama_barang = '$nama_barang', 
                  satuan = '$satuan' 
              WHERE 
                  id_barang = '$id_barang'";

    // 5. Eksekusi Query
    $sql = mysqli_query($koneksi, $query);

    // 6. Cek Hasil Query
    if ($sql) {
        // Jika berhasil, alihkan kembali ke barang.php
        header("Location: barang.php?pesan=edit_sukses");
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal mengupdate data: " . mysqli_error($koneksi);
    }
}

// ===================================================================
// KONDISI 3: JIKA AKSI-NYA ADALAH 'HAPUS' (dari barang.php)
// ===================================================================
// Kita cek apakah ada data 'aksi' yang dikirim via GET (dari link/URL)
else if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {

    // 3. Ambil ID dari URL
    $id_barang = mysqli_real_escape_string($koneksi, $_GET['id']);

    // 4. Buat Query SQL untuk Delete (Hapus)
    $query = "DELETE FROM tbl_barang WHERE id_barang = '$id_barang'";

    // 5. Eksekusi Query
    $sql = mysqli_query($koneksi, $query);

    // 6. Cek Hasil Query
    if ($sql) {
        // Jika berhasil, alihkan kembali ke barang.php
        header("Location: barang.php?pesan=hapus_sukses");
    } else {
        // Jika gagal (kemungkinan besar karena Foreign Key 'ON DELETE RESTRICT'),
        // alihkan kembali dengan pesan gagal.
        header("Location: barang.php?pesan=hapus_gagal");
    }
}

// ===================================================================
// KONDISI 4: JIKA TIDAK ADA AKSI YANG JELAS
// ===================================================================
else {
    // Jika file ini diakses langsung tanpa 'aksi', kembalikan ke halaman barang
    header("Location: barang.php");
}
