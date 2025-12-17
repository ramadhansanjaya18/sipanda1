<?php
header('Content-Type: application/json');
require '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$nik_balita = $data['nik_balita'] ?? '';
$tanggal_lahir = $data['tanggal_lahir'] ?? '';

if (empty($nik_balita) || empty($tanggal_lahir)) {
    echo json_encode(['status' => 'error', 'message' => 'NIK dan Tanggal Lahir wajib diisi.']);
    exit();
}

$stmt = $conn->prepare("SELECT id_balita, nama_balita FROM balita WHERE nik_balita = ? AND tanggal_lahir = ?");
$stmt->bind_param("ss", $nik_balita, $tanggal_lahir);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $balita = $result->fetch_assoc();
    $_SESSION['role'] = 'orangtua';
    $_SESSION['id_balita'] = $balita['id_balita'];
    $_SESSION['nama_balita'] = $balita['nama_balita'];

    echo json_encode(['status' => 'success', 'message' => 'Login berhasil', 'redirect_url' => BASE_URL . '/dashboard_orangtua.php']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'NIK atau Tanggal Lahir salah.']);
}

$stmt->close();
$conn->close();
?>