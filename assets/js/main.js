// --- LOGIKA TAB LOGIN ---
document.addEventListener("DOMContentLoaded", function () {
  // Pastikan elemen ini ada sebelum menambahkan event listener
  const loginWrapper = document.querySelector(".login-wrapper");
  if (loginWrapper) {
    const tabBtns = loginWrapper.querySelectorAll(".tab-btn");
    const formContents = loginWrapper.querySelectorAll(".form-content");
    const messageDiv = loginWrapper.querySelector("#message"); // Ambil messageDiv di dalam wrapper
    const loginFormOrangtua = loginWrapper.querySelector("#loginFormOrangtua");
    const loginFormUser = loginWrapper.querySelector("#loginFormUser");

    // Hapus: const BASE_URL = window.location.origin + '/sipanda';
    // Kita sekarang gunakan JS_BASE_URL dari header.php

    tabBtns.forEach((tab) => {
      tab.addEventListener("click", () => {
        tabBtns.forEach((t) => t.classList.remove("active"));
        formContents.forEach((f) => f.classList.remove("active"));
        tab.classList.add("active");

        // Cek data-form, karena Kader & Bidan pakai form yg sama
        const targetFormId = `form-${tab.dataset.form}`;
        const targetForm = document.getElementById(targetFormId);
        if (targetForm) {
          targetForm.classList.add("active");
        }

        if (messageDiv) messageDiv.textContent = ""; // Bersihkan pesan jika ada
      });
    });

    const handleFormSubmit = async (form, url) => {
      if (messageDiv) messageDiv.textContent = "Memproses...";
      const data = Object.fromEntries(new FormData(form).entries());

      try {
        const response = await fetch(url, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        });
        const result = await response.json();

        if (result.status === "success") {
          if (messageDiv) {
            messageDiv.className = "success";
            messageDiv.textContent = result.message;
          }
          setTimeout(() => {
            window.location.href = result.redirect_url;
          }, 1000);
        } else {
          if (messageDiv) {
            messageDiv.className = "error";
            messageDiv.textContent = result.message;
          }
        }
      } catch (error) {
        if (messageDiv) {
          messageDiv.className = "error";
          messageDiv.textContent = "Tidak dapat terhubung ke server.";
        }
        console.error("Login Error:", error);
      }
    };

    if (loginFormOrangtua) {
      loginFormOrangtua.addEventListener("submit", function (e) {
        e.preventDefault();
        // Ganti BASE_URL menjadi JS_BASE_URL
        handleFormSubmit(this, `${JS_BASE_URL}/process/proses_loginortu.php`);
      });
    }

    if (loginFormUser) {
      loginFormUser.addEventListener("submit", function (e) {
        e.preventDefault();
        // Ganti BASE_URL menjadi JS_BASE_URL
        handleFormSubmit(this, `${JS_BASE_URL}/process/proses_loginuser.php`);
      });
    }
  } // Akhir if (loginWrapper)
});

// --- LOGIKA TAB KONTEN (Dashboard) ---
document.addEventListener("DOMContentLoaded", function () {
  // Cek jika kita berada di halaman dengan tab konten
  const contentTabBtns = document.querySelectorAll(".content-tab-btn");
  const contentTabs = document.querySelectorAll(".content-tab");

  if (contentTabBtns.length > 0 && contentTabs.length > 0) {
    contentTabBtns.forEach((tab) => {
      tab.addEventListener("click", () => {
        // Hapus 'active' dari semua tombol dan konten
        contentTabBtns.forEach((t) => t.classList.remove("active"));
        contentTabs.forEach((c) => c.classList.remove("active"));

        // Tambah 'active' ke tombol yang di-klik
        tab.classList.add("active");

        // Tampilkan konten yang sesuai
        const contentId = tab.dataset.tab;
        const targetContent = document.getElementById(contentId);
        if (targetContent) {
          targetContent.classList.add("active");
        }
      });
    });
  }
});

