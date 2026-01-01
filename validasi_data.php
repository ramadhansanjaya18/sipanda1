<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'bidan') {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$query_pemeriksaan = $conn->prepare(
    "SELECT p.id_pemeriksaan, p.tanggal_periksa, p.berat_badan, p.tinggi_badan, p.lingkar_kepala, 
            b.nama_balita, u.nama_lengkap AS nama_kader
     FROM pemeriksaan p
     JOIN balita b ON p.id_balita = b.id_balita
     LEFT JOIN users u ON p.id_kader = u.id_user
     WHERE p.status_validasi = 'Belum Divalidasi'
     ORDER BY p.tanggal_periksa DESC"
);
$query_pemeriksaan->execute();
$result_pemeriksaan = $query_pemeriksaan->get_result();

$query_imunisasi = $conn->prepare(
    "SELECT i.id_imunisasi, i.tanggal_imunisasi, i.jenis_vaksin, 
            b.nama_balita, u.nama_lengkap AS nama_kader
     FROM imunisasi i
     JOIN balita b ON i.id_balita = b.id_balita
     LEFT JOIN users u ON i.id_kader = u.id_user
     WHERE i.status_validasi = 'Belum Divalidasi'
     ORDER BY i.tanggal_imunisasi DESC"
);
$query_imunisasi->execute();
$result_imunisasi = $query_imunisasi->get_result();
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
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama_lengkap']) ?>&background=FBCFE8&color=B42373" alt="User Avatar">
            <div class="user-info">
                <h4><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h4>
                <p><?= htmlspecialchars(ucfirst($_SESSION['role'])) ?></p>
            </div>
        </div>

        <ul class="sidebar-nav">
            <li><a href="dashboard_bidan.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="data_anak_bidan.php"><i class="fa-solid fa-child"></i> Data Anak</a></li>
            <li><a href="jadwal_imunisasi.php"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
            <li><a href="validasi_data.php" class="active"><i class="fa-solid fa-clipboard-check"></i> Validasi Data</a></li>
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
                <h1 style="margin: 0;">Validasi Data</h1>
            </div>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <div class="message-area <?= (strpos($_GET['status'], 'sukses') !== false) ? 'success' : 'error' ?>" style="margin-bottom: 1.5rem;">
                <?php
                    switch ($_GET['status']) {
                        case 'sukses_validasi': echo 'Data berhasil divalidasi!'; break;
                        case 'sukses_tolak': echo 'Data berhasil ditolak!'; break;
                        case 'gagal_validasi': echo 'Gagal memvalidasi data.'; break;
                        case 'gagal_tolak': echo 'Gagal menolak data.'; break;
                        case 'error_id': echo 'ID data tidak valid.'; break;
                        case 'error_aksi': echo 'Aksi tidak valid.'; break;
                    }
                ?>
            </div>
        <?php endif; ?>


        <div class="history-section">
            <h4 class="sub-section-title">Data Pemeriksaan Menunggu Validasi (<?= $result_pemeriksaan->num_rows ?>)</h4>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Periksa</th>
                            <th>Nama Balita</th>
                            <th>BB (kg)</th>
                            <th>TB (cm)</th>
                            <th>LK (cm)</th>
                            <th>Diinput Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pemeriksaan->num_rows > 0): ?>
                            <?php while ($row = $result_pemeriksaan->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal_periksa'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama_balita']) ?></td>
                                    <td><?= htmlspecialchars($row['berat_badan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['tinggi_badan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['lingkar_kepala'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['nama_kader'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="process/proses_validasi.php?tipe=pemeriksaan&aksi=valid&id=<?= $row['id_pemeriksaan'] ?>" class="btn-aksi btn-validasi" title="Validasi Data" onclick="return confirm('Anda yakin ingin memvalidasi data pemeriksaan ini?')"><i class="fa-solid fa-check"></i></a>
                                        <a href="process/proses_validasi.php?tipe=pemeriksaan&aksi=tolak&id=<?= $row['id_pemeriksaan'] ?>" class="btn-aksi btn-tolak" title="Tolak Data" onclick="return confirm('Anda yakin ingin menolak data pemeriksaan ini?')"><i class="fa-solid fa-times"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center;">Tidak ada data pemeriksaan yang perlu divalidasi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="history-section-2">
            <h4 class="sub-section-title">Data Imunisasi Menunggu Validasi (<?= $result_imunisasi->num_rows ?>)</h4>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Imunisasi</th>
                            <th>Nama Balita</th>
                            <th>Jenis Vaksin</th>
                            <th>Diinput Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php if ($result_imunisasi->num_rows > 0): ?>
                            <?php while ($row = $result_imunisasi->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal_imunisasi'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama_balita']) ?></td>
                                    <td><?= htmlspecialchars($row['jenis_vaksin']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_kader'] ?? 'N/A') ?></td>
                                     <td>
                                        <a href="process/proses_validasi.php?tipe=imunisasi&aksi=valid&id=<?= $row['id_imunisasi'] ?>" class="btn-aksi btn-validasi" title="Validasi Data" onclick="return confirm('Anda yakin ingin memvalidasi data imunisasi ini?')"><i class="fa-solid fa-check"></i></a>
                                        <a href="process/proses_validasi.php?tipe=imunisasi&aksi=tolak&id=<?= $row['id_imunisasi'] ?>" class="btn-aksi btn-tolak" title="Tolak Data" onclick="return confirm('Anda yakin ingin menolak data imunisasi ini?')"><i class="fa-solid fa-times"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;">Tidak ada data imunisasi yang perlu divalidasi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main> </div> <?php
$query_pemeriksaan->close();
$query_imunisasi->close();
$conn->close();
include 'templates/footer.php';
?>