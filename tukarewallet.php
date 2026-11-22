<?php
require_once __DIR__ . '/tukar_helper.php';

$poin_user   = poin_user($conn, $user_id);
$flash_error = ambil_flash_error();

// ---- nominal dan harga poin dari produk_poin (tipe = ewallet) ----
$nominal_list = daftar_nominal($conn, 'ewallet');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tukar E-wallet | RecyclePoint</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="tukarewallet.css">
<script src="header/sidebar.js" defer></script>
</head>
<body>
<?php include 'header/top_nav.php'; ?>

<div class="te">
  <section class="te-hero">
    <div class="te-hero-in">
      <h1 class="te-h1">Tukar Poin, Dapatkan <em>Lebih Banyak!</em></h1>
      <p class="te-sub">Gunakan poin untuk kebutuhan anda</p>

      <div class="te-pills">
        <a class="te-pill" href="marketpoin.php"><i class="te-ico te-i-leaf"></i>Produk Daur Ulang</a>
        <a class="te-pill" href="tukarpulsa.php"><i class="te-ico te-i-phone"></i>Pulsa</a>
        <span class="te-pill is-active"><i class="te-ico te-i-wallet"></i>E-wallet</span>
      </div>
    </div>

    <div class="te-poin">
      <div class="te-poin-left">
        <img src="images/coin.svg" alt="">
        <div>
          <div class="te-poin-label">Poin Anda</div>
          <div class="te-poin-val"><?= (int)$poin_user ?></div>
        </div>
      </div>
      <span class="te-poin-divider"></span>
      <a class="te-poin-hist" href="riwayat_transaksi.php">
        Riwayat poin
        <i class="te-ico te-i-chev"></i>
      </a>
    </div>
  </section>

  <?php if ($flash_error !== ''): ?><div class="rp-alert rp-alert--err" role="alert"><?= e($flash_error) ?></div><?php endif; ?>

  <form class="te-form" action="konfirmasiewallet.php" method="get">
    <div class="te-wrap">
      <h2>Top-up E-wallet dengan Poin</h2>

      <div class="te-app">
        <p>Pilih aplikasi e-wallet kamu</p>
        <div class="te-app-opts">
          <?php foreach (EWALLET_APP as $i => $app_nama): ?>
          <label class="te-radio">
            <input type="radio" name="app" value="<?= e($app_nama) ?>"<?= $i === 0 ? ' checked' : '' ?>>
            <span class="te-dot"></span>
            <img src="images/<?= e(strtolower($app_nama)) ?>.svg" alt="<?= e($app_nama) ?>">
          </label>
          <?php endforeach; ?>
</div>
      </div>

      <label class="te-label" for="teNomor">Nomor Telepon</label>
      <input class="te-input" type="tel" id="teNomor" name="nomor" inputmode="numeric" maxlength="13"
             placeholder="Silahkan Masukkan Nomor Teleponmu" autocomplete="off">

      <input type="hidden" id="teNominal" name="nominal" value="">

      <div class="te-opts">
        <?php foreach ($nominal_list as $row): $n = (int) $row['nominal']; ?>
        <button type="button" class="te-opt" data-nominal="<?= $n ?>" data-poin="<?= (int) $row['harga_poin'] ?>" aria-pressed="false">
          <span><?= number_format($n, 0, ',', '.') ?></span>
          <span class="te-hrg"><img src="images/coin.svg" alt=""><?= (int) $row['harga_poin'] ?></span>
        </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="te-foot">
      <span class="te-tot">Total Poin:</span>
      <span class="te-tp"><img src="images/coin.svg" alt=""><span id="teTotal">0</span></span>
      <button type="submit" class="te-btn" id="teBtn" disabled>Tukar Sekarang</button>
    </div>
  </form>
</div>

<script>
const POIN_USER = <?= (int)$poin_user ?>;

const opts = document.querySelectorAll('.te-opt');
const nomor = document.getElementById('teNomor');
const nominal = document.getElementById('teNominal');
const totalEl = document.getElementById('teTotal');
const btn = document.getElementById('teBtn');
let terpilih = 0;
let poinTerpilih = 0;

function cek() {
  const nomorOk = /^08\d{8,11}$/.test(nomor.value);
  const poinCukup = poinTerpilih <= POIN_USER;
  btn.disabled = !(nomorOk && terpilih > 0 && poinCukup);
}

opts.forEach(o => o.addEventListener('click', () => {
  opts.forEach(x => { x.classList.remove('is-selected'); x.setAttribute('aria-pressed', 'false'); });
  o.classList.add('is-selected');
  o.setAttribute('aria-pressed', 'true');
  terpilih = parseInt(o.dataset.nominal, 10);
  poinTerpilih = parseInt(o.dataset.poin, 10);
  nominal.value = terpilih;
  totalEl.textContent = poinTerpilih;
  cek();
}));

nomor.addEventListener('input', () => {
  nomor.value = nomor.value.replace(/\D/g, '').slice(0, 13);
  cek();
});
</script>
</body>
</html>