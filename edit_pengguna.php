<?php
require 'config/database.php'; // Muat konfigurasi

// Pengecekan sesi HANYA untuk Kader
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

// 1. Ambil ID Pengguna dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/manajemen_pengguna.php');
    exit();
}
$id_user_edit = $_GET['id'];

// 2. Query untuk mengambil data pengguna yang akan diedit
$stmt = $conn->prepare("SELECT id_user, nama_lengkap, username, role FROM users WHERE id_user = ?"); // Query tabel users
$stmt->bind_param("i", $id_user_edit);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika pengguna tidak ditemukan
    header('Location: ' . BASE_URL . '/manajemen_pengguna.php?status=not_found');
    exit();
}
$user = $result->fetch_assoc();
$stmt->close();

// Menyiapkan variabel untuk header
$page_title = 'SIPANDA';
$page_css = 'dashboard.css'; // Gunakan CSS dasbor
include 'templates/header.php'; // Muat header
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
            <h1>Edit Pengguna</h1>
            <a href="manajemen_pengguna.php" class="btn-kembali"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="form-card">
            <form action="process/proses_edit_pengguna.php" method="POST">
                <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">

                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-input"
                                value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-input"
                                value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="role">Role Pengguna</label>
                            <select id="role" name="role" class="form-select" required>
                                <option value="kader" <?= ($user['role'] == 'kader') ? 'selected' : '' ?>>Kader</option>
                                <option value="bidan" <?= ($user['role'] == 'bidan') ? 'selected' : '' ?>>Bidan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru (Opsional)</label>
                            <input type="password" id="password" name="password" class="form-input"
                                placeholder="Isi hanya jika ingin mengubah" minlength="6">
                            <small>Kosongkan jika tidak ingin mengubah password.</small>
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
include 'templates/footer.php'; // Muat footer ?>