<?php
require '../config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    die("Akses dilarang.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_balita = $_POST['id_balita'] ?? null;
    $nama_balita = $_POST['nama_balita'] ?? '';
    $nik_balita = $_POST['nik_balita'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $nama_orang_tua = $_POST['nama_orang_tua'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    if (empty($id_balita) || empty($nama_balita) || empty($nik_balita) || empty($tanggal_lahir) || empty($jenis_kelamin) || empty($nama_orang_tua)) {
        header('Location: ' . BASE_URL . '/edit_anak.php?id=' . $id_balita . '&status=error');
        exit();
    }

    $stmt = $conn->prepare(
        "UPDATE balita SET 
            nama_balita = ?, 
            nik_balita = ?, 
            tanggal_lahir = ?, 
            jenis_kelamin = ?, 
            nama_orang_tua = ?, 
            alamat = ? 
         WHERE id_balita = ?"
    );

    $stmt->bind_param(
        "ssssssi",
        $nama_balita,
        $nik_balita,
        $tanggal_lahir,
        $jenis_kelamin,
        $nama_orang_tua,
        $alamat,
        $id_balita
    );

    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . '/data_anak.php?status=sukses_edit');
    } else {
        if ($conn->errno == 1062) {
            header('Location: ' . BASE_URL . '/edit_anak.php?id=' . $id_balita . '&status=nik_duplikat');
        } else {
            header('Location: ' . BASE_URL . '/edit_anak.php?id=' . $id_balita . '&status=gagal_edit');
        }
    }

    $stmt->close();
    $conn->close();

} else {
    header('Location: ' . BASE_URL . '/dashboard_kader.php');
}
exit();
?>