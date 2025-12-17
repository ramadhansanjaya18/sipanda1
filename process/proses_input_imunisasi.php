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

$id_balita = $data['id_balita'] ?? null;
$tanggal_imunisasi = $data['tanggal_imunisasi'] ?? '';
$jenis_vaksin = $data['jenis_vaksin'] ?? '';
$id_kader = $data['id_kader'] ?? null;
$catatan = !empty($data['catatan']) ? $data['catatan'] : NULL;

if (empty($id_balita) || empty($tanggal_imunisasi) || empty($jenis_vaksin) || empty($id_kader)) {
    echo json_encode(['status' => 'error', 'message' => 'Data wajib (Balita, Tanggal, Vaksin, Kader) harus diisi.']);
    exit();
}

$stmt = $conn->prepare(
    "INSERT INTO imunisasi (id_balita, tanggal_imunisasi, jenis_vaksin, catatan, id_kader) 
     VALUES (?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query: ' . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param(
    "isssi",
    $id_balita,
    $tanggal_imunisasi,
    $jenis_vaksin,
    $catatan,
    $id_kader
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data imunisasi berhasil disimpan!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
exit();
?>