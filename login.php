<?php
require 'config/database.php';

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'kader':
            header('Location: ' . BASE_URL . '/dashboard_kader.php');
            exit();
        case 'bidan':
            header('Location: ' . BASE_URL . '/dashboard_bidan.php');
            exit();
        case 'orangtua':
            header('Location: ' . BASE_URL . '/dashboard_orangtua.php');
            exit();
    }
}

$page_title = 'SIPANDA';
include 'templates/header.php';
?>

<div class="login-wrapper">
    <div class="login-banner">
        <p>SELAMAT DATANG DI</p>
        <h1>SIPANDA</h1>
    </div>

    <div class="login-form-container">
        <h2 class="login-title">LOGIN</h2>

        <div class="role-tabs">
            <button class="tab-btn" data-form="orangtua">Orang Tua</button>
            <button class="tab-btn active" data-form="kader-bidan">Kader</button>
            <button class="tab-btn" data-form="kader-bidan">Bidan</button>
        </div>

        <div id="form-orangtua" class="form-content">
            <form id="loginFormOrangtua">
                <div class="input-group">
                    <label for="nik_balita">NIK Anak</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-id-card"></i>
                        <input type="text" id="nik_balita" name="nik_balita" placeholder="Masukkan NIK anak" required>
                    </div>
                </div>
                <div class="input-group">
                    <label for="tanggal_lahir">Tanggal Lahir Anak</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-calendar-days"></i>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
                    </div>
                </div>
                <button type="submit" class="submit-btn">Masuk</button>
            </form>
        </div>

        <div id="form-kader-bidan" class="form-content active">
            <form id="loginFormUser">
                <div class="input-group">
                    <label for="username">Nama Pengguna</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Masukkan nama pengguna" required>
                    </div>
                </div>
                <div class="input-group">
                    <label for="password">Kata Sandi</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
                    </div>
                </div>
                <button type="submit" class="submit-btn">Masuk</button>
            </form>
        </div>

        <div id="message"></div>
    </div>
</div>

<script>
    const tabs = document.querySelectorAll(".tab-btn");
    const forms = document.querySelectorAll(".form-content");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");
            forms.forEach(f => f.classList.remove("active"));
            document.getElementById("form-" + tab.dataset.form).classList.add("active");
        });
    });
</script>

<?php include 'templates/footer.php'; ?>