// --- LOGIKA MODAL INPUT PERKEMBANGAN ---
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("modalPerkembangan");
  const openBtn = document.getElementById("openModalPerkembanganBtn");
  const closeBtn = modal ? modal.querySelector(".close-button") : null;
  const formModal = document.getElementById("formInputPerkembanganModal");
  const modalMessageDiv = document.getElementById("modalMessage"); // Pastikan ID ini ada di modal perkembangan

  // Hapus: const BASE_URL_PROCESS = window.location.origin + '/sipanda/process';

  // Fungsi Buka Modal
  if (openBtn) {
    openBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if (modal) {
        if (modalMessageDiv) {
          modalMessageDiv.textContent = "";
          modalMessageDiv.className = "message-area";
        }
        if (formModal) formModal.reset();
        modal.classList.add("show");
      }
    });
  }

  // Fungsi Tutup Modal (Tombol X)
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      if (modal) modal.classList.remove("show");
    });
  }

  // Fungsi Tutup Modal (Klik di luar)
  window.addEventListener("click", (event) => {
    if (event.target === modal) {
      modal.classList.remove("show");
    }
  });

  // Fungsi Submit Form via AJAX
  if (formModal) {
    formModal.addEventListener("submit", async function (e) {
      e.preventDefault();
      if (modalMessageDiv) {
        modalMessageDiv.textContent = "Menyimpan...";
        modalMessageDiv.className = "message-area";
      }

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      try {
        // Ganti BASE_URL_PROCESS menjadi JS_BASE_URL
        const response = await fetch(
          `${JS_BASE_URL}/process/proses_input_perkembangan.php`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          }
        );
        const result = await response.json();

        if (result.status === "success") {
          if (modalMessageDiv) {
            modalMessageDiv.className = "message-area success";
            modalMessageDiv.textContent = result.message;
          }
          setTimeout(() => {
            modal.classList.remove("show");
            location.reload(); // Refresh halaman dashboard agar aktivitas terbaru update
          }, 1500);
        } else {
          if (modalMessageDiv) {
            modalMessageDiv.className = "message-area error";
            modalMessageDiv.textContent =
              result.message || "Terjadi kesalahan.";
          }
        }
      } catch (error) {
        if (modalMessageDiv) {
          modalMessageDiv.className = "message-area error";
          modalMessageDiv.textContent = "Tidak dapat terhubung ke server.";
        }
        console.error("Error submitting form:", error);
      }
    });
  }
});

// --- LOGIKA MODAL INPUT IMUNISASI ---
document.addEventListener("DOMContentLoaded", function () {
  const imunModal = document.getElementById("modalImunisasi");
  const imunOpenBtn = document.getElementById("openModalImunisasiBtn");
  const imunCloseBtn = imunModal
    ? imunModal.querySelector(".close-button")
    : null;
  const imunFormModal = document.getElementById("formInputImunisasiModal");
  const imunModalMessageDiv = document.getElementById("modalImunisasiMessage");

  // Hapus: const BASE_URL_PROCESS = window.location.origin + '/sipanda/process';

  if (imunOpenBtn) {
    imunOpenBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if (imunModal) {
        if (imunModalMessageDiv) {
          imunModalMessageDiv.textContent = "";
          imunModalMessageDiv.className = "message-area";
        }
        if (imunFormModal) imunFormModal.reset();
        imunModal.classList.add("show");
      }
    });
  }

  if (imunCloseBtn) {
    imunCloseBtn.addEventListener("click", () => {
      if (imunModal) imunModal.classList.remove("show");
    });
  }

  window.addEventListener("click", (event) => {
    if (event.target === imunModal) {
      imunModal.classList.remove("show");
    }
  });

  if (imunFormModal) {
    imunFormModal.addEventListener("submit", async function (e) {
      e.preventDefault();
      if (imunModalMessageDiv) {
        imunModalMessageDiv.textContent = "Menyimpan...";
        imunModalMessageDiv.className = "message-area";
      }

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      try {
        // Ganti BASE_URL_PROCESS menjadi JS_BASE_URL
        const response = await fetch(
          `${JS_BASE_URL}/process/proses_input_imunisasi.php`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          }
        );
        const result = await response.json();

        if (result.status === "success") {
          if (imunModalMessageDiv) {
            imunModalMessageDiv.className = "message-area success";
            imunModalMessageDiv.textContent = result.message;
          }
          setTimeout(() => {
            imunModal.classList.remove("show");
            location.reload(); // Refresh halaman dashboard agar aktivitas terbaru update
          }, 1500);
        } else {
          if (imunModalMessageDiv) {
            imunModalMessageDiv.className = "message-area error";
            imunModalMessageDiv.textContent =
              result.message || "Terjadi kesalahan.";
          }
        }
      } catch (error) {
        if (imunModalMessageDiv) {
          imunModalMessageDiv.className = "message-area error";
          imunModalMessageDiv.textContent = "Tidak dapat terhubung ke server.";
        }
        console.error("Error submitting imunisasi form:", error);
      }
    });
  }
});

