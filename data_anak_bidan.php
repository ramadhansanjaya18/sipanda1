<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'bidan') {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$query_balita = "SELECT id_balita, nama_balita, tanggal_lahir, nama_orang_tua FROM balita ORDER BY nama_balita ASC";
$result_balita = $conn->query($query_balita);

$page_title = 'SIPANDA';
$page_css = 'dashboard.css';
include 'templates/header.php';

function hitungUmur($tanggal_lahir)
{
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
                <h1 style="margin: 0;">Daftar Data Anak</h1>
            </div>
        </div>

        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Anak</th>
                        <th>Usia</th>
                        <th>Nama Orang Tua</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_balita && $result_balita->num_rows > 0): ?>
                        <?php while ($row = $result_balita->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_balita']) ?></td>
                                <td><?= hitungUmur($row['tanggal_lahir']) ?></td>
                                <td><?= htmlspecialchars($row['nama_orang_tua']) ?></td>
                                <td> <a href='detail_anak_bidan.php?id=<?= $row['id_balita'] ?>' class='btn-aksi btn-detail'
                                        title='Lihat Detail'><i class='fa-solid fa-eye'></i></a> </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">Belum ada data anak.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php if ($conn)
    $conn->close();
include 'templates/footer.php'; ?>