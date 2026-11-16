<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Riwayat Setor Sampah – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="riwayat-setor.css">
</head>
<body class="bg-cream">

  <?php include 'header/top_nav.php' ?>

  <main class="page">
    <nav class="breadcrumb" aria-label="Breadcrumb">Dashboard/Riwayat Setor</nav>
    <h1 class="page__title">Riwayat Setor Sampah</h1>

    <!-- Ringkasan -->
    <section class="rs-summary" aria-label="Ringkasan setoran">
      <div class="rs-sum">
        <span class="rs-sum__ico" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 19l-3-5 3-5M17 5l3 5-3 5M9 5h6M9 19h6M4 14h5M15 10h5"/></svg></span>
        <div><p>Total Sampah Terkumpul</p><strong>12,5 Kg</strong></div>
      </div>
      <span class="rs-summary__line" aria-hidden="true"></span>
      <div class="rs-sum">
        <span class="rs-sum__ico rs-sum__ico--star" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg></span>
        <div><p>Total Poin Didapat</p><strong>+ 1.250 Poin</strong></div>
      </div>
    </section>

    <!-- Filter -->
    <div class="rs-tabs" role="group" aria-label="Filter jenis sampah">
      <button class="rs-tab" type="button" data-filter="semua" aria-pressed="true">Semua <span class="rs-count" id="countAll">5</span></button>
      <button class="rs-tab" type="button" data-filter="plastik" aria-pressed="false">Plastik</button>
      <button class="rs-tab" type="button" data-filter="kardus" aria-pressed="false">Kardus</button>
      <button class="rs-tab" type="button" data-filter="kertas" aria-pressed="false">Kertas</button>
      <button class="rs-tab" type="button" data-filter="logam" aria-pressed="false">Logam</button>
      <button class="rs-tab" type="button" data-filter="kaca" aria-pressed="false">Kaca</button>
    </div>
    <p class="rs-status-msg" id="status" role="status" aria-live="polite"></p>

    <!-- Daftar riwayat (bukan tabel: <ul> + CSS grid) -->
    <section class="rs-list-card" aria-label="Daftar riwayat setor">
      <ul class="rs-list">
        <li data-cat="plastik">
          <a class="rs-row" href="#" aria-label="Lihat detail setor sampah plastik, 12 Nov 2026, 2 kg, +20 poin, berhasil">
            <span class="rs-item"><svg class="rs-cat" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 2.5h4v2.5l1.5 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-4a1.5 1.5 0 0 1-1.5-1.5v-12L10 5zM8.5 12h7M8.5 16h7"/></svg><span class="rs-name"><strong>Setor Sampah Plastik</strong><time datetime="2026-11-12T14:30">12 Nov 2026 &nbsp; 14.30</time></span></span>
            <span class="rs-meta">
              <span class="rs-cell rs-weight"><svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 9a3.5 3.5 0 1 1 7 0h3.2a1.5 1.5 0 0 1 1.5 1.3l1.3 9.5a1.5 1.5 0 0 1-1.5 1.7H4a1.5 1.5 0 0 1-1.5-1.7l1.3-9.5A1.5 1.5 0 0 1 5.3 9zm5 0a1.5 1.5 0 1 0-3 0zm-1.5 3.5l2.2 3.2-1.4 1-2.2-3.2z"/></svg>2 kg</span>
              <span class="rs-cell rs-points"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg>+20 Poin</span>
              <span class="rs-cell rs-status"><span class="rs-badge">Berhasil</span></span>
            </span>
            <i class="rs-chev" aria-hidden="true"></i>
          </a>
        </li>
        <li data-cat="kertas">
          <a class="rs-row" href="#" aria-label="Lihat detail setor sampah kertas, 8 Nov 2026, 1,5 kg, +15 poin, berhasil">
            <span class="rs-item"><svg class="rs-cat" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 5h6.5a3 3 0 0 1 3 3v12a2.5 2.5 0 0 0-2.5-2.5H2.5zM21.5 5H15a3 3 0 0 0-3 3v12a2.5 2.5 0 0 1 2.5-2.5h7zM5 9h4M15 9h4M5 12h4M15 12h4"/></svg><span class="rs-name"><strong>Setor Sampah Kertas</strong><time datetime="2026-11-08T16:20">8 Nov 2026 &nbsp; 16.20</time></span></span>
            <span class="rs-meta">
              <span class="rs-cell rs-weight"><svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 9a3.5 3.5 0 1 1 7 0h3.2a1.5 1.5 0 0 1 1.5 1.3l1.3 9.5a1.5 1.5 0 0 1-1.5 1.7H4a1.5 1.5 0 0 1-1.5-1.7l1.3-9.5A1.5 1.5 0 0 1 5.3 9zm5 0a1.5 1.5 0 1 0-3 0zm-1.5 3.5l2.2 3.2-1.4 1-2.2-3.2z"/></svg>1,5 kg</span>
              <span class="rs-cell rs-points"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg>+15 Poin</span>
              <span class="rs-cell rs-status"><span class="rs-badge">Berhasil</span></span>
            </span>
            <i class="rs-chev" aria-hidden="true"></i>
          </a>
        </li>
        <li data-cat="kaca">
          <a class="rs-row" href="#" aria-label="Lihat detail setor sampah kaca, 5 Nov 2026, 1 kg, +10 poin, berhasil">
            <span class="rs-item"><svg class="rs-cat" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 2.5h3v4l2 3.5v10.5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10l2-3.5zM15 12h4v8.5a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg><span class="rs-name"><strong>Setor Sampah Kaca</strong><time datetime="2026-11-05T11:15">5 Nov 2026 &nbsp; 11.15</time></span></span>
            <span class="rs-meta">
              <span class="rs-cell rs-weight"><svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 9a3.5 3.5 0 1 1 7 0h3.2a1.5 1.5 0 0 1 1.5 1.3l1.3 9.5a1.5 1.5 0 0 1-1.5 1.7H4a1.5 1.5 0 0 1-1.5-1.7l1.3-9.5A1.5 1.5 0 0 1 5.3 9zm5 0a1.5 1.5 0 1 0-3 0zm-1.5 3.5l2.2 3.2-1.4 1-2.2-3.2z"/></svg>1 kg</span>
              <span class="rs-cell rs-points"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg>+10 Poin</span>
              <span class="rs-cell rs-status"><span class="rs-badge">Berhasil</span></span>
            </span>
            <i class="rs-chev" aria-hidden="true"></i>
          </a>
        </li>
        <li data-cat="logam">
          <a class="rs-row" href="#" aria-label="Lihat detail setor sampah logam, 1 Nov 2026, 0,5 kg, +5 poin, berhasil">
            <span class="rs-item"><svg class="rs-cat" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="7" rx="8" ry="3"/><path d="M4 7v9c0 1.7 3.6 3 8 3s8-1.3 8-3V7M4 11.5c0 1.7 3.6 3 8 3s8-1.3 8-3"/></svg><span class="rs-name"><strong>Setor Sampah Logam</strong><time datetime="2026-11-01T09:50">1 Nov 2026 &nbsp; 09.50</time></span></span>
            <span class="rs-meta">
              <span class="rs-cell rs-weight"><svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 9a3.5 3.5 0 1 1 7 0h3.2a1.5 1.5 0 0 1 1.5 1.3l1.3 9.5a1.5 1.5 0 0 1-1.5 1.7H4a1.5 1.5 0 0 1-1.5-1.7l1.3-9.5A1.5 1.5 0 0 1 5.3 9zm5 0a1.5 1.5 0 1 0-3 0zm-1.5 3.5l2.2 3.2-1.4 1-2.2-3.2z"/></svg>0,5 kg</span>
              <span class="rs-cell rs-points"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg>+5 Poin</span>
              <span class="rs-cell rs-status"><span class="rs-badge">Berhasil</span></span>
            </span>
            <i class="rs-chev" aria-hidden="true"></i>
          </a>
        </li>
        <li data-cat="plastik">
          <a class="rs-row" href="#" aria-label="Lihat detail setor sampah plastik, 28 Okt 2026, 1,5 kg, +15 poin, berhasil">
            <span class="rs-item"><svg class="rs-cat" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 2.5h4v2.5l1.5 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-4a1.5 1.5 0 0 1-1.5-1.5v-12L10 5zM8.5 12h7M8.5 16h7"/></svg><span class="rs-name"><strong>Setor Sampah Plastik</strong><time datetime="2026-10-28T13:10">28 Okt 2026 &nbsp; 13.10</time></span></span>
            <span class="rs-meta">
              <span class="rs-cell rs-weight"><svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 9a3.5 3.5 0 1 1 7 0h3.2a1.5 1.5 0 0 1 1.5 1.3l1.3 9.5a1.5 1.5 0 0 1-1.5 1.7H4a1.5 1.5 0 0 1-1.5-1.7l1.3-9.5A1.5 1.5 0 0 1 5.3 9zm5 0a1.5 1.5 0 1 0-3 0zm-1.5 3.5l2.2 3.2-1.4 1-2.2-3.2z"/></svg>1,5 kg</span>
              <span class="rs-cell rs-points"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg>+15 Poin</span>
              <span class="rs-cell rs-status"><span class="rs-badge">Berhasil</span></span>
            </span>
            <i class="rs-chev" aria-hidden="true"></i>
          </a>
        </li>
      </ul>
      <p class="rs-empty" id="emptyMsg" hidden>Belum ada setoran untuk jenis sampah ini.</p>
    </section>
  </main>
  
<script src="header/sidebar.js" defer></script>
</body>
</html>