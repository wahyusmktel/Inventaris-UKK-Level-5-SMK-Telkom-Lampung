<?php
// 1. Mulai Session dan Cek Login
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: login.php?pesan=belum_login");
    exit;
}

// 2. Hubungkan ke Database
include 'koneksi.php';

// 3. Ambil ID dari URL
// Kita akan mengambil data barang berdasarkan ID yang dikirim lewat URL (GET)
// Contoh: edit_barang.php?id=5
if (isset($_GET['id'])) {
    // Amankan ID dari karakter aneh
    $id_barang = mysqli_real_escape_string($koneksi, $_GET['id']);

    // 4. Query untuk mengambil data barang yang spesifik
    $query_data = "SELECT * FROM tbl_barang WHERE id_barang = '$id_barang'";
    $sql_data = mysqli_query($koneksi, $query_data);

    // Cek apakah data ditemukan
    if (mysqli_num_rows($sql_data) > 0) {
        // Ambil data dan simpan ke variabel $data
        $data = mysqli_fetch_assoc($sql_data);
    } else {
        // Jika ID tidak ditemukan, 'tendang' kembali ke halaman barang
        echo "<script>alert('Data tidak ditemukan!'); window.location.href='barang.php';</script>";
        exit;
    }
} else {
    // Jika tidak ada ID di URL, 'tendang' kembali ke halaman barang
    echo "<script>alert('ID Barang tidak valid!'); window.location.href='barang.php';</script>";
    exit;
}

// (Template HTML, Sidebar, Topbar, dll... kita copy dari barang.php)
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Barang - Inventaris</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon"><i class="fas fa-boxes"></i></div>
                <div class="sidebar-brand-text mx-3">Inventaris</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="index.php"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Menu Utama</div>
            <li class="nav-item active"> <a class="nav-link" href="barang.php"><i class="fas fa-fw fa-box"></i><span>Data Barang</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="transaksi.php"><i class="fas fa-fw fa-exchange-alt"></i><span>Transaksi Barang</span></a>
            </li>
            <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'admin') : ?>
                <hr class="sidebar-divider">
                <div class="sidebar-heading">Administrator</div>
                <li class="nav-item">
                    <a class="nav-link" href="user.php"><i class="fas fa-fw fa-users"></i><span>Manajemen User</span></a>
                </li>
            <?php endif; ?>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Edit Data Barang</h1>
                    <p class="mb-4">Ubah data barang yang sudah ada.</p>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Barang</h6>
                        </div>
                        <div class="card-body">
                            <form action="aksi_barang.php" method="POST">

                                <input type="hidden" name="aksi" value="edit">

                                <input type="hidden" name="id_barang" value="<?php echo $data['id_barang']; ?>">

                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="kode_barang">Kode Barang</label>
                                        <input type="text" class="form-control" id="kode_barang" name="kode_barang"
                                            value="<?php echo htmlspecialchars($data['kode_barang']); ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="nama_barang">Nama Barang</label>
                                        <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                                            value="<?php echo htmlspecialchars($data['nama_barang']); ?>" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="satuan">Satuan</label>
                                        <input type="text" class="form-control" id="satuan" name="satuan"
                                            value="<?php echo htmlspecialchars($data['satuan']); ?>" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save fa-fw"></i> Simpan Perubahan
                                </button>

                                <a href="barang.php" class="btn btn-secondary">
                                    <i class="fas fa-times fa-fw"></i> Batal
                                </a>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; UKK Inventaris | SMK Telkom Lampung 2025</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin Keluar?</h5><button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">Pilih "Logout" di bawah ini jika Anda siap untuk mengakhiri sesi Anda saat ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>