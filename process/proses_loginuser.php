<?php
header('Content-Type: application/json');
require '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Username dan Password wajib diisi.']);
    exit();
}

$stmt = $conn->prepare("SELECT id_user, nama_lengkap, password, role, is_active FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $password_di_db_hash = $user['password'];

    if (password_verify($password, $password_di_db_hash)) {

        if ($user['is_active'] != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Akun Anda sudah tidak aktif.']);
            $stmt->close();
            $conn->close();
            exit();
        }
        $_SESSION['role'] = $user['role'];
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

        $base_url = '/sipanda';
        $redirect_url = ($user['role'] == 'kader')
            ? $base_url . '/dashboard_kader.php'
            : $base_url . '/dashboard_bidan.php';

        echo json_encode(['status' => 'success', 'message' => 'Login berhasil', 'redirect_url' => $redirect_url]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Password salah.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Username tidak ditemukan.']);
}

$stmt->close();
$conn->close();
?>