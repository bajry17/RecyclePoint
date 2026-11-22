<?php
require_once __DIR__ . '/tukar_helper.php';

$poin_user   = poin_user($conn, $user_id);
$flash       = ambil_flash();
$flash_error = ambil_flash_error();

// ---- produk daur ulang dari database ----
$produk_list = [];
$st = $conn->prepare("SELECT id, nama, harga_poin, kategori, foto
                        FROM produk_poin
                       WHERE tipe = 'produk_daur_ulang' AND aktif = 1
                       ORDER BY id");
$st->execute();
foreach ($st->get_result()->fetch_all(MYSQLI_ASSOC) as $r) {
    $produk_list[(int) $r['id']] = [
        'nama'     => $r['nama'],
        'poin'     => (int) $r['harga_poin'],
        'kategori' => $r['kategori'],
        'gambar'   => foto_aman($r['foto']),
    ];
}
$st->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Market Poin | RecyclePoint</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="marketpoin.css">
<script src="header/sidebar.js" defer></script>
</head>
<body>
<?php include 'header/top_nav.php'; ?>

<div class="mp">
  <section class="mp-hero">
    <div class="mp-hero-in">
      <h1 class="mp-h1">Tukar Poin, Dapatkan <em>Lebih Banyak!</em></h1>
      <p class="mp-sub">Gunakan poin untuk kebutuhan anda</p>

      <div class="mp-pills">
        <span class="mp-pill is-active"><i class="mp-ico mp-i-leaf"></i>Produk Daur Ulang</span>
        <a class="mp-pill" href="tukarpulsa.php"><i class="mp-ico mp-i-phone"></i>Pulsa</a>
        <a class="mp-pill" href="tukarewallet.php"><i class="mp-ico mp-i-wallet"></i>E-wallet</a>
      </div>

      <div class="mp-search">
        <input type="text" id="mpCari" placeholder="Cari di Market Poin" autocomplete="off">
        <button type="button" id="mpCariBtn" aria-label="Cari"><img src="images/search.svg" alt=""></button>
      </div>
    </div>

    <div class="mp-poin">
      <div class="mp-poin-left">
        <img src="images/coin.svg" alt="">
        <div>
          <div class="mp-poin-label">Poin Anda</div>
          <div class="mp-poin-val"><?= (int)$poin_user ?></div>
        </div>
      </div>
      <span class="mp-poin-divider"></span>
      <a class="mp-poin-hist" href="riwayat_transaksi.php">
        Riwayat poin
        <i class="mp-ico mp-i-chev"></i>
      </a>
    </div>
  </section>

  <div class="mp-tabs">
    <button type="button" class="mp-tab is-on" data-kat="semua">For You</button>
    <button type="button" class="mp-tab" data-kat="plastik">Plastik</button>
    <button type="button" class="mp-tab" data-kat="logam">Logam</button>
    <button type="button" class="mp-tab" data-kat="kardus">Kardus</button>
    <button type="button" class="mp-tab" data-kat="kertas">Kertas</button>
    <button type="button" class="mp-tab" data-kat="lainnya">Lainnya</button>
  </div>

  <?php if ($flash !== ''): ?><div class="rp-alert rp-alert--ok" role="status"><?= e($flash) ?></div><?php endif; ?>
  <?php if ($flash_error !== ''): ?><div class="rp-alert rp-alert--err" role="alert"><?= e($flash_error) ?></div><?php endif; ?>

  <div class="mp-grid">
    <?php foreach ($produk_list as $id => $p): ?>
    <div class="mp-card" data-kat="<?= htmlspecialchars($p['kategori']) ?>" data-nama="<?= htmlspecialchars(mb_strtolower($p['nama'])) ?>">
      <img class="mp-thumb" src="<?= e($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>">
      <div class="mp-nm"><?= htmlspecialchars($p['nama']) ?></div>
      <div class="mp-pr"><img src="images/coin.svg" alt=""><?= (int)$p['poin'] ?></div>
      <a class="mp-btn" href="konfirmasidaurulang.php?id=<?= (int)$id ?>">Tukar Sekarang</a>
    </div>
    <?php endforeach; ?>
    <p class="mp-kosong" id="mpKosong" hidden></p>
  </div>
</div>

<script>
const tabs = document.querySelectorAll('.mp-tab');
const kartu = document.querySelectorAll('.mp-card');
const cari = document.getElementById('mpCari');
const kosong = document.getElementById('mpKosong');
let kategori = 'semua';

function tampil() {
  const q = cari.value.trim().toLowerCase();
  let jumlah = 0;
  kartu.forEach(k => {
    const cocokKat = kategori === 'semua' || k.dataset.kat === kategori;
    const cocokNama = k.dataset.nama.includes(q);
    k.hidden = !(cocokKat && cocokNama);
    if (!k.hidden) jumlah++;
  });
  kosong.hidden = jumlah > 0;
  kosong.textContent = q ? 'Produk tidak ditemukan.' : 'Belum ada produk di kategori ini.';
}

tabs.forEach(t => t.addEventListener('click', () => {
  tabs.forEach(x => x.classList.remove('is-on'));
  t.classList.add('is-on');
  kategori = t.dataset.kat;
  tampil();
}));
cari.addEventListener('input', tampil);
document.getElementById('mpCariBtn').addEventListener('click', tampil);
cari.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); tampil(); } });
</script>
</body>
</html>