<?php
require '../config/database.php';

// Cek sesi (Pastikan hanya Kader yang bisa akses)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id_user = $_GET['id'];

    // --- BAGIAN PENTING: PUTUSKAN HUBUNGAN DATA TERLEBIH DAHULU ---
    // Agar tidak terkena error Foreign Key Constraint
    
    // 1. Ubah data Balita yang didaftarkan user ini menjadi NULL (Tanpa pendaftar)
    $stmt1 = $conn->prepare("UPDATE balita SET id_kader_pendaftar = NULL WHERE id_kader_pendaftar = ?");
    $stmt1->bind_param("i", $id_user);
    $stmt1->execute();
    $stmt1->close();

    // 2. Ubah data Pemeriksaan yang dilakukan user ini menjadi NULL (Penginput)
    $stmt2 = $conn->prepare("UPDATE pemeriksaan SET id_kader = NULL WHERE id_kader = ?");
    $stmt2->bind_param("i", $id_user);
    $stmt2->execute();
    $stmt2->close();

    // 3. Ubah data Imunisasi yang dilakukan user ini menjadi NULL (Penginput)
    $stmt3 = $conn->prepare("UPDATE imunisasi SET id_kader = NULL WHERE id_kader = ?");
    $stmt3->bind_param("i", $id_user);
    $stmt3->execute();
    $stmt3->close();

    // --- PERBAIKAN: TAMBAHKAN PENANGANAN VALIDATOR ---
    
    // 4. Ubah data Imunisasi (Validator/Bidan) menjadi NULL
    // Ini yang menyebabkan error "imunisasi_ibfk_3"
    $stmt4 = $conn->prepare("UPDATE imunisasi SET id_bidan_validator = NULL WHERE id_bidan_validator = ?");
    $stmt4->bind_param("i", $id_user);
    $stmt4->execute();
    $stmt4->close();

    // 5. Ubah data Pemeriksaan (Validator/Bidan) menjadi NULL
    // Jaga-jaga jika user ini juga memvalidasi pemeriksaan
    $stmt5 = $conn->prepare("UPDATE pemeriksaan SET id_bidan_validator = NULL WHERE id_bidan_validator = ?");
    $stmt5->bind_param("i", $id_user);
    $stmt5->execute();
    $stmt5->close();

    // --- SETELAH DATA BERSIH, BARU HAPUS USERNYA ---
    $stmt = $conn->prepare("DELETE FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);

    if ($stmt->execute()) {
        header("Location: ../manajemen_pengguna.php?msg=deleted");
    } else {
        // Tampilkan error jika masih gagal (misal constraint lain)
        echo "Gagal menghapus data. Detail: " . $conn->error;
        echo "<br><br><a href='../manajemen_pengguna.php'>Kembali</a>";
    }
    
    $stmt->close();
} else {
    header("Location: ../manajemen_pengguna.php");
}
$conn->close();
?>