// --- LOGIKA LAPORAN (VERSI FINAL) ---
document.addEventListener("DOMContentLoaded", function () {
  const formGenerate = document.getElementById("formGenerateLaporan");
  const resultContainer = document.getElementById("reportResultContainer");
  const emptyState = document.getElementById("emptyState");
  const resultPeriod = document.getElementById("resultPeriod");
  const countPemeriksaan = document.getElementById("countPemeriksaan");
  const countImunisasi = document.getElementById("countImunisasi");

  // Container Tabel
  const tablePemeriksaanContainer = document.getElementById(
    "tablePemeriksaanContainer"
  );
  const tableImunisasiContainer = document.getElementById(
    "tableImunisasiContainer"
  );

  // 1. FUNGSI GENERATE
  if (formGenerate) {
    formGenerate.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const bulan = formData.get("bulan");
      const tahun = formData.get("tahun");

      if (!bulan || !tahun) {
        alert("Silakan pilih Bulan dan Tahun terlebih dahulu.");
        return;
      }

      // UI Loading
      const btnSubmit = this.querySelector('button[type="submit"]');
      const originalText = btnSubmit.innerHTML;
      btnSubmit.innerHTML =
        '<i class="fa-solid fa-spinner fa-spin"></i> Memuat...';
      btnSubmit.disabled = true;

      try {
        // Fetch Data (Pastikan URL sesuai)
        const response = await fetch(
          `${JS_BASE_URL}/process/proses_generate_laporan.php?bulan=${bulan}&tahun=${tahun}`
        );
        const result = await response.json();

        if (result.status === "success") {
          // Sembunyikan Empty State, Tampilkan Hasil
          if (emptyState) emptyState.style.display = "none";
          if (resultContainer) resultContainer.style.display = "block";

          // Isi Header & Ringkasan
          if (resultPeriod)
            resultPeriod.textContent = `Periode: ${result.periode}`;
          if (countPemeriksaan)
            countPemeriksaan.textContent = result.data.pemeriksaan.length;
          if (countImunisasi)
            countImunisasi.textContent = result.data.imunisasi.length;

          // Generate Tabel 1: Pemeriksaan
          let htmlP = "";
          if (result.data.pemeriksaan.length > 0) {
            htmlP += '<table class="paper-table">';
            htmlP +=
              '<thead><tr><th width="15%">Tanggal</th><th width="35%">Nama Balita</th><th width="10%">BB (kg)</th><th width="10%">TB (cm)</th><th width="10%">LK (cm)</th><th width="20%">Kader</th></tr></thead><tbody>';
            result.data.pemeriksaan.forEach((row) => {
              htmlP += `<tr>
                                <td>${row.tanggal_periksa}</td>
                                <td><strong>${row.nama_balita}</strong></td>
                                <td class="text-right">${
                                  row.berat_badan || "-"
                                }</td>
                                <td class="text-right">${
                                  row.tinggi_badan || "-"
                                }</td>
                                <td class="text-right">${
                                  row.lingkar_kepala || "-"
                                }</td>
                                <td>${row.nama_kader || "-"}</td>
                            </tr>`;
            });
            htmlP += "</tbody></table>";
          } else {
            htmlP +=
              '<p style="font-style:italic; color:#777; text-align:center; padding:10px; border:1px dashed #ccc;">Tidak ada data pemeriksaan valid pada periode ini.</p>';
          }
          tablePemeriksaanContainer.innerHTML = htmlP;

          // Generate Tabel 2: Imunisasi
          let htmlI = "";
          if (result.data.imunisasi.length > 0) {
            htmlI += '<table class="paper-table">';
            htmlI +=
              '<thead><tr><th width="15%">Tanggal</th><th width="40%">Nama Balita</th><th width="25%">Jenis Vaksin</th><th width="20%">Kader</th></tr></thead><tbody>';
            result.data.imunisasi.forEach((row) => {
              htmlI += `<tr>
                                <td>${row.tanggal_imunisasi}</td>
                                <td><strong>${row.nama_balita}</strong></td>
                                <td>${row.jenis_vaksin}</td>
                                <td>${row.nama_kader || "-"}</td>
                            </tr>`;
            });
            htmlI += "</tbody></table>";
          } else {
            htmlI +=
              '<p style="font-style:italic; color:#777; text-align:center; padding:10px; border:1px dashed #ccc;">Tidak ada data imunisasi valid pada periode ini.</p>';
          }
          tableImunisasiContainer.innerHTML = htmlI;
        } else {
          alert("Gagal: " + result.message);
        }
      } catch (error) {
        console.error("Error:", error);
        alert("Terjadi kesalahan koneksi.");
      } finally {
        btnSubmit.innerHTML = originalText;
        btnSubmit.disabled = false;
      }
    });
  }

  // 2. FUNGSI CETAK (PREVIEW & PRINT)
  const btnPrint = document.querySelector(".btn-print");
  if (btnPrint) {
    btnPrint.addEventListener("click", function () {
      // Ambil konten dari dalam div .paper-sheet saja
      const paperContent = document.querySelector(".paper-sheet").innerHTML;

      // Buka jendela baru
      const printWindow = window.open("", "", "height=800,width=900");

      printWindow.document.write(
        "<html><head><title>Cetak Laporan - SIPANDA</title>"
      );
      // Style khusus untuk cetak (mirip dengan CSS dashboard tapi disederhanakan)
      printWindow.document.write("<style>");
      printWindow.document.write(
        'body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; padding: 20px; color: #000; }'
      );
      printWindow.document.write(
        ".report-title { text-align: center; text-transform: uppercase; font-size: 18px; margin-bottom: 5px; }"
      );
      printWindow.document.write(
        ".report-period { text-align: center; font-size: 14px; margin-bottom: 20px; color: #333; }"
      );
      printWindow.document.write(
        ".paper-divider { height: 2px; background: #000; width: 100%; margin-bottom: 30px; }"
      );

      printWindow.document.write(
        ".stats-summary-grid { display: flex; justify-content: center; gap: 40px; margin-bottom: 40px; }"
      );
      printWindow.document.write(
        ".stat-box { text-align: center; border: 1px solid #ccc; padding: 15px; width: 150px; }"
      );
      printWindow.document.write(
        ".stat-value { display: block; font-size: 24px; font-weight: bold; margin: 10px 0; }"
      );
      printWindow.document.write(
        ".stat-label { font-size: 12px; text-transform: uppercase; }"
      );

      printWindow.document.write(
        ".sub-header-report { margin-top: 30px; margin-bottom: 10px; font-size: 14px; font-weight: bold; text-decoration: underline; }"
      );

      printWindow.document.write(
        "table { width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 20px; }"
      );
      printWindow.document.write(
        "th, td { border: 1px solid #000; padding: 8px; }"
      );
      printWindow.document.write(
        "th { background-color: #f0f0f0; text-align: left; }"
      );
      printWindow.document.write(".text-right { text-align: right; }");

      printWindow.document.write(
        ".paper-footer-sign { margin-top: 60px; display: flex; justify-content: flex-end; }"
      );
      printWindow.document.write(
        ".sign-box { text-align: center; width: 200px; float: right; }"
      ); // float for print comp
      printWindow.document.write("</style>");
      printWindow.document.write("</head><body>");

      printWindow.document.write(paperContent);

      printWindow.document.write("</body></html>");
      printWindow.document.close();

      // Tunggu sebentar lalu print
      setTimeout(() => {
        printWindow.focus();
        printWindow.print();
        // printWindow.close(); // Opsional: tutup otomatis setelah print
      }, 500);
    });
  }
});

