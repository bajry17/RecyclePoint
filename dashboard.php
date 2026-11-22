<?php
session_start();

// Asumsi: koneksi.php mendefinisikan $conn (mysqli); login mengisi $_SESSION['user_id'].
require_once 'koneksi.php';

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];

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
if (!function_exists('waktuRelatif')) {
    function waktuRelatif(string $datetime): string
    {
        $s = time() - strtotime($datetime);
        if ($s < 60)     return 'Baru saja';
        if ($s < 3600)   return floor($s / 60) . ' menit lalu';
        if ($s < 86400)  return floor($s / 3600) . ' jam lalu';
        if ($s < 172800) return 'Kemarin';
        if ($s < 604800) return floor($s / 86400) . ' hari lalu';
        return formatTanggal($datetime, false);
    }
}

/* ---------- FUNCTION DASHBOARD ---------- */

// Ringkasan: nama_panggilan, poin, total_berat_kg, jumlah_setoran, notif_belum_dibaca
function getRingkasanUser(int $userId): array
{
    return dbRow('SELECT * FROM v_ringkasan_user WHERE user_id = ?', 'i', [$userId]) ?? [
        'user_id' => $userId, 'nama_panggilan' => '', 'poin' => 0,
        'total_berat_kg' => 0, 'jumlah_setoran' => 0, 'notif_belum_dibaca' => 0,
    ];
}

// Setoran terbaru: id, kode, waktu_setor, total_berat_kg, total_poin, bank_sampah
function getSetoranTerbaru(int $userId, int $limit = 5): array
{
    return dbAll(
        'SELECT id, kode, waktu_setor, total_berat_kg, total_poin, bank_sampah
           FROM v_riwayat_setor
          WHERE user_id = ?
          ORDER BY waktu_setor DESC
          LIMIT ?',
        'ii', [$userId, $limit]
    );
}

// Notifikasi terbaru: id, kategori, judul, isi_pesan, dibaca_at, created_at
function getNotifTerbaru(int $userId, int $limit = 3): array
{
    return dbAll(
        'SELECT id, kategori, judul, isi_pesan, dibaca_at, created_at
           FROM notifikasi
          WHERE user_id = ?
          ORDER BY created_at DESC
          LIMIT ?',
        'ii', [$userId, $limit]
    );
}

// Total kg & poin per jenis sampah (untuk chart). Hanya setoran 'berhasil'.
function getStatistikPerJenis(int $userId): array
{
    return dbAll(
    "SELECT i.jenis_sampah AS jenis,
            SUM(i.berat_kg) AS total_kg,
            SUM(i.poin_didapat) AS total_poin
       FROM setoran s
       JOIN setoran_item i ON i.setoran_id = s.id
      WHERE s.user_id = ?
      GROUP BY i.jenis_sampah
      ORDER BY total_kg DESC",
    'i', [$userId]
);
}

// Total kg & poin per bulan (untuk chart tren), default 6 bulan terakhir.
function getStatistikBulanan(int $userId, int $jumlahBulan = 6): array
{
    return dbAll(
        "SELECT DATE_FORMAT(waktu_setor, '%Y-%m') AS bulan,
                SUM(total_berat_kg) AS total_kg,
                SUM(total_poin)     AS total_poin,
                COUNT(*)            AS jumlah_setoran
           FROM setoran
          WHERE user_id = ?
            AND waktu_setor >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL ? MONTH)
          GROUP BY bulan
          ORDER BY bulan ASC",
        'ii', [$userId, $jumlahBulan - 1]
    );
}

// Alamat user: alamat_rumah, kode_pos
function getProfilUser(int $userId): array
{
    return dbRow('SELECT alamat_rumah, provinsi, kecamatan, kota, kode_pos FROM users WHERE id = ?', 'i', [$userId])
        ?? ['alamat_rumah' => null,'provinsi' => null,'kota' => null,'kecamatan' => null, 'kode_pos' => null];
}

// Jumlah hadiah yang tersedia di marketplace poin (aktif & stok ada)
function getJumlahHadiah(): int
{
    return (int) (dbRow('SELECT COUNT(*) AS n FROM produk_poin WHERE aktif = 1 AND stok > 0')['n'] ?? 0);
}

// Bank sampah paling relevan untuk user: prioritas kode pos sama, lalu kota sama, lalu yang pertama aktif.
function getBankSampahTerdekat(int $userId): ?array
{
    return dbRow(
        "SELECT b.id, b.nama, b.alamat, b.jam_operasional
           FROM bank_sampah b
           JOIN users u ON u.id = ?
          ORDER BY COALESCE(b.kode_pos = u.kode_pos, 0) DESC,
                   COALESCE(b.kota_id = u.kota, 0) DESC,
                   b.id ASC
          LIMIT 1",
        'i', [$userId]
    );
}



