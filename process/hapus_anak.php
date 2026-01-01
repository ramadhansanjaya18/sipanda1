<?php
require '../config/database.php';

// Cek sesi
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id_balita = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM balita WHERE id_balita = ?");
    $stmt->bind_param("i", $id_balita);

    if ($stmt->execute()) {
        header("Location: ../data_anak.php?msg=deleted");
    } else {
        echo "Gagal menghapus data: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: ../data_anak.php");
}
$conn->close();
?>