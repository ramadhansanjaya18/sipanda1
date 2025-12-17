<?php
header('Content-Type: application/json');
require '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
    exit();
}

$nama_balita = $data['nama_balita'] ?? '';
$nik_balita = $data['nik_balita'] ?? '';
$tanggal_lahir = $data['tanggal_lahir'] ?? '';
$jenis_kelamin = $data['jenis_kelamin'] ?? '';
$nama_orang_tua = $data['nama_orang_tua'] ?? '';
$alamat = $data['alamat'] ?? '';
$id_kader_pendaftar = $_SESSION['id_user'] ?? null;

if (empty($nama_balita) || empty($nik_balita) || empty($tanggal_lahir) || empty($jenis_kelamin) || empty($nama_orang_tua)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi kecuali alamat.']);
    exit();
}

$stmt_cek = $conn->prepare("SELECT id_balita FROM balita WHERE nik_balita = ? AND is_active = 1");
$stmt_cek->bind_param("s", $nik_balita);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();

if ($result_cek->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'NIK Balita sudah terdaftar!']);
    $stmt_cek->close();
    exit();
}
$stmt_cek->close();

$stmt = $conn->prepare(
    "INSERT INTO balita (nama_balita, nik_balita, tanggal_lahir, jenis_kelamin, nama_orang_tua, alamat, id_kader_pendaftar) 
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssssssi",
    $nama_balita,
    $nik_balita,
    $tanggal_lahir,
    $jenis_kelamin,
    $nama_orang_tua,
    $alamat,
    $id_kader_pendaftar
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data anak berhasil disimpan!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
exit();
?>