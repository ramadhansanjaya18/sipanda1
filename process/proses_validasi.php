<?php
require '../config/database.php'; // Muat konfigurasi

// Keamanan: Pastikan hanya Bidan yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'bidan' || !isset($_SESSION['id_user'])) {
    // Jika bukan bidan atau tidak ada ID user, redirect
    header('Location: ' . BASE_URL . '/login.php');
    exit("Akses ditolak.");
}
$id_bidan_validator = $_SESSION['id_user']; // Ambil ID Bidan yang sedang login

// 1. Ambil Parameter dari URL
$tipe = $_GET['tipe'] ?? ''; // 'pemeriksaan' atau 'imunisasi'
$aksi = $_GET['aksi'] ?? ''; // 'valid' atau 'tolak'
$id_data = $_GET['id'] ?? null; // ID pemeriksaan atau ID imunisasi

// 2. Validasi Parameter
if (
    ($tipe !== 'pemeriksaan' && $tipe !== 'imunisasi') ||
    ($aksi !== 'valid' && $aksi !== 'tolak') ||
    !is_numeric($id_data)
) {
    header('Location: ' . BASE_URL . '/validasi_data.php?status=error_parameter');
    exit();
}

// 3. Tentukan Tabel, Kolom ID, dan Status Baru
$nama_tabel = '';
$nama_kolom_id = '';
$status_baru = ($aksi === 'valid') ? 'Valid' : 'Tidak Valid';

if ($tipe === 'pemeriksaan') {
    $nama_tabel = 'pemeriksaan';
    $nama_kolom_id = 'id_pemeriksaan';
} else { // tipe === 'imunisasi'
    $nama_tabel = 'imunisasi';
    $nama_kolom_id = 'id_imunisasi';
}

// 4. Siapkan Query UPDATE
$sql = "UPDATE {$nama_tabel} SET 
            status_validasi = ?, 
            id_bidan_validator = ?, 
            validated_at = NOW() 
        WHERE {$nama_kolom_id} = ? AND status_validasi = 'Belum Divalidasi'";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    // Handle error jika prepare gagal
    header('Location: ' . BASE_URL . '/validasi_data.php?status=error_prepare');
    exit("Error preparing statement: " . $conn->error); // Tampilkan error saat development
}

// 5. Bind Parameter (s = status, i = id_bidan, i = id_data)
$stmt->bind_param(
    "sii",
    $status_baru,
    $id_bidan_validator,
    $id_data
);

// 6. Eksekusi Query
if ($stmt->execute()) {
    // Cek apakah ada baris yang terpengaruh
    if ($stmt->affected_rows > 0) {
        // Redirect dengan status sukses sesuai aksi
        $status_redirect = ($aksi === 'valid') ? 'sukses_validasi' : 'sukses_tolak';
        header('Location: ' . BASE_URL . '/validasi_data.php?status=' . $status_redirect);
    } else {
        // Data mungkin sudah divalidasi sebelumnya atau ID tidak cocok
        header('Location: ' . BASE_URL . '/validasi_data.php?status=gagal_update_notfound');
    }
} else {
    // Gagal eksekusi query
    // error_log("Error executing validation update: " . $stmt->error); // Log error
    $status_redirect_gagal = ($aksi === 'valid') ? 'gagal_validasi' : 'gagal_tolak';
    header('Location: ' . BASE_URL . '/validasi_data.php?status=' . $status_redirect_gagal);
}

$stmt->close();
$conn->close();
exit();
?>