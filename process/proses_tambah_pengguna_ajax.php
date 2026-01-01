<?php
header('Content-Type: application/json');
require '../config/database.php';

// Cek session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) { 
    echo json_encode(['status' => 'error', 'message' => 'Data invalid']); 
    exit; 
}

$nama = $data['nama_lengkap'] ?? '';
$user = $data['username'] ?? '';
$pass_raw = $data['password'] ?? '';
$role = $data['role'] ?? '';

// Validasi sederhana
if(empty($nama) || empty($user) || empty($pass_raw) || empty($role)){
    echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi']); 
    exit; 
}

$stmt_cek = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
$stmt_cek->bind_param("s", $user);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();

if ($result_cek->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan']);
} else {
    // Hash password
    $pass = password_hash($pass_raw, PASSWORD_DEFAULT);
    
    // Simpan ke database
   $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $user, $pass, $role);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Pengguna berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ke database']);
    }
}
?>