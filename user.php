<?php
// 1. Mulai Session dan Cek Login
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: login.php?pesan=belum_login");
    exit;
}

// 2. PENJAGAAN KETAT: Cek Level Admin
// Jika yang login BUKAN 'admin', tendang paksa ke halaman 'index.php'
if ($_SESSION['level'] != 'admin') {
    echo "<script>
            alert('Anda tidak punya akses ke halaman ini!'); 
            window.location.href = 'index.php';
          </script>";
    exit;
}

// 3. Hubungkan ke Database
include 'koneksi.php';

// 4. Query untuk mengambil semua data user
$query_user = "SELECT * FROM tbl_user ORDER BY nama_lengkap ASC";
$sql_user = mysqli_query($koneksi, $query_user);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manajemen User - Inventaris</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Menu Utama</div>

            <li class="nav-item">
                <a class="nav-link" href="barang.php">
                    <i class="fas fa-fw fa-box"></i><span>Data Barang</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="transaksi.php">
                    <i class="fas fa-fw fa-exchange-alt"></i><span>Transaksi Barang</span></a>
            </li>

            <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'admin') : ?>
                <hr class="sidebar-divider">
                <div class="sidebar-heading">Administrator</div>
                <li class="nav-item active">
                    <a class="nav-link" href="user.php">
                        <i class="fas fa-fw fa-users"></i><span>Manajemen User</span></a>
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
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Manajemen User</h1>
                    <p class="mb-4">Halaman ini digunakan untuk mengelola akun admin dan petugas.</p>

                    <?php if (isset($_GET['pesan'])): ?>
                        <?php if ($_GET['pesan'] == 'tambah_sukses'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> User baru berhasil ditambahkan.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php elseif ($_GET['pesan'] == 'edit_sukses'): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> Data user berhasil diubah.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php elseif ($_GET['pesan'] == 'hapus_sukses'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> Data user berhasil dihapus.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php elseif ($_GET['pesan'] == 'hapus_gagal'): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Gagal!</strong> User tidak bisa dihapus karena sudah pernah melakukan transaksi.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php elseif ($_GET['pesan'] == 'hapus_diri_sendiri_gagal'): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Gagal!</strong> Anda tidak bisa menghapus akun Anda sendiri.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-plus fa-fw"></i> Tambah User Baru
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="aksi_user.php" method="POST">
                                <input type="hidden" name="aksi" value="tambah">

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="nama_lengkap">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="level">Level Akun</label>
                                        <select id="level" name="level" class="form-control" required>
                                            <option value="petugas" selected>Petugas</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save fa-fw"></i> Simpan
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar User</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Lengkap</th>
                                            <th>Username</th>
                                            <th>Level</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($data = mysqli_fetch_assoc($sql_user)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($data['nama_lengkap']); ?></td>
                                                <td><?php echo htmlspecialchars($data['username']); ?></td>
                                                <td>
                                                    <?php if ($data['level'] == 'admin'): ?>
                                                        <span class="badge badge-danger">Admin</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-info">Petugas</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="edit_user.php?id=<?php echo $data['id_user']; ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>

                                                    <?php
                                                    // --- PENGAMANAN TAMBAHAN ---
                                                    // Cek apakah ID user ini adalah ID user yang sedang login
                                                    // Jika iya, JANGAN TAMPILKAN tombol hapus
                                                    if ($data['id_user'] != $_SESSION['id_user']) :
                                                    ?>
                                                        <a href="aksi_user.php?aksi=hapus&id=<?php echo $data['id_user']; ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Peringatan: Menghapus user mungkin gagal jika user sudah memiliki riwayat transaksi. \n\nYakin ingin mencoba menghapus user ini?');">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </a>
                                                    <?php
                                                    endif; // Akhir dari if cek id_user
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php
                                        } // Akhir dari 'while'
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin Keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
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
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });
        });
    </script>
</body>

</html>