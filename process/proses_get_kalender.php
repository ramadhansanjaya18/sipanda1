<?php
header('Content-Type: application/json');
require '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'orangtua') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}


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
$conn->close();

$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$hari_pertama = date('N', $timestamp_bulan_ini);
$hari_ini_tanggal = (date('Y-m') == "$tahun-$bulan") ? date('j') : null;

$html_grid = "<tr>";
for ($i = 1; $i < $hari_pertama; $i++) {
    $html_grid .= "<td class='empty'></td>";
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

    $html_grid .= "<td class='" . implode(' ', $class_list) . "'><div class='day-number'>" . $tanggal . "</div></td>";

    if ($hari_ke % 7 == 0 && $tanggal != $jumlah_hari) {
        $html_grid .= "</tr><tr>";
    }
}
while ($hari_ke % 7 != 0) {
    $html_grid .= "<td class='empty'></td>";
    $hari_ke++;
}
$html_grid .= "</tr>";

$html_event_list = "<h5>Kegiatan Bulan " . date('F', $timestamp_bulan_ini) . ":</h5>";
if (!empty($jadwal_bulan_ini)) {
    $html_event_list .= "<ul>";
    foreach ($jadwal_bulan_ini as $tanggal => $kegiatan_hari_ini) {
        foreach ($kegiatan_hari_ini as $kegiatan) {
            $deskripsi_kegiatan = !empty($kegiatan['deskripsi']) ? ' (' . htmlspecialchars($kegiatan['deskripsi']) . ')' : '';
            $html_event_list .= "<li><strong>" . $tanggal . " " . date('F', $timestamp_bulan_ini) . ":</strong> "
                . htmlspecialchars($kegiatan['judul_kegiatan'])
                . $deskripsi_kegiatan . "</li>";
        }
    }
    $html_event_list .= "</ul>";
} else {
    $html_event_list .= "<p>Tidak ada jadwal kegiatan bulan ini.</p>";
}

echo json_encode([
    'status' => 'success',
    'nama_bulan_ini' => $nama_bulan_ini,
    'nav_lalu' => [
        'bulan' => date('m', $timestamp_bulan_lalu),
        'tahun' => date('Y', $timestamp_bulan_lalu)
    ],
    'nav_depan' => [
        'bulan' => date('m', $timestamp_bulan_depan),
        'tahun' => date('Y', $timestamp_bulan_depan)
    ],
    'html_grid' => $html_grid,
    'html_event_list' => $html_event_list
]);
exit();
?>