// --- LOGIKA MODAL INPUT JADWAL ---
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("modalJadwal");
  const openBtn = document.getElementById("openModalJadwalBtn");
  const closeBtn = modal ? modal.querySelector(".close-button") : null;
  const formModal = document.getElementById("formInputJadwalModal");
  const modalMessageDiv = document.getElementById("modalJadwalMessage");

  // Fungsi Buka Modal
  if (openBtn) {
    openBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if (modal) {
        if (modalMessageDiv) {
          modalMessageDiv.textContent = "";
          modalMessageDiv.className = "message-area";
        }
        if (formModal) formModal.reset();
        modal.classList.add("show");
      }
    });
  }

  // Fungsi Tutup Modal (Tombol X)
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      if (modal) modal.classList.remove("show");
    });
  }

  // Fungsi Tutup Modal (Klik di luar)
  window.addEventListener("click", (event) => {
    if (event.target === modal) {
      modal.classList.remove("show");
    }
  });

  // Fungsi Submit Form via AJAX
  if (formModal) {
    formModal.addEventListener("submit", async function (e) {
      e.preventDefault();
      if (modalMessageDiv) {
        modalMessageDiv.textContent = "Menyimpan...";
        modalMessageDiv.className = "message-area";
      }

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      try {
        // Panggil file AJAX baru yang kita buat
        const response = await fetch(
          `${JS_BASE_URL}/process/proses_input_jadwal_ajax.php`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          }
        );
        const result = await response.json();

        if (result.status === "success") {
          if (modalMessageDiv) {
            modalMessageDiv.className = "message-area success";
            modalMessageDiv.textContent = result.message;
          }
          setTimeout(() => {
            modal.classList.remove("show");
            location.reload(); // Refresh halaman agar jadwal baru muncul di tabel
          }, 1500);
        } else {
          if (modalMessageDiv) {
            modalMessageDiv.className = "message-area error";
            modalMessageDiv.textContent =
              result.message || "Terjadi kesalahan.";
          }
        }
      } catch (error) {
        if (modalMessageDiv) {
          modalMessageDiv.className = "message-area error";
          modalMessageDiv.textContent = "Tidak dapat terhubung ke server.";
        }
        console.error("Error submitting form:", error);
      }
    });
  }
});

