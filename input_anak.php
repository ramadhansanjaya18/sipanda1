<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    header('Location: ' . BASE_URL . '/login.php');
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
            <li>
                <a href="dashboard_kader.php">
                    <i class="fa-solid fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="data_anak.php" class="active">
                    <i class="fa-solid fa-child"></i> Data Anak
                </a>
            </li>
            <li>
                <a href="jadwal_imunisasi.php">
                    <i class="fa-solid fa-calendar-check"></i> Jadwal
                </a>
            </li>
            <li>
                <a href="laporan.php">
                    <i class="fa-solid fa-chart-pie"></i> Laporan
                </a>
            </li>
            <li>
                <a href="manajemen_pengguna.php">
                    <i class="fa-solid fa-users-cog"></i> Manajemen Pengguna
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li>
                    <a href="<?= BASE_URL ?>/logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i> LOG OUT
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <main class="main-content">

        <div class="main-header">
            <h1>Input Data Anak Baru</h1>
        </div>

        <div class="form-card">
            <form action="process/proses_input_anak.php" method="POST">
                <input type="hidden" name="id_kader_pendaftar" value="<?= htmlspecialchars($_SESSION['id_user']) ?>">

                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="nama_balita">Nama Lengkap (Anak)</label>
                            <input type="text" id="nama_balita" name="nama_balita" class="form-input"
                                placeholder="Masukkan nama balita" required>
                        </div>
                        <div class="form-group">
                            <label for="nik_balita">NIK (Anak)</label>
                            <input type="text" id="nik_balita" name="nik_balita" class="form-input"
                                placeholder="Masukkan 16 digit NIK" required maxlength="16" minlength="16">
                        </div>
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-input" required>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama_orang_tua">Nama Orang Tua</label>
                            <input type="text" id="nama_orang_tua" name="nama_orang_tua" class="form-input"
                                placeholder="Nama Ayah / Ibu" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" class="form-textarea" rows="3"
                                placeholder="Masukkan alamat lengkap"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-simpan">
                        <i class="fa-solid fa-save"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>

<?php include 'templates/footer.php'; ?>