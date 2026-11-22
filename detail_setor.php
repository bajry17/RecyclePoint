<?php
session_start();

require_once 'koneksi.php';

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];

function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
// 1.20 -> "1,2" | 2.00 -> "2"
function kg($v) { return rtrim(rtrim(number_format((float) $v, 2, ',', '.'), '0'), ','); }

// detail_setor.php?kode=RP-1211-00128 ; tanpa kode = setoran terbaru milik user
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
    $stmt = $conn->prepare(
        'SELECT jenis_sampah AS nama, i.berat_kg, i.poin_didapat
           FROM setoran_item i
          WHERE i.setoran_id = ?
       ORDER BY i.id'
    );
    $stmt->bind_param('i', $s['id']);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $w       = new DateTime($s['waktu_setor']);
    $tanggal = $w->format('j') . ' ' . $bulan[(int) $w->format('n')] . ' ' . $w->format('Y');
    $jam     = $w->format('H.i') . ' WIB';
    $jenis   = implode(', ', array_unique(array_column($items, 'jenis')));

    $info = [
        'Jenis'        => 'Setor Sampah ' . $jenis,
        'ID Transaksi' => $s['kode'],
        'Tanggal'      => $tanggal,
        'Waktu'        => $jam,
        'Petugas'      => $s['petugas'] ?? '-',
        'Lokasi'       => $s['bank_sampah'],
    ];
    // [judul, keterangan, jam] - hanya tahap terakhir yang punya jam (waktu_setor)
    $tahap = [
        ['Setoran diajukan',    'Sampah jenis ' . mb_strtolower($jenis), ''],
        ['Diterima petugas',    'Petugas memeriksa dan memilahnya', ''],
        ['Penimbangan selesai', 'Total berat terverifikasi ' . kg($s['total_berat_kg']) . ' kg', ''],
        ['Poin masuk ke akun',  '+' . $s['total_poin'] . ' poin ditambahkan', $jam],
    ];
    $cek = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>';
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
        <div class="ds-actions">
          <button type="button" class="ds-button ds-button--orange" onclick="window.print()">Cetak bukti</button>
        </div>
      <?php endif; ?>
    </div>

  <?php if (!$s): ?>

    <section class="ds-info-card">
      <h2>Setoran tidak ditemukan</h2>
      <p>Data setoran tidak ada atau bukan milik akun Anda. <a href="riwayat_setor.php">Kembali ke Riwayat Setor</a></p>
    </section>

  <?php else: ?>

    <?php if ($s['status'] === 'berhasil'): ?>
    <section class="ds-timeline-card">
      <span class="ds-success">Berhasil</span>
      <div class="ds-timeline">
        <?php foreach ($tahap as $i => [$judul, $ket, $waktu]): ?>
          <div class="ds-step<?= $i === 3 ? ' ds-step--last' : '' ?>">
            <div class="ds-step__circle"><?= $cek ?></div>
            <strong><?= e($judul) ?></strong>
            <p><?= e($ket) ?></p>
            <?php if ($waktu): ?><time><?= e($waktu) ?></time><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <div class="ds-content">

      <section class="ds-info-card">
        <h2>Informasi</h2>
        <?php foreach ($info as $label => $nilai): ?>
          <div class="ds-info-row"><span><?= e($label) ?></span><strong><?= e($nilai) ?></strong></div>
        <?php endforeach; ?>
      </section>

      <div class="ds-right-column">

        <section class="ds-total-card">
          <div class="ds-total-item">
            <p>Total berat</p>
            <strong><?= e(kg($s['total_berat_kg'])) ?> <small>kg</small></strong>
          </div>
          <div class="ds-total-item ds-total-item--orange">
            <p>Poin diperoleh</p>
            <strong>+<?= e($s['total_poin']) ?> <small>poin</small></strong>
          </div>
        </section>

        <section class="ds-detail-card">
          <h2>Rincian Item Sampah</h2>
          <div class="ds-table-row ds-table-head"><span>Detail</span><span>Berat</span><span>Poin</span></div>
          <?php foreach ($items as $it): ?>
            <div class="ds-table-row">
              <span><?= e($it['nama']) ?></span>
              <span><?= e(kg($it['berat_kg'])) ?> kg</span>
              <strong>+<?= e($it['poin_didapat']) ?></strong>
            </div>
          <?php endforeach; ?>
        </section>

      </div>
    </div>

  <?php endif; ?>

  </main>

  <script src="header/sidebar.js" defer></script>
</body>
</html>