<?php
session_start();

// Asumsi: koneksi.php mendefinisikan $conn (mysqli); login mengisi $_SESSION['user_id'].
require_once 'koneksi.php';

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];

// Samakan zona waktu PHP dengan waktu yang tampil (WIB)
date_default_timezone_set('Asia/Jakarta');

/* ---------- HELPER MYSQLI (prepared statement) ---------- */
// $types: string tipe bind_param ('i' int, 's' string, 'd' desc), $params: array nilai
if (!function_exists('dbAll')) {
    function dbAll(string $sql, string $types = '', array $params = []): array
    {
        global $conn;
        $st = $conn->prepare($sql);
        if ($types !== '') $st->bind_param($types, ...$params);
        $st->execute();
        $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
        return $rows;
    }
}
if (!function_exists('dbRow')) {
    function dbRow(string $sql, string $types = '', array $params = []): ?array
    {
        return dbAll($sql, $types, $params)[0] ?? null;
    }
}

/* ---------- HELPER TAMPILAN ---------- */
if (!function_exists('formatTanggal')) {
    function formatTanggal(string $datetime, bool $denganJam = true): string
    {
        $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $t   = strtotime($datetime);
        $out = date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
        return $denganJam ? $out . ', ' . date('H:i', $t) : $out;
    }
}
// Berat tanpa nol di belakang koma: 2.00 -> "2", 1.50 -> "1,5"
if (!function_exists('formatKg')) {
    function formatKg($kg): string
    {
        return rtrim(rtrim(number_format((float) $kg, 2, ',', '.'), '0'), ',');
    }
}

/* ---------- FUNCTION RIWAYAT SETOR ---------- */
// Jenis sampah diinput manual dan detail bisa langsung dilihat, jadi riwayat cukup dari tabel setoran
// (tanpa jenis_sampah dan tanpa status). Tiap baris dikenali lewat kode transaksi (setoran.kode).

// Daftar riwayat setor milik user, terbaru dulu.
// Kolom: id, kode, waktu_setor, total_berat_kg, total_poin
function getRiwayatSetor(int $userId, int $limit = 100, int $offset = 0): array
{
    return dbAll(
        'SELECT id, kode, waktu_setor, total_berat_kg, total_poin
           FROM setoran
          WHERE user_id = ?
          ORDER BY waktu_setor DESC
          LIMIT ? OFFSET ?',
        'iii', [$userId, $limit, $offset]
    );
}

// Ringkasan: jumlah setoran, total kg & total poin milik user.
function getTotalRiwayatSetor(int $userId): array
{
    return dbRow(
        'SELECT COUNT(*) AS jumlah,
                COALESCE(SUM(total_berat_kg), 0) AS total_kg,
                COALESCE(SUM(total_poin), 0)     AS total_poin
           FROM setoran
          WHERE user_id = ?',
        'i', [$userId]
    ) ?? ['jumlah' => 0, 'total_kg' => 0, 'total_poin' => 0];
}

/* ---------- DATA UNTUK HTML ---------- */
$riwayat = getRiwayatSetor($userId, 100, 0);   // 100 setoran terbaru
$total   = getTotalRiwayatSetor($userId);      // $total['total_kg'], $total['total_poin']
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Riwayat Setor Sampah – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="riwayat_setor.css">
</head>
<body class="bg-cream">

  <?php include 'header/top_nav.php' ?>

  <main class="page">
    <nav class="breadcrumb" aria-label="Breadcrumb">Dashboard/Riwayat Setor</nav>
    <h1 class="page__title">Riwayat Setor Sampah</h1>

    <!-- Ringkasan -->
    <section class="rs-summary" aria-label="Ringkasan setoran">
      <div class="rs-sum">
        <span class="rs-sum__ico" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 19l-3-5 3-5M17 5l3 5-3 5M9 5h6M9 19h6M4 14h5M15 10h5"/></svg></span>
        <div><p>Total Sampah Terkumpul</p><strong><?= formatKg($total['total_kg']) ?> Kg</strong></div>
      </div>
      <span class="rs-summary__line" aria-hidden="true"></span>
      <div class="rs-sum">
        <span class="rs-sum__ico rs-sum__ico--star" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg></span>
        <div><p>Total Poin Didapat</p><strong>+ <?= number_format((int) $total['total_poin'], 0, ',', '.') ?> Poin</strong></div>
      </div>
    </section>

    <!-- Daftar riwayat -->
    <section class="rs-list-card" aria-label="Daftar riwayat setor" style="margin-top:32px">
       <div class="rs-head" aria-hidden="true">
    <span class="rs-head-item">Transaksi Sampah</span>
    <span class="rs-head-meta">
      <span>Berat</span>
      <span>Poin</span>
      <span>Lihat Detail</span>
    </span>
    <span></span>
  </div>
      <ul class="rs-list">
        <?php foreach ($riwayat as $r): ?>
          <?php $ts = strtotime($r['waktu_setor']); ?>
        <li>
          <a class="rs-row" href="detail_setor.php?kode=<?= urlencode($r['kode']) ?>" aria-label="<?= htmlspecialchars('Lihat detail setoran ' . $r['kode'] . ', ' . formatTanggal($r['waktu_setor'], false) . ', ' . formatKg($r['total_berat_kg']) . ' kg, +' . number_format((int) $r['total_poin'], 0, ',', '.') . ' poin') ?>">
            <span class="rs-item"><svg class="rs-cat" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8l9-5 9 5v9l-9 5-9-5zM3 8l9 5 9-5M12 13v9M7.5 5.5l9 5"/></svg><span class="rs-name"><strong>ID - <?= htmlspecialchars($r['kode']) ?></strong><time datetime="<?= date('Y-m-d\TH:i', $ts) ?>"><?= formatTanggal($r['waktu_setor'], false) ?> &nbsp; <?= date('H.i', $ts) ?></time></span></span>
            <span class="rs-meta">
              <span class="rs-cell rs-weight"><svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 9a3.5 3.5 0 1 1 7 0h3.2a1.5 1.5 0 0 1 1.5 1.3l1.3 9.5a1.5 1.5 0 0 1-1.5 1.7H4a1.5 1.5 0 0 1-1.5-1.7l1.3-9.5A1.5 1.5 0 0 1 5.3 9zm5 0a1.5 1.5 0 1 0-3 0zm-1.5 3.5l2.2 3.2-1.4 1-2.2-3.2z"/></svg><?= formatKg($r['total_berat_kg']) ?> kg</span>
              <span class="rs-cell rs-points"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg>+<?= number_format((int) $r['total_poin'], 0, ',', '.') ?> Poin</span>
              <span class="rs-cell rs-status"><span class="rs-badge">Detail</span></span>
            </span>
            <i class="rs-chev" aria-hidden="true"></i>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php if (empty($riwayat)): ?>
      <p class="rs-empty" id="emptyMsg">Belum ada riwayat setoran.</p>
      <?php endif; ?>
    </section>
  </main>

<script src="header/sidebar.js" defer></script>
</body>
</html>