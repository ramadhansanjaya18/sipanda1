<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
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
            <li><a href="dashboard_kader.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="data_anak.php"><i class="fa-solid fa-child"></i> Data Anak</a></li>
            <li><a href="jadwal_imunisasi.php"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
            <li><a href="laporan.php"><i class="fa-solid fa-chart-pie"></i> Laporan</a></li>
            <li><a href="manajemen_pengguna.php" class="active"><i class="fa-solid fa-users-cog"></i> Manajemen
                    Pengguna</a></li>
        </ul>
        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li><a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOG OUT</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content">

        <div class="main-header">
            <h1>Tambah Pengguna Baru</h1>
            <a href="manajemen_pengguna.php" class="btn-kembali"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="form-card">
            <form action="process/proses_tambah_pengguna.php" method="POST">
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-input"
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-input"
                                placeholder="Buat username unik" required>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-input"
                                placeholder="Minimal 6 karakter" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="role">Role Pengguna</label>
                            <select id="role" name="role" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Role --</option>
                                <option value="kader">Kader</option>
                                <option value="bidan">Bidan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-simpan">
                        <i class="fa-solid fa-user-plus"></i> Tambah Pengguna
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>

<?php include 'templates/footer.php'; ?>