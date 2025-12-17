<?php
require '../config/database.php'; // Muat konfigurasi

// Keamanan: Pastikan hanya Kader yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    die("Akses dilarang.");
}

// Pastikan data dikirim menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validasi dasar
    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
        header('Location: ' . BASE_URL . '/tambah_pengguna.php?status=error_kosong');
        exit();
    }
    if (strlen($password) < 6) {
        header('Location: ' . BASE_URL . '/tambah_pengguna.php?status=error_password');
        exit();
    }
    if ($role !== 'kader' && $role !== 'bidan') {
        header('Location: ' . BASE_URL . '/tambah_pengguna.php?status=error_role');
        exit();
    }

    $stmt_cek = $conn->prepare("SELECT id_user FROM users WHERE username = ? AND is_active = 1");
    $stmt_cek->bind_param("s", $username);
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();
    if ($result_cek->num_rows > 0) {
        $stmt_cek->close();
        header('Location: ' . BASE_URL . '/tambah_pengguna.php?status=error_username');
        exit();
    }
    $stmt_cek->close();

    // Enkripsi password (Hashing) - SANGAT PENTING
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Gunakan PREPARED STATEMENTS untuk INSERT ke tabel 'users'
    $stmt_insert = $conn->prepare(
        "INSERT INTO users (nama_lengkap, username, password, role) 
         VALUES (?, ?, ?, ?)"
    );

    // Bind parameter (s = string)
    $stmt_insert->bind_param(
        "ssss",
        $nama_lengkap,
        $username,
        $hashed_password, // Simpan password yang sudah di-hash
        $role
    );

    // Eksekusi query
    if ($stmt_insert->execute()) {
        // Jika berhasil, arahkan kembali ke halaman manajemen pengguna
        header('Location: ' . BASE_URL . '/manajemen_pengguna.php?status=sukses_tambah');
    } else {
        // Jika gagal
        header('Location: ' . BASE_URL . '/tambah_pengguna.php?status=gagal_tambah');
    }

    $stmt_insert->close();
    $conn->close();

} else {
    // Jika file diakses langsung, redirect
    header('Location: ' . BASE_URL . '/dashboard_kader.php');
}
exit();
?>