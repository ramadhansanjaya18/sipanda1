<?php
require 'config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kader') {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$query_total_anak = "SELECT COUNT(id_balita) AS total FROM balita";
$result_total_anak = $conn->query($query_total_anak);
$data_total_anak = $result_total_anak->fetch_assoc();
$total_anak = $data_total_anak['total'] ?? 0;

$query_belum_imunisasi = "
    SELECT COUNT(id_balita) AS total_belum
    FROM balita
    WHERE id_balita NOT IN (SELECT DISTINCT id_balita FROM imunisasi)";
$result_belum_imunisasi = $conn->query($query_belum_imunisasi);
$data_belum_imunisasi = $result_belum_imunisasi->fetch_assoc();
$total_belum_imunisasi = $data_belum_imunisasi['total_belum'] ?? 0;
$query_pemeriksaan_bulan_ini = "
    SELECT COUNT(id_pemeriksaan) AS total_bulan_ini
    FROM pemeriksaan
    WHERE MONTH(tanggal_periksa) = MONTH(CURDATE())
      AND YEAR(tanggal_periksa) = YEAR(CURDATE())";
$result_pemeriksaan_bulan_ini = $conn->query($query_pemeriksaan_bulan_ini);
$data_pemeriksaan_bulan_ini = $result_pemeriksaan_bulan_ini->fetch_assoc();
$total_pemeriksaan_bulan_ini = $data_pemeriksaan_bulan_ini['total_bulan_ini'] ?? 0;

$query_aktivitas = "
    (SELECT 
        b.nama_balita, 
        'Data Anak' AS jenis_data, 
        b.created_at AS waktu_input 
    FROM balita b)
    UNION ALL
    (SELECT 
        b.nama_balita, 
        'Perkembangan Bulanan' AS jenis_data, 
        p.created_at AS waktu_input
    FROM pemeriksaan p
    JOIN balita b ON p.id_balita = b.id_balita)
    UNION ALL
    (SELECT 
        b.nama_balita, 
        'Imunisasi' AS jenis_data, 
        i.created_at AS waktu_input
    FROM imunisasi i
    JOIN balita b ON i.id_balita = b.id_balita)
    ORDER BY waktu_input DESC
    LIMIT 5
";
$result_aktivitas = $conn->query($query_aktivitas);

$query_modal_balita = "SELECT id_balita, nama_balita, nik_balita FROM balita ORDER BY nama_balita ASC";
$result_modal_balita = $conn->query($query_modal_balita);

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
                <a href="dashboard_kader.php" class="active">
                    <i class="fa-solid fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="data_anak.php">
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
                <h1 style="margin: 0;">Dashboard</h1>
            </div>
        </div>

        <div class="stats-cards">
            <div class="card">
                <p>Total Anak Terdaftar</p>
                <h3><?= $total_anak ?></h3>
                <span class="percentage">Anak</span>
            </div>
            <div class="card">
                <p>Anak Belum Imunisasi</p>
                <h3><?= $total_belum_imunisasi ?></h3>
                <span class="percentage">Perlu Ditindak</span>
            </div>
            <div class="card">
                <p>Pemeriksaan Bulan Ini</p>
                <h3><?= $total_pemeriksaan_bulan_ini ?></h3>
                <span class="percentage">dari <?= $total_anak ?> anak terdaftar</span>
            </div>
        </div>

        <div class="header-actions">
            <a href="#" id="openModalImunisasiBtn" class="btn-primary"> <i class="fa-solid fa-syringe"></i> Input Data
                Imunisasi
            </a>
            <a href="#" id="openModalPerkembanganBtn" class="btn-primary">
                <i class="fa-solid fa-chart-line"></i> Input Perkembangan Bulanan
            </a>
        </div>

        <div class="activity-section">
            <h2>Aktivitas Terbaru</h2>
            <div class="table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Nama Anak</th>
                            <th>Jenis Data</th>
                            <th>Waktu Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_aktivitas && $result_aktivitas->num_rows > 0) {
                            while ($row = $result_aktivitas->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='activity-name'>" . htmlspecialchars($row['nama_balita']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['jenis_data']) . "</td>";
                                echo "<td>" . date('Y-m-d H:i', strtotime($row['waktu_input'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center;'>Belum ada aktivitas terbaru.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
<div id="modalPerkembangan" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Input Perkembangan Bulanan</h2>
        <div id="modalMessage" class="message-area"></div>

        <form id="formInputPerkembanganModal">
            <input type="hidden" name="id_kader" value="<?= htmlspecialchars($_SESSION['id_user']) ?>">

            <div class="form-group">
                <label for="modal_id_balita">Nama Balita</label>
                <select id="modal_id_balita" name="id_balita" class="form-select" required>
                    <option value="" disabled selected>Pilih Nama Balita</option>
                    <?php

                    if ($result_modal_balita->num_rows > 0) {
                        while ($row = $result_modal_balita->fetch_assoc()) {
                            echo "<option value='" . $row['id_balita'] . "'>" . htmlspecialchars($row['nama_balita']) . " (NIK: " . htmlspecialchars($row['nik_balita']) . ")</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="modal_tanggal_periksa">Tanggal Pemeriksaan</label>
                <input type="date" id="modal_tanggal_periksa" name="tanggal_periksa" class="form-input"
                    value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-grid">
                <div class="form-column">
                    <div class="form-group">
                        <label for="modal_berat_badan">Berat Badan (kg)</label>
                        <input type="number" step="0.01" id="modal_berat_badan" name="berat_badan" class="form-input"
                            placeholder="Contoh: 8.5">
                    </div>
                    <div class="form-group">
                        <label for="modal_tinggi_badan">Tinggi Badan (cm)</label>
                        <input type="number" step="0.01" id="modal_tinggi_badan" name="tinggi_badan" class="form-input"
                            placeholder="Contoh: 70.2">
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-group">
                        <label for="modal_lingkar_kepala">Lingkar Kepala (cm)</label>
                        <input type="number" step="0.01" id="modal_lingkar_kepala" name="lingkar_kepala"
                            class="form-input" placeholder="Contoh: 45.0">
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
</div>

<div id="modalImunisasi" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Input Data Imunisasi</h2>
        <div id="modalImunisasiMessage" class="message-area"></div>

        <form id="formInputImunisasiModal">
            <input type="hidden" name="id_kader" value="<?= htmlspecialchars($_SESSION['id_user']) ?>">

            <div class="form-group">
                <label for="imun_id_balita">Nama Balita</label>
                <select id="imun_id_balita" name="id_balita" class="form-select" required>
                    <option value="" disabled selected>Pilih Nama Balita</option>
                    <?php
                    if ($result_modal_balita && $result_modal_balita->num_rows > 0) {
                        $result_modal_balita->data_seek(0);
                        while ($row = $result_modal_balita->fetch_assoc()) {
                            echo "<option value='" . $row['id_balita'] . "'>" . htmlspecialchars($row['nama_balita']) . " (NIK: " . htmlspecialchars($row['nik_balita']) . ")</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-grid">
                <div class="form-column">
                    <div class="form-group">
                        <label for="imun_tanggal_imunisasi">Tanggal Imunisasi</label>
                        <input type="date" id="imun_tanggal_imunisasi" name="tanggal_imunisasi" class="form-input"
                            value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-group">
                        <label for="imun_jenis_vaksin">Jenis Vaksin</label>
                        <input type="text" id="imun_jenis_vaksin" name="jenis_vaksin" class="form-input"
                            placeholder="Contoh: Polio, DPT, BCG" required>
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
</div>
<?php include 'templates/footer.php'; ?>