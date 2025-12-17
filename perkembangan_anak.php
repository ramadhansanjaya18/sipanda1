<?php
require 'config/database.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'orangtua' || !isset($_SESSION['id_balita'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}
$id_balita = $_SESSION['id_balita'];

$stmt_balita = $conn->prepare("SELECT nama_balita, nama_orang_tua FROM balita WHERE id_balita = ?");
$stmt_balita->bind_param("i", $id_balita);
$stmt_balita->execute();
$result_balita = $stmt_balita->get_result();
$balita = $result_balita->fetch_assoc();
$stmt_balita->close();
if (!$balita) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$stmt_pemeriksaan = $conn->prepare(
    "SELECT tanggal_periksa, berat_badan, tinggi_badan, lingkar_kepala, catatan, status_validasi 
     FROM pemeriksaan 
     WHERE id_balita = ? 
     ORDER BY tanggal_periksa ASC"
);
$stmt_pemeriksaan->bind_param("i", $id_balita);
$stmt_pemeriksaan->execute();
$result_pemeriksaan = $stmt_pemeriksaan->get_result();
$chart_labels = [];
$chart_berat_badan = [];
$chart_tinggi_badan = [];
$pemeriksaan_data = [];

if ($result_pemeriksaan->num_rows > 0) {
    while ($row = $result_pemeriksaan->fetch_assoc()) {
        $pemeriksaan_data[] = $row;
        if ($row['status_validasi'] == 'Valid') {
            $chart_labels[] = date('d M Y', strtotime($row['tanggal_periksa']));
            $chart_berat_badan[] = $row['berat_badan'];
            $chart_tinggi_badan[] = $row['tinggi_badan'];
        }
    }
}
$stmt_pemeriksaan->close();

$stmt_imunisasi = $conn->prepare(
    "SELECT tanggal_imunisasi, jenis_vaksin, catatan, status_validasi 
     FROM imunisasi 
     WHERE id_balita = ? 
     ORDER BY tanggal_imunisasi DESC"
);
$stmt_imunisasi->bind_param("i", $id_balita);
$stmt_imunisasi->execute();
$result_imunisasi = $stmt_imunisasi->get_result();


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
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($balita['nama_orang_tua']) ?>&background=FBCFE8&color=B42373"
                alt="Avatar Anak">
            <div class="user-info">
                <h4><?= htmlspecialchars($balita['nama_orang_tua']) ?></h4>
                <p>Orang Tua</p>
            </div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard_orangtua.php"><i class="fa-solid fa-house"></i> Beranda</a></li>
            <li><a href="perkembangan_anak.php" class="active"><i class="fa-solid fa-chart-line"></i> Perkembangan
                    Anak</a></li>
            <li><a href="profil_anak.php"><i class="fa-solid fa-user"></i> Profil Anak</a></li>
        </ul>
        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li><a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOG OUT</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content">

        <div class="main-header">
            <h1>Riwayat Perkembangan Anak</h1>
        </div>

        <div class="report-section chart-section">
            <label class="section-label">Grafik Berat Badan & Tinggi Badan</label>
            <div class="chart-container" style="position: relative; height:300px; width:100%">
                <canvas id="developmentChart"></canvas>
            </div>
        </div>

        <div class="history-section">
            <h2>Riwayat Pemeriksaan</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Pemeriksaan</th>
                            <th>Berat (kg)</th>
                            <th>Tinggi (cm)</th>
                            <th>Lingkar Kepala (cm)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pemeriksaan_data)): ?>
                            <?php foreach (array_reverse($pemeriksaan_data) as $row): ?>
                                <tr>
                                    <td><?= date('d F Y', strtotime($row['tanggal_periksa'])) ?></td>
                                    <td><?= htmlspecialchars($row['berat_badan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['tinggi_badan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['lingkar_kepala'] ?? '-') ?></td>
                                    <td>
                                        <span
                                            class="status-badge <?= strtolower(str_replace(' ', '-', $row['status_validasi'])) ?>">
                                            <?= htmlspecialchars($row['status_validasi']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Belum ada riwayat pemeriksaan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="history-section">
            <h2>Status Imunisasi</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Imunisasi</th>
                            <th>Jenis Vaksin</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_imunisasi->num_rows > 0): ?>
                            <?php while ($row = $result_imunisasi->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d F Y', strtotime($row['tanggal_imunisasi'])) ?></td>
                                    <td><?= htmlspecialchars($row['jenis_vaksin']) ?></td>
                                    <td>
                                        <span
                                            class="status-badge <?= strtolower(str_replace(' ', '-', $row['status_validasi'])) ?>">
                                            <?= htmlspecialchars($row['status_validasi']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align:center;">Belum ada riwayat imunisasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
<?php
$stmt_imunisasi->close();
$conn->close();
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartLabels = <?= json_encode($chart_labels) ?>;
        const weightData = <?= json_encode($chart_berat_badan) ?>;
        const heightData = <?= json_encode($chart_tinggi_badan) ?>;

        const ctx = document.getElementById('developmentChart');

        if (ctx && chartLabels.length > 0) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Berat Badan (kg)',
                            data: weightData,
                            borderColor: 'rgb(243, 126, 188)',
                            backgroundColor: 'rgba(243, 126, 188, 0.1)',
                            tension: 0.1,
                            fill: true,
                            yAxisID: 'yWeight'
                        },
                        {
                            label: 'Tinggi Badan (cm)',
                            data: heightData,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            tension: 0.1,
                            fill: true,
                            yAxisID: 'yHeight'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: 10
                            }
                        },
                        yWeight: {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Berat Badan (kg)'
                            },
                            beginAtZero: true
                        },
                        yHeight: {
                            type: 'linear',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Tinggi Badan (cm)'
                            },
                            beginAtZero: true,

                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        } else if (ctx) {
            const chartContainer = ctx.parentElement;
            chartContainer.innerHTML = '<p style="text-align:center; padding: 50px; color: #6b7280;">Belum ada data pemeriksaan yang valid untuk ditampilkan dalam grafik.</p>';
        }
    });
</script>
<?php include 'templates/footer.php';