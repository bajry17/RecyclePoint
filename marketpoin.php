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

$produk = [
    ['nama' => 'tes1', 'poin' => 2121],
    ['nama' => 'tes1', 'poin' => 2121],
    ['nama' => 'tes1', 'poin' => 2121],
    ['nama' => 'tes1', 'poin' => 2121],
];
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
        <input type="text" placeholder="Cari di Market Poin">
        <button type="button" aria-label="Cari"><img src="images/search.svg" alt=""></button>
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
      <span class="mp-poin-hist">Riwayat poin</span>
      <i class="mp-ico mp-i-chev"></i>
    </div>
  </section>

  <div class="mp-tabs">
    <span class="is-on">For You</span>
    <span>Plastik</span>
    <span>Logam</span>
    <span>Kardus</span>
    <span>Kertas</span>
    <span>Lainnya</span>
  </div>

  <div class="mp-grid">
    <?php foreach ($produk as $p): ?>
    <div class="mp-card">
      <img class="mp-thumb" src="images/Placeholder.png" alt="">
      <div class="mp-nm"><?= htmlspecialchars($p['nama']) ?></div>
      <div class="mp-pr"><img src="images/coin.svg" alt=""><?= (int)$p['poin'] ?></div>
      <a class="mp-btn" href="konfirmasidaurulang.php?produk=<?= urlencode($p['nama']) ?>&amp;poin=<?= (int)$p['poin'] ?>">Tukar Sekarang</a>
    </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>