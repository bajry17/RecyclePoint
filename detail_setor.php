
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Setor Sampah - RecyclePoint</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="detail_setor.css">
</head>

<body class="bg-cream">

  <?php include 'header/top_nav.php' ?>

  <main class="page ds-page">

    <nav class="breadcrumb" aria-label="Breadcrumb">
      Dashboard/Riwayat Setor/Detail
    </nav>

    <div class="ds-heading">
      <h1 class="page__title">Detail Setor Sampah</h1>

      <div class="ds-actions">
        <button type="button" class="ds-btn ds-btn--white" onclick="window.print()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
               stroke-linejoin="round" aria-hidden="true">
            <path d="M6 9V3h12v6"/>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <path d="M6 14h12v7H6z"/>
          </svg>
          Cetak
        </button>

        <button type="button" class="ds-btn ds-btn--orange" id="downloadButton">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round"
               stroke-linejoin="round" aria-hidden="true">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <path d="m7 10 5 5 5-5"/>
            <path d="M12 15V3"/>
          </svg>
          Unduh bukti
        </button>
      </div>
    </div>

    <!-- Status transaksi -->
    <section class="ds-timeline-card">

      <span class="ds-success">Berhasil</span>

      <div class="ds-timeline">

        <div class="ds-step">
          <div class="ds-step__circle">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="m5 12 4 4L19 6"/>
            </svg>
          </div>
          <strong>Setoran diajukan</strong>
          <p>Sampah jenis plastik</p>
          <time>14.15 WIB</time>
        </div>

        <div class="ds-step">
          <div class="ds-step__circle">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="m5 12 4 4L19 6"/>
            </svg>
          </div>
          <strong>Diterima petugas</strong>
          <p>Petugas memeriksa dan memilahnya</p>
          <time>14.18 WIB</time>
        </div>

        <div class="ds-step">
          <div class="ds-step__circle">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="m5 12 4 4L19 6"/>
            </svg>
          </div>
          <strong>Penimbangan selesai</strong>
          <p class="ds-link-text">Lihat bukti total berat terverifikasi 2 kg</p>
          <time>14.25 WIB</time>
        </div>

        <div class="ds-step ds-step--last">
          <div class="ds-step__circle">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="m5 12 4 4L19 6"/>
            </svg>
          </div>
          <strong>Poin masuk ke akun</strong>
          <p>+20 poin ditambahkan</p>
          <time>14.30 WIB</time>
        </div>

      </div>
    </section>

    <!-- Informasi transaksi dan total -->
    <div class="ds-content">

      <section class="ds-info-card">
        <h2>Informasi</h2>

        <div class="ds-info-row">
          <span>Jenis</span>
          <strong>Setor Sampah Plastik</strong>
        </div>

        <div class="ds-info-row">
          <span>ID Transaksi</span>
          <strong>RP-1211-00128</strong>
        </div>

        <div class="ds-info-row">
          <span>Tanggal</span>
          <strong>12 November 2026</strong>
        </div>

        <div class="ds-info-row">
          <span>Waktu</span>
          <strong>14.30 WIB</strong>
        </div>

        <div class="ds-info-row">
          <span>Petugas</span>
          <strong>Wulandari</strong>
        </div>

        <div class="ds-info-row">
          <span>Lokasi</span>
          <strong>Bank Sampah Asri</strong>
        </div>
      </section>

      <div class="ds-right-column">

        <section class="ds-total-card">

          <div class="ds-total-item">
            <p>Total berat</p>
            <strong>2 <small>kg</small></strong>
          </div>

          <div class="ds-total-item ds-total-item--orange">
            <p>Poin diperoleh</p>
            <strong>+20 <small>poin</small></strong>
          </div>

        </section>

        <section class="ds-detail-card">
          <h2>Rincian Item Sampah</h2>

          <div class="ds-table">

            <div class="ds-table-row ds-table-head">
              <span>Detail</span>
              <span>Berat</span>
              <span>Poin</span>
            </div>

            <div class="ds-table-row">
              <span>Botol PET Bening</span>
              <span>1,2 kg</span>
              <strong>+12</strong>
            </div>

            <div class="ds-table-row">
              <span>Gelas Plastik</span>
              <span>0,5 kg</span>
              <strong>+5</strong>
            </div>

            <div class="ds-table-row">
              <span>Plastik Campuran</span>
              <span>0,3 kg</span>
              <strong>+3</strong>
            </div>

          </div>
        </section>

      </div>
    </div>

  </main>

  <script src="header/sidebar.js" defer></script>

  <script>
    // Tombol notifikasi pada topbar menuju halaman notifikasi.
    document.addEventListener("DOMContentLoaded", function () {
      const tombolNotifikasi = document.querySelector(
        '[aria-label="Notifikasi"]'
      );

      if (tombolNotifikasi) {
        tombolNotifikasi.addEventListener("click", function () {
          window.location.href = "notifikasi.php";
        });
      }

      // Tombol unduh bukti membuat file bukti transaksi sederhana.
      const tombolUnduh = document.getElementById("downloadButton");

      tombolUnduh.addEventListener("click", function () {
        const bukti = `
BUKTI SETOR SAMPAH RECYCLEPOINT

ID Transaksi : RP-1211-00128
Tanggal      : 12 November 2026
Waktu        : 14.30 WIB
Jenis        : Setor Sampah Plastik
Petugas      : Wulandari
Lokasi       : Bank Sampah Asri

TOTAL BERAT  : 2 kg
POIN DIDAPAT : +20 poin

RINCIAN ITEM:
Botol PET Bening : 1,2 kg (+12 poin)
Gelas Plastik   : 0,5 kg (+5 poin)
Plastik Campuran: 0,3 kg (+3 poin)

Status: BERHASIL
        `.trim();

        const file = new Blob([bukti], {
          type: "text/plain;charset=utf-8"
        });

        const url = URL.createObjectURL(file);
        const link = document.createElement("a");

        link.href = url;
        link.download = "bukti-setor-RP-1211-00128.txt";
        document.body.appendChild(link);
        link.click();
        link.remove();

        URL.revokeObjectURL(url);
      });
    });
  </script>

</body>
</html>