/* ---------- DATA UNTUK HTML ---------- */
$ringkasan   = getRingkasanUser($userId);       // $ringkasan['poin'], ['total_berat_kg'], ['jumlah_setoran'], ['notif_belum_dibaca']
$setoranBaru = getSetoranTerbaru($userId, 5);   // loop: foreach ($setoranBaru as $s)
$notifBaru   = getNotifTerbaru($userId, 3);     // loop: foreach ($notifBaru as $n)
$perJenis    = getStatistikPerJenis($userId);   // chart pie/bar
$perBulan    = getStatistikBulanan($userId, 6); // chart garis/bar
$profil       = getProfilUser($userId);          // alamat untuk sapaan
$jumlahHadiah = getJumlahHadiah();               // kartu "Hadiah Tersedia"
$bankTerdekat = getBankSampahTerdekat($userId);  // panel lokasi bank sampah
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body class="bg-cream">

  <?php include 'header/top_nav.php'?>

  <section class="dash-hero" aria-labelledby="hero-title">
    <div class="dash-hero__inner">
      <div class="dash-hero__text">
          <p class="dash-hero__crumb">Dashboard</p>
          <h1 id="hero-title">Small Action,<br><span>Big</span> Impact</h1>
          <p class="dash-hero__sub">Selamat datang di Recycle Point!</p>
        </div>

      <div class="dash-hero__right">
          <p class="dash-hero__tag">Setor sampah pilahanmu, disini!</p>
          <ul class="dash-chips" aria-label="Jenis sampah">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 2.5h4v2.5l1.5 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-4a1.5 1.5 0 0 1-1.5-1.5v-12L10 5zM8.5 12h7M8.5 16h7"/></svg>Plastik</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 5h6.5a3 3 0 0 1 3 3v12a2.5 2.5 0 0 0-2.5-2.5H2.5zM21.5 5H15a3 3 0 0 0-3 3v12a2.5 2.5 0 0 1 2.5-2.5h7zM5 9h4M15 9h4M5 12h4M15 12h4"/></svg>Kertas</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 2.5h3v4l2 3.5v10.5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10l2-3.5zM15 12h4v8.5a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg>Kaca</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="7" rx="8" ry="3"/><path d="M4 7v9c0 1.7 3.6 3 8 3s8-1.3 8-3V7M4 11.5c0 1.7 3.6 3 8 3s8-1.3 8-3"/></svg>Logam</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8l9-5 9 5v9l-9 5-9-5zM3 8l9 5 9-5M12 13v9M7.5 5.5l9 5"/></svg>Kardus</li>
          </ul>
        </div>
    </div>
  </section>

  <main class="page dash">
    <!-- Sapaan -->
    <section class="dash-greet">
        <h2>Halo, <?= htmlspecialchars($ringkasan['nama_panggilan']) ?>!</h2>
        <p>
          <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M12 2a7.5 7.5 0 0 0-7.5 7.5C4.5 15 12 22 12 22s7.5-7 7.5-12.5A7.5 7.5 0 0 0 12 2zm0 10.5a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
          </svg>
          <?php
          $bagianAlamat = array_filter([
              $profil['alamat_rumah'] ?? '',
              !empty($profil['kecamatan']) ? 'Kec. ' . $profil['kecamatan'] : '',
              $profil['kota'] ?? '',
              $profil['provinsi'] ?? '',
              !empty($profil['kode_pos']) ? $profil['kode_pos'] : '',
          ]);

          echo htmlspecialchars(implode(', ', $bagianAlamat), ENT_QUOTES, 'UTF-8');
          ?>
        </p>
      </section>

    <!-- Statistik -->
    <section class="dash-stats" aria-label="Ringkasan">
        <a class="dash-stat" href="riwayat_transaksi.php">
          <svg class="dash-stat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6.5l1.7 3.5 3.8.5-2.8 2.6.7 3.8-3.4-1.9-3.4 1.9.7-3.8L6.5 10.5l3.8-.5z"/></svg>
          <span class="dash-stat__txt"><strong><?= number_format((int) $ringkasan['poin'], 0, ',', '.') ?></strong><span>Poin Anda</span></span>
          <i class="dash-chev" aria-hidden="true"></i></a>
        <div class="dash-stat">
          <svg class="dash-stat__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.5 9a3.5 3.5 0 1 1 7 0h3.2a1.5 1.5 0 0 1 1.5 1.3l1.3 9.5a1.5 1.5 0 0 1-1.5 1.7H4a1.5 1.5 0 0 1-1.5-1.7l1.3-9.5A1.5 1.5 0 0 1 5.3 9zm5 0a1.5 1.5 0 1 0-3 0zm-1.5 3.5l2.2 3.2-1.4 1-2.2-3.2z"/></svg>
          <span class="dash-stat__txt"><strong><?= number_format((float) $ringkasan['total_berat_kg'], 1, ',', '.') ?> Kg</strong><span>Sampah Terkumpul</span></span></div>
        <a class="dash-stat" href="marketpoin.php">
          <svg class="dash-stat__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 8h18v4H3zM4 13h7v8H4zM13 13h7v8h-7zM11 8h2v13h-2zM12 8C8.5 8 7 5 9 4s3 1.5 3 4zm0 0c3.5 0 5-3 3-4s-3 1.5-3 4z"/></svg>
          <span class="dash-stat__txt"><strong><?= (int) $jumlahHadiah ?></strong><span>Hadiah Tersedia</span></span>
          <i class="dash-chev" aria-hidden="true"></i></a>
       
      </section>

    <!-- Lokasi & transaksi -->
    <div class="dash-panels">
        <section class="dash-panel" aria-labelledby="loc-title">
          <div class="dash-panel__head">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12 2a7.5 7.5 0 0 0-7.5 7.5C4.5 15 12 22 12 22s7.5-7 7.5-12.5A7.5 7.5 0 0 0 12 2zm0 10.5a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
            <h3 id="loc-title">Lokasi Bank Sampah Terdekat</h3>
            <a href="daftar_lokasi.php">Selanjutnya <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor" aria-hidden="true"><path d="M1 1l5 4-5 4zM8 1h1.5v8H8z"/></svg></a>
          </div>
          <div class="dash-map" role="img" aria-label="Peta: <?= $bankTerdekat ? htmlspecialchars($bankTerdekat['nama'] . ', ' . $bankTerdekat['alamat']) : 'Belum ada bank sampah' ?>">
            <svg class="dash-map__bg" viewBox="0 0 500 160" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
              <rect width="500" height="160" fill="#F3E6DF"/>
              <path d="M0 20L120 0h40L0 60zM330 160l120-70 50 10v60z" fill="#DDEFD2"/>
              <path d="M-10 40C80 70 160 120 260 140s180 30 260 20v-30C440 140 340 120 250 100 170 80 90 40 20 10z" fill="#A9DDE8"/>
              <g stroke="#fff" stroke-width="7" fill="none" stroke-linecap="round"><path d="M0 120L500 20"/><path d="M110 0l90 160"/><path d="M300 0l60 160"/><path d="M0 70l500 40"/></g>
              <g stroke="#fff" stroke-width="3" fill="none"><path d="M40 160l40-160M420 0l30 160M200 0l-30 160"/></g>
            </svg>
            <div class="dash-map__card">
              <span class="dash-map__pin" aria-hidden="true">
                <svg width="52" height="62" viewBox="0 0 52 62"><path d="M26 61S3 40 3 24a23 23 0 0 1 46 0C49 40 26 61 26 61z" fill="#1F9D5C"/><circle cx="26" cy="24" r="14" fill="#fff"/><g transform="translate(26 24)" fill="none" stroke="#1F9D5C" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M-6 6l-3-5 3-5M6-6l3 5-3 5M-3-8h5M-2 8h5"/></g></svg>
              </span>
              <?php if ($bankTerdekat): ?>
              <span class="dash-map__txt"><strong><?= htmlspecialchars($bankTerdekat['nama']) ?></strong><span><?= htmlspecialchars($bankTerdekat['alamat']) ?></span></span>
              <?php else: ?>
              <span class="dash-map__txt"><strong>Belum ada bank sampah</strong><span>Lokasi belum tersedia</span></span>
              <?php endif; ?>
              <i class="dash-chev dash-chev--dark" aria-hidden="true"></i>
            </div>
          </div>
        </section>

        <section class="dash-panel" aria-labelledby="trx-title">
          <div class="dash-panel__head">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2h12v8M6 2v19l3-2 3 2 1.5-1M9 7h6M9 11h3"/><circle cx="17" cy="16" r="3.2"/><path d="M19.5 18.5L22 21"/></svg>
            <h3 id="trx-title">Transaksi Terbaru</h3>
            <a href="riwayat_setor.php">Selanjutnya <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor" aria-hidden="true"><path d="M1 1l5 4-5 4zM8 1h1.5v8H8z"/></svg></a>
          </div>
          <ul class="dash-trx">
             <?php if (empty($setoranBaru)): ?>
            <li>
              <div><strong>Belum ada transaksi</strong><span>Setor sampah pertamamu sekarang</span></div>
            </li>
             <?php else: ?>
              <?php foreach (array_slice($setoranBaru, 0, 2) as $s): ?>
            <li>
              <div><span><?= formatTanggal($s['waktu_setor'], false) ?> &nbsp; <?= date('H.i', strtotime($s['waktu_setor'])) ?></span></div>
            </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </section>
      </div>
  </main>

<script src="header/sidebar.js" defer></script>
</body>
</html>