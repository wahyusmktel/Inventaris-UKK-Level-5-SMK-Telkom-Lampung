<?php
// File: aksi_user.php
// Deskripsi: 'Otak' untuk memproses Tambah, Edit, dan Hapus data user.
// Wajib ada HASHING PASSWORD di sini.

// 1. Mulai Session dan Cek Login
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    die("Akses dilarang! Silakan login terlebih dahulu.");
}

// 2. PENJAGAAN KETAT: Hanya Admin
// Seluruh aksi di file ini (tambah, edit, hapus) HANYA boleh oleh ADMIN.
if ($_SESSION['level'] != 'admin') {
    die("Akses dilarang! Anda bukan admin.");
}

// 3. Hubungkan ke Database
include 'koneksi.php';

// ===================================================================
// KONDISI 1: JIKA AKSI-NYA ADALAH 'TAMBAH'
// ===================================================================
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {

    // Ambil data dari form
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password     = mysqli_real_escape_string($koneksi, $_POST['password']);
    $level        = mysqli_real_escape_string($koneksi, $_POST['level']);

    // PENTING: HASH PASSWORD!
    // Kita 'enkripsi' password sebelum disimpan ke database.
    // Ini adalah standar keamanan modern.
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Buat Query SQL untuk Insert
    $query = "INSERT INTO tbl_user (nama_lengkap, username, password, level) 
              VALUES ('$nama_lengkap', '$username', '$password_hashed', '$level')";

    // Eksekusi Query
    $sql = mysqli_query($koneksi, $query);
    if ($sql) {
        header("Location: user.php?pesan=tambah_sukses");
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}

// ===================================================================
// KONDISI 2: JIKA AKSI-NYA ADALAH 'EDIT'
// ===================================================================
else if (isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {

    // Ambil data dari form
    $id_user       = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $nama_lengkap  = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username      = mysqli_real_escape_string($koneksi, $_POST['username']);
    $level         = mysqli_real_escape_string($koneksi, $_POST['level']);
    $password_baru = mysqli_real_escape_string($koneksi, $_POST['password_baru']);

    // LOGIKA UPDATE PASSWORD:
    // Kita cek apakah admin mengisi field 'password_baru'

    if (!empty($password_baru)) {
        // ---- JIKA PASSWORD DIISI (Mau diubah) ----

        // 1. Hash password baru
        $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);

        // 2. Buat query UPDATE LENGKAP (termasuk password)
        $query = "UPDATE tbl_user 
                  SET 
                      nama_lengkap = '$nama_lengkap', 
                      username = '$username', 
                      level = '$level',
                      password = '$password_hashed' 
                  WHERE 
                      id_user = '$id_user'";
    } else {
        // ---- JIKA PASSWORD DIKOSONGI (Tidak mau diubah) ----

        // 2. Buat query UPDATE TANPA password
        $query = "UPDATE tbl_user 
                  SET 
                      nama_lengkap = '$nama_lengkap', 
                      username = '$username', 
                      level = '$level'
                  WHERE 
                      id_user = '$id_user'";
    }

    // Eksekusi Query (apapun query-nya, dari if atau else)
    $sql = mysqli_query($koneksi, $query);
    if ($sql) {
        header("Location: user.php?pesan=edit_sukses");
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($koneksi);
    }
}

// ===================================================================
// KONDISI 3: JIKA AKSI-NYA ADALAH 'HAPUS'
// ===================================================================
else if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {

    // Ambil ID dari URL
    $id_user = mysqli_real_escape_string($koneksi, $_GET['id']);

    // PENTING: PENGAMANAN DIRI SENDIRI
    // Cek apakah 'id_user' yang mau dihapus adalah 'id_user' yang sedang login
    if ($id_user == $_SESSION['id_user']) {
        // Jika iya, GAGALKAN!
        header("Location: user.php?pesan=hapus_diri_sendiri_gagal");
        exit;
    }

    // Buat Query SQL untuk Delete
    $query = "DELETE FROM tbl_user WHERE id_user = '$id_user'";

    // Eksekusi Query
    $sql = mysqli_query($koneksi, $query);

    if ($sql) {
        // Jika berhasil
        header("Location: user.php?pesan=hapus_sukses");
    } else {
        // Jika gagal (kena Foreign Key 'ON DELETE RESTRICT' dari tbl_transaksi)
        header("Location: user.php?pesan=hapus_gagal");
    }
}

// ===================================================================
// KONDISI 4: JIKA TIDAK ADA AKSI YANG JELAS
// ===================================================================
else {
    header("Location: user.php");
}
