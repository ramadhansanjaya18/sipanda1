<?php
require '../config/database.php'; // Muat konfigurasi

// Keamanan: Pastikan hanya Kader yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    die("Akses dilarang.");
}

// Pastikan data dikirim menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil semua data dari form
    $id_user = $_POST['id_user'] ?? null;
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $username = $_POST['username'] ?? '';
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? ''; // Password baru (opsional, bisa kosong)

    // Validasi dasar
    if (empty($id_user) || empty($nama_lengkap) || empty($username) || empty($role)) {
        // Jika data wajib kosong, kembalikan ke form edit
        header('Location: ' . BASE_URL . '/edit_pengguna.php?id=' . $id_user . '&status=error_kosong');
        exit();
    }

    // Cek apakah username (jika diubah) sudah dipakai oleh user LAIN yang aktif
    // Ini perbaikan dari logika pengecekan username kita sebelumnya
    $stmt_cek = $conn->prepare("SELECT id_user FROM users WHERE username = ? AND id_user != ? AND is_active = 1");
    $stmt_cek->bind_param("si", $username, $id_user);
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();
    if ($result_cek->num_rows > 0) {
        $stmt_cek->close();
        // Username sudah dipakai, kembalikan ke form edit
        header('Location: ' . BASE_URL . '/edit_pengguna.php?id=' . $id_user . '&status=error_username');
        exit();
    }
    $stmt_cek->close();


    // 2. Logika update password
    if (!empty($password)) {
        // --- JIKA PASSWORD BARU DIISI ---

        // Validasi panjang password
        if (strlen($password) < 6) {
            header('Location: ' . BASE_URL . '/edit_pengguna.php?id=' . $id_user . '&status=error_password_pendek');
            exit();
        }

        // Hash password baru
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Siapkan query UPDATE dengan password baru
        $stmt = $conn->prepare(
            "UPDATE users SET 
                nama_lengkap = ?, 
                username = ?, 
                role = ?, 
                password = ? 
             WHERE id_user = ?"
        );
        $stmt->bind_param("ssssi", $nama_lengkap, $username, $role, $hashed_password, $id_user);

    } else {
        // --- JIKA PASSWORD DIKOSONGKAN (tidak diubah) ---

        // Siapkan query UPDATE TANPA mengubah password
        $stmt = $conn->prepare(
            "UPDATE users SET 
                nama_lengkap = ?, 
                username = ?, 
                role = ?
             WHERE id_user = ?"
        );
        $stmt->bind_param("sssi", $nama_lengkap, $username, $role, $id_user);
    }

    // 3. Eksekusi query
    if ($stmt->execute()) {
        // Jika berhasil, arahkan kembali ke halaman manajemen pengguna
        header('Location: ' . BASE_URL . '/manajemen_pengguna.php?status=sukses_edit');
    } else {
        // Jika gagal
        header('Location: ' . BASE_URL . '/edit_pengguna.php?id=' . $id_user . '&status=gagal_edit');
    }

    $stmt->close();
    $conn->close();

} else {
}
exit();
?>