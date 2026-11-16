<?php
$namaUser = 'Dudung';
$poinUser = 999999;

$tabKategori = ['For You', 'Plastik', 'Logam', 'Kardus', 'Kertas', 'Lainnya'];

$produk = [];
for ($i = 0; $i < 4; $i++) {
    $produk[] = [
        'nama'   => 'tes1',
        'poin'   => 2121,
        'rating' => '5.0',
        'gambar' => 'images/Placeholder.png',
    ];
}

if (!function_exists('e')) {
    function e($teks) { return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Market Poin | RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="marketpoin.css">
</head>
<body>

<?php include 'header/top_nav.php' ?>

<main>
  <section class="hero">
    <div class="hero_inner">
      <h1 class="hero_title">Tukar Poin, Dapatkan <span class="accent">Lebih Banyak!</span></h1>
      <p class="hero_sub">Gunakan poin untuk kebutuhan anda</p>

      <nav class="pills" aria-label="Kategori market">
        <a class="pill pill--active" href="marketpoin.php">
          <span class="ico" style="--icon:url('images/leaf.svg')"></span><span>Produk Daur Ulang</span>
        </a>
        <a class="pill" href="tukarpulsa.php">
          <span class="ico" style="--icon:url('images/pulsa.svg')"></span><span>Pulsa</span>
        </a>
        <a class="pill" href="#">
          <span class="ico" style="--icon:url('images/ewallet.svg')"></span><span>E-wallet</span>
        </a>
        <a class="pill" href="#">
          <span class="ico" style="--icon:url('images/voucher.svg')"></span><span>Voucher</span>
        </a>
      </nav>

      <div class="search_row">
        <div class="search">
          <input type="text" class="search_input" placeholder="Cari di Market Poin" aria-label="Cari di Market Poin">
          <button type="button" class="search_btn" aria-label="Cari">
            <span class="ico" style="--icon:url('images/search.svg')"></span>
          </button>
        </div>
        <a class="basket" href="#" aria-label="Keranjang">
          <span class="ico" style="--icon:url('images/basket.svg')"></span>
        </a>
      </div>

      <div class="points_card">
        <div class="points_info">
          <img src="images/coin.svg" alt="" class="points_coin">
          <div>
            <div class="points_label">Poin Anda</div>
            <div class="points_value"><?= e($poinUser) ?></div>
          </div>
        </div>
        <span class="points_divider"></span>
        <a class="points_history" href="#">
          <span>Riwayat poin</span>
          <span class="ico" style="--icon:url('images/chevron-right.svg')"></span>
        </a>
      </div>
    </div>
  </section>

  <nav class="tabs" aria-label="Kategori produk">
    <?php foreach ($tabKategori as $i => $tab): ?>
      <a href="#" class="tab<?= $i === 0 ? ' tab--active' : '' ?>"><?= e($tab) ?></a>
    <?php endforeach; ?>
  </nav>

  <section class="products">
    <?php foreach ($produk as $p): ?>
      <article class="card">
        <img class="card_img" src="<?= e($p['gambar']) ?>" alt="<?= e($p['nama']) ?>">
        <div class="card_body">
          <h2 class="card_name"><?= e($p['nama']) ?></h2>
          <div class="card_price"><img src="images/coin.svg" alt=""><span><?= e($p['poin']) ?></span></div>
          <div class="card_rating"><img src="images/star.svg" alt=""><span><?= e($p['rating']) ?></span></div>
        </div>
      </article>
    <?php endforeach; ?>
  </section>
</main>

<script src="header/sidebar.js" defer></script>
</body>
</html>
