<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'bidan') {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$query_pending_pemeriksaan_count = "SELECT COUNT(id_pemeriksaan) AS total FROM pemeriksaan WHERE status_validasi = 'Belum Divalidasi'";
$result_pending_pemeriksaan_count = $conn->query($query_pending_pemeriksaan_count);
$data_pending_pemeriksaan_count = $result_pending_pemeriksaan_count->fetch_assoc();
$total_pending_pemeriksaan = $data_pending_pemeriksaan_count['total'] ?? 0;

$query_pending_imunisasi_count = "SELECT COUNT(id_imunisasi) AS total FROM imunisasi WHERE status_validasi = 'Belum Divalidasi'";
$result_pending_imunisasi_count = $conn->query($query_pending_imunisasi_count);
$data_pending_imunisasi_count = $result_pending_imunisasi_count->fetch_assoc();
$total_pending_imunisasi = $data_pending_imunisasi_count['total'] ?? 0;

$query_total_anak = "SELECT COUNT(id_balita) AS total FROM balita";
$result_total_anak = $conn->query($query_total_anak);
$data_total_anak = $result_total_anak->fetch_assoc();
$total_anak = $data_total_anak['total'] ?? 0;

$query_list_pemeriksaan = $conn->prepare(
    "SELECT p.id_pemeriksaan, p.tanggal_periksa, b.nama_balita, b.id_balita, u.nama_lengkap AS nama_kader
     FROM pemeriksaan p
     JOIN balita b ON p.id_balita = b.id_balita
     LEFT JOIN users u ON p.id_kader = u.id_user
     WHERE p.status_validasi = 'Belum Divalidasi'
     ORDER BY p.tanggal_periksa DESC LIMIT 5"
);
$query_list_pemeriksaan->execute();
$result_list_pemeriksaan = $query_list_pemeriksaan->get_result();

$query_list_imunisasi = $conn->prepare(
    "SELECT i.id_imunisasi, i.tanggal_imunisasi, i.jenis_vaksin, b.nama_balita, b.id_balita, u.nama_lengkap AS nama_kader
     FROM imunisasi i
     JOIN balita b ON i.id_balita = b.id_balita
     LEFT JOIN users u ON i.id_kader = u.id_user
     WHERE i.status_validasi = 'Belum Divalidasi'
     ORDER BY i.tanggal_imunisasi DESC LIMIT 5"
);
$query_list_imunisasi->execute();
$result_list_imunisasi = $query_list_imunisasi->get_result();

$page_title = 'SIPANDA';
$page_css = 'dashboard.css';
include 'templates/header.php';
?>

<div class="dashboard-container">

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>SIPANDA</h2>
        </div>
        <div class="sidebar-user">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama_lengkap']) ?>&background=FBCFE8&color=B42373"
                alt="User Avatar">
            <div class="user-info">
                <h4><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h4>
                <p><?= htmlspecialchars(ucfirst($_SESSION['role'])) ?></p>
            </div>
        </div>

        <ul class="sidebar-nav">
            <li><a href="dashboard_bidan.php" class="active"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li><a href="data_anak_bidan.php"><i class="fa-solid fa-child"></i> Data Anak</a></li>
            <li><a href="jadwal_imunisasi.php"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
            <li><a href="validasi_data.php"><i class="fa-solid fa-clipboard-check"></i> Validasi Data</a></li>
            <li><a href="laporan.php"><i class="fa-solid fa-chart-pie"></i> Laporan</a></li>
        </ul>
        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li><a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOG OUT</a></li>
            </ul>
        </div>
    </aside>
    <main class="main-content">

        <div class="main-header">
            <div style="display: flex; align-items: center;">
                <button id="sidebarToggle" class="btn-toggle-sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 style="margin: 0;">Dashboard Bidan</h1>
            </div>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <div class="message-area <?= (strpos($_GET['status'], 'sukses') !== false) ? 'success' : 'error' ?>"
                style="margin-bottom: 1.5rem;">
                <?php
                switch ($_GET['status']) {
                    case 'sukses_validasi':
                        echo 'Data berhasil divalidasi!';
                        break;
                    case 'sukses_tolak':
                        echo 'Data berhasil ditolak!';
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="stats-cards">
            <div class="card card-pemeriksaan">
                <p>Pemeriksaan Menunggu Validasi</p>
                <h3><?= $total_pending_pemeriksaan ?></h3>
                <span class="percentage">Data</span>
            </div>
            <div class="card card-imunisasi">
                <p>Imunisasi Menunggu Validasi</p>
                <h3><?= $total_pending_imunisasi ?></h3>
                <span class="percentage">Data</span>
            </div>
            <div class="card">
                <p>Total Anak Terdaftar</p>
                <h3><?= $total_anak ?></h3>
                <span class="percentage">Anak</span>
            </div>
        </div>

        <div class="history-section-2">
            <h2>Data Terbaru Menunggu Validasi</h2>

            <h4 class="sub-section-title">
                Pemeriksaan (<?= $result_list_pemeriksaan->num_rows ?> terbaru)
            </h4>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Periksa</th>
                            <th>Nama Balita</th>
                            <th>Diinput Oleh</th>
                            <th>Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_list_pemeriksaan && $result_list_pemeriksaan->num_rows > 0): ?>
                            <?php while ($row = $result_list_pemeriksaan->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal_periksa'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama_balita']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_kader'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="process/proses_validasi.php?tipe=pemeriksaan&aksi=valid&id=<?= $row['id_pemeriksaan'] ?>&ref=dashboard"
                                            class="btn-aksi btn-validasi" title="Validasi"
                                            onclick="return confirm('Validasi data pemeriksaan ini?')"><i
                                                class="fa-solid fa-check"></i></a>
                                        <a href="process/proses_validasi.php?tipe=pemeriksaan&aksi=tolak&id=<?= $row['id_pemeriksaan'] ?>&ref=dashboard"
                                            class="btn-aksi btn-tolak" title="Tolak"
                                            onclick="return confirm('Tolak data pemeriksaan ini?')"><i
                                                class="fa-solid fa-times"></i></a>
                                        <a href='detail_anak_bidan.php?id=<?= $row['id_balita'] ?>' class='btn-aksi btn-detail'
                                            title='Lihat Detail Anak'><i class='fa-solid fa-eye'></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">Tidak ada data pemeriksaan menunggu
                                    validasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <h4 class="sub-section-title">
                Imunisasi (<?= $result_list_imunisasi->num_rows ?> terbaru)
            </h4>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Imunisasi</th>
                            <th>Nama Balita</th>
                            <th>Jenis Vaksin</th>
                            <th>Diinput Oleh</th>
                            <th>Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_list_imunisasi && $result_list_imunisasi->num_rows > 0): ?>
                            <?php while ($row = $result_list_imunisasi->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal_imunisasi'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama_balita']) ?></td>
                                    <td><?= htmlspecialchars($row['jenis_vaksin']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_kader'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="process/proses_validasi.php?tipe=imunisasi&aksi=valid&id=<?= $row['id_imunisasi'] ?>&ref=dashboard"
                                            class="btn-aksi btn-validasi" title="Validasi"
                                            onclick="return confirm('Validasi data imunisasi ini?')"><i
                                                class="fa-solid fa-check"></i></a>
                                        <a href="process/proses_validasi.php?tipe=imunisasi&aksi=tolak&id=<?= $row['id_imunisasi'] ?>&ref=dashboard"
                                            class="btn-aksi btn-tolak" title="Tolak"
                                            onclick="return confirm('Tolak data imunisasi ini?')"><i
                                                class="fa-solid fa-times"></i></a>
                                        <a href='detail_anak_bidan.php?id=<?= $row['id_balita'] ?>' class='btn-aksi btn-detail'
                                            title='Lihat Detail Anak'><i class='fa-solid fa-eye'></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Tidak ada data imunisasi menunggu
                                    validasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="action-footer">
                <a href="validasi_data.php" class="btn-primary">
                    Lihat Semua Data Validasi <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </main>
</div>
<?php
$query_list_pemeriksaan->close();
$query_list_imunisasi->close();
$conn->close();
include 'templates/footer.php';
?>