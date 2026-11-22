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

$nomor = $_GET['nomor'] ?? '';
$nominal = (int)($_GET['nominal'] ?? 0);

// kalau halaman dibuka langsung atau datanya tidak valid, pakai nilai default
if (!preg_match('/^08\d{8,11}$/', $nomor)) {
    $nomor = '0812121212121';
}
if (!in_array($nominal, $nominal_valid, true)) {
    $nominal = 5000;
}
$total_poin = $nominal / $rp_per_poin;
$poin_sesudah = $poin_user - $total_poin;

// operator dari 4 digit awal nomor (daftar awalan perlu diverifikasi)
$operator_peta = [
    'telkomsel' => ['Telkomsel', ['0811', '0812', '0813', '0821', '0822', '0823', '0851', '0852', '0853']],
    'indosat'   => ['Indosat',   ['0814', '0815', '0816', '0855', '0856', '0857', '0858']],
    'xl'        => ['XL',        ['0817', '0818', '0819', '0859', '0877', '0878']],
    'axis'      => ['Axis',      ['0831', '0832', '0833', '0838']],
    'tri'       => ['Tri',       ['0895', '0896', '0897', '0898', '0899']],
    'smartfren' => ['Smartfren', ['0881', '0882', '0883', '0884', '0885', '0886', '0887', '0888', '0889']],
];
$op_key = '';
$operator = 'Operator tidak dikenali';
foreach ($operator_peta as $key => [$nama_op, $awalan]) {
    if (in_array(substr($nomor, 0, 4), $awalan, true)) {
        $op_key = $key;
        $operator = $nama_op;
        break;
    }
}
$gambar = $op_key !== '' ? "images/$op_key.svg" : 'images/Placeholder.png';
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
<link rel="stylesheet" href="konfirmasipulsa.css">
<script src="header/sidebar.js" defer></script>
</head>
<body>
<?php include 'header/top_nav.php'; ?>

<div class="pp">
  <div class="pp-head">
    <a class="pp-back" href="tukarpulsa.php" aria-label="Kembali"><img src="images/back.svg" alt=""></a>
    <div class="pp-tab"><h1>Konfirmasi Penukaran</h1></div>
  </div>

  <div class="pp-box pp-item">
    <img src="<?= htmlspecialchars($gambar) ?>" alt="<?= htmlspecialchars($operator) ?>">
    <div>
      <h2>Pulsa</h2>
      <div class="pp-sub"><?= htmlspecialchars($operator) ?></div>
      <div class="pp-info">
        Nomor Telepon: <?= htmlspecialchars($nomor) ?><br>
        Top Up Pulsa: Rp<?= number_format($nominal, 0, ',', '.') ?>
      </div>
    </div>
  </div>

  <div class="pp-box pp-pay">
    <h3>Rincian Pembayaran</h3>
    <div class="pp-row"><span>Pulsa <?= htmlspecialchars($operator) ?></span><span class="pp-val"><?= (int)$total_poin ?></span></div>
    <div class="pp-tot">
      <span class="pp-lbl">Total Penukaran</span>
      <span class="pp-tp"><img src="images/coin.svg" alt=""><?= (int)$total_poin ?></span>
    </div>
    <div class="pp-sisa">
      <span class="pp-sl"><img src="images/coin.svg" alt="">Poin Anda</span>
      <span class="pp-sr"><span><?= (int)$poin_user ?></span><img src="images/arrow-right.svg" alt=""><span><?= (int)$poin_sesudah ?></span></span>
    </div>
  </div>

  <div class="pp-btn-wrap"><a class="pp-btn" href="tukarpulsa.php">Tukar Sekarang</a></div>
</div>

</body>
</html>