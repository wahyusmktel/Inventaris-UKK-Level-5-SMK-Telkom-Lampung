<?php
// 1. Mulai Session dan Cek Login
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: login.php?pesan=belum_login");
    exit;
}

// 2. Hubungkan ke Database
include 'koneksi.php';

// 3. Ambil Daftar Barang (untuk Dropdown)
// Kita butuh ini agar user bisa memilih barang saat transaksi
$query_barang = "SELECT id_barang, kode_barang, nama_barang 
                 FROM tbl_barang 
                 ORDER BY nama_barang ASC";
$sql_barang_list = mysqli_query($koneksi, $query_barang);

// 4. Ambil Riwayat Transaksi (untuk Tabel)
// Kita 'JOIN' 3 tabel sekaligus:
// tbl_transaksi (t)
// tbl_barang (b) -> untuk dapat NAMA BARANG
// tbl_user (u) -> untuk dapat NAMA PETUGAS
$query_history = "
    SELECT 
        t.id_transaksi,
        t.tgl_transaksi,
        b.nama_barang,
        t.jenis_transaksi,
        t.jumlah,
        u.nama_lengkap AS nama_petugas,
        t.keterangan
    FROM 
        tbl_transaksi t
    JOIN 
        tbl_barang b ON t.id_barang = b.id_barang
    JOIN 
        tbl_user u ON t.id_user = u.id_user
    ORDER BY 
        t.id_transaksi DESC
";
$sql_history = mysqli_query($koneksi, $query_history);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Transaksi Barang - Inventaris</title>

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
            <li class="nav-item active">
                <a class="nav-link" href="transaksi.php">
                    <i class="fas fa-fw fa-exchange-alt"></i><span>Transaksi Barang</span></a>
            </li>

            <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'admin') : ?>
                <hr class="sidebar-divider">
                <div class="sidebar-heading">Administrator</div>
                <li class="nav-item">
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
                    <h1 class="h3 mb-2 text-gray-800">Transaksi Barang</h1>
                    <p class="mb-4">Catat barang yang masuk atau keluar dari inventaris.</p>

                    <?php if (isset($_GET['pesan'])): ?>
                        <?php if ($_GET['pesan'] == 'tambah_sukses'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> Transaksi berhasil dicatat.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php elseif ($_GET['pesan'] == 'tambah_gagal'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Gagal!</strong> Terjadi kesalahan saat menyimpan transaksi.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>


                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Form Catat Transaksi</h6>
                        </div>
                        <div class="card-body">
                            <form action="aksi_transaksi.php" method="POST">
                                <input type="hidden" name="aksi" value="tambah">

                                <input type="hidden" name="id_user" value="<?php echo $_SESSION['id_user']; ?>">

                                <div class="form-row">
                                    <div class="form-group col-md-5">
                                        <label for="id_barang">Pilih Barang</label>
                                        <select id="id_barang" name="id_barang" class="form-control" required>
                                            <option value="" disabled selected>-- Pilih Barang --</option>

                                            <?php
                                            // Looping data barang untuk dropdown
                                            while ($barang = mysqli_fetch_assoc($sql_barang_list)):
                                            ?>
                                                <option value="<?php echo $barang['id_barang']; ?>">
                                                    (<?php echo htmlspecialchars($barang['kode_barang']); ?>)
                                                    <?php echo htmlspecialchars($barang['nama_barang']); ?>
                                                </option>
                                            <?php endwhile; ?>

                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="jenis_transaksi">Jenis Transaksi</label>
                                        <select id="jenis_transaksi" name="jenis_transaksi" class="form-control" required>
                                            <option value="" disabled selected>-- Pilih Jenis --</option>
                                            <option value="MASUK">Barang MASUK (Stok Bertambah)</option>
                                            <option value="KELUAR">Barang KELUAR (Stok Berkurang)</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="jumlah">Jumlah</label>
                                        <input type="number" class="form-control" id="jumlah" name="jumlah"
                                            placeholder="0" min="1" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan (Opsional)</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="2"
                                        placeholder="Misal: Pembelian dari PT. Maju Jaya / Dipakai divisi IT"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save fa-fw"></i> Simpan Transaksi
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Waktu</th>
                                            <th>Nama Barang</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                            <th>Petugas</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($data = mysqli_fetch_assoc($sql_history)):
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo date('d-M-Y H:i', strtotime($data['tgl_transaksi'])); ?></td>
                                                <td><?php echo htmlspecialchars($data['nama_barang']); ?></td>

                                                <?php if ($data['jenis_transaksi'] == 'MASUK'): ?>
                                                    <td class="text-success font-weight-bold">
                                                        <i class="fas fa-arrow-down fa-fw"></i> MASUK
                                                    </td>
                                                <?php else: ?>
                                                    <td class="text-danger font-weight-bold">
                                                        <i class="fas fa-arrow-up fa-fw"></i> KELUAR
                                                    </td>
                                                <?php endif; ?>

                                                <td class="text-center font-weight-bold">
                                                    <?php echo htmlspecialchars($data['jumlah']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($data['nama_petugas']); ?></td>
                                                <td><?php echo htmlspecialchars($data['keterangan']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
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
                },
                "order": [
                    [0, "desc"]
                ] // Urutkan berdasarkan kolom pertama (No.) secara descending
            });
        });
    </script>
</body>

</html>