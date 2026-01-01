<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'orangtua' || !isset($_SESSION['id_balita'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}
$id_balita = $_SESSION['id_balita'];

$stmt_balita = $conn->prepare("SELECT * FROM balita WHERE id_balita = ?");
$stmt_balita->bind_param("i", $id_balita);
$stmt_balita->execute();
$result_balita = $stmt_balita->get_result();
$balita = $result_balita->fetch_assoc();
$stmt_balita->close();
if (!$balita) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$stmt_pemeriksaan = $conn->prepare("SELECT berat_badan, tinggi_badan, lingkar_kepala, tanggal_periksa FROM pemeriksaan WHERE id_balita = ? ORDER BY tanggal_periksa DESC LIMIT 1");
$stmt_pemeriksaan->bind_param("i", $id_balita);
$stmt_pemeriksaan->execute();
$result_pemeriksaan = $stmt_pemeriksaan->get_result();
$pemeriksaan_terakhir = $result_pemeriksaan->fetch_assoc();
$stmt_pemeriksaan->close();

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$timestamp_bulan_ini = mktime(0, 0, 0, $bulan, 1, $tahun);
$nama_bulan_ini = date('F Y', $timestamp_bulan_ini);

$timestamp_bulan_lalu = strtotime('-1 month', $timestamp_bulan_ini);
$timestamp_bulan_depan = strtotime('+1 month', $timestamp_bulan_ini);

$query_jadwal_bulan_ini = $conn->prepare(
    "SELECT DAY(tanggal_kegiatan) as tanggal, judul_kegiatan, deskripsi 
     FROM jadwal 
     WHERE MONTH(tanggal_kegiatan) = ? AND YEAR(tanggal_kegiatan) = ? 
     ORDER BY tanggal_kegiatan ASC"
);
$query_jadwal_bulan_ini->bind_param("ss", $bulan, $tahun);
$query_jadwal_bulan_ini->execute();
$result_jadwal_kalender = $query_jadwal_bulan_ini->get_result();

$jadwal_bulan_ini = [];
if ($result_jadwal_kalender->num_rows > 0) {
    while ($row = $result_jadwal_kalender->fetch_assoc()) {
        $jadwal_bulan_ini[$row['tanggal']][] = $row;
    }
}
$query_jadwal_bulan_ini->close();

$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$hari_pertama = date('N', $timestamp_bulan_ini);
$hari_ini_tanggal = (date('Y-m') == "$tahun-$bulan") ? date('j') : null;


$query_jadwal_mendatang = $conn->prepare(
    "SELECT tanggal_kegiatan, judul_kegiatan, DATEDIFF(tanggal_kegiatan, CURDATE()) AS sisa_hari
     FROM jadwal 
     WHERE tanggal_kegiatan >= CURDATE() AND tanggal_kegiatan <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
     ORDER BY tanggal_kegiatan ASC"
);
$query_jadwal_mendatang->execute();
$result_jadwal_notifikasi = $query_jadwal_mendatang->get_result();


$page_title = 'SIPANDA';
$page_css = 'dashboard.css';
include 'templates/header.php';


function hitungUmurLengkap($tanggal_lahir)
{
    if (empty($tanggal_lahir))
        return "N/A";
    $lahir = new DateTime($tanggal_lahir);
    $hari_ini = new DateTime();
    $umur = $hari_ini->diff($lahir);
    $hasil = "";
    if ($umur->y > 0)
        $hasil .= $umur->y . " Tahun ";
    if ($umur->m > 0)
        $hasil .= $umur->m . " Bulan";
    if ($umur->y == 0 && $umur->m == 0)
        $hasil = $umur->d . " Hari";
    return trim($hasil);
}
?>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>SIPANDA</h2>
        </div>
        <div class="sidebar-user">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($balita['nama_orang_tua']) ?>&background=FBCFE8&color=B42373"
                alt="Avatar Anak">
            <div class="user-info">
                <h4><?= htmlspecialchars($balita['nama_orang_tua']) ?></h4>
                <p>Orang Tua</p>
            </div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard_orangtua.php" class="active"><i class="fa-solid fa-house"></i> Beranda</a></li>
            <li><a href="perkembangan_anak.php"><i class="fa-solid fa-chart-line"></i> Perkembangan Anak</a></li>
            <li><a href="profil_anak.php"><i class="fa-solid fa-user"></i> Profil Anak</a></li>
        </ul>
        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li><a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOG OUT</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content">


        <div class="welcome-header">
            <div style="display: flex; align-items: center;">
                <button id="sidebarToggle" class="btn-toggle-sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            <h2>Selamat Datang, Bunda!</h2>
            <p><?= htmlspecialchars($balita['nama_balita']) ?>, <?= hitungUmurLengkap($balita['tanggal_lahir']) ?></p>
        </div>
        <h3 class="section-title">Ringkasan perkembangan bulan ini</h3>
        <div class="summary-cards">
            <div class="summary-card"> <i class="fa-solid fa-weight-scale card-icon"></i>
                <p class="card-label">Berat Badan</p>
                <p class="card-value"><?= htmlspecialchars($pemeriksaan_terakhir['berat_badan'] ?? 'N/A') ?> kg</p>
                <p class="card-subtext">
                    <?= isset($pemeriksaan_terakhir['tanggal_periksa']) ? 'Terakhir update: ' . date('d/m/Y', strtotime($pemeriksaan_terakhir['tanggal_periksa'])) : 'Belum ada data' ?>
                </p>
            </div>
            <div class="summary-card"> <i class="fa-solid fa-ruler-vertical card-icon"></i>
                <p class="card-label">Tinggi Badan</p>
                <p class="card-value"><?= htmlspecialchars($pemeriksaan_terakhir['tinggi_badan'] ?? 'N/A') ?> cm</p>
                <p class="card-subtext">
                    <?= isset($pemeriksaan_terakhir['tanggal_periksa']) ? 'Terakhir update: ' . date('d/m/Y', strtotime($pemeriksaan_terakhir['tanggal_periksa'])) : 'Belum ada data' ?>
                </p>
            </div>
            <div class="summary-card"> <i class="fa-solid fa-child-reaching card-icon"></i>
                <p class="card-label">Lingkar Kepala</p>
                <p class="card-value"><?= htmlspecialchars($pemeriksaan_terakhir['lingkar_kepala'] ?? 'N/A') ?> cm</p>
                <p class="card-subtext">
                    <?= isset($pemeriksaan_terakhir['tanggal_periksa']) ? 'Terakhir update: ' . date('d/m/Y', strtotime($pemeriksaan_terakhir['tanggal_periksa'])) : 'Belum ada data' ?>
                </p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-section">
                <h3 class="section-title">Jadwal Posyandu & Imunisasi</h3>
                <div class="calendar-widget" id="kalender-widget">

                    <div class="calendar-header">
                        <button id="kalender-nav-lalu" class="calendar-nav" title="Bulan Sebelumnya"
                            data-bulan="<?= date('m', $timestamp_bulan_lalu) ?>"
                            data-tahun="<?= date('Y', $timestamp_bulan_lalu) ?>">&laquo;</button>

                        <h4 id="nama-bulan-kalender"><?= $nama_bulan_ini ?></h4>

                        <button id="kalender-nav-depan" class="calendar-nav" title="Bulan Berikutnya"
                            data-bulan="<?= date('m', $timestamp_bulan_depan) ?>"
                            data-tahun="<?= date('Y', $timestamp_bulan_depan) ?>">&raquo;</button>
                    </div>

                    <table class="calendar-table">
                        <thead>
                            <tr>
                                <th>Sen</th>
                                <th>Sel</th>
                                <th>Rab</th>
                                <th>Kam</th>
                                <th>Jum</th>
                                <th>Sab</th>
                                <th>Min</th>
                            </tr>
                        </thead>
                        <tbody id="kalender-grid-body">
                            <tr>
                                <?php
                                for ($i = 1; $i < $hari_pertama; $i++) {
                                    echo "<td class='empty'></td>";
                                }
                                $hari_ke = $hari_pertama - 1;
                                for ($tanggal = 1; $tanggal <= $jumlah_hari; $tanggal++) {
                                    $hari_ke++;
                                    $class_list = [];
                                    if ($tanggal == $hari_ini_tanggal)
                                        $class_list[] = 'today';

                                    if (isset($jadwal_bulan_ini[$tanggal])) {
                                        $ada_posyandu = false;
                                        $ada_imunisasi = false;
                                        foreach ($jadwal_bulan_ini[$tanggal] as $kegiatan) {
                                            if (strtolower($kegiatan['judul_kegiatan']) == 'posyandu')
                                                $ada_posyandu = true;
                                            if (strtolower($kegiatan['judul_kegiatan']) == 'imunisasi')
                                                $ada_imunisasi = true;
                                        }
                                        if ($ada_posyandu)
                                            $class_list[] = 'event-posyandu';
                                        if ($ada_imunisasi)
                                            $class_list[] = 'event-imunisasi';
                                    }

                                    echo "<td class='" . implode(' ', $class_list) . "'><div class='day-number'>" . $tanggal . "</div></td>";

                                    if ($hari_ke % 7 == 0 && $tanggal != $jumlah_hari) {
                                        echo "</tr><tr>";
                                    }
                                }
                                while ($hari_ke % 7 != 0) {
                                    echo "<td class='empty'></td>";
                                    $hari_ke++;
                                }
                                ?>
                            </tr>
                        </tbody>
                    </table>

                    <div class="calendar-events-list" id="kalender-event-list">
                        <h5>Kegiatan Bulan <?= date('F', $timestamp_bulan_ini) ?>:</h5>
                        <?php if (!empty($jadwal_bulan_ini)): ?>
                            <ul>
                                <?php foreach ($jadwal_bulan_ini as $tanggal => $kegiatan_hari_ini): ?>
                                    <?php foreach ($kegiatan_hari_ini as $kegiatan): ?>
                                        <li><strong><?= $tanggal ?>             <?= date('F', $timestamp_bulan_ini) ?>:</strong>
                                            <?= htmlspecialchars($kegiatan['judul_kegiatan']) ?>
                                            <?= !empty($kegiatan['deskripsi']) ? '(' . htmlspecialchars($kegiatan['deskripsi']) . ')' : '' ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Tidak ada jadwal kegiatan bulan ini.</p>
                        <?php endif; ?>
                    </div>

                    <div class="calendar-legend">
                        <h5>Keterangan:</h5>
                        <ul>
                            <li><span class="legend-color today"></span>Hari Ini</li>
                            <li><span class="legend-color event-posyandu"></span>Posyandu</li>
                            <li><span class="legend-color event-imunisasi"></span>Imunisasi</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="info-section">
                <h3 class="section-title">Notifikasi Terbaru</h3>
                <div class="notification-placeholder">
                    <ul class="notification-list">
                        <?php if ($result_jadwal_notifikasi && $result_jadwal_notifikasi->num_rows > 0): ?>
                            <?php while ($notif = $result_jadwal_notifikasi->fetch_assoc()): ?>
                                <li class="notification-item">
                                    <div class="notif-icon <?= strtolower($notif['judul_kegiatan']) ?>">
                                        <i
                                            class="fa-solid <?= (strtolower($notif['judul_kegiatan']) == 'imunisasi') ? 'fa-syringe' : 'fa-hospital' ?>"></i>
                                    </div>
                                    <div class="notif-content">
                                        <strong>Jadwal <?= htmlspecialchars($notif['judul_kegiatan']) ?> Mendatang</strong>
                                        <span><?= date('d F Y', strtotime($notif['tanggal_kegiatan'])) ?></span>
                                        <span class="notif-countdown">
                                            <?php
                                            if ($notif['sisa_hari'] == 0)
                                                echo "HARI INI";
                                            elseif ($notif['sisa_hari'] == 1)
                                                echo "BESOK";
                                            else
                                                echo "dalam " . $notif['sisa_hari'] . " hari";
                                            ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endwhile;
                            $query_jadwal_mendatang->close(); ?>
                        <?php else: ?>
                            <li class="notification-item-empty">
                                <p style="text-align:center; padding: 20px; color: #6b7280;">Tidak ada jadwal dalam 7 hari
                                    ke depan.</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

    </main>
</div>

<?php
$conn->close();
include 'templates/footer.php';
?>