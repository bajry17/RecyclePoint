<?php
/**
 * header/top_nav.php
 * Memakai $conn (mysqli) dari koneksi.php dan $_SESSION['user_id'], sama seperti profile.php.
 * Menampilkan: nama panggilan, foto/inisial (users) dan jumlah notifikasi belum dibaca (notifikasi).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($conn)) {
    require_once __DIR__ . '/../koneksi.php';
}
if (!function_exists('e')) {
    function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
}

$navNama = 'Akun';
$navFoto = '';
$navBelumDibaca = 0;

$navId = (int) ($_SESSION['user_id'] ?? 0);
if ($navId > 0) {
    $navStmt = $conn->prepare(
        'SELECT u.nama_panggilan, u.foto,
                (SELECT COUNT(*) FROM notifikasi n
                  WHERE n.user_id = u.id AND n.dibaca_at IS NULL) AS belum_dibaca
           FROM users u
          WHERE u.id = ?
          LIMIT 1'
    );
    $navStmt->bind_param('i', $navId);
    $navStmt->execute();
    $navRow = $navStmt->get_result()->fetch_assoc();
    $navStmt->close();

    if ($navRow) {
        $navNama        = (string) $navRow['nama_panggilan'];
        $navBelumDibaca = (int) $navRow['belum_dibaca'];
        $f = trim((string) $navRow['foto']);
        // hanya path relatif yang aman (bukan URL luar / path traversal)
        if ($f !== '' && strpos($f, '..') === false && $f[0] !== '/' && !preg_match('~^([a-z]+:)?//~i', $f)) {
            $navFoto = $f;
        }
    }
}

$navInisial = function_exists('mb_substr') ? mb_strtoupper(mb_substr($navNama, 0, 1)) : strtoupper(substr($navNama, 0, 1));
$navBadge   = $navBelumDibaca > 99 ? '99+' : (string) $navBelumDibaca;
?>
<style>
  /* Tambahan khusus navbar (boleh dipindah ke profile.css) */
  .navbar a.brand, .navbar a.icon-btn, .navbar a.user-chip { text-decoration: none; }
  .navbar .icon-btn { position: relative; }
  .navbar .notif-badge {
    position: absolute; top: 3px; right: 1px; min-width: 18px; height: 18px; padding: 0 5px;
    border-radius: 999px; background: #F47B2E; color: #12293A;
    font: 700 11px/18px "Poppins", sans-serif; text-align: center;
  }
  .navbar .user-chip__avatar { overflow: hidden; }
  .navbar .user-chip__avatar img { display: block; width: 100%; height: 100%; object-fit: cover; }
</style>
<header class="navbar">
    <div class="navbar__left">
      <button class="icon-btn" aria-label="Buka menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <a class="brand" href="dashboard.php">
        <img src="images/logo.png" width="60%" alt="RecyclePoint">
      </a>
    </div>
    <div class="navbar__right">
      <a class="icon-btn" href="profile.php" aria-label="Pengaturan">
        <img src="images/setting.png" width="26" height="26" alt="">
      </a>
      <a class="icon-btn" href="notifikasi.php"
         aria-label="Notifikasi<?= $navBelumDibaca > 0 ? ', ' . $navBelumDibaca . ' belum dibaca' : '' ?>">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 0 0 4 0"/></svg>
        <?php if ($navBelumDibaca > 0): ?>
          <span class="notif-badge" aria-hidden="true"><?= e($navBadge) ?></span>
        <?php endif; ?>
      </a>
      <a class="user-chip" href="profile.php" aria-label="Profil <?= e($navNama) ?>">
        <span class="user-chip__avatar" aria-hidden="true">
          <?php if ($navFoto !== ''): ?><img src="<?= e($navFoto) ?>" alt=""><?php else: ?><?= e($navInisial) ?><?php endif; ?>
        </span>
        <span class="user-chip__name"><?= e($navNama) ?></span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
      </a>
    </div>
  </header>