<?php
require '../config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    die("Akses dilarang.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/data_anak.php?status=error_id');
    exit();
}
$id_balita = $_GET['id'];

// Ubah DELETE menjadi UPDATE
$stmt = $conn->prepare("UPDATE balita SET is_active = 0 WHERE id_balita = ?");
$stmt->bind_param("i", $id_balita);

if ($stmt->execute()) {
    header('Location: ' . BASE_URL . '/data_anak.php?status=sukses_hapus');
} else {
    header('Location: ' . BASE_URL . '/data_anak.php?status=gagal_hapus');
}

$stmt->close();
$conn->close();
exit();
?>