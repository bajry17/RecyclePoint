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

$rp_per_poin = 100;
$nominal_valid = [5000, 10000, 15000, 20000, 25000, 30000, 50000, 75000, 100000, 150000];
$app_valid = ['Gopay', 'Dana'];

$app = $_GET['app'] ?? 'Gopay';
$nomor = $_GET['nomor'] ?? '';
$nominal = (int)($_GET['nominal'] ?? 0);

// kalau halaman dibuka langsung atau datanya tidak valid, pakai nilai default
if (!in_array($app, $app_valid, true)) {
    $app = 'Gopay';
}
if (!preg_match('/^08\d{8,11}$/', $nomor)) {
    $nomor = '0812121212121';
}
if (!in_array($nominal, $nominal_valid, true)) {
    $nominal = 5000;
}
$total_poin = $nominal / $rp_per_poin;
$poin_sesudah = $poin_user - $total_poin;
$gambar = 'images/' . strtolower($app) . '.svg'; // gopay.svg atau dana.svg
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Konfirmasi Penukaran | RecyclePoint</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="konfirmasiewallet.css">
<script src="header/sidebar.js" defer></script>
</head>
<body>
<?php include 'header/top_nav.php'; ?>

<div class="pp">
  <div class="pp-head">
    <a class="pp-back" href="tukarewallet.php" aria-label="Kembali"><img src="images/back.svg" alt=""></a>
    <div class="pp-tab"><h1>Konfirmasi Penukaran</h1></div>
  </div>

  <div class="pp-box pp-item">
    <img src="<?= htmlspecialchars($gambar) ?>" alt="<?= htmlspecialchars($app) ?>">
    <div>
      <h2>E-wallet</h2>
      <div class="pp-sub"><?= htmlspecialchars($app) ?></div>
      <div class="pp-info">
        Nomor Telepon: <?= htmlspecialchars($nomor) ?><br>
        Top Up E-wallet: Rp<?= number_format($nominal, 0, ',', '.') ?>
      </div>
    </div>
  </div>

  <div class="pp-box pp-pay">
    <h3>Rincian Pembayaran</h3>
    <div class="pp-row"><span>Top-up <?= htmlspecialchars($app) ?></span><span class="pp-val"><?= (int)$total_poin ?></span></div>
    <div class="pp-tot">
      <span class="pp-lbl">Total Penukaran</span>
      <span class="pp-tp"><img src="images/coin.svg" alt=""><?= (int)$total_poin ?></span>
    </div>
    <div class="pp-sisa">
      <span class="pp-sl"><img src="images/coin.svg" alt="">Poin Anda</span>
      <span class="pp-sr"><span><?= (int)$poin_user ?></span><img src="images/arrow-right.svg" alt=""><span><?= (int)$poin_sesudah ?></span></span>
    </div>
  </div>

  <div class="pp-btn-wrap"><a class="pp-btn" href="tukarewallet.php">Tukar Sekarang</a></div>
</div>

</body>
</html>