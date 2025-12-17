<?php
require '../config/database.php';


if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    die("Akses dilarang.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $id_pemeriksaan = $_POST['id_pemeriksaan'] ?? null;
    $id_balita = $_POST['id_balita'] ?? null;
    $tanggal_periksa = $_POST['tanggal_periksa'] ?? '';
    $berat_badan = !empty($_POST['berat_badan']) ? $_POST['berat_badan'] : NULL;
    $tinggi_badan = !empty($_POST['tinggi_badan']) ? $_POST['tinggi_badan'] : NULL;
    $lingkar_kepala = !empty($_POST['lingkar_kepala']) ? $_POST['lingkar_kepala'] : NULL;
    $catatan = !empty($_POST['catatan']) ? $_POST['catatan'] : NULL;


    if (empty($id_pemeriksaan) || empty($id_balita) || empty($tanggal_periksa)) {
        header('Location: ' . BASE_URL . '/edit_pemeriksaan.php?id_pemeriksaan=' . $id_pemeriksaan . '&status=error');
        exit();
    }


    $stmt = $conn->prepare(
        "UPDATE pemeriksaan SET 
            tanggal_periksa = ?, 
            berat_badan = ?, 
            tinggi_badan = ?, 
            lingkar_kepala = ?, 
            catatan = ?           -- <<< TAMBAHKAN INI
         WHERE id_pemeriksaan = ? AND status_validasi = 'Belum Divalidasi'"
    );
    if (!$stmt) {
        die("Error preparing update statement: " . $conn->error);
    }


    $stmt->bind_param(
        "sddssi",
        $tanggal_periksa,
        $berat_badan,
        $tinggi_badan,
        $lingkar_kepala,
        $catatan,
        $id_pemeriksaan
    );


    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header('Location: ' . BASE_URL . '/detail_anak.php?id=' . $id_balita . '&status=sukses_edit_pemeriksaan');
        } else {
            header('Location: ' . BASE_URL . '/detail_anak.php?id=' . $id_balita . '&status=gagal_edit_pemeriksaan_2');
        }
    } else {

        header('Location: ' . BASE_URL . '/edit_pemeriksaan.php?id_pemeriksaan=' . $id_pemeriksaan . '&status=gagal_edit_pemeriksaan_1');
    }

    $stmt->close();
    $conn->close();

} else {
    header('Location: ' . BASE_URL . '/dashboard_kader.php');
}
exit();
?>