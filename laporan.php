<?php
require 'config/database.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'kader' && $_SESSION['role'] !== 'bidan')) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$tahun_sekarang = date('Y');
$daftar_tahun = range($tahun_sekarang, $tahun_sekarang - 5);

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
                alt="User">
            <div class="user-info">
                <h4><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h4>
                <p><?= htmlspecialchars(ucfirst($_SESSION['role'])) ?></p>
            </div>
        </div>
        <ul class="sidebar-nav">
            <?php if ($_SESSION['role'] == 'bidan'): ?>
                <li><a href="dashboard_bidan.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="data_anak_bidan.php"><i class="fa-solid fa-child"></i> Data Anak</a></li>
                <li><a href="jadwal_imunisasi.php"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
                <li><a href="validasi_data.php"><i class="fa-solid fa-clipboard-check"></i> Validasi Data</a></li>
                <li><a href="laporan.php" class="active"><i class="fa-solid fa-chart-pie"></i> Laporan</a></li>
            <?php else: ?>
                <li><a href="dashboard_kader.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="data_anak.php"><i class="fa-solid fa-child"></i> Data Anak</a></li>
                <li><a href="jadwal_imunisasi.php"><i class="fa-solid fa-calendar-check"></i> Jadwal</a></li>
                <li><a href="laporan.php" class="active"><i class="fa-solid fa-chart-pie"></i> Laporan</a></li>
                <li><a href="manajemen_pengguna.php"><i class="fa-solid fa-users-cog"></i> Manajemen Pengguna</a></li>
            <?php endif; ?>
        </ul>
        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li><a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOG OUT</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content minimalist-layout bg-gray">

        <div class="main-header">
            <div style="display: flex; align-items: center;">
                <button id="sidebarToggle" class="btn-toggle-sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 style="margin: 0;">Laporan Bulanan</h1>
            </div>
        </div>

        <div class="card filter-card-modern">
            <p>Rekapitulasi data kegiatan Posyandu & Imunisasi</p>
            <form id="formGenerateLaporan" class="filter-flex">
                <div class="filter-group">
                    <label>Bulan</label>
                    <div class="input-with-icon">
                        <i class="fa-regular fa-calendar"></i>
                        <select name="bulan" required class="form-select-clean">
                            <option value="" disabled selected>Pilih Bulan</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>">
                                    <?= DateTime::createFromFormat('!m', $i)->format('F') ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Tahun</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-calendar-days"></i>
                        <select name="tahun" required class="form-select-clean">
                            <option value="" disabled selected>Pilih Tahun</option>
                            <?php foreach ($daftar_tahun as $tahun): ?>
                                <option value="<?= $tahun ?>"><?= $tahun ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-primary">
                    Tampilkan Laporan <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <div id="emptyState" class="empty-state-container">
            <div class="empty-icon">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <h3>Belum ada laporan yang ditampilkan</h3>
            <p>Silakan pilih <strong>Bulan</strong> dan <strong>Tahun</strong> di atas, lalu klik tombol "Tampilkan
                Laporan" untuk melihat data.</p>
        </div>

        <div id="reportResultContainer" style="display: none;">

            <div class="report-actions-bar">
                <button class="btn-action-light btn-print">
                    <i class="fa-solid fa-print"></i> Cetak Laporan
                </button>
            </div>

            <div class="paper-sheet">
                <div class="paper-header">
                    <h2 class="report-title">LAPORAN KEGIATAN POSYANDU</h2>
                    <p class="report-period" id="resultPeriod">Periode: -</p>
                    <div class="paper-divider"></div>
                </div>

                <div class="stats-summary-grid">
                    <div class="stat-box">
                        <span class="stat-label">Total Pemeriksaan</span>
                        <strong class="stat-value" id="countPemeriksaan">0</strong>
                        <span class="stat-unit">Anak</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-label">Total Imunisasi</span>
                        <strong class="stat-value" id="countImunisasi">0</strong>
                        <span class="stat-unit">Anak</span>
                    </div>
                </div>

                <div class="report-section-body">
                    <h4 class="sub-header-report">A. Data Pemeriksaan Bulanan (Valid)</h4>
                    <div id="tablePemeriksaanContainer"></div>
                </div>

                <div class="report-section-body">
                    <h4 class="sub-header-report">B. Data Imunisasi (Valid)</h4>
                    <div id="tableImunisasiContainer"></div>
                </div>

                <div class="paper-footer-sign">
                    <div class="sign-box">
                        <p>Mengetahui,</p>
                        <p>Bidan Desa</p>
                        <br><br><br>
                        <p class="sign-line">____________________</p>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<?php if ($conn)
    $conn->close();
include 'templates/footer.php'; ?>