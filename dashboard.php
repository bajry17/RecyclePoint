<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body class="bg-cream">

  <?php include 'header/top_nav.php'?>

  <section class="dash-hero" aria-labelledby="hero-title">
    <div class="dash-hero__inner">
      <div class="dash-hero__text">
          <p class="dash-hero__crumb">Dashboard</p>
          <h1 id="hero-title">Small Action,<br><span>Big</span> Impact</h1>
          <p class="dash-hero__sub">Selamat datang di Recycle Point!</p>
        </div>

      <div class="dash-hero__right">
          <p class="dash-hero__tag">Setor sampah pilahanmu, disini!</p>
          <ul class="dash-chips" aria-label="Jenis sampah">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 2.5h4v2.5l1.5 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-4a1.5 1.5 0 0 1-1.5-1.5v-12L10 5zM8.5 12h7M8.5 16h7"/></svg>Plastik</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 5h6.5a3 3 0 0 1 3 3v12a2.5 2.5 0 0 0-2.5-2.5H2.5zM21.5 5H15a3 3 0 0 0-3 3v12a2.5 2.5 0 0 1 2.5-2.5h7zM5 9h4M15 9h4M5 12h4M15 12h4"/></svg>Kertas</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 2.5h3v4l2 3.5v10.5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10l2-3.5zM15 12h4v8.5a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg>Kaca</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="7" rx="8" ry="3"/><path d="M4 7v9c0 1.7 3.6 3 8 3s8-1.3 8-3V7M4 11.5c0 1.7 3.6 3 8 3s8-1.3 8-3"/></svg>Logam</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8l9-5 9 5v9l-9 5-9-5zM3 8l9 5 9-5M12 13v9M7.5 5.5l9 5"/></svg>Kardus</li>
          </ul>
        </div>
    </div>
  </section>

  <main class="page dash">
    <!-- Sapaan -->
    <section class="dash-greet">
        <h2>Halo, Dudung!</h2>
        <p><svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12 2a7.5 7.5 0 0 0-7.5 7.5C4.5 15 12 22 12 22s7.5-7 7.5-12.5A7.5 7.5 0 0 0 12 2zm0 10.5a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>Jl. Kembangan Timur, Jakarta Barat</p>
      </section>

    <!-- Statistik -->
    <section class="dash-stats" aria-label="Ringkasan">
        <a class="dash-stat" href="#">
          <svg class="dash-stat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg>
          <span class="dash-stat__txt"><strong>1.250</strong><span>Poin Anda</span></span>
          <i class="dash-chev" aria-hidden="true"></i></a>
        <div class="dash-stat">
          <svg class="dash-stat__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 9a3.5 3.5 0 1 1 7 0h3.2a1.5 1.5 0 0 1 1.5 1.3l1.3 9.5a1.5 1.5 0 0 1-1.5 1.7H4a1.5 1.5 0 0 1-1.5-1.7l1.3-9.5A1.5 1.5 0 0 1 5.3 9zm5 0a1.5 1.5 0 1 0-3 0zm-1.5 3.5l2.2 3.2-1.4 1-2.2-3.2z"/></svg>
          <span class="dash-stat__txt"><strong>3,5 Kg</strong><span>Sampah Terkumpul</span></span></div>
        <a class="dash-stat" href="#">
          <svg class="dash-stat__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 8h18v4H3zM4 13h7v8H4zM13 13h7v8h-7zM11 8h2v13h-2zM12 8C8.5 8 7 5 9 4s3 1.5 3 4zm0 0c3.5 0 5-3 3-4s-3 1.5-3 4z"/></svg>
          <span class="dash-stat__txt"><strong>2</strong><span>Hadiah Tersedia</span></span>
          <i class="dash-chev" aria-hidden="true"></i></a>
        <a class="dash-stat" href="#">
          <svg class="dash-stat__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5l10 5V10H2V7.5zM4 11h2.5v7H4zM8.8 11h2.4v7H8.8zM12.8 11h2.4v7h-2.4zM17.5 11H20v7h-2.5zM2 19.5h20V22H2z"/></svg>
          <span class="dash-stat__txt"><strong>5</strong><span>Mitra Bank Sampah</span></span>
          <i class="dash-chev" aria-hidden="true"></i></a>
      </section>

    <!-- Lokasi & transaksi -->
    <div class="dash-panels">
        <section class="dash-panel" aria-labelledby="loc-title">
          <div class="dash-panel__head">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12 2a7.5 7.5 0 0 0-7.5 7.5C4.5 15 12 22 12 22s7.5-7 7.5-12.5A7.5 7.5 0 0 0 12 2zm0 10.5a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
            <h3 id="loc-title">Lokasi Bank Sampah Terdekat</h3>
            <a href="#">Selanjutnya <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor" aria-hidden="true"><path d="M1 1l5 4-5 4zM8 1h1.5v8H8z"/></svg></a>
          </div>
          <div class="dash-map" role="img" aria-label="Peta: Bank Sampah Melati, 1,2 km dari lokasi Anda">
            <svg class="dash-map__bg" viewBox="0 0 500 160" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
              <rect width="500" height="160" fill="#F3E6DF"/>
              <path d="M0 20L120 0h40L0 60zM330 160l120-70 50 10v60z" fill="#DDEFD2"/>
              <path d="M-10 40C80 70 160 120 260 140s180 30 260 20v-30C440 140 340 120 250 100 170 80 90 40 20 10z" fill="#A9DDE8"/>
              <g stroke="#fff" stroke-width="7" fill="none" stroke-linecap="round"><path d="M0 120L500 20"/><path d="M110 0l90 160"/><path d="M300 0l60 160"/><path d="M0 70l500 40"/></g>
              <g stroke="#fff" stroke-width="3" fill="none"><path d="M40 160l40-160M420 0l30 160M200 0l-30 160"/></g>
            </svg>
            <div class="dash-map__card">
              <span class="dash-map__pin" aria-hidden="true">
                <svg width="52" height="62" viewBox="0 0 52 62"><path d="M26 61S3 40 3 24a23 23 0 0 1 46 0C49 40 26 61 26 61z" fill="#1F9D5C"/><circle cx="26" cy="24" r="14" fill="#fff"/><g transform="translate(26 24)" fill="none" stroke="#1F9D5C" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M-6 6l-3-5 3-5M6-6l3 5-3 5M-3-8h5M-2 8h5"/></g></svg>
              </span>
              <span class="dash-map__txt"><strong>Bank Sampah Melati</strong><span>1,2 km dari lokasi Anda</span></span>
              <i class="dash-chev dash-chev--dark" aria-hidden="true"></i>
            </div>
          </div>
        </section>

        <section class="dash-panel" aria-labelledby="trx-title">
          <div class="dash-panel__head">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2h12v8M6 2v19l3-2 3 2 1.5-1M9 7h6M9 11h3"/><circle cx="17" cy="16" r="3.2"/><path d="M19.5 18.5L22 21"/></svg>
            <h3 id="trx-title">Transaksi Terbaru</h3>
            <a href="#">Selanjutnya <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor" aria-hidden="true"><path d="M1 1l5 4-5 4zM8 1h1.5v8H8z"/></svg></a>
          </div>
          <ul class="dash-trx">
            <li>
              <div><strong>Setor Sampah Plastik</strong><span>12 Nov 2026 &nbsp; 14.30</span></div>
              <b>+20 Poin</b>
            </li>
            <li>
              <div><strong>Setor Sampah Kertas</strong><span>8 Nov 2026 &nbsp; 16.20</span></div>
              <b>+15 Poin</b>
            </li>
          </ul>
        </section>
      </div>
  </main>

<script src="header/sidebar.js" defer></script>
</body>
</html>