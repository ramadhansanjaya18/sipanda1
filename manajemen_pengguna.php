<?php
require 'config/database.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {

    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$query_users = "SELECT id_user, nama_lengkap, username, role FROM users WHERE is_active = 1 ORDER BY nama_lengkap ASC";
$result_users = $conn->query($query_users);

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
            <h2>Manajemen Pengguna</h2>
            <a href="#" id="openModalUserBtn" class="btn-primary">
                <i class="fa-solid fa-user-plus"></i> Tambah Pengguna Baru
            </a>
        </div>

        <div class="data-table-wrapper">
            <table class="data-table" id="tabel-pengguna">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_users && $result_users->num_rows > 0): ?>
                        <?php while ($row = $result_users->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= ucfirst(htmlspecialchars($row['role'])) ?></td>
                                <td>
                                    <a href="edit_pengguna.php?id=<?= $row['id_user'] ?>" class="btn-aksi btn-edit"
                                        title="Edit Pengguna"><i class="fa-solid fa-pencil"></i></a>
                                    <a href="process/hapus_pengguna.php?id=<?= $row['id_user'] ?>" class="btn-aksi btn-hapus"
                                        title="Hapus Pengguna"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna <?= htmlspecialchars(addslashes($row['nama_lengkap'])) // Gunakan addslashes untuk nama ?>? Aksi ini tidak dapat dibatalkan.');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">Belum ada pengguna terdaftar.</td>
                        </tr> {/* Colspan 4 */}
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>
<div id="modalTambahUser" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Tambah Pengguna Baru</h2>
        <div id="modalUserMessage" class="message-area"></div>

        <form id="formInputUserModal">
            <div class="form-grid">
                <div class="form-column">
                    <div class="form-group">
                        <label for="modal_nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="modal_nama_lengkap" name="nama_lengkap" class="form-input"
                            placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_username">Username</label>
                        <input type="text" id="modal_username" name="username" class="form-input"
                            placeholder="Buat username unik" required>
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-group">
                        <label for="modal_password">Password</label>
                        <input type="password" id="modal_password" name="password" class="form-input"
                            placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label for="modal_role">Role Pengguna</label>
                        <select id="modal_role" name="role" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Role --</option>
                            <option value="kader">Kader</option>
                            <option value="bidan">Bidan</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-simpan">
                    <i class="fa-solid fa-save"></i> Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<?php
if ($conn)
    $conn->close();
include 'templates/footer.php';
?>