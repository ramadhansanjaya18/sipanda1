<?php
require '../config/database.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'orangtua' || !isset($_SESSION['id_balita'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit("Akses ditolak.");
}

$id_balita = $_SESSION['id_balita'];
$redirect_url = BASE_URL . '/profil_anak.php';


if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {

    $target_dir = "../uploads/profil/";
    $file = $_FILES['foto_profil'];




    $max_size = 2 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        header('Location: ' . $redirect_url . '?status=error_size');
        exit();
    }


    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $file_type = mime_content_type($file['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        header('Location: ' . $redirect_url . '?status=error_type');
        exit();
    }


    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid('balita_', true) . '.' . strtolower($extension);
    $target_file = $target_dir . $new_filename;


    $stmt_old = $conn->prepare("SELECT foto_profil FROM balita WHERE id_balita = ?");
    $stmt_old->bind_param("i", $id_balita);
    $stmt_old->execute();
    $result_old = $stmt_old->get_result();
    $data_lama = $result_old->fetch_assoc();
    $stmt_old->close();


    if (move_uploaded_file($file['tmp_name'], $target_file)) {


        $stmt_update = $conn->prepare("UPDATE balita SET foto_profil = ? WHERE id_balita = ?");
        $stmt_update->bind_param("si", $new_filename, $id_balita);

        if ($stmt_update->execute()) {

            if (!empty($data_lama['foto_profil']) && file_exists($target_dir . $data_lama['foto_profil'])) {
                unlink($target_dir . $data_lama['foto_profil']);
            }
            header('Location: ' . $redirect_url . '?status=sukses_upload');
        } else {
            header('Location: ' . $redirect_url . '?status=error_db');
        }
        $stmt_update->close();

    } else {
        header('Location: ' . $redirect_url . '?status=error_move');
    }

} else {

    header('Location: ' . $redirect_url . '?status=error_file');
}

$conn->close();
exit();
?>