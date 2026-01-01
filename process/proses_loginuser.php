<?php
// Pastikan tidak ada output sebelum header JSON
ob_start(); 
header('Content-Type: application/json');
require '../config/database.php';

try {
    // 1. Ambil Input JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Data JSON tidak valid.');
    }

    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    // 2. Validasi Input
    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username dan Password wajib diisi.']);
        exit();
    }

    // 3. Query Database (Menggunakan Prepared Statement)
    $stmt = $conn->prepare("SELECT id_user, nama_lengkap, password, role, is_active FROM users WHERE username = ?");
    
    if (!$stmt) {
        throw new Exception("Database Error (Prepare): " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    
    if (!$stmt->execute()) {
        throw new Exception("Database Error (Execute): " . $stmt->error);
    }

    $result = $stmt->get_result();

    // 4. Cek Pengguna
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $password_di_db_hash = $user['password'];

        // Verifikasi Password
        if (password_verify($password, $password_di_db_hash)) {

            if ($user['is_active'] != 1) {
                echo json_encode(['status' => 'error', 'message' => 'Akun Anda sudah tidak aktif.']);
                exit();
            }

            // Set Session
            $_SESSION['role'] = $user['role'];
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

            // Tentukan Redirect URL
            // Menggunakan konstanta BASE_URL dari database.php agar lebih dinamis
            $redirect_url = BASE_URL . (($user['role'] == 'kader') ? '/dashboard_kader.php' : '/dashboard_bidan.php');

            echo json_encode([
                'status' => 'success', 
                'message' => 'Login berhasil', 
                'redirect_url' => $redirect_url
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Password salah.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username tidak ditemukan.']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Tangkap error fatal dan kirim sebagai JSON agar bisa dibaca di layar
    echo json_encode([
        'status' => 'error', 
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
    ]);
}
?>