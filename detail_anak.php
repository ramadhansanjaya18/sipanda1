<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/data_anak.php');
    exit();
}
$id_balita = $_GET['id'];

// Query Data Balita
$stmt_balita = $conn->prepare("SELECT * FROM balita WHERE id_balita = ?");
$stmt_balita->bind_param("i", $id_balita);
$stmt_balita->execute();
$result_balita = $stmt_balita->get_result();

if ($result_balita->num_rows === 0) {
    header('Location: ' . BASE_URL . '/data_anak.php?status=not_found');
    exit();
}
$balita = $result_balita->fetch_assoc();

// Query Pemeriksaan
$stmt_pemeriksaan = $conn->prepare(
    "SELECT p.*, u.nama_lengkap AS nama_kader
     FROM pemeriksaan p
     LEFT JOIN users u ON p.id_kader = u.id_user
     WHERE p.id_balita = ?
     ORDER BY p.tanggal_periksa DESC"
);
$stmt_pemeriksaan->bind_param("i", $id_balita);
$stmt_pemeriksaan->execute();
$result_pemeriksaan = $stmt_pemeriksaan->get_result();

// Query Imunisasi
$stmt_imunisasi = $conn->prepare(
    "SELECT i.*, u.nama_lengkap AS nama_kader
     FROM imunisasi i
     LEFT JOIN users u ON i.id_kader = u.id_user
     WHERE i.id_balita = ?
     ORDER BY i.tanggal_imunisasi DESC"
);
$stmt_imunisasi->bind_param("i", $id_balita);
$stmt_imunisasi->execute();
$result_imunisasi = $stmt_imunisasi->get_result();

$page_title = 'Detail Anak - SIPANDA';
$page_css = 'dashboard.css';
include 'templates/header.php';

function hitungUmurSimple($tanggal_lahir)
{
    if (empty($tanggal_lahir))
        return "-";
    $lahir = new DateTime($tanggal_lahir);
    $hari_ini = new DateTime();
    $umur = $hari_ini->diff($lahir);

    if ($umur->y > 0)
        return $umur->y . " Th " . $umur->m . " Bln";
    if ($umur->m > 0)
        return $umur->m . " Bln";
    return $umur->d . " Hari";
}
?>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>SIPANDA</h2>
        </div>
        <div class="sidebar-user">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama_lengkap']) ?>&background=FBCFE8&color=B42373"
                alt="User">
            <div class="user-info">
                <h4><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h4>
                <p><?= htmlspecialchars(ucfirst($_SESSION['role'])) ?></p>
            </div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard_kader.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="data_anak.php" class="active"><i class="fa-solid fa-child"></i> Data Anak</a></li>
            <li><a href="jadwal_imunisasi.php"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
            <li><a href="laporan.php"><i class="fa-solid fa-chart-pie"></i> Laporan</a></li>
            <li><a href="manajemen_pengguna.php"><i class="fa-solid fa-users-cog"></i> Manajemen Pengguna</a></li>
        </ul>
        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li><a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOG OUT</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content minimalist-layout">

        <div class="header-minimal">
            <a href="data_anak.php" class="btn-back-icon" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
            <h1>Detail Data Anak</h1>
        </div>

        <div class="profile-card-minimal">
            <div class="profile-head">
                <div class="name-area">
                    <h2 class="child-name">
                        <?= htmlspecialchars($balita['nama_balita']) ?>
                        <span class="age-badge"><?= hitungUmurSimple($balita['tanggal_lahir']) ?></span>
                    </h2>
                    <p class="parent-sub">Anak dari: <?= htmlspecialchars($balita['nama_orang_tua']) ?></p>
                </div>
            </div>

            <div class="info-grid-compact">
                <div class="info-item">
                    <label>NIK & Tanggal Lahir</label>
                    <div class="value">
                        <?= htmlspecialchars($balita['nik_balita']) ?> <br>
                        <span class="sub-value"><?= date('d M Y', strtotime($balita['tanggal_lahir'])) ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <label>Jenis Kelamin</label>
                    <div class="value"><?= htmlspecialchars($balita['jenis_kelamin']) ?></div>
                </div>
                <div class="info-item full-width">
                    <label>Alamat</label>
                    <div class="value address-text"><?= htmlspecialchars($balita['alamat'] ?? '-') ?></div>
                </div>
            </div>
        </div>

        <div class="section-minimal">
            <h3 class="section-title-min">Riwayat Pemeriksaan</h3>
            <div class="table-responsive-min">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">TB (cm)</th>
                            <th class="text-center">BB (kg)</th>
                            <th class="text-center">LK (cm)</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pemeriksaan->num_rows > 0): ?>
                            <?php while ($row = $result_pemeriksaan->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?= date('d/m/y', strtotime($row['tanggal_periksa'])) ?></td>
                                    <td class="text-center"><?= $row['berat_badan'] ?></td>
                                    <td class="text-center"><?= $row['tinggi_badan'] ?></td>
                                    <td class="text-center"><?= $row['lingkar_kepala'] ?></td>
                                    <td class="text-center">
                                        <?php
                                        $statusClass = ($row['status_validasi'] == 'Valid') ? 'badge-valid' : 'badge-pending';
                                        $statusLabel = ($row['status_validasi'] == 'Valid') ? 'Valid' : 'Pending';
                                        ?>
                                        <span class="badge-min <?= $statusClass ?>"><?= $statusLabel ?></span>
                                    </td>
                                    <td class="text-center action-col">
                                        <?php if ($row['status_validasi'] == 'Belum Divalidasi'): ?>
                                            <a href='edit_pemeriksaan.php?id_pemeriksaan=<?= $row['id_pemeriksaan'] ?>'
                                                class='icon-edit' title="Edit">
                                                <i class='fa-solid fa-pen'></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-minimal">
            <h3 class="section-title-min">Riwayat Imunisasi</h3>
            <div class="table-responsive-min">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Jenis Vaksin</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_imunisasi->num_rows > 0): ?>
                            <?php while ($row = $result_imunisasi->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?= date('d/m/y', strtotime($row['tanggal_imunisasi'])) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['jenis_vaksin']) ?></td>
                                    <td class="text-center">
                                        <?php
                                        $statusClass = ($row['status_validasi'] == 'Valid') ? 'badge-valid' : 'badge-pending';
                                        $statusLabel = ($row['status_validasi'] == 'Valid') ? 'Valid' : 'Pending';
                                        ?>
                                        <span class="badge-min <?= $statusClass ?>"><?= $statusLabel ?></span>
                                    </td>
                                    <td class="text-center action-col">
                                        <?php if ($row['status_validasi'] == 'Belum Divalidasi'): ?>
                                            <a href='edit_imunisasi.php?id_imunisasi=<?= $row['id_imunisasi'] ?>' class='icon-edit'
                                                title="Edit">
                                                <i class='fa-solid fa-pen'></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
<?php
$stmt_balita->close();
$stmt_pemeriksaan->close();
$stmt_imunisasi->close();
$conn->close();
include 'templates/footer.php';
?>