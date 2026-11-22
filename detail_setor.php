<?php
session_start();
require_once 'koneksi.php';

if (empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$userId = (int) $_SESSION['user_id'];

function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
function kg($v) { return rtrim(rtrim(number_format((float) $v, 2, ',', '.'), '0'), ','); }

$kode = trim($_GET['kode'] ?? '');
$stmt = $conn->prepare(
    'SELECT s.id, s.kode, s.status, s.total_berat_kg, s.total_poin, s.waktu_setor,
            b.nama AS bank_sampah, p.nama AS petugas
       FROM setoran s
       JOIN bank_sampah b ON b.id = s.bank_sampah_id
  LEFT JOIN petugas p     ON p.id = s.petugas_id
      WHERE s.user_id = ? AND (CHAR_LENGTH(?) = 0 OR s.kode = ?)
   ORDER BY s.waktu_setor DESC
      LIMIT 1'
);
$stmt->bind_param('iss', $userId, $kode, $kode);
$stmt->execute();
$s = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($s) {
    $stmt = $conn->prepare('SELECT jenis_sampah, berat_kg, poin_didapat FROM setoran_item WHERE setoran_id = ? ORDER BY id');
    $stmt->bind_param('i', $s['id']);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $berat = $items ? array_sum(array_column($items, 'berat_kg'))     : $s['total_berat_kg'];
    $poin  = $items ? array_sum(array_column($items, 'poin_didapat')) : $s['total_poin'];

    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $w    = new DateTime($s['waktu_setor']);
    $info = [
        'Tanggal' => $w->format('j') . ' ' . $bulan[(int) $w->format('n')] . ' ' . $w->format('Y'),
        'Waktu'   => $w->format('H.i') . ' WIB',
        'Petugas' => $s['petugas'] ?? '-',
        'Lokasi'  => $s['bank_sampah'],
    ];
} else {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Setor Sampah - RecyclePoint</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="detail_setor.css">
</head>

<body class="bg-cream">

  <?php include 'header/top_nav.php' ?>

  <main class="page ds-page">

    <nav class="breadcrumb" aria-label="Breadcrumb"><a href="dashboard.php">Dashboard</a>/<a href="riwayat_setor.php">Riwayat Setor</a>/ Detail</nav>

    <div class="ds-heading">
      <h1 class="page__title">Detail Setor Sampah</h1>
      <?php if ($s): ?>
        <div class="ds-actions"><button type="button" class="ds-button" onclick="window.print()">Cetak bukti</button></div>
      <?php endif; ?>
    </div>

  <?php if (!$s): ?>

    <section class="ds-card ds-info">
      <h2>Setoran tidak ditemukan</h2>
      <p>Data setoran tidak ada atau bukan milik akun Anda. <a href="riwayat_setor.php">Kembali ke Riwayat Setor</a></p>
    </section>

  <?php else: ?>

    <section class="ds-banner">
      <span class="ds-badge ds-badge--<?= e($s['status']) ?>"><?= e(ucfirst($s['status'])) ?></span>
      <h2><?= e($s['kode']) ?></h2>
      <div class="ds-total"><p>Total berat</p><strong><?= e(kg($berat)) ?><small>kg</small></strong></div>
      <div class="ds-total ds-total--orange"><p>Poin diperoleh</p><strong>+<?= e($poin) ?><small>poin</small></strong></div>
    </section>

    <div class="ds-content">

      <section class="ds-card ds-info">
        <h2>Informasi</h2>
        <?php foreach ($info as $label => $nilai): ?>
          <div class="ds-row"><span><?= e($label) ?></span><strong><?= e($nilai) ?></strong></div>
        <?php endforeach; ?>
      </section>

      <section class="ds-card ds-detail">
        <h2>Rincian Item Sampah</h2>
        <div class="ds-row ds-head"><span>Detail</span><span>Berat</span><span>Poin</span></div>
        <?php foreach ($items as $it): ?>
          <div class="ds-row">
            <span><?= e($it['jenis_sampah']) ?></span>
            <span><?= e(kg($it['berat_kg'])) ?> kg</span>
            <strong>+<?= e($it['poin_didapat']) ?></strong>
          </div>
        <?php endforeach; ?>
      </section>

    </div>

  <?php endif; ?>

  </main>

  <script src="header/sidebar.js" defer></script>
</body>
</html>