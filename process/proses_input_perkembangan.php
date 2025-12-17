<?php

header('Content-Type: application/json');
require '../config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader')) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
    exit();
}

$id_balita = $data['id_balita'] ?? null;
$tanggal_periksa = $data['tanggal_periksa'] ?? '';
$id_kader = $data['id_kader'] ?? null;

$berat_badan = !empty($data['berat_badan']) ? $data['berat_badan'] : NULL;
$tinggi_badan = !empty($data['tinggi_badan']) ? $data['tinggi_badan'] : NULL;
$lingkar_kepala = !empty($data['lingkar_kepala']) ? $data['lingkar_kepala'] : NULL;
$catatan = !empty($data['catatan']) ? $data['catatan'] : NULL;

if (empty($id_balita) || empty($tanggal_periksa) || empty($id_kader)) {
    echo json_encode(['status' => 'error', 'message' => 'Data wajib (Balita, Tanggal, Kader) harus diisi.']);
    exit();
}

$stmt = $conn->prepare(
    "INSERT INTO pemeriksaan (id_balita, tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, catatan, id_kader) 
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query: ' . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param(
    "isddssi",
    $id_balita,
    $tanggal_periksa,
    $berat_badan,
    $tinggi_badan,
    $lingkar_kepala,
    $catatan,
    $id_kader
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data perkembangan berhasil disimpan!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
exit();
?>