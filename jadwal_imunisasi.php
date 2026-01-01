<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$is_kader = ($_SESSION['role'] == 'kader');

$query_jadwal = "SELECT * FROM jadwal ORDER BY tanggal_kegiatan DESC";
$result_jadwal = $conn->query($query_jadwal);

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

        <?php if ($is_kader): ?>
            <ul class="sidebar-nav">
                <li><a href="dashboard_kader.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="data_anak.php"><i class="fa-solid fa-child"></i> Data Anak</a></li>
                <li><a href="jadwal_imunisasi.php" class="active"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
                <li><a href="laporan.php"><i class="fa-solid fa-chart-pie"></i> Laporan</a></li>
                <li><a href="manajemen_pengguna.php"><i class="fa-solid fa-users-cog"></i> Manajemen Pengguna</a></li>
            </ul>
        <?php else: ?>
            <ul class="sidebar-nav">
                <li><a href="dashboard_bidan.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="data_anak_bidan.php"><i class="fa-solid fa-child"></i> Data Anak</a></li>
                <li><a href="jadwal_imunisasi.php" class="active"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
                <li><a href="validasi_data.php"><i class="fa-solid fa-clipboard-check"></i> Validasi Data</a></li>
                <li><a href="laporan.php"><i class="fa-solid fa-chart-pie"></i> Laporan</a></li>
            </ul>
        <?php endif; ?>

        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li><a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOG OUT</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content">

        <div class="main-header">
            <div style="display: flex; align-items: center;">
                <button id="sidebarToggle" class="btn-toggle-sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 style="margin: 0;">Jadwal Kegiatan</h1>
            </div>

            <?php if ($is_kader): ?>
                <div class="header-actions">
                    <a href="#" id="openModalJadwalBtn" class="btn-primary">
                        <i class="fa-solid fa-calendar-plus"></i> Input Jadwal Kegiatan Baru
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="history-section-2">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Kegiatan</th>
                            <th>Judul Kegiatan</th>
                            <th>Deskripsi</th>
                            <?php if ($is_kader): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_jadwal->num_rows > 0): ?>
                            <?php while ($row = $result_jadwal->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?></td>
                                    <td><?= htmlspecialchars($row['judul_kegiatan']) ?></td>
                                    <td><?= htmlspecialchars($row['deskripsi'] ?? '-') ?></td>
                                    <?php if ($is_kader): ?>
                                        <td>
                                            <a href='edit_jadwal.php?id_jadwal=<?= $row['id_jadwal'] ?>' class='btn-aksi btn-edit'
                                                title="Edit Jadwal"><i class='fa-solid fa-pencil'></i></a>
                                            <a href='process/hapus_jadwal.php?id_jadwal=<?= $row['id_jadwal'] ?>'
                                                class='btn-aksi btn-hapus' title="Hapus Jadwal"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal?');">
                                                <i class='fa-solid fa-trash'></i>
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $is_kader ? '4' : '3' ?>" style="text-align:center;">Belum ada jadwal
                                    kegiatan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<div id="modalJadwal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Input Jadwal Kegiatan Baru</h2>
        <div id="modalJadwalMessage" class="message-area"></div>

        <form id="formInputJadwalModal">
            <div class="form-grid">
                <div class="form-column">
                    <div class="form-group">
                        <label for="modal_judul_kegiatan">Judul Kegiatan</label>
                        <select id="modal_judul_kegiatan" name="judul_kegiatan" class="form-select" required>
                            <option value="" disabled selected>Pilih Jenis Kegiatan</option>
                            <option value="Imunisasi">Imunisasi</option>
                            <option value="Posyandu">Posyandu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modal_tanggal_kegiatan">Tanggal Kegiatan</label>
                        <input type="date" id="modal_tanggal_kegiatan" name="tanggal_kegiatan" class="form-input"
                            value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-group">
                        <label for="modal_deskripsi">Deskripsi (Opsional)</label>
                        <textarea id="modal_deskripsi" name="deskripsi" class="form-textarea" rows="4"
                            placeholder="Masukkan deskripsi..."></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="border-top: none; padding-top: 0;">
                <button type="submit" class="btn-simpan">
                    <i class="fa-solid fa-save"></i> Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
<?php
$conn->close();
include 'templates/footer.php';
?>