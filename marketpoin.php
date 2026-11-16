<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Market Poin – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="marketpoin.css">
</head>
<body class="bg-cream">

  <?php include 'header/top_nav.php'?>

  <!-- ===== HERO ===== -->
  <section class="mp-hero" aria-labelledby="mp-title">
    <div class="mp-hero__inner">
      <div class="mp-hero__main">
        <h1 id="mp-title">Tukar Poin, Dapatkan <span>Lebih Banyak!</span></h1>
        <p class="mp-hero__sub">Gunakan poin untuk kebutuhan anda</p>

        <ul class="mp-cats" aria-label="Kategori penukaran">
          <li><a class="mp-cat is-active" href="#" aria-current="page"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 19C5 10 10 5 20 4c0 10-5 15-13 15zM5 19c3-5 6-8 10-10"/></svg>Produk Daur Ulang</a></li>
          <li><a class="mp-cat" href="#"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M12 9v6M9 12h6"/></svg>Pulsa</a></li>
          <li><a class="mp-cat" href="#"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="6" width="19" height="13" rx="2.5"/><path d="M2.5 10h19M16 14.5h2"/></svg>E-wallet</a></li>
          <li><a class="mp-cat" href="#"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4zM9 9l6 6M9.5 14.5h.01M14.5 9.5h.01"/></svg>Voucher</a></li>
        </ul>

        <div class="mp-searchrow">
          <form class="mp-search" role="search" id="searchForm">
            <label class="sr-only" for="q">Cari di Market Poin</label>
            <input id="q" name="q" type="search" placeholder="Cari di Market Poin" autocomplete="off">
            <button type="submit" aria-label="Cari"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg></button>
          </form>
          <a class="mp-cart" href="#" aria-label="Keranjang">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h18l-2 10H5zM8 10l3-6M16 10l-3-6M9 14v3M12 14v3M15 14v3"/></svg>
          </a>
        </div>
      </div>

      <aside class="mp-points" aria-label="Poin kamu">
        <svg width="46" height="46" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#F7C531" stroke="#C99A00" stroke-width="1.2"/><circle cx="12" cy="12" r="6.8" fill="none" stroke="#C99A00" stroke-width="1.5"/></svg>
        <div class="mp-points__txt"><span>Poin Anda</span><strong>999999</strong></div>
        <a class="mp-points__link" href="riwayat_poin.php">Riwayat poin <i class="mp-chev" aria-hidden="true"></i></a>
      </aside>
    </div>
  </section>

  <main class="page mp-page">
    <!-- Tab kategori produk -->
    <div class="mp-tabs" role="group" aria-label="Filter produk">
      <button class="mp-tab" type="button" data-filter="semua" aria-pressed="true">For You</button>
      <button class="mp-tab" type="button" data-filter="plastik" aria-pressed="false">Plastik</button>
      <button class="mp-tab" type="button" data-filter="logam" aria-pressed="false">Logam</button>
      <button class="mp-tab" type="button" data-filter="kardus" aria-pressed="false">Kardus</button>
      <button class="mp-tab" type="button" data-filter="kertas" aria-pressed="false">Kertas</button>
      <button class="mp-tab" type="button" data-filter="lainnya" aria-pressed="false">Lainnya</button>
    </div>
    <p class="mp-status" id="status" role="status" aria-live="polite"></p>

    <!-- Daftar produk -->
    <ul class="mp-grid" id="grid">
        <li data-cat="plastik" data-name="tes1">
          <a class="mp-card" href="#" aria-label="tes1, 2.121 poin, rating 5.0">
            <!-- Ganti .mp-thumb dengan: <img class="mp-img" src="images/produk/tes1.jpg" alt="tes1"> -->
            <span class="mp-thumb" aria-hidden="true"><svg viewBox="0 0 200 150" preserveAspectRatio="none"><path d="M0 0L200 150M200 0L0 150" stroke="#fff" stroke-width="3" opacity=".55"/></svg><b>PLACEHOLDER</b></span>
            <span class="mp-info">
              <strong class="mp-name">tes1</strong>
              <span class="mp-price"><svg class="mp-coin" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10.5" fill="#F7C531" stroke="#C99A00" stroke-width="1.5"/><circle cx="12" cy="12" r="6.5" fill="none" stroke="#C99A00" stroke-width="1.5"/></svg>2.121</span>
              <span class="mp-rate"><svg width="18" height="18" viewBox="0 0 24 24" fill="#F5B400" aria-hidden="true"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21.1 7 14.2 2 9.3l6.9-1z"/></svg>5.0</span>
            </span>
          </a>
        </li>
        <li data-cat="logam" data-name="tes2">
          <a class="mp-card" href="#" aria-label="tes2, 2.121 poin, rating 5.0">
            <!-- Ganti .mp-thumb dengan: <img class="mp-img" src="images/produk/tes2.jpg" alt="tes2"> -->
            <span class="mp-thumb" aria-hidden="true"><svg viewBox="0 0 200 150" preserveAspectRatio="none"><path d="M0 0L200 150M200 0L0 150" stroke="#fff" stroke-width="3" opacity=".55"/></svg><b>PLACEHOLDER</b></span>
            <span class="mp-info">
              <strong class="mp-name">tes2</strong>
              <span class="mp-price"><svg class="mp-coin" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10.5" fill="#F7C531" stroke="#C99A00" stroke-width="1.5"/><circle cx="12" cy="12" r="6.5" fill="none" stroke="#C99A00" stroke-width="1.5"/></svg>2.121</span>
              <span class="mp-rate"><svg width="18" height="18" viewBox="0 0 24 24" fill="#F5B400" aria-hidden="true"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21.1 7 14.2 2 9.3l6.9-1z"/></svg>5.0</span>
            </span>
          </a>
        </li>
        <li data-cat="kardus" data-name="tes3">
          <a class="mp-card" href="#" aria-label="tes3, 2.121 poin, rating 5.0">
            <!-- Ganti .mp-thumb dengan: <img class="mp-img" src="images/produk/tes3.jpg" alt="tes3"> -->
            <span class="mp-thumb" aria-hidden="true"><svg viewBox="0 0 200 150" preserveAspectRatio="none"><path d="M0 0L200 150M200 0L0 150" stroke="#fff" stroke-width="3" opacity=".55"/></svg><b>PLACEHOLDER</b></span>
            <span class="mp-info">
              <strong class="mp-name">tes3</strong>
              <span class="mp-price"><svg class="mp-coin" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10.5" fill="#F7C531" stroke="#C99A00" stroke-width="1.5"/><circle cx="12" cy="12" r="6.5" fill="none" stroke="#C99A00" stroke-width="1.5"/></svg>2.121</span>
              <span class="mp-rate"><svg width="18" height="18" viewBox="0 0 24 24" fill="#F5B400" aria-hidden="true"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21.1 7 14.2 2 9.3l6.9-1z"/></svg>5.0</span>
            </span>
          </a>
        </li>
        <li data-cat="kertas" data-name="tes4">
          <a class="mp-card" href="#" aria-label="tes4, 2.121 poin, rating 5.0">
            <!-- Ganti .mp-thumb dengan: <img class="mp-img" src="images/produk/tes4.jpg" alt="tes4"> -->
            <span class="mp-thumb" aria-hidden="true"><svg viewBox="0 0 200 150" preserveAspectRatio="none"><path d="M0 0L200 150M200 0L0 150" stroke="#fff" stroke-width="3" opacity=".55"/></svg><b>PLACEHOLDER</b></span>
            <span class="mp-info">
              <strong class="mp-name">tes4</strong>
              <span class="mp-price"><svg class="mp-coin" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10.5" fill="#F7C531" stroke="#C99A00" stroke-width="1.5"/><circle cx="12" cy="12" r="6.5" fill="none" stroke="#C99A00" stroke-width="1.5"/></svg>2.121</span>
              <span class="mp-rate"><svg width="18" height="18" viewBox="0 0 24 24" fill="#F5B400" aria-hidden="true"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21.1 7 14.2 2 9.3l6.9-1z"/></svg>5.0</span>
            </span>
          </a>
        </li>
    </ul>
    <p class="mp-empty" id="emptyMsg" hidden>Produk tidak ditemukan.</p>
  </main>

<script>
  (function () {
    var tabs = document.querySelectorAll('.mp-tab');
    var items = document.querySelectorAll('#grid > li');
    var empty = document.getElementById('emptyMsg');
    var status = document.getElementById('status');
    var form = document.getElementById('searchForm');
    var q = document.getElementById('q');
    var current = 'semua';

    function apply(announce) {
      var term = q.value.trim().toLowerCase(), shown = 0;
      items.forEach(function (li) {
        var ok = (current === 'semua' || li.dataset.cat === current) &&
                 (term === '' || li.dataset.name.toLowerCase().indexOf(term) !== -1);
        li.hidden = !ok; if (ok) shown++;
      });
      empty.hidden = shown !== 0;
      if (announce) status.textContent = shown + ' produk ditampilkan.';
    }

    tabs.forEach(function (t) {
      t.addEventListener('click', function () {
        current = t.dataset.filter;
        tabs.forEach(function (x) { x.setAttribute('aria-pressed', x === t ? 'true' : 'false'); });
        apply(true);
      });
    });
    form.addEventListener('submit', function (e) { e.preventDefault(); apply(true); });
    q.addEventListener('input', function () { apply(false); });
  })();
</script>
<script src="header/sidebar.js" defer></script>
</body>
</html>