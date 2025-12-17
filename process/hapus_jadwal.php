<?php
require '../config/database.php';


if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader')) {

    header('Location: ' . BASE_URL . '/login.php');
    exit("Akses ditolak.");
}


if (!isset($_GET['id_jadwal']) || !is_numeric($_GET['id_jadwal'])) {
    header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?status=error_id');
    exit();
}
$id_jadwal_hapus = $_GET['id_jadwal'];


$stmt = $conn->prepare("DELETE FROM jadwal WHERE id_jadwal = ?");
if (!$stmt) {

    header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?status=error_prepare');
    exit();
}
$stmt->bind_param("i", $id_jadwal_hapus);


if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?status=sukses_hapus_jadwal');
    } else {

        header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?status=gagal_hapus_jadwal_notfound');
    }
} else {

    header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?status=gagal_hapus_jadwal_db');
}

$stmt->close();
$conn->close();
exit();
?>