<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/data_anak.php');
    exit();
}
$id_balita = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM balita WHERE id_balita = ?");
$stmt->bind_param("i", $id_balita);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . BASE_URL . '/data_anak.php?status=not_found');
    exit();
}
$balita = $result->fetch_assoc();
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
            <h1>Edit Data Anak</h1>
            <a href="data_anak.php" class="btn-kembali"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="form-card">
            <form action="process/proses_edit_anak.php" method="POST">

                <input type="hidden" name="id_balita" value="<?= $balita['id_balita'] ?>">

                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="nama_balita">Nama Lengkap (Anak)</label>
                            <input type="text" id="nama_balita" name="nama_balita" class="form-input"
                                value="<?= htmlspecialchars($balita['nama_balita']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nik_balita">NIK (Anak)</label>
                            <input type="text" id="nik_balita" name="nik_balita" class="form-input"
                                value="<?= htmlspecialchars($balita['nik_balita']) ?>" required maxlength="16"
                                minlength="16">
                        </div>
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-input"
                                value="<?= htmlspecialchars($balita['tanggal_lahir']) ?>" required>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                                <option value="Laki-laki" <?= ($balita['jenis_kelamin'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($balita['jenis_kelamin'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama_orang_tua">Nama Orang Tua</label>
                            <input type="text" id="nama_orang_tua" name="nama_orang_tua" class="form-input"
                                value="<?= htmlspecialchars($balita['nama_orang_tua']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" class="form-textarea"
                                rows="3"><?= htmlspecialchars($balita['alamat']) ?></textarea>
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