// --- LOGIKA LAPORAN ---
document.addEventListener("DOMContentLoaded", function () {
  const formPeriode = document.getElementById("formPilihPeriode");
  const previewArea = document.querySelector(".report-preview .preview-area");
  const previewPeriodSpan = document.getElementById("preview-period");
  const reportPreviewContainer = document.querySelector(".report-preview");
  const reportActionsContainer = document.querySelector(".report-actions"); // Aksi Generate/Batal
  const btnCancel = document.querySelector(".btn-cancel");

  if (formPeriode) {
    // Saat form 'Pilih' di-submit
    formPeriode.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const bulan = formData.get("bulan");
      const tahun = formData.get("tahun");

      // Tampilkan tombol "Generate Laporan"
      if (reportActionsContainer)
        reportActionsContainer.style.display = "block";
      if (reportPreviewContainer) reportPreviewContainer.style.display = "none"; // Sembunyikan preview lama

      // Simpan data bulan/tahun di tombol generate untuk digunakan nanti
      const btnGenerate = document.querySelector(".btn-generate");
      if (btnGenerate) {
        btnGenerate.dataset.bulan = bulan;
        btnGenerate.dataset.tahun = tahun;
      }
    });
  }

  // Saat tombol "Batal" diklik
  if (btnCancel) {
    btnCancel.addEventListener("click", function (e) {
      e.preventDefault();
      if (formPeriode) formPeriode.reset();
      if (reportActionsContainer) reportActionsContainer.style.display = "none";
      if (reportPreviewContainer) reportPreviewContainer.style.display = "none";
    });
  }

  // Saat tombol "Generate Laporan" diklik
  const btnGenerate = document.querySelector(".btn-generate");
  if (btnGenerate) {
    btnGenerate.addEventListener("click", async function (e) {
      e.preventDefault();
      const bulan = this.dataset.bulan;
      const tahun = this.dataset.tahun;

      if (!bulan || !tahun) {
        alert("Pilih bulan dan tahun terlebih dahulu.");
        return;
      }

      if (previewArea) previewArea.innerHTML = "<p>Memuat data laporan...</p>";
      if (reportPreviewContainer)
        reportPreviewContainer.style.display = "block";

      try {
        // Panggil script PHP menggunakan JS_BASE_URL dari header.php
        const response = await fetch(
          `${JS_BASE_URL}/process/proses_generate_laporan.php?bulan=${bulan}&tahun=${tahun}`
        );
        const result = await response.json();

        if (result.status === "success") {
          // Update judul periode
          if (previewPeriodSpan) previewPeriodSpan.textContent = result.periode;

          // Buat HTML tabel laporannya
          previewArea.innerHTML = generateReportHTML(
            result.data,
            result.periode
          );
        } else {
          previewArea.innerHTML = `<p style="color: red;">Gagal memuat laporan: ${result.message}</p>`;
        }
      } catch (error) {
        console.error("Error generating report:", error);
        previewArea.innerHTML =
          '<p style="color: red;">Terjadi kesalahan koneksi.</p>';
      }
    });
  }

  // Fungsi helper untuk mengubah JSON data menjadi HTML Tabel
  function generateReportHTML(data, periode) {
    let html = `<h3>Laporan Data Valid Posyandu - Periode ${periode}</h3>`;

    // Tabel 1: Pemeriksaan
    html += "<h4>Data Pemeriksaan Bulanan (Valid)</h4>";
    if (data.pemeriksaan.length > 0) {
      html +=
        '<table class="data-table" style="width:100%; margin-bottom: 1.5rem;"><thead>';
      html +=
        "<tr><th>Tanggal</th><th>Nama Balita</th><th>BB (kg)</th><th>TB (cm)</th><th>LK (cm)</th><th>Diinput Oleh</th></tr>";
      html += "</thead><tbody>";
      data.pemeriksaan.forEach((row) => {
        html += `<tr>
                    <td>${row.tanggal_periksa}</td>
                    <td>${row.nama_balita}</td>
                    <td>${row.berat_badan || "-"}</td>
                    <td>${row.tinggi_badan || "-"}</td>
                    <td>${row.lingkar_kepala || "-"}</td>
                    <td>${row.nama_kader || "N/A"}</td>
                </tr>`;
      });
      html += "</tbody></table>";
    } else {
      html += "<p>Tidak ada data pemeriksaan valid pada periode ini.</p>";
    }

    // Tabel 2: Imunisasi
    html += "<h4>Data Imunisasi (Valid)</h4>";
    if (data.imunisasi.length > 0) {
      html += '<table class="data-table" style="width:100%;"><thead>';
      html +=
        "<tr><th>Tanggal</th><th>Nama Balita</th><th>Jenis Vaksin</th><th>Diinput Oleh</th></tr>";
      html += "</thead><tbody>";
      data.imunisasi.forEach((row) => {
        html += `<tr>
                    <td>${row.tanggal_imunisasi}</td>
                    <td>${row.nama_balita}</td>
                    <td>${row.jenis_vaksin}</td>
                    <td>${row.nama_kader || "N/A"}</td>
                </tr>`;
      });
      html += "</tbody></table>";
    } else {
      html += "<p>Tidak ada data imunisasi valid pada periode ini.</p>";
    }

    return html;
  }
});

