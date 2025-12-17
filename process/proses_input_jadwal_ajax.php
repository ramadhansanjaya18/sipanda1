<?php
header('Content-Type: application/json');
require '../config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid atau kosong.']);
    exit();
}

$judul_kegiatan = $data['judul_kegiatan'] ?? '';
$tanggal_kegiatan = $data['tanggal_kegiatan'] ?? '';
$deskripsi = !empty($data['deskripsi']) ? $data['deskripsi'] : NULL;

if (empty($judul_kegiatan) || empty($tanggal_kegiatan)) {
    echo json_encode(['status' => 'error', 'message' => 'Judul dan Tanggal wajib diisi.']);
    exit();
}

$stmt = $conn->prepare(
    "INSERT INTO jadwal (judul_kegiatan, tanggal_kegiatan, deskripsi) 
     VALUES (?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query: ' . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param(
    "sss",
    $judul_kegiatan,
    $tanggal_kegiatan,
    $deskripsi
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Jadwal baru berhasil disimpan!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan jadwal: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
exit();
?>