# Aplikasi Inventaris Barang Sederhana (PHP Native - UKK)

Ini adalah project Uji Kualifikasi Kejuruan (UKK) untuk siswa SMK jurusan Rekayasa Perangkat Lunak (RPL), khususnya di **SMK Telkom Lampung**.

Project ini membangun aplikasi inventaris barang sederhana dari nol (scratch) menggunakan **PHP Native** dan database **MySQL**. Aplikasi ini sengaja dibuat dengan kodingan yang simpel, prosedural, dan penuh komentar agar mudah dipahami oleh siswa sebagai media pembelajaran.

---

## 🖼️ Tampilan Aplikasi (Contoh)

*(Catatan: Ganti gambar di bawah ini dengan screenshot aplikasi Anda)*

| Halaman Login | Dashboard Admin |
| :---: | :---: |
| ![Halaman Login](https://via.placeholder.com/400x300.png?text=Screenshot+Login) | ![Dashboard](https://via.placeholder.com/400x300.png?text=Screenshot+Dashboard) |
| **Data Barang & Stok** | **Riwayat Transaksi** |
| ![Data Barang](https://via.placeholder.com/400x300.png?text=Screenshot+Data+Barang) | ![Riwayat Transaksi](https://via.placeholder.com/400x300.png?text=Screenshot+Transaksi) |

---

## 🚀 Fitur Utama

Aplikasi ini memiliki fungsionalitas inti sebagai berikut:

* 🔒 **Sistem Autentikasi (Login & Logout)** menggunakan Session PHP.
* 🔐 **Keamanan Password** menggunakan `password_hash()` dan `password_verify()`.
* 👤 **Hak Akses (Otorisasi)** dengan 2 level pengguna:
    * **Admin**: Bisa mengelola semua menu, termasuk data user.
    * **Petugas**: Hanya bisa mengelola data barang dan transaksi.
* 🖥️ **Dashboard Dinamis** menampilkan info card (Total Barang, Total User).
* 📦 **Modul Data Barang (CRUD)**: Tambah, Tampil, Edit, dan Hapus data master barang.
* 📊 **Kalkulasi Stok Otomatis**: Stok barang dihitung secara *real-time* dari data transaksi (`Total MASUK - Total KELUAR`).
* 🔁 **Modul Transaksi**: Mencatat riwayat barang `MASUK` dan barang `KELUAR`.
* 👥 **Modul Manajemen User (CRUD)**: (Khusus Admin) Tambah, Edit, dan Hapus data user.
* ✨ **Tampilan UI/UX** modern menggunakan template **SB Admin 2** (Bootstrap 4).
* 🔍 **Tabel Interaktif** menggunakan plugin **DataTables** (Search, Sort, Paging).

---

## 🛠️ Teknologi yang Digunakan

* **Backend**: PHP Native (v7.4 / 8.x)
* **Database**: MySQL (MariaDB)
* **Frontend**: HTML, CSS, Bootstrap 4
* **JavaScript**: jQuery, SB Admin 2 script, DataTables Plugin
* **Web Server**: XAMPP (Apache)

---

## ⚙️ Cara Instalasi (Lokal)

Ikuti langkah-langkah ini untuk menjalankan project di komputer lokal Anda:

1.  **Clone Repository**
    ```bash
    git clone [https://github.com/username-anda/nama-repo-anda.git](https://github.com/username-anda/nama-repo-anda.git)
    ```
    *(Atau unduh file ZIP dan ekstrak)*

2.  **Pindahkan ke htdocs**
    * Pindahkan folder project ke dalam folder `htdocs` di direktori XAMPP Anda.
    * Contoh: `C:\xampp\htdocs\inventaris-ukk`

3.  **Setup Database**
    * Nyalakan Apache dan MySQL di XAMPP Control Panel.
    * Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
    * Buat database baru dengan nama `db_inventaris`.
    * Pilih database `db_inventaris` lalu klik tab **SQL**.
    * Copy-paste seluruh isi skrip SQL di bawah ini ke dalam kotak query, lalu klik **Go**.

    <details>
    <summary>Click untuk melihat Skrip SQL (db_inventaris)</summary>

    ```sql
    -- 1. Tabel User
    CREATE TABLE `tbl_user` (
      `id_user` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(50) NOT NULL,
      `password` varchar(255) NOT NULL,
      `nama_lengkap` varchar(100) DEFAULT NULL,
      `level` enum('admin','petugas') DEFAULT 'petugas',
      PRIMARY KEY (`id_user`),
      UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- 2. Tabel Barang
    CREATE TABLE `tbl_barang` (
      `id_barang` int(11) NOT NULL AUTO_INCREMENT,
      `kode_barang` varchar(20) NOT NULL,
      `nama_barang` varchar(100) NOT NULL,
      `satuan` varchar(20) DEFAULT NULL,
      PRIMARY KEY (`id_barang`),
      UNIQUE KEY `kode_barang` (`kode_barang`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- 3. Tabel Transaksi
    CREATE TABLE `tbl_transaksi` (
      `id_transaksi` int(11) NOT NULL AUTO_INCREMENT,
      `id_barang` int(11) NOT NULL,
      `id_user` int(11) NOT NULL,
      `jenis_transaksi` enum('MASUK','KELUAR') NOT NULL,
      `jumlah` int(11) NOT NULL,
      `tgl_transaksi` datetime DEFAULT current_timestamp(),
      `keterangan` text DEFAULT NULL,
      PRIMARY KEY (`id_transaksi`),
      KEY `id_barang` (`id_barang`),
      KEY `id_user` (`id_user`),
      CONSTRAINT `tbl_transaksi_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`) ON DELETE RESTRICT ON UPDATE CASCADE,
      CONSTRAINT `tbl_transaksi_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ```
    </details>

4.  **Koneksi Database**
    * Buka file `koneksi.php`.
    * Pastikan setting-an (`$db_host`, `$db_user`, `$db_pass`, `$db_name`) sudah sesuai dengan XAMPP Anda. (Default-nya sudah benar).

5.  **Selesai! Jalankan Aplikasi**
    * Buka browser dan akses: `http://localhost/inventaris-ukk` (sesuaikan dengan nama folder Anda).

---

## 🔑 Akun Default (Admin Pertama)

Aplikasi ini menggunakan `password_hash()`, sehingga Anda tidak bisa mendaftar manual via phpMyAdmin dengan password biasa.

**Untuk membuat user Admin pertama, ikuti langkah berikut:**

1.  Pastikan database (langkah instalasi di atas) sudah berhasil dibuat.
2.  Buka file `registrasi_awal.php` yang ada di dalam project ini langsung dari browser Anda:
    `http://localhost/inventaris-ukk/registrasi_awal.php`
3.  File tersebut akan otomatis membuatkan user Admin ke database.
4.  **PENTING: SEGERA HAPUS** file `registrasi_awal.php` dari folder project Anda setelah berhasil digunakan!

Setelah itu, silakan login menggunakan akun:
* **Username:** `admin`
* **Password:** `admin123`

---

## 📄 Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).

## 🧑‍🤝‍🧑 Kredit

* **Penyusun**: Siswa Kelas XII RPL, **SMK Telkom Lampung**.
* **Template**: [SB Admin 2](https://startbootstrap.com/theme/sb-admin-2)
* **Plugin**: [DataTables](https://datatables.net/)