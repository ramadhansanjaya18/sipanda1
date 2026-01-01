<?php
require 'config/database.php';


if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}


if (!isset($_GET['id_jadwal']) || !is_numeric($_GET['id_jadwal'])) {
    header('Location: ' . BASE_URL . '/jadwal_imunisasi.php');
    exit();
}
$id_jadwal_edit = $_GET['id_jadwal'];


$stmt = $conn->prepare("SELECT * FROM jadwal WHERE id_jadwal = ?");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $id_jadwal_edit);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {

    header('Location: ' . BASE_URL . '/jadwal_imunisasi.php?status=not_found');
    exit();
}
$jadwal = $result->fetch_assoc();
$stmt->close();

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
            <li><a href="data_anak.php"><i class="fa-solid fa-child"></i> Data Anak</a></li>
            <li><a href="jadwal_imunisasi.php" class="active"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
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
            <h1>Edit Jadwal Kegiatan</h1>
            <a href="jadwal_imunisasi.php" class="btn-kembali"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="form-card">
            <form action="process/proses_edit_jadwal.php" method="POST">
                <input type="hidden" name="id_jadwal" value="<?= $jadwal['id_jadwal'] ?>">

                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="judul_kegiatan">Judul Kegiatan</label>
                            <select id="judul_kegiatan" name="judul_kegiatan" class="form-select" required>
                                <option value="Imunisasi" <?= ($jadwal['judul_kegiatan'] == 'Imunisasi') ? 'selected' : '' ?>>Imunisasi</option>
                                <option value="Posyandu" <?= ($jadwal['judul_kegiatan'] == 'Posyandu') ? 'selected' : '' ?>>Posyandu</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_kegiatan">Tanggal Kegiatan</label>
                            <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" class="form-input"
                                value="<?= htmlspecialchars($jadwal['tanggal_kegiatan']) ?>" required>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi (Opsional)</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-textarea"
                                rows="4"><?= htmlspecialchars($jadwal['deskripsi'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-simpan">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan Jadwal
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>

<?php $conn->close();
include 'templates/footer.php';