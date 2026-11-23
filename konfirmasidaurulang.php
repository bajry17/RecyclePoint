<?php
require_once __DIR__ . '/tukar_helper.php';

$src = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
$id  = (int) ($src['id'] ?? 0);

$p = produk_daur_ulang($conn, $id);
$langsung = !$p;
if ($langsung) {
    $p = produk_daur_ulang($conn);
}
if (!$p) {
    $_SESSION['flash_error'] = 'Belum ada produk daur ulang.';
    header('Location: marketpoin.php');
    exit;
}
$produk = ['nama' => $p['nama'], 'gambar' => foto_aman($p['foto'])];

$user      = data_user($conn, $user_id);
$no_hp     = (string) ($user['no_telepon'] ?? '');
$alamat    = alamat_lengkap($user);
$poin_user = poin_user($conn, $user_id);

$harga        = (int) $p['harga_poin'];
$ongkir       = (int) $p['ongkir_poin'];
$total_poin   = $harga + $ongkir;
$poin_sesudah = $poin_user - $total_poin;

$err = '';
if ($langsung)                  { $err = 'Halaman ini hanya contoh tampilan. Pilih dulu dari halaman sebelumnya untuk menukar.'; }
elseif ((int) $p['stok'] < 1)   { $err = 'Stok produk habis.'; }
elseif ($alamat === '')         { $err = 'Alamat rumah belum diisi. Lengkapi dulu di Edit Profil.'; }
elseif ($poin_sesudah < 0)      { $err = 'Poin kamu belum cukup untuk produk ini.'; }

$error = $err;
if ($error === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $error = 'Sesi tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $hasil = simpan_penukaran($conn, $user_id, (int) $p['id'], 'produk_daur_ulang', ['alamat_pengiriman' => $alamat]);
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
<link rel="stylesheet" href="konfirmasidaurulang.css">
<script src="header/sidebar.js" defer></script>
</head>
<body>
<?php include 'header/top_nav.php'; ?>

<div class="pp">
  <div class="pp-head">
    <a class="pp-back" href="marketpoin.php" aria-label="Kembali"><img src="images/back.svg" alt=""></a>
    <div class="pp-tab"><h1>Konfirmasi Penukaran</h1></div>
  </div>

  <div class="pp-box pp-item">
    <img src="<?= e($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama']) ?>">
    <div>
      <h2>Produk Daur Ulang</h2>
      <div class="pp-sub"><?= htmlspecialchars($produk['nama']) ?></div>
      <div class="pp-info">Nomor Telepon: <?= htmlspecialchars($no_hp) ?></div>
    </div>
    <div class="pp-addr">
      <h4>Alamat Rumah</h4>
      <p><?= $alamat !== '' ? e($alamat) : 'Belum diisi' ?></p>
    </div>
  </div>

  <div class="pp-box pp-pay">
    <h3>Rincian Pembayaran</h3>
    <div class="pp-row"><span><?= htmlspecialchars($produk['nama']) ?></span><span class="pp-val"><?= (int)$harga ?></span></div>
    <div class="pp-row pp-r2"><span>Biaya Ongkir</span><span class="pp-val"><?= (int)$ongkir ?></span></div>
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
  <form class="pp-btn-wrap" method="post" action="konfirmasidaurulang.php">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
    <button type="submit" class="pp-btn"<?= $error !== '' ? ' disabled' : '' ?>>Tukar Sekarang</button>
  </form>
</div>

</body>
</html>