// --- LOGIKA CETAK LAPORAN ---
document.addEventListener("DOMContentLoaded", function () {
  const btnPrint = document.querySelector(".btn-print");

  if (btnPrint) {
    btnPrint.addEventListener("click", function () {
      const previewArea = document.querySelector(
        ".report-preview .preview-area"
      );
      // Ambil periode dari span, bukan dari variabel JS
      const periodeSpan = document.getElementById("preview-period");
      const periode = periodeSpan ? periodeSpan.textContent : "Laporan Bulanan";

      if (!previewArea) {
        alert("Area pratinjau tidak ditemukan.");
        return;
      }

      // 1. Ambil HTML dari area pratinjau
      const contentToPrint = previewArea.innerHTML;

      // 2. Buat HTML baru untuk jendela cetak
      const printTemplate = `
                <html>
                <head>
                    <title>Laporan Posyandu - Periode ${periode}</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 20px;
                        }
                        h1 { 
                            font-size: 1.5rem; 
                            text-align: center;
                            margin-bottom: 0;
                        }
                        p.periode {
                            font-size: 1rem;
                            text-align: center;
                            margin-top: 5px;
                            margin-bottom: 25px;
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            font-size: 0.9rem;
                            margin-bottom: 1.5rem;
                        }
                        th, td { 
                            border: 1px solid #000; 
                            padding: 8px; 
                            text-align: left; 
                        }
                        th { 
                            background-color: #f2f2f2; 
                            text-align: center;
                        }
                        h3, h4 {
                             margin-bottom: 10px;
                        }
                        p {
                            font-size: 0.9rem;
                        }
                    </style>
                </head>
                <body>
                    ${contentToPrint}
                </body>
                </html>
            `;

      // 3. Buka jendela baru, tulis HTML, dan panggil fungsi cetak
      const printWindow = window.open("", "", "height=600,width=800");
      printWindow.document.write(printTemplate);
      printWindow.document.close();
      printWindow.focus();
      printWindow.print();
      printWindow.close();
    });
  }
});

