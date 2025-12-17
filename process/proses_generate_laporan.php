<?php
header('Content-Type: application/json'); // Set header ke JSON
require '../config/database.php';

// Pastikan hanya Kader/Bidan yang bisa akses
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

// 1. Ambil parameter dari request (kita gunakan GET)
$bulan = $_GET['bulan'] ?? null;
$tahun = $_GET['tahun'] ?? null;

if (empty($bulan) || empty($tahun)) {
    echo json_encode(['status' => 'error', 'message' => 'Bulan dan Tahun wajib diisi.']);
    exit();
}

$data_laporan = [
    'pemeriksaan' => [],
    'imunisasi' => []
];

// 2. Query data PEMERIKSAAN yang SUDAH VALID
// Penting: Laporan hanya boleh berisi data yang sudah divalidasi Bidan
$stmt_pemeriksaan = $conn->prepare(
    "SELECT p.tanggal_periksa, b.nama_balita, p.berat_badan, p.tinggi_badan, p.lingkar_kepala, u.nama_lengkap AS nama_kader
     FROM pemeriksaan p
     JOIN balita b ON p.id_balita = b.id_balita
     LEFT JOIN users u ON p.id_kader = u.id_user
     WHERE MONTH(p.tanggal_periksa) = ? 
       AND YEAR(p.tanggal_periksa) = ?
       AND p.status_validasi = 'Valid' 
     ORDER BY p.tanggal_periksa ASC"
);
$stmt_pemeriksaan->bind_param("ss", $bulan, $tahun);
$stmt_pemeriksaan->execute();
$result_pemeriksaan = $stmt_pemeriksaan->get_result();
while ($row = $result_pemeriksaan->fetch_assoc()) {
    $data_laporan['pemeriksaan'][] = $row;
}
$stmt_pemeriksaan->close();

// 3. Query data IMUNISASI yang SUDAH VALID
$stmt_imunisasi = $conn->prepare(
    "SELECT i.tanggal_imunisasi, b.nama_balita, i.jenis_vaksin, u.nama_lengkap AS nama_kader
     FROM imunisasi i
     JOIN balita b ON i.id_balita = b.id_balita
     LEFT JOIN users u ON i.id_kader = u.id_user
     WHERE MONTH(i.tanggal_imunisasi) = ? 
       AND YEAR(i.tanggal_imunisasi) = ?
       AND i.status_validasi = 'Valid'
     ORDER BY i.tanggal_imunisasi ASC"
);
$stmt_imunisasi->bind_param("ss", $bulan, $tahun);
$stmt_imunisasi->execute();
$result_imunisasi = $stmt_imunisasi->get_result();
while ($row = $result_imunisasi->fetch_assoc()) {
    $data_laporan['imunisasi'][] = $row;
}
$stmt_imunisasi->close();
$conn->close();

// 4. Kembalikan data sebagai JSON
echo json_encode([
    'status' => 'success',
    'data' => $data_laporan,
    'periode' => DateTime::createFromFormat('!m', $bulan)->format('F') . ' ' . $tahun
]);
exit();
?>