<?php
session_start();
 
// Asumsi: koneksi.php mendefinisikan $conn (mysqli); login mengisi $_SESSION['user_id'].
require_once 'koneksi.php';
 
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];
 
// Samakan zona waktu PHP dengan waktu yang tampil (WIB) supaya "1 jam lalu", "Hari ini", "Kemarin" akurat
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
// Untuk INSERT / UPDATE / DELETE. Return jumlah baris yang berubah.
if (!function_exists('dbExec')) {
    function dbExec(string $sql, string $types = '', array $params = []): int
    {
        global $conn;
        $st = $conn->prepare($sql);
        if ($types !== '') $st->bind_param($types, ...$params);
        $st->execute();
        $n = $st->affected_rows;
        $st->close();
        return $n;
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
 
/* ---------- FUNCTION NOTIFIKASI ---------- */
 
// Sesuai enum kolom notifikasi.kategori
const KATEGORI_NOTIF = ['aktivitas_sampah', 'poin_reward', 'marketplace'];
 
// (internal) tambahkan filter kategori ke SQL
function _filterKategori(string &$sql, string &$types, array &$params, ?string $kategori): void
{
    if ($kategori !== null && in_array($kategori, KATEGORI_NOTIF, true)) {
        $sql     .= ' AND kategori = ?';
        $types   .= 's';
        $params[] = $kategori;
    }
}
 
// Daftar notifikasi. $status: 'belum' | 'sudah' | null
function getNotifikasi(int $userId, ?string $kategori = null, ?string $status = null, int $limit = 20, int $offset = 0): array
{
    $sql    = 'SELECT id, kategori, judul, isi_pesan, dibaca_at, created_at FROM notifikasi WHERE user_id = ?';
    $types  = 'i';
    $params = [$userId];
    _filterKategori($sql, $types, $params, $kategori);
 
    if ($status === 'belum')     $sql .= ' AND dibaca_at IS NULL';
    elseif ($status === 'sudah') $sql .= ' AND dibaca_at IS NOT NULL';
 
    $sql     .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
    $types   .= 'ii';
    $params[] = $limit;
    $params[] = $offset;
 
    return dbAll($sql, $types, $params);
}
 
// Total notifikasi
function hitungNotifikasi(int $userId, ?string $kategori = null): int
{
    $sql    = 'SELECT COUNT(*) AS n FROM notifikasi WHERE user_id = ?';
    $types  = 'i';
    $params = [$userId];
    _filterKategori($sql, $types, $params, $kategori);
    return (int) dbRow($sql, $types, $params)['n'];
}
 
// Jumlah belum dibaca (badge)
function hitungNotifBelumDibaca(int $userId, ?string $kategori = null): int
{
    $sql    = 'SELECT COUNT(*) AS n FROM notifikasi WHERE user_id = ? AND dibaca_at IS NULL';
    $types  = 'i';
    $params = [$userId];
    _filterKategori($sql, $types, $params, $kategori);
    return (int) dbRow($sql, $types, $params)['n'];
}
 
 
// Buat notifikasi baru (dipanggil dari proses lain, mis. setoran berhasil / penukaran poin)
// Contoh: tambahNotifikasi($userId, 'poin_reward', 'Poin Berhasil Ditambahkan', 'Kamu mendapatkan 20 poin ...');
function tambahNotifikasi(int $userId, string $kategori, string $judul, string $isiPesan): int
{
    global $conn;
    if (!in_array($kategori, KATEGORI_NOTIF, true)) {
        throw new InvalidArgumentException('Kategori notifikasi tidak valid: ' . $kategori);
    }
    dbExec(
        'INSERT INTO notifikasi (user_id, kategori, judul, isi_pesan) VALUES (?, ?, ?, ?)',
        'isss', [$userId, $kategori, mb_substr($judul, 0, 150), mb_substr($isiPesan, 0, 255)]
    );
    return (int) $conn->insert_id;
}
 
 
/* ---------- DATA UNTUK HTML ---------- */
$listNotif   = getNotifikasi($userId, null, null, 50, 0);   // 50 terbaru; filter kategori dilakukan di browser (tab)
$belumDibaca = hitungNotifBelumDibaca($userId);             // angka di tab "Semua"
 
// Kategori database -> nilai data-cat yang dipakai tab filter
$mapKategori = ['aktivitas_sampah' => 'aktivitas', 'poin_reward' => 'poin', 'marketplace' => 'marketplace'];
 
// Kelompokkan: Hari ini / Kemarin / Sebelumnya (urutan terbaru tetap terjaga)
$grup = [
    'g-today'     => ['label' => 'Hari ini',   'items' => []],
    'g-yesterday' => ['label' => 'Kemarin',    'items' => []],
    'g-older'     => ['label' => 'Sebelumnya', 'items' => []],
];
$tglHariIni = date('Y-m-d');
$tglKemarin = date('Y-m-d', strtotime('-1 day'));
$idHighlight = 0;   // notifikasi belum dibaca paling baru diberi class notif-item--hl
foreach ($listNotif as $n) {
    $tgl = substr($n['created_at'], 0, 10);
    $kunci = $tgl === $tglHariIni ? 'g-today' : ($tgl === $tglKemarin ? 'g-yesterday' : 'g-older');
    $grup[$kunci]['items'][] = $n;
    if ($idHighlight === 0 && empty($n['dibaca_at'])) $idHighlight = (int) $n['id'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifikasi – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="notifikasi.css">
</head>
<body class="bg-cream">
<?php include 'header/top_nav.php' ?>

  <main class="page">
    <nav class="breadcrumb" aria-label="Breadcrumb">Dashboard/Notifikasi</nav>

    <div class="notif-titlebar">
      <h1 class="page__title">Notifikasi</h1>
      <button class="notif-readall" id="readAll" type="button">Tandai semua dibaca</button>
    </div>

    <section class="notif-card" aria-label="Daftar notifikasi">

      <!-- Filter -->
      <div class="notif-tabs" role="group" aria-label="Filter notifikasi">
        <button class="notif-tab" type="button" data-filter="semua" aria-pressed="true">
          Semua <span class="notif-count" id="countAll" id="countAll"><?= (int) $belumDibaca ?></span>
        </button>
        <button class="notif-tab" type="button" data-filter="aktivitas" aria-pressed="false">Aktivitas sampah</button>
        <button class="notif-tab" type="button" data-filter="poin" aria-pressed="false">Poin &amp; Reward</button>
        <button class="notif-tab" type="button" data-filter="marketplace" aria-pressed="false">Marketplace</button>
      </div>

      <p class="notif-status" id="status" role="status" aria-live="polite"></p>

       <?php foreach ($grup as $gid => $g): ?>
        <?php if (empty($g['items'])) continue; ?>
      <section class="notif-group" aria-labelledby="<?= $gid ?>">
        <h2 id="<?= $gid ?>"><?= $g['label'] ?></h2>
        <ul class="notif-list">
          <?php foreach ($g['items'] as $n): ?>
            <?php
              $belum = empty($n['dibaca_at']);
              $cat   = $mapKategori[$n['kategori']] ?? 'aktivitas';
              $jam   = date('H.i', strtotime($n['created_at'])) . ' WIB';
              $teksWaktu = $gid === 'g-older' ? formatTanggal($n['created_at'], false) . ', ' . $jam : $jam;
            ?>
          <li class="notif-item<?= (int) $n['id'] === $idHighlight ? ' notif-item--hl' : '' ?>" data-cat="<?= $cat ?>" data-id="<?= (int) $n['id'] ?>"<?= $belum ? ' data-unread="1"' : '' ?>>
            <span class="notif-ico" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="10" cy="6" rx="6" ry="2.5"/><path d="M4 6v3.5c0 1.4 2.7 2.5 6 2.5M4 9.5V13c0 1.4 2.7 2.5 6 2.5"/><ellipse cx="15" cy="13" rx="6" ry="2.5"/><path d="M9 13v3.5c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V13M9 16.5V20c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-3.5"/></svg></span>
            <div class="notif-body">
              <h3><?= htmlspecialchars($n['judul']) ?></h3>
              <p><?= htmlspecialchars($n['isi_pesan']) ?></p>
              <time class="notif-time" datetime="<?= date('Y-m-d\TH:i', strtotime($n['created_at'])) ?>"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><?= $teksWaktu ?></time>
            </div>
            <?php if ($belum): ?><span class="notif-new"><?= htmlspecialchars(waktuRelatif($n['created_at'])) ?></span><?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>

      <p class="notif-empty" id="emptyMsg"<?= $listNotif ? ' hidden' : '' ?>><?= $listNotif ? 'Tidak ada notifikasi di kategori ini.' : 'Belum ada notifikasi.' ?></p>
    </section>
  </main>

<script>
  (function () {
    var tabs = document.querySelectorAll('.notif-tab');
    var items = document.querySelectorAll('.notif-list > li');
    var empty = document.getElementById('emptyMsg');
    var status = document.getElementById('status');
    var current = 'semua';

    function apply(announce) {
      var shown = 0;
      items.forEach(function (li) {
        var ok = (current === 'semua' || li.dataset.cat === current);
        li.hidden = !ok;
        if (ok) shown++;
      });
      empty.hidden = shown !== 0;
      if (announce) status.textContent = shown + ' setoran ditampilkan.';
    }

    tabs.forEach(function (t) {
      t.addEventListener('click', function () {
        current = t.dataset.filter;
        tabs.forEach(function (x) {
          x.setAttribute('aria-pressed', x === t ? 'true' : 'false');
        });
        apply(true);
      });
    });
  })();
</script>

<script src="header/sidebar.js" defer></script>
</body>
</html>