<?php
$namaUser = 'Dudung';
$poinUser = 999999;

$rpPerPoin    = 100;
$daftarNominal = [5000, 10000, 15000, 20000, 25000, 30000, 50000, 75000, 100000, 150000];

if (!function_exists('e')) {
    function e($teks) { return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tukar Pulsa | RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="tukarpulsa.css">
</head>
<body>

<?php include 'header/top_nav.php' ?>

<main>
  <section class="hero">
    <div class="hero_inner">
      <h1 class="hero_title">Tukar Poin, Dapatkan <span class="accent">Lebih Banyak!</span></h1>
      <p class="hero_sub">Gunakan poin untuk kebutuhan anda</p>

      <nav class="pills" aria-label="Kategori market">
        <a class="pill" href="marketpoin.php">
          <span class="ico" style="--icon:url('images/leaf.svg')"></span><span>Produk Daur Ulang</span>
        </a>
        <a class="pill pill--active" href="tukarpulsa.php">
          <span class="ico" style="--icon:url('images/pulsa.svg')"></span><span>Pulsa</span>
        </a>
        <a class="pill" href="#">
          <span class="ico" style="--icon:url('images/ewallet.svg')"></span><span>E-wallet</span>
        </a>
        <a class="pill" href="#">
          <span class="ico" style="--icon:url('images/voucher.svg')"></span><span>Voucher</span>
        </a>
      </nav>

      <div class="points_card">
        <div class="points_info">
          <img src="images/coin.svg" alt="" class="points_coin">
          <div>
            <div class="points_label">Poin Anda</div>
            <div class="points_value"><?= e($poinUser) ?></div>
          </div>
        </div>
        <span class="points_divider"></span>
        <a class="points_history" href="#">
          <span>Riwayat poin</span>
          <span class="ico" style="--icon:url('images/chevron-right.svg')"></span>
        </a>
      </div>
    </div>
  </section>

  <form class="topup" id="formPulsa" action="penukaranpulsa.php" method="get" autocomplete="off">
    <h2 class="topup_title">Top-up Pulsa dengan Poin</h2>

    <label class="field_label" for="nomor">Nomor Telepon</label>
    <input class="field_input" type="tel" inputmode="numeric" id="nomor" name="nomor"
           placeholder="Silahkan Masukkan Nomor Teleponmu" maxlength="15">

    <div class="nominal_grid" role="radiogroup" aria-label="Pilih nominal pulsa">
      <?php foreach ($daftarNominal as $nom): $p = intdiv($nom, $rpPerPoin); ?>
        <label class="nominal">
          <input type="radio" name="nominal" value="<?= $nom ?>" data-poin="<?= $p ?>">
          <span class="nominal_box">
            <span class="nominal_rp"><?= number_format($nom, 0, ',', '.') ?></span>
            <span class="nominal_poin"><img src="images/coin.svg" alt=""><?= $p ?></span>
          </span>
        </label>
      <?php endforeach; ?>
    </div>

    <input type="hidden" name="poin" id="poin" value="">

    <div class="checkout">
      <div class="checkout_total">
        <span class="checkout_label">Total Poin:</span>
        <img src="images/coin.svg" alt="">
        <span class="checkout_value" id="totalPoin">0</span>
      </div>
      <button type="submit" class="checkout_btn" id="btnTukar" disabled>Tukar Sekarang</button>
    </div>
  </form>
</main>

<script src="header/sidebar.js" defer></script>
<script>
(function () {
  var MIN_DIGITS = 1;

  var form   = document.getElementById('formPulsa');
  var nomor  = document.getElementById('nomor');
  var poin   = document.getElementById('poin');
  var total  = document.getElementById('totalPoin');
  var btn    = document.getElementById('btnTukar');

  function sync() {
    var dipilih = form.querySelector('input[name="nominal"]:checked');
    var digit   = nomor.value.replace(/\D/g, '');

    total.textContent = dipilih ? dipilih.dataset.poin : '0';
    poin.value        = dipilih ? dipilih.dataset.poin : '';
    btn.disabled      = !(dipilih && digit.length >= MIN_DIGITS);
  }

  nomor.addEventListener('input', function () {
    this.value = this.value.replace(/[^\d+]/g, '').replace(/(?!^)\+/g, '');
    sync();
  });
  form.addEventListener('change', sync);
  form.addEventListener('submit', function (e) { sync(); if (btn.disabled) e.preventDefault(); });
  window.addEventListener('pageshow', sync);
  sync();
})();
</script>
</body>
</html>
