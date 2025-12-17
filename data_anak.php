<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader')) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$query_balita = "SELECT id_balita, nama_balita, tanggal_lahir, nama_orang_tua FROM balita WHERE is_active = 1 ORDER BY nama_balita ASC";
$result_balita = $conn->query($query_balita);
$page_title = 'SIPANDA';
$page_css = 'dashboard.css';
include 'templates/header.php';

function hitungUmur($tanggal_lahir)
{
    if (empty($tanggal_lahir)) {
        return "N/A";
    }
    $lahir = new DateTime($tanggal_lahir);
    $hari_ini = new DateTime();
    $umur = $hari_ini->diff($lahir);

    if ($umur->y > 0) {
        return $umur->y . " tahun";
    } else if ($umur->m > 0) {
        return $umur->m . " bulan";
    } else {
        return $umur->d . " hari";
    }
}
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
            <div style="display: flex; align-items: center;">
                <button id="sidebarToggle" class="btn-toggle-sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 style="margin: 0;"> Manajemen Data Anak</h1>
            </div>

            <div class="header-actions">
                <a href="#" id="openModalAnakBtn" class="btn-primary">
                    <i class="fa-solid fa-child"></i> Input Data Anak
                </a>
            </div>
        </div>

        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Anak</th>
                        <th>Usia</th>
                        <th>Nama Orang Tua</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_balita && $result_balita->num_rows > 0) {
                        while ($row = $result_balita->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['nama_balita']) . "</td>";
                            echo "<td>" . hitungUmur($row['tanggal_lahir']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_orang_tua']) . "</td>";
                            echo "<td>
                                    <a href='detail_anak.php?id=" . $row['id_balita'] . "' class='btn-aksi btn-detail'><i class='fa-solid fa-eye'></i></a>
                                    <a href='edit_anak.php?id=" . $row['id_balita'] . "' class='btn-aksi btn-edit'><i class='fa-solid fa-pencil'></i></a>
                                    <a href='process/hapus_anak.php?id=" . $row['id_balita'] . "' 
                                       class='btn-aksi btn-hapus' 
                                       onclick=\"return confirm('Apakah Anda yakin ingin menghapus data anak ini? Data pemeriksaan dan imunisasi yang terkait juga akan terhapus permanen.');\">
                                       <i class='fa-solid fa-trash'></i>
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center;'>Belum ada data anak.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

<div id="modalInputAnak" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Input Data Anak Baru</h2>
        <div id="modalAnakMessage" class="message-area"></div>

        <form id="formInputAnakModal">
            <div class="form-grid">
                <div class="form-column">
                    <div class="form-group">
                        <label for="modal_nama_balita">Nama Lengkap (Anak)</label>
                        <input type="text" id="modal_nama_balita" name="nama_balita" class="form-input"
                            placeholder="Masukkan nama balita" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_nik_balita">NIK (Anak)</label>
                        <input type="text" id="modal_nik_balita" name="nik_balita" class="form-input"
                            placeholder="16 digit NIK" required maxlength="16" minlength="16">
                    </div>
                    <div class="form-group">
                        <label for="modal_tanggal_lahir_anak">Tanggal Lahir</label>
                        <input type="date" id="modal_tanggal_lahir_anak" name="tanggal_lahir" class="form-input"
                            required>
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-group">
                        <label for="modal_jenis_kelamin">Jenis Kelamin</label>
                        <select id="modal_jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modal_nama_orang_tua">Nama Orang Tua</label>
                        <input type="text" id="modal_nama_orang_tua" name="nama_orang_tua" class="form-input"
                            placeholder="Nama Ayah / Ibu" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_alamat">Alamat</label>
                        <textarea id="modal_alamat" name="alamat" class="form-textarea" rows="2"
                            placeholder="Alamat lengkap..."></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-simpan">
                    <i class="fa-solid fa-save"></i> Simpan Data Anak
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'templates/footer.php'; ?>