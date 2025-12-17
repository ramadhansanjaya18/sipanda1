<?php
require 'config/database.php'; // Muat konfigurasi

// Pengecekan sesi HANYA untuk Bidan
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'bidan') {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

// Ambil ID Balita dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/lihat_data_anak_bidan.php');
    exit();
}
$id_balita = $_GET['id'];

// Query Data Balita, Pemeriksaan, Imunisasi (sama seperti detail_anak.php)
$stmt_balita = $conn->prepare("SELECT * FROM balita WHERE id_balita = ?"); /* ... bind, execute, fetch ... */
$stmt_balita->bind_param("i", $id_balita);
$stmt_balita->execute();
$result_balita = $stmt_balita->get_result();
if ($result_balita->num_rows === 0) {
    header('Location: ' . BASE_URL . '/lihat_data_anak_bidan.php?status=not_found');
    exit();
}
$balita = $result_balita->fetch_assoc();
$stmt_balita->close();

$stmt_pemeriksaan = $conn->prepare("SELECT p.*, u.nama_lengkap AS nama_kader FROM pemeriksaan p LEFT JOIN users u ON p.id_kader = u.id_user WHERE p.id_balita = ? ORDER BY p.tanggal_periksa DESC"); /* ... bind, execute ... */
$stmt_pemeriksaan->bind_param("i", $id_balita);
$stmt_pemeriksaan->execute();
$result_pemeriksaan = $stmt_pemeriksaan->get_result();

$stmt_imunisasi = $conn->prepare("SELECT i.*, u.nama_lengkap AS nama_kader FROM imunisasi i LEFT JOIN users u ON i.id_kader = u.id_user WHERE i.id_balita = ? ORDER BY i.tanggal_imunisasi DESC"); /* ... bind, execute ... */
$stmt_imunisasi->bind_param("i", $id_balita);
$stmt_imunisasi->execute();
$result_imunisasi = $stmt_imunisasi->get_result();

$page_title = 'SIPANDA';
$page_css = 'dashboard.css'; // Gunakan CSS dasbor
include 'templates/header.php'; // Muat header

// Fungsi hitung umur
function hitungUmur($tanggal_lahir)
{ /* ... (kode fungsi hitungUmur) ... */
    if (empty($tanggal_lahir))
        return "N/A";
    $lahir = new DateTime($tanggal_lahir);
    $hari_ini = new DateTime();
    $umur = $hari_ini->diff($lahir);
    if ($umur->y > 0)
        return $umur->y . " tahun";
    elseif ($umur->m > 0)
        return $umur->m . " bulan";
    else
        return $umur->d . " hari";
}
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
            <li><a href="dashboard_bidan.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="data_anak_bidan.php" class="active"><i class="fa-solid fa-child"></i> Data Anak</a></li>
            <li><a href="jadwal_bidan.php"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
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
            <h1>Detail Data Anak</h1>
            <a href="data_anak_bidan.php" class="btn-kembali"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="profile-card">
            <div class="profile-header">
                <h3><?= htmlspecialchars($balita['nama_balita']) ?></h3>
                <p>Anak dari : <?= htmlspecialchars($balita['nama_orang_tua']) ?></p>
            </div>
            <div class="profile-details">
                <div><span>NIK</span><strong><?= htmlspecialchars($balita['nik_balita']) ?></strong></div>
                <div><span>Tanggal
                        Lahir</span><strong><?= date('d F Y', strtotime($balita['tanggal_lahir'])) ?></strong></div>
                <div><span>Usia</span><strong><?= hitungUmur($balita['tanggal_lahir']) ?></strong></div>
                <div><span>Jenis Kelamin</span><strong><?= htmlspecialchars($balita['jenis_kelamin']) ?></strong></div>
                <div><span>Alamat</span><strong><?= htmlspecialchars($balita['alamat'] ?? 'N/A') ?></strong></div>
            </div>
        </div>
        <div class="history-section">
            <h2>Riwayat Pemeriksaan Bulanan</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Pemeriksaan</th>
                            <th>Berat (kg)</th>
                            <th>Tinggi (cm)</th>
                            <th>Lingkar Kepala (cm)</th>
                            <th>Diinput oleh</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pemeriksaan->num_rows > 0): ?>
                            <?php while ($row = $result_pemeriksaan->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d F Y', strtotime($row['tanggal_periksa'])) ?></td>
                                    <td><?= htmlspecialchars($row['berat_badan'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['tinggi_badan'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['lingkar_kepala'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['nama_kader'] ?? 'N/A') ?></td>
                                    <td> <span
                                            class="status-badge <?= strtolower(str_replace(' ', '-', $row['status_validasi'])) ?>">
                                            <?= htmlspecialchars($row['status_validasi']) ?> </span> </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">Belum ada riwayat pemeriksaan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="history-section">
            <h2>Riwayat Imunisasi</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Imunisasi</th>
                            <th>Jenis Vaksin</th>
                            <th>Diinput oleh</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_imunisasi->num_rows > 0): ?>
                            <?php while ($row = $result_imunisasi->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d F Y', strtotime($row['tanggal_imunisasi'])) ?></td>
                                    <td><?= htmlspecialchars($row['jenis_vaksin']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_kader'] ?? 'N/A') ?></td>
                                    <td> <span
                                            class="status-badge <?= strtolower(str_replace(' ', '-', $row['status_validasi'])) ?>">
                                            <?= htmlspecialchars($row['status_validasi']) ?> </span> </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">Belum ada riwayat imunisasi.</td>
                            </tr> {/* Colspan 4 */}
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php
$stmt_pemeriksaan->close();
$stmt_imunisasi->close();
$conn->close();
include 'templates/footer.php'; // Muat footer
?>