<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader')) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

if (!isset($_GET['id_pemeriksaan']) || !is_numeric($_GET['id_pemeriksaan'])) {
    header('Location: ' . BASE_URL . '/data_anak.php');
    exit();
}
$id_pemeriksaan = $_GET['id_pemeriksaan'];

$stmt = $conn->prepare(
    "SELECT p.*, b.nama_balita 
     FROM pemeriksaan p 
     JOIN balita b ON p.id_balita = b.id_balita 
     WHERE p.id_pemeriksaan = ?"
);
$stmt->bind_param("i", $id_pemeriksaan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . BASE_URL . '/data_anak.php?status=not_found');
    exit();
}
$pemeriksaan = $result->fetch_assoc();
$stmt->close();

if ($pemeriksaan['status_validasi'] !== 'Belum Divalidasi') {
    header('Location: ' . BASE_URL . '/detail_anak.php?id=' . $pemeriksaan['id_balita'] . '&status=sudah_valid');
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
            <h1>Edit Data Pemeriksaan</h1>
            <a href="detail_anak.php?id=<?= $pemeriksaan['id_balita'] ?>" class="btn-kembali"><i
                    class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="form-card">
            <form action="process/proses_edit_pemeriksaan.php" method="POST">
                <input type="hidden" name="id_pemeriksaan" value="<?= $pemeriksaan['id_pemeriksaan'] ?>">
                <input type="hidden" name="id_balita" value="<?= $pemeriksaan['id_balita'] ?>">
                <div class="form-group">
                    <label>Nama Balita</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($pemeriksaan['nama_balita']) ?>"
                        readonly disabled>
                </div>

                <div class="form-group">
                    <label for="tanggal_periksa">Tanggal Pemeriksaan</label>
                    <input type="date" id="tanggal_periksa" name="tanggal_periksa" class="form-input"
                        value="<?= htmlspecialchars($pemeriksaan['tanggal_periksa']) ?>" required>
                </div>

                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="berat_badan">Berat Badan (kg)</label>
                            <input type="number" step="0.01" id="berat_badan" name="berat_badan" class="form-input"
                                value="<?= htmlspecialchars($pemeriksaan['berat_badan']) ?>" placeholder="Contoh: 8.5">
                        </div>
                        <div class="form-group">
                            <label for="tinggi_badan">Tinggi Badan (cm)</label>
                            <input type="number" step="0.01" id="tinggi_badan" name="tinggi_badan" class="form-input"
                                value="<?= htmlspecialchars($pemeriksaan['tinggi_badan']) ?>"
                                placeholder="Contoh: 70.2">
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="lingkar_kepala">Lingkar Kepala (cm)</label>
                            <input type="number" step="0.01" id="lingkar_kepala" name="lingkar_kepala"
                                class="form-input" value="<?= htmlspecialchars($pemeriksaan['lingkar_kepala']) ?>"
                                placeholder="Contoh: 45.0">
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