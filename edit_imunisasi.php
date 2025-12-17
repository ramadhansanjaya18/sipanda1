<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

if (!isset($_GET['id_imunisasi']) || !is_numeric($_GET['id_imunisasi'])) {
    header('Location: ' . BASE_URL . '/data_anak.php');
    exit();
}
$id_imunisasi = $_GET['id_imunisasi'];

$stmt = $conn->prepare(
    "SELECT i.*, b.nama_balita 
     FROM imunisasi i 
     JOIN balita b ON i.id_balita = b.id_balita 
     WHERE i.id_imunisasi = ?"
);
$stmt->bind_param("i", $id_imunisasi);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . BASE_URL . '/data_anak.php?status=not_found');
    exit();
}
$imunisasi = $result->fetch_assoc();
$stmt->close();

if ($imunisasi['status_validasi'] !== 'Belum Divalidasi') {
    header('Location: ' . BASE_URL . '/detail_anak.php?id=' . $imunisasi['id_balita'] . '&status=sudah_valid');
    exit();
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
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama_lengkap']) ?>&background=FBCFE8&color=B42373"
                alt="User Avatar">
            <div class="user-info">
                <h4><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h4>
                <p><?= htmlspecialchars(ucfirst($_SESSION['role'])) ?></p>
            </div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard_kader.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="data_anak.php" class="active"><i class="fa-solid fa-child"></i> Data Anak</a></li>
            <li><a href="jadwal_imunisasi.php"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
            <li><a href="laporan.php"><i class="fa-solid fa-chart-pie"></i> Laporan</a></li>
            <li><a href="manajemen_pengguna.php"><i class="fa-solid fa-users-cog"></i> Manajemen Pengguna</a></li>
        </ul>
        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li><a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOG OUT</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content">

        <div class="main-header">
            <h1>Edit Data Imunisasi</h1>
            <a href="detail_anak.php?id=<?= $imunisasi['id_balita'] ?>" class="btn-kembali"><i
                    class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="form-card">
            <form action="process/proses_edit_imunisasi.php" method="POST">
                <input type="hidden" name="id_imunisasi" value="<?= $imunisasi['id_imunisasi'] ?>">
                <input type="hidden" name="id_balita" value="<?= $imunisasi['id_balita'] ?>">
                <div class="form-group">
                    <label>Nama Balita</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($imunisasi['nama_balita']) ?>"
                        readonly disabled>
                </div>

                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="tanggal_imunisasi">Tanggal Imunisasi</label>
                            <input type="date" id="tanggal_imunisasi" name="tanggal_imunisasi" class="form-input"
                                value="<?= htmlspecialchars($imunisasi['tanggal_imunisasi']) ?>" required>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="jenis_vaksin">Jenis Vaksin</label>
                            <input type="text" id="jenis_vaksin" name="jenis_vaksin" class="form-input"
                                value="<?= htmlspecialchars($imunisasi['jenis_vaksin']) ?>"
                                placeholder="Contoh: Polio, DPT, BCG" required>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-simpan">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>

<?php $conn->close();
include 'templates/footer.php'; ?>