<?php
require_once __DIR__ . '/tukar_helper.php';

$src     = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
$nomor   = (string) ($src['nomor'] ?? '');
$nominal = (int) ($src['nominal'] ?? 0);

// ---- harga poin dari database; data tidak valid / dibuka langsung -> tampilan contoh dari database ----
$produk   = produk_nominal($conn, 'pulsa', $nominal);
$nomor_ok = (bool) preg_match('/^08\d{8,11}$/', $nomor);
$langsung = !($produk && $nomor_ok);
if (!$produk) {
    $produk = nominal_default($conn, 'pulsa');
}
if (!$produk) {
    $_SESSION['flash_error'] = 'Data nominal pulsa belum tersedia.';
    header('Location: tukarpulsa.php');
    exit;
}
if (!$nomor_ok) {
    $nomor = (string) (data_user($conn, $user_id)['no_telepon'] ?? '');
}
$nominal = (int) $produk['nominal'];

// ---- operator dari tabel operator_prefix ----
$op_nama  = operator_dari_nomor($conn, $nomor);
$operator = $op_nama ?? 'Operator tidak dikenali';
$op_key   = $op_nama !== null ? strtolower($op_nama) : '';
$gambar   = $op_key !== '' ? "images/$op_key.svg" : 'images/Placeholder.png';

$poin_user    = poin_user($conn, $user_id);
$total_poin   = (int) $produk['harga_poin'];
$poin_sesudah = $poin_user - $total_poin;

$err = '';
if ($langsung)             { $err = 'Halaman ini hanya contoh tampilan. Pilih dulu dari halaman sebelumnya untuk menukar.'; }
elseif ($op_nama === null) { $err = 'Operator nomor ini tidak dikenali.'; }
elseif ($poin_sesudah < 0) { $err = 'Poin kamu belum cukup untuk nominal ini.'; }

$error = $err;
if ($error === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $error = 'Sesi tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $hasil = simpan_penukaran($conn, $user_id, (int) $produk['id'], 'pulsa', ['nomor_tujuan' => $nomor, 'penyedia' => $op_nama]);
        if ($hasil['ok']) {
            $_SESSION['flash'] = 'Penukaran berhasil. Kode: ' . $hasil['kode'];
            header('Location: marketpoin.php');
            exit;
        }
        $error = $hasil['pesan'];
    }
}
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

  <?php if ($error !== ''): ?><div class="rp-alert rp-alert--err" role="alert"><?= e($error) ?></div><?php endif; ?>
  <form class="pp-btn-wrap" method="post" action="konfirmasipulsa.php">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <input type="hidden" name="nomor" value="<?= e($nomor) ?>">
    <input type="hidden" name="nominal" value="<?= (int) $nominal ?>">
    <button type="submit" class="pp-btn"<?= $error !== '' ? ' disabled' : '' ?>>Tukar Sekarang</button>
  </form>
</div>

</body>
</html>