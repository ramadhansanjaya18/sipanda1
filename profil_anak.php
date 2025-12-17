<?php
session_start();
require 'config/database.php';

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'orangtua' ||
    !isset($_SESSION['id_balita'])
) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$id_balita = $_SESSION['id_balita'];

$stmt = $conn->prepare("SELECT * FROM balita WHERE id_balita = ?");
$stmt->bind_param("i", $id_balita);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . BASE_URL . '/logout.php');
    exit;
}

$balita = $result->fetch_assoc();
$stmt->close();
$data_kelahiran = [
    'bbl' => $balita['berat_lahir'] ?? '3.2',
    'tbl' => $balita['tinggi_lahir'] ?? '50',
    'lkl' => $balita['lingkar_kepala_lahir'] ?? '34',
];

$data_orang_tua = [
    'nama_Orang_Tua' => $balita['nama_Orang_Tua'] ?? htmlspecialchars($balita['nama_orang_tua']), // Gunakan nama ortu jika nama ibu tidak ada
    'Alamat' => $balita['Alamat'] ?? 'Blok Celo RT.21/RW.05',
];

$folder_foto = 'uploads/profil/';
if (!empty($balita['foto_profil']) && file_exists($folder_foto . $balita['foto_profil'])) {
    $foto_profil_url = BASE_URL . '/' . $folder_foto . $balita['foto_profil'];
} else {
    $foto_profil_url = "https://ui-avatars.com/api/?name=" . urlencode($balita['nama_balita']) . "&background=FBCFE8&color=B42373&size=100";
}

function hitungUmur($tgl_lahir)
{
    if (empty($tgl_lahir))
        return "N/A";
    $lahir = new DateTime($tgl_lahir);
    $sekarang = new DateTime();
    $umur = $sekarang->diff($lahir);
    if ($umur->y > 0)
        return $umur->y . " tahun";
    if ($umur->m > 0)
        return $umur->m . " bulan";
    return $umur->d . " hari";
}

$page_title = 'SIPANDA';
$page_css = 'dashboard.css';
include 'templates/header.php';
?>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>SIPANDA</h2>
        </div>
        <div class="sidebar-user">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($balita['nama_orang_tua']) ?>&background=FBCFE8&color=B42373"
                alt="Avatar Orang Tua">
            <div class="user-info">
                <h4><?= htmlspecialchars($balita['nama_orang_tua']) ?></h4>
                <p>Orang Tua</p>
            </div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard_orangtua.php"><i class="fa-solid fa-house"></i> Beranda</a></li>
            <li><a href="perkembangan_anak.php"><i class="fa-solid fa-chart-line"></i> Perkembangan Anak</a></li>
            <li><a href="profil_anak.php" class="active"><i class="fa-solid fa-user"></i> Profil Anak</a></li>
        </ul>
        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li><a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOG OUT</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content">
        <div class="profile-layout-container">

            <div class="profile-info-center">
                <div class="profile-photo-lg-container">
                    <img src="<?= $foto_profil_url ?>" alt="Foto Profil Balita" class="profile-photo-lg">
                </div>

                <h3><?= htmlspecialchars($balita['nama_balita']) ?></h3>
                <p class="detail-info">NIK: <?= htmlspecialchars($balita['nik_balita']) ?></p>
                <p class="detail-info">
                    <?= htmlspecialchars($balita['jenis_kelamin']) ?>,
                    <?= date('d F Y', strtotime($balita['tanggal_lahir'])) ?>
                </p>
                <p class="detail-info" style="font-style: italic;">Usia: <?= hitungUmur($balita['tanggal_lahir']) ?></p>

                <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 30px 0;">

                <div class="data-section-2-cols">

                    <div class="data-card">
                        <h4>Data Kelahiran</h4>
                        <div class="data-row">
                            <span>Berat Badan Lahir (BBL)</span>
                            <strong><?= htmlspecialchars($data_kelahiran['bbl']) ?><?= ($data_kelahiran['bbl'] !== 'N/A') ? ' kg' : '' ?></strong>
                        </div>
                        <div class="data-row">
                            <span>Tinggi Badan Lahir (TBL)</span>
                            <strong><?= htmlspecialchars($data_kelahiran['tbl']) ?><?= ($data_kelahiran['tbl'] !== 'N/A') ? ' cm' : '' ?></strong>
                        </div>
                        <div class="data-row">
                            <span>Lingkar Kepala Lahir</span>
                            <strong><?= htmlspecialchars($data_kelahiran['lkl']) ?><?= ($data_kelahiran['lkl'] !== 'N/A') ? ' cm' : '' ?></strong>
                        </div>
                    </div>

                    <div class="data-card">
                        <h4>Data Wali / Orang Tua</h4>
                        <div class="data-row-wali">
                            <span>Nama Orang Tua</span>
                            <strong><?= htmlspecialchars($data_orang_tua['nama_Orang_Tua']) ?></strong>
                        </div>
                        <div class="data-row-wali">
                            <span>Alamat</span>
                            <strong><?= htmlspecialchars($data_orang_tua['Alamat']) ?></strong>
                        </div>
                    </div>

                </div>

            </div>

    </main>
</div>

<?php
$conn->close();
include 'templates/footer.php';
?>