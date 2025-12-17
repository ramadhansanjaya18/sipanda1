<?php
require '../config/database.php'; 


if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    die("Akses dilarang.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $id_imunisasi = $_POST['id_imunisasi'] ?? null;
    $id_balita = $_POST['id_balita'] ?? null; 
    $tanggal_imunisasi = $_POST['tanggal_imunisasi'] ?? '';
    $jenis_vaksin = $_POST['jenis_vaksin'] ?? '';
    
    $catatan = !empty($_POST['catatan']) ? $_POST['catatan'] : NULL;

    
    if (empty($id_imunisasi) || empty($id_balita) || empty($tanggal_imunisasi) || empty($jenis_vaksin)) {
        header('Location: ' . BASE_URL . '/edit_imunisasi.php?id_imunisasi=' . $id_imunisasi . '&status=error');
        exit();
    }

    
    
    $stmt = $conn->prepare(
        "UPDATE imunisasi SET 
            tanggal_imunisasi = ?, 
            jenis_vaksin = ?, 
            catatan = ?          -- <<< TAMBAHKAN INI
         WHERE id_imunisasi = ? AND status_validasi = 'Belum Divalidasi'" 
    );
    if (!$stmt) {
        die("Error preparing update statement: " . $conn->error);
    } 

    
    
    $stmt->bind_param(
        "sssi",
        $tanggal_imunisasi,
        $jenis_vaksin,
        $catatan,       
        $id_imunisasi
    );

    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?id=' . $id_balita . '&status=sukses_edit_imunisasi');
        } else {
            header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?id=' . $id_balita . '&status=gagal_edit_imunisasi_2'); 
        }
    } else {
        
        header('Location: ' . BASE_URL . '/edit_imunisasi.php?id_imunisasi=' . $id_imunisasi . '&status=gagal_edit_imunisasi_1');
    }

    $stmt->close();
    $conn->close();

} else {
    
    header('Location: ' . BASE_URL . '/dashboard_kader.php');
}
exit();
?>