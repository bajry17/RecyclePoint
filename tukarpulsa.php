<?php
require_once __DIR__ . '/tukar_helper.php';

$poin_user   = poin_user($conn, $user_id);
$flash_error = ambil_flash_error();

$nominal_list = daftar_nominal($conn, 'pulsa');
$peta_operator = peta_operator($conn);
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
      <a class="tp-poin-hist" href="riwayat_transaksi.php">
        Riwayat poin
        <i class="tp-ico tp-i-chev"></i>
      </a>
    </div>
  </section>

  <?php if ($flash_error !== ''): ?><div class="rp-alert rp-alert--err" role="alert"><?= e($flash_error) ?></div><?php endif; ?>

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
        <?php foreach ($nominal_list as $row): $n = (int) $row['nominal']; ?>
        <button type="button" class="tp-opt" data-nominal="<?= $n ?>" data-poin="<?= (int) $row['harga_poin'] ?>" aria-pressed="false">
          <span><?= number_format($n, 0, ',', '.') ?></span>
          <span class="tp-hrg"><img src="images/coin.svg" alt=""><?= (int) $row['harga_poin'] ?></span>
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
const POIN_USER = <?= (int)$poin_user ?>;

const PREFIX_OPERATOR = <?= json_encode($peta_operator, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

const opts = document.querySelectorAll('.tp-opt');
const nomor = document.getElementById('tpNomor');
const nominal = document.getElementById('tpNominal');
const totalEl = document.getElementById('tpTotal');
const btn = document.getElementById('tpBtn');
const opr = document.getElementById('tpOpr');
const oprImg = document.getElementById('tpOprImg');
const oprNama = document.getElementById('tpOprNama');
let terpilih = 0;
let poinTerpilih = 0;

function cariOperator(no) {
  const nama = PREFIX_OPERATOR[no.slice(0, 4)];
  return nama ? { key: nama.toLowerCase(), nama: nama } : null;
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
  const poinCukup = poinTerpilih <= POIN_USER;
  const operatorOk = cariOperator(nomor.value) !== null;
  btn.disabled = !(nomorOk && operatorOk && terpilih > 0 && poinCukup);
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
  labelOperator();
  cek();
});
</script>
</body>
</html>