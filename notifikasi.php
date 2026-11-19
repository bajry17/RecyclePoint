<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifikasi – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="notifikasi.css">
</head>
<body class="bg-cream">
<?php include 'header/top_nav.php' ?>

  <main class="page">
    <nav class="breadcrumb" aria-label="Breadcrumb">Dashboard/Notifikasi</nav>

    <div class="notif-titlebar">
      <h1 class="page__title">Notifikasi</h1>
      <button class="notif-readall" id="readAll" type="button">Tandai semua dibaca</button>
    </div>

    <section class="notif-card" aria-label="Daftar notifikasi">

      <!-- Filter -->
      <div class="notif-tabs" role="group" aria-label="Filter notifikasi">
        <button class="notif-tab" type="button" data-filter="semua" aria-pressed="true">
          Semua <span class="notif-count" id="countAll">3</span>
        </button>
        <button class="notif-tab" type="button" data-filter="aktivitas" aria-pressed="false">Aktivitas sampah</button>
        <button class="notif-tab" type="button" data-filter="poin" aria-pressed="false">Poin &amp; Reward</button>
        <button class="notif-tab" type="button" data-filter="marketplace" aria-pressed="false">Marketplace</button>
      </div>

      <p class="notif-status" id="status" role="status" aria-live="polite"></p>

      <section class="notif-group" aria-labelledby="g-today">
        <h2 id="g-today">Hari ini</h2>
        <ul class="notif-list">
          <li class="notif-item notif-item--hl" data-cat="poin" data-unread="1">
            <span class="notif-ico" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="10" cy="6" rx="6" ry="2.5"/><path d="M4 6v3.5c0 1.4 2.7 2.5 6 2.5M4 9.5V13c0 1.4 2.7 2.5 6 2.5"/><ellipse cx="15" cy="13" rx="6" ry="2.5"/><path d="M9 13v3.5c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V13M9 16.5V20c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-3.5"/></svg></span>
            <div class="notif-body">
              <h3>Poin Berhasil Ditambahkan</h3>
              <p>Kamu mendapatkan 150 poin dari penyetoran sampah plastik</p>
              <time class="notif-time" datetime="10:30"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>10.30 WIB</time>
            </div>
            <span class="notif-new">Baru</span>
          </li>
          <li class="notif-item" data-cat="aktivitas" data-unread="1">
            <span class="notif-ico" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="10" cy="6" rx="6" ry="2.5"/><path d="M4 6v3.5c0 1.4 2.7 2.5 6 2.5M4 9.5V13c0 1.4 2.7 2.5 6 2.5"/><ellipse cx="15" cy="13" rx="6" ry="2.5"/><path d="M9 13v3.5c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V13M9 16.5V20c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-3.5"/></svg></span>
            <div class="notif-body">
              <h3>Penyetoran Berhasil</h3>
              <p>Sampah yang kamu setorkan telah berhasil ditimbang dan dicatat</p>
              <time class="notif-time" datetime="09:31"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>09.31 WIB</time>
            </div>
            <span class="notif-new">1 jam lalu</span>
          </li>
        </ul>
      </section>

      <section class="notif-group" aria-labelledby="g-yesterday">
        <h2 id="g-yesterday">Kemarin</h2>
        <ul class="notif-list">
          <li class="notif-item" data-cat="marketplace" data-unread="1">
            <span class="notif-ico" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="10" cy="6" rx="6" ry="2.5"/><path d="M4 6v3.5c0 1.4 2.7 2.5 6 2.5M4 9.5V13c0 1.4 2.7 2.5 6 2.5"/><ellipse cx="15" cy="13" rx="6" ry="2.5"/><path d="M9 13v3.5c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V13M9 16.5V20c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-3.5"/></svg></span>
            <div class="notif-body">
              <h3>Pesanan Berhasil</h3>
              <p>Pesanan produk kerajinan kamu sedang diproses penjual</p>
              <time class="notif-time" datetime="10:30"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>10.30 WIB</time>
            </div>
            <span class="notif-new"> 1 hari lalu</span>
          </li>
        </ul>
      </section>

      <p class="notif-empty" id="emptyMsg" hidden>Tidak ada notifikasi di kategori ini.</p>
    </section>
  </main>

<script>
  (function () {
    var tabs = document.querySelectorAll('.notif-tab');
    var items = document.querySelectorAll('.notif-list > li');
    var empty = document.getElementById('emptyMsg');
    var status = document.getElementById('status');
    var current = 'semua';

    function apply(announce) {
      var shown = 0;
      items.forEach(function (li) {
        var ok = (current === 'semua' || li.dataset.cat === current);
        li.hidden = !ok;
        if (ok) shown++;
      });
      empty.hidden = shown !== 0;
      if (announce) status.textContent = shown + ' setoran ditampilkan.';
    }

    tabs.forEach(function (t) {
      t.addEventListener('click', function () {
        current = t.dataset.filter;
        tabs.forEach(function (x) {
          x.setAttribute('aria-pressed', x === t ? 'true' : 'false');
        });
        apply(true);
      });
    });
  })();
</script>

<script src="header/sidebar.js" defer></script>
</body>
</html>