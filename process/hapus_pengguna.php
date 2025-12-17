<?php
require '../config/database.php'; // Muat konfigurasi

// Keamanan: Pastikan hanya Kader yang bisa menghapus
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    die("Akses dilarang.");
}

// 1. Ambil ID Pengguna dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/manajemen_pengguna.php?status=error_id');
    exit();
}
$id_user_hapus = $_GET['id'];

// 2. Keamanan Tambahan: Jangan biarkan user menghapus dirinya sendiri
if (isset($_SESSION['id_user']) && $_SESSION['id_user'] == $id_user_hapus) {
    header('Location: ' . BASE_URL . '/manajemen_pengguna.php?status=error_self_delete');
    exit();
}

// 3. --- PERUBAHAN DI SINI ---
// Ganti query DELETE dengan query UPDATE
// $stmt = $conn->prepare("DELETE FROM users WHERE id_user = ?");
$stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id_user = ?"); // Set is_active menjadi 0 (Tidak Aktif)
$stmt->bind_param("i", $id_user_hapus);

// 4. Eksekusi query
if ($stmt->execute()) {
    // Cek apakah ada baris yang ter-update
    if ($stmt->affected_rows > 0) {
        header('Location: ' . BASE_URL . '/manajemen_pengguna.php?status=sukses_hapus');
    } else {
        // ID tidak ditemukan
        header('Location: ' . BASE_URL . '/manajemen_pengguna.php?status=gagal_hapus_notfound');
    }
} else {
    // Gagal eksekusi query
    header('Location: ' . BASE_URL . '/manajemen_pengguna.php?status=gagal_hapus_db');
}

$stmt->close();
$conn->close();
exit();
?>