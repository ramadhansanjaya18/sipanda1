<?php
require '../config/database.php'; // Muat konfigurasi

// Keamanan: Izinkan Kader dan Bidan
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    die("Akses dilarang.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil data dari form edit_jadwal.php
    $id_jadwal = $_POST['id_jadwal'] ?? null;
    $judul_kegiatan = $_POST['judul_kegiatan'] ?? '';
    $tanggal_kegiatan = $_POST['tanggal_kegiatan'] ?? '';
    // Ambil deskripsi dari form
    $deskripsi = !empty($_POST['deskripsi']) ? $_POST['deskripsi'] : NULL;

    // 2. Validasi data
    if (empty($id_jadwal) || empty($judul_kegiatan) || empty($tanggal_kegiatan)) {
        header('Location: ' . BASE_URL . '/edit_jadwal.php?id_jadwal=' . $id_jadwal . '&status=error_kosong');
        exit();
    }

    // 3. Siapkan Query UPDATE untuk tabel 'jadwal'
    $stmt = $conn->prepare(
        "UPDATE jadwal SET 
            judul_kegiatan = ?, 
            tanggal_kegiatan = ?, 
            deskripsi = ?
         WHERE id_jadwal = ?"
    );

    if (!$stmt) {
        die("Error preparing update statement: " . $conn->error);
    }

    // 4. Bind Parameter (sss = string, string, string; i = integer)
    $stmt->bind_param(
        "sssi",
        $judul_kegiatan,
        $tanggal_kegiatan,
        $deskripsi,
        $id_jadwal
    );

    // 5. Eksekusi dan Redirect
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?status=sukses_edit');
        } else {
            header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?status=tidak_ada_perubahan');
        }
    } else {
        header('Location: ' . BASE_URL . '/edit_jadwal.php?id_jadwal=' . $id_jadwal . '&status=gagal_edit');
    }

    $stmt->close();
    $conn->close();

} else {
    header('Location: ' . BASE_URL . '/dashboard_kader.php');
}
exit();
?>