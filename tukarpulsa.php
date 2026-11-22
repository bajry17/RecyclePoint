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
<title>Tukar Pulsa | RecyclePoint</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="tukarpulsa.css">
<script src="header/sidebar.js" defer></script>
</head>
<body>
<?php include 'header/top_nav.php'; ?>

<div class="tp">
  <section class="tp-hero">
    <div class="tp-hero-in">
      <h1 class="tp-h1">Tukar Poin, Dapatkan <em>Lebih Banyak!</em></h1>
      <p class="tp-sub">Gunakan poin untuk kebutuhan anda</p>

      <div class="tp-pills">
        <a class="tp-pill" href="marketpoin.php"><i class="tp-ico tp-i-leaf"></i>Produk Daur Ulang</a>
        <span class="tp-pill is-active"><i class="tp-ico tp-i-phone"></i>Pulsa</span>
        <a class="tp-pill" href="tukarewallet.php"><i class="tp-ico tp-i-wallet"></i>E-wallet</a>
      </div>
    </div>

    <div class="tp-poin">
      <div class="tp-poin-left">
        <img src="images/coin.svg" alt="">
        <div>
          <div class="tp-poin-label">Poin Anda</div>
          <div class="tp-poin-val"><?= (int)$poin_user ?></div>
        </div>
      </div>
      <span class="tp-poin-divider"></span>
      <span class="tp-poin-hist">Riwayat poin</span>
      <i class="tp-ico tp-i-chev"></i>
    </div>
  </section>

  <form class="tp-form" id="tpForm" action="konfirmasipulsa.php" method="get">
    <div class="tp-wrap">
      <h2>Top-up Pulsa dengan Poin</h2>

      <label class="tp-label" for="tpNomor">Nomor Telepon</label>
      <div class="tp-inputrow">
        <input class="tp-input" type="tel" id="tpNomor" name="nomor" inputmode="numeric" maxlength="13"
               placeholder="Silahkan Masukkan Nomor Teleponmu" autocomplete="off">
        <div class="tp-opr" id="tpOpr" hidden>
          <img id="tpOprImg" alt="" hidden>
          <span id="tpOprNama"></span>
        </div>
      </div>

      <input type="hidden" id="tpNominal" name="nominal" value="">

      <div class="tp-opts">
        <?php foreach ($nominal_list as $n): ?>
        <button type="button" class="tp-opt" data-nominal="<?= $n ?>" aria-pressed="false">
          <span><?= number_format($n, 0, ',', '.') ?></span>
          <span class="tp-hrg"><img src="images/coin.svg" alt=""><?= $n / $rp_per_poin ?></span>
        </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="tp-foot">
      <span class="tp-tot">Total Poin:</span>
      <span class="tp-tp"><img src="images/coin.svg" alt=""><span id="tpTotal">0</span></span>
      <button type="submit" class="tp-btn" id="tpBtn" disabled>Tukar Sekarang</button>
    </div>
  </form>
</div>

<script>
const RP_PER_POIN = <?= (int)$rp_per_poin ?>;
const POIN_USER = <?= (int)$poin_user ?>;

// awalan nomor per operator (perlu diverifikasi)
const OPERATOR = {
  telkomsel: ['Telkomsel', ['0811', '0812', '0813', '0821', '0822', '0823', '0851', '0852', '0853']],
  indosat:   ['Indosat',   ['0814', '0815', '0816', '0855', '0856', '0857', '0858']],
  xl:        ['XL',        ['0817', '0818', '0819', '0859', '0877', '0878']],
  axis:      ['Axis',      ['0831', '0832', '0833', '0838']],
  tri:       ['Tri',       ['0895', '0896', '0897', '0898', '0899']],
  smartfren: ['Smartfren', ['0881', '0882', '0883', '0884', '0885', '0886', '0887', '0888', '0889']]
};

const opts = document.querySelectorAll('.tp-opt');
const nomor = document.getElementById('tpNomor');
const nominal = document.getElementById('tpNominal');
const totalEl = document.getElementById('tpTotal');
const btn = document.getElementById('tpBtn');
const opr = document.getElementById('tpOpr');
const oprImg = document.getElementById('tpOprImg');
const oprNama = document.getElementById('tpOprNama');
let terpilih = 0;

function cariOperator(no) {
  const awal = no.slice(0, 4);
  for (const k in OPERATOR) {
    if (OPERATOR[k][1].includes(awal)) return { key: k, nama: OPERATOR[k][0] };
  }
  return null;
}

function labelOperator() {
  if (nomor.value.length < 4) { opr.hidden = true; return; }
  const o = cariOperator(nomor.value);
  opr.hidden = false;
  if (o) {
    oprImg.hidden = false;
    oprImg.onerror = () => { oprImg.hidden = true; };
    oprImg.src = 'images/' + o.key + '.svg';
    oprNama.textContent = o.nama;
    opr.classList.remove('is-unknown');
  } else {
    oprImg.hidden = true;
    oprNama.textContent = 'Operator tidak dikenali';
    opr.classList.add('is-unknown');
  }
}

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
  labelOperator();
  cek();
});
</script>
</body>
</html>