// --- LOGIKA KALENDER AJAX (ORANG TUA) ---
document.addEventListener("DOMContentLoaded", function () {
  const btnLalu = document.getElementById("kalender-nav-lalu");
  const btnDepan = document.getElementById("kalender-nav-depan");

  // Fungsi untuk mengambil data kalender baru
  const updateKalender = async (bulan, tahun) => {
    // Tampilkan loading (opsional, tapi bagus)
    const gridBody = document.getElementById("kalender-grid-body");
    const eventList = document.getElementById("kalender-event-list");
    const namaBulan = document.getElementById("nama-bulan-kalender");
    if (gridBody) gridBody.style.opacity = "0.5";
    if (eventList) eventList.style.opacity = "0.5";

    try {
      // Panggil backend API baru kita
      const response = await fetch(
        `${JS_BASE_URL}/process/proses_get_kalender.php?bulan=${bulan}&tahun=${tahun}`
      );
      const result = await response.json();

      if (result.status === "success") {
        // 1. Perbarui Judul Bulan
        if (namaBulan) namaBulan.textContent = result.nama_bulan_ini;

        // 2. Perbarui Grid Kalender (tbody)
        if (gridBody) gridBody.innerHTML = result.html_grid;

        // 3. Perbarui Daftar Acara
        if (eventList) eventList.innerHTML = result.html_event_list;

        // 4. Perbarui data-attributes pada tombol navigasi
        if (btnLalu) {
          btnLalu.dataset.bulan = result.nav_lalu.bulan;
          btnLalu.dataset.tahun = result.nav_lalu.tahun;
        }
        if (btnDepan) {
          btnDepan.dataset.bulan = result.nav_depan.bulan;
          btnDepan.dataset.tahun = result.nav_depan.tahun;
        }
      } else {
        alert("Gagal memuat data kalender.");
      }
    } catch (error) {
      console.error("Error fetching calendar data:", error);
      alert("Tidak dapat terhubung ke server.");
    } finally {
      // Hentikan loading
      if (gridBody) gridBody.style.opacity = "1";
      if (eventList) eventList.style.opacity = "1";
    }
  };

  // Tambahkan event listener ke tombol "Bulan Lalu"
  if (btnLalu) {
    btnLalu.addEventListener("click", function (e) {
      e.preventDefault(); // Mencegah reload
      const bulan = e.currentTarget.dataset.bulan;
      const tahun = e.currentTarget.dataset.tahun;
      updateKalender(bulan, tahun);
    });
  }

  // Tambahkan event listener ke tombol "Bulan Depan"
  if (btnDepan) {
    btnDepan.addEventListener("click", function (e) {
      e.preventDefault(); // Mencegah reload
      const bulan = e.currentTarget.dataset.bulan;
      const tahun = e.currentTarget.dataset.tahun;
      updateKalender(bulan, tahun);
    });
  }
});

// --- LOGIKA MODAL INPUT DATA ANAK BARU ---
document.addEventListener("DOMContentLoaded", function () {
  const anakModal = document.getElementById("modalInputAnak");
  const anakOpenBtn = document.getElementById("openModalAnakBtn");
  const anakCloseBtn = anakModal
    ? anakModal.querySelector(".close-button")
    : null;
  const anakFormModal = document.getElementById("formInputAnakModal");
  const anakModalMessageDiv = document.getElementById("modalAnakMessage");

  // 1. Buka Modal
  if (anakOpenBtn) {
    anakOpenBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if (anakModal) {
        // Reset pesan dan form
        if (anakModalMessageDiv) {
          anakModalMessageDiv.textContent = "";
          anakModalMessageDiv.className = "message-area";
        }
        if (anakFormModal) anakFormModal.reset();

        anakModal.classList.add("show");
      }
    });
  }

  // 2. Tutup Modal (Tombol X)
  if (anakCloseBtn) {
    anakCloseBtn.addEventListener("click", () => {
      if (anakModal) {
        anakModal.classList.remove("show");
      }
    });
  }

  // 3. Tutup Modal (Klik di Luar)
  window.addEventListener("click", (event) => {
    if (event.target === anakModal) {
      anakModal.classList.remove("show");
    }
  });

  // 4. Submit Form via AJAX
  if (anakFormModal) {
    anakFormModal.addEventListener("submit", async function (e) {
      e.preventDefault();
      if (anakModalMessageDiv) {
        anakModalMessageDiv.textContent = "Menyimpan...";
        anakModalMessageDiv.className = "message-area";
      }

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      try {
        // Panggil file proses AJAX baru
        const response = await fetch(
          `${JS_BASE_URL}/process/proses_input_anak_ajax.php`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          }
        );
        const result = await response.json();

        if (result.status === "success") {
          if (anakModalMessageDiv) {
            anakModalMessageDiv.className = "message-area success";
            anakModalMessageDiv.textContent = result.message;
          }
          setTimeout(() => {
            anakModal.classList.remove("show");
            document.body.style.overflow = "auto"; // Buka kunci scroll
            location.reload(); // Refresh halaman agar tabel terupdate
          }, 1500);
        } else {
          if (anakModalMessageDiv) {
            anakModalMessageDiv.className = "message-area error";
            anakModalMessageDiv.textContent =
              result.message || "Terjadi kesalahan.";
          }
        }
      } catch (error) {
        if (anakModalMessageDiv) {
          anakModalMessageDiv.className = "message-area error";
          anakModalMessageDiv.textContent = "Tidak dapat terhubung ke server.";
        }
        console.error("Error submitting form:", error);
      }
    });
  }
});

// --- LOGIKA SIDEBAR RESPONSIVE ---
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector(".sidebar");
  const body = document.body;

  // Buat elemen overlay secara dinamis jika belum ada
  let overlay = document.querySelector(".sidebar-overlay");
  if (!overlay) {
    overlay = document.createElement("div");
    overlay.className = "sidebar-overlay";
    body.appendChild(overlay);
  }

  if (toggleBtn && sidebar) {
    // Klik Tombol Menu
    toggleBtn.addEventListener("click", function (e) {
      e.stopPropagation(); // Mencegah event bubbling
      sidebar.classList.toggle("active");
      overlay.classList.toggle("active");
    });

    // Klik Overlay (Tutup Menu)
    overlay.addEventListener("click", function () {
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
    });

    // Klik Link di Sidebar (Tutup Menu otomatis - Opsional)
    const sidebarLinks = sidebar.querySelectorAll("a");
    sidebarLinks.forEach((link) => {
      link.addEventListener("click", () => {
        // Cek jika layar kecil
        if (window.innerWidth <= 900) {
          sidebar.classList.remove("active");
          overlay.classList.remove("active");
        }
      });
    });
  }
});

// ... (Kode sebelumnya biarkan saja) ...

// --- LOGIKA MODAL TAMBAH PENGGUNA ---
document.addEventListener("DOMContentLoaded", function () {
  const userModal = document.getElementById("modalTambahUser");
  const userOpenBtn = document.getElementById("openModalUserBtn");
  const userCloseBtn = userModal
    ? userModal.querySelector(".close-button")
    : null;
  const userFormModal = document.getElementById("formInputUserModal");
  const userModalMessageDiv = document.getElementById("modalUserMessage");

  // 1. Buka Modal
  if (userOpenBtn) {
    userOpenBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if (userModal) {
        if (userModalMessageDiv) {
          userModalMessageDiv.textContent = "";
          userModalMessageDiv.className = "message-area";
        }
        if (userFormModal) userFormModal.reset();
        userModal.classList.add("show");
      }
    });
  }

  // 2. Tutup Modal (Tombol X)
  if (userCloseBtn) {
    userCloseBtn.addEventListener("click", () => {
      if (userModal) userModal.classList.remove("show");
    });
  }

  // 3. Tutup Modal (Klik Luar)
  window.addEventListener("click", (event) => {
    if (event.target === userModal) {
      userModal.classList.remove("show");
    }
  });

  // 4. Submit Form (AJAX)
  if (userFormModal) {
    userFormModal.addEventListener("submit", async function (e) {
      e.preventDefault();

      if (userModalMessageDiv) {
        userModalMessageDiv.textContent = "Menyimpan...";
        userModalMessageDiv.className = "message-area";
      }

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      try {
        // KODE BARU (BENAR) - Hapus kata 'proses_'
        const response = await fetch(
          `${JS_BASE_URL}/process/proses_tambah_pengguna_ajax.php`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          }
        );
        const result = await response.json();

        if (result.status === "success") {
          if (userModalMessageDiv) {
            userModalMessageDiv.className = "message-area success";
            userModalMessageDiv.textContent = result.message;
          }
          // Refresh halaman setelah 1.5 detik
          setTimeout(() => {
            userModal.classList.remove("show");
            location.reload();
          }, 1500);
        } else {
          if (userModalMessageDiv) {
            userModalMessageDiv.className = "message-area error";
            userModalMessageDiv.textContent =
              result.message || "Terjadi kesalahan.";
          }
        }
      } catch (error) {
        if (userModalMessageDiv) {
          userModalMessageDiv.className = "message-area error";
          userModalMessageDiv.textContent = "Gagal menghubungi server.";
        }
        console.error("Error:", error);
      }
    });
  }
});
