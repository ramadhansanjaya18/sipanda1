<?php
header('Content-Type: application/json');
require '../config/database.php';

// 1. Cek Keamanan (Hanya Kader)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

// 2. Ambil Data JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
    exit();
}

// 3. Definisi Variabel
$nama_lengkap = $data['nama_lengkap'] ?? '';
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';

// 4. Validasi Input
if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(['status' => 'error', 'message' => 'Password minimal 6 karakter.']);
    exit();
}

// 5. Cek Username Kembar
$stmt_cek = $conn->prepare("SELECT id_user FROM users WHERE username = ? AND is_active = 1");
$stmt_cek->bind_param("s", $username);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();

if ($result_cek->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan orang lain.']);
    $stmt_cek->close();
    exit();
}
$stmt_cek->close();

// 6. Simpan Data
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nama_lengkap, $username, $hashed_password, $role);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Pengguna baru berhasil ditambahkan!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan database: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
exit();
?>