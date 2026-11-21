<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// ---- data user dari database (sesuaikan nama db, tabel, kolom) ----
$id_user = $_SESSION['id_user'] ?? 1;
$poin_user = 999999; // nilai cadangan jika database tidak terbaca
mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli('localhost', 'root', '', 'recyclepoint');
if (!$db->connect_errno && ($st = $db->prepare('SELECT poin FROM users WHERE id_user = ?'))) {
    $st->bind_param('i', $id_user);
    $st->execute();
    $st->bind_result($p);
    if ($st->fetch()) $poin_user = (int)$p;
    $st->close();
}

$rp_per_poin = 100; // 1 poin = Rp100
$nominal_list = [5000, 10000, 15000, 20000, 25000, 30000, 50000, 75000, 100000, 150000];
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
      <span class="te-poin-hist">Riwayat poin</span>
      <i class="te-ico te-i-chev"></i>
    </div>
  </section>

  <form class="te-form" action="konfirmasiewallet.php" method="get">
    <div class="te-wrap">
      <h2>Top-up E-wallet dengan Poin</h2>

      <div class="te-app">
        <p>Pilih aplikasi e-wallet kamu</p>
        <div class="te-app-opts">
          <label class="te-radio"><input type="radio" name="app" value="Gopay" checked><span class="te-dot"></span>Gopay</label>
          <label class="te-radio"><input type="radio" name="app" value="Dana"><span class="te-dot"></span>Dana</label>
        </div>
      </div>

      <label class="te-label" for="teNomor">Nomor Telepon</label>
      <input class="te-input" type="tel" id="teNomor" name="nomor" inputmode="numeric" maxlength="13"
             placeholder="Silahkan Masukkan Nomor Teleponmu" autocomplete="off">

      <input type="hidden" id="teNominal" name="nominal" value="">

      <div class="te-opts">
        <?php foreach ($nominal_list as $n): ?>
        <button type="button" class="te-opt" data-nominal="<?= $n ?>" aria-pressed="false">
          <span><?= number_format($n, 0, ',', '.') ?></span>
          <span class="te-hrg"><img src="images/coin.svg" alt=""><?= $n / $rp_per_poin ?></span>
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
const RP_PER_POIN = <?= (int)$rp_per_poin ?>;
const POIN_USER = <?= (int)$poin_user ?>;

const opts = document.querySelectorAll('.te-opt');
const nomor = document.getElementById('teNomor');
const nominal = document.getElementById('teNominal');
const totalEl = document.getElementById('teTotal');
const btn = document.getElementById('teBtn');
let terpilih = 0;

function cek() {
  const nomorOk = /^08\d{8,11}$/.test(nomor.value);
  const poinCukup = terpilih / RP_PER_POIN <= POIN_USER;
  btn.disabled = !(nomorOk && terpilih > 0 && poinCukup);
}

opts.forEach(o => o.addEventListener('click', () => {
  opts.forEach(x => { x.classList.remove('is-selected'); x.setAttribute('aria-pressed', 'false'); });
  o.classList.add('is-selected');
  o.setAttribute('aria-pressed', 'true');
  terpilih = parseInt(o.dataset.nominal, 10);
  nominal.value = terpilih;
  totalEl.textContent = terpilih / RP_PER_POIN;
  cek();
}));

nomor.addEventListener('input', () => {
  nomor.value = nomor.value.replace(/\D/g, '').slice(0, 13);
  cek();
});
</script>
</body>
</html>