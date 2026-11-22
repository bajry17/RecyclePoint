<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// ---- data user dari database (sesuaikan nama db, tabel, kolom) ----
$id_user = $_SESSION['id_user'] ?? 1;
$poin_user = 999999; // nilai cadangan jika database tidak terbaca
$no_hp = '0812121212121';
$alamat = 'Jl. Melati No. 24, Coblong, Kota Bandung, Jawa Barat 40132';
mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli('localhost', 'root', '', 'recyclepoint');
if (!$db->connect_errno && ($st = $db->prepare('SELECT poin, no_hp, alamat, kecamatan, kota, provinsi, kode_pos FROM users WHERE id_user = ?'))) {
    $st->bind_param('i', $id_user);
    $st->execute();
    $st->bind_result($p, $hp, $al, $kec, $kota, $prov, $pos);
    if ($st->fetch()) {
        $poin_user = (int)$p;
        $no_hp = $hp;
        $alamat = "$al, $kec, $kota, $prov $pos";
    }
    $st->close();
}

// ---- data produk (sementara di PHP; array yang sama ada di marketpoin.php) ----
$produk_list = [
    1 => ['nama' => 'Jurnal Handmade',    'poin' => 100, 'kategori' => 'kertas',  'gambar' => 'jurnal.jpg'],
    2 => ['nama' => 'Gantungan Kunci',    'poin' => 70,  'kategori' => 'logam',   'gambar' => 'gantungan-kunci.svg'],
    3 => ['nama' => 'Hanger Baju 5 pcs',  'poin' => 250, 'kategori' => 'plastik', 'gambar' => 'hanger.svg'],
    4 => ['nama' => 'Tissue Box Kirby',   'poin' => 150, 'kategori' => 'kardus',  'gambar' => 'tissue-box.svg'],
];
$ongkir = 100; // poin, tetap untuk semua produk

// produk dicari lewat id; kalau tidak valid, pakai produk pertama
$id = (int)($_GET['id'] ?? 0);
$produk = $produk_list[$id] ?? $produk_list[1];

$harga = $produk['poin'];
$total_poin = $harga + $ongkir;
$poin_sesudah = $poin_user - $total_poin;
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
    <img src="images/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama']) ?>">
    <div>
      <h2>Produk Daur Ulang</h2>
      <div class="pp-sub"><?= htmlspecialchars($produk['nama']) ?></div>
      <div class="pp-info">Nomor Telepon: <?= htmlspecialchars($no_hp) ?></div>
    </div>
    <div class="pp-addr">
      <h4>Alamat Rumah</h4>
      <p><?= htmlspecialchars($alamat) ?></p>
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

  <div class="pp-btn-wrap"><a class="pp-btn" href="marketpoin.php">Tukar Sekarang</a></div>
</div>

</body>
</html>