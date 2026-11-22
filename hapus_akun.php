<?php
session_start();

// Asumsi: koneksi.php mendefinisikan $conn (mysqli); login mengisi $_SESSION['user_id'].
require_once 'koneksi.php';

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];

if (!function_exists('e')) {
    function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// --- Data user ---
$stmt = $conn->prepare('SELECT nama_panggilan, password_hash, foto FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// --- Transaksi yang masih berjalan: akun belum boleh dihapus ---
$q = $conn->prepare(
    "SELECT (SELECT COUNT(*) FROM penukaran_poin WHERE user_id = ? AND status = 'diproses')
          + (SELECT COUNT(*) FROM setoran        WHERE user_id = ? AND status = 'menunggu') AS n"
);
$q->bind_param('ii', $userId, $userId);
$q->execute();
$pending = (int) $q->get_result()->fetch_assoc()['n'];
$q->close();

// --- Sisa poin (hanya untuk peringatan; abaikan jika view bermasalah) ---
$poin = null;
try {
    $q = $conn->prepare('SELECT poin FROM v_ringkasan_user WHERE user_id = ?');
    $q->bind_param('i', $userId);
    $q->execute();
    $r = $q->get_result()->fetch_assoc();
    $q->close();
    $poin = $r ? (int) $r['poin'] : null;
} catch (Throwable $ex) {
    $poin = null;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Permintaan tidak valid.');
    }

    $password   = $_POST['password'] ?? '';
    $konfirmasi = trim($_POST['konfirmasi'] ?? '');

    if ($pending > 0) {
        $errors[] = 'Masih ada ' . $pending . ' transaksi yang diproses. Selesaikan dulu sebelum menghapus akun.';
    }
    if (!password_verify($password, $user['password_hash'])) {
        $errors[] = 'Kata sandi salah.';
    }
    if ($konfirmasi !== 'HAPUS') {
        $errors[] = 'Ketik HAPUS (huruf besar) untuk mengonfirmasi.';
    }

    if (!$errors) {
        try {
            $conn->begin_transaction();

            // 1. Notifikasi milik user dihapus
            $d = $conn->prepare('DELETE FROM notifikasi WHERE user_id = ?');
            $d->bind_param('i', $userId);
            $d->execute();
            $d->close();

            // 2. Data pribadi dihapus; baris user dipertahankan agar riwayat setoran
            //    dan tukar poin tetap utuh (foreign key tidak putus)
            $emailAnon = 'dihapus_' . $userId . '_' . bin2hex(random_bytes(4)) . '@dihapus.invalid';
            $hashAcak  = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

            $u = $conn->prepare(
                "UPDATE users
                    SET nama_lengkap = 'Akun Dihapus', nama_panggilan = 'Dihapus',
                        email = ?, no_telepon = '-', password_hash = ?,
                        foto = NULL, alamat_rumah = NULL,
                        provinsi = NULL, kota = NULL, kecamatan = NULL, kode_pos = NULL
                  WHERE id = ?"
            );
            $u->bind_param('ssi', $emailAnon, $hashAcak, $userId);
            $u->execute();
            $u->close();

            $conn->commit();
        } catch (mysqli_sql_exception $ex) {
            $conn->rollback();
            error_log('hapus_akun: ' . $ex->getMessage());
            $errors[] = 'Terjadi kesalahan saat menghapus akun. Coba lagi.';
        }

        if (!$errors) {
            // 3. Hapus file foto profil
            if ($user['foto'] && strpos($user['foto'], 'uploads/foto/') === 0) {
                @unlink(__DIR__ . '/' . $user['foto']);
            }

            // 4. Akhiri sesi
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }
            session_destroy();

            header('Location: index.php?akun=dihapus');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hapus Akun – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
  <style>
    .alert{background:#fdecea;color:#b42318;border:1px solid #f5c2c0;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:14px}
    .alert ul{margin:0;padding-left:18px}
    .warn{background:#fff4e5;color:#8a4b00;border:1px solid #ffd8a8;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:14px}
    .warn ul{margin:6px 0 0;padding-left:18px}
  </style>
</head>
<body class="bg-cream">

  <?php include 'header/top_nav.php' ?>

  <main class="page">
    <nav class="breadcrumb" aria-label="Breadcrumb">Dashboard/Profil/Hapus Akun</nav>
    <h1 class="page__title">Hapus Akun</h1>

    <form class="card" action="hapus_akun.php" method="post" style="padding:24px;max-width:640px">
      <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

      <div class="warn" role="note">
        <strong>Tindakan ini tidak bisa dibatalkan.</strong>
        <ul>
          <li>Data pribadi Anda (nama, email, telepon, alamat, foto) dihapus.</li>
          <li>Anda tidak bisa login lagi dengan akun ini.</li>
          <?php if ($poin !== null && $poin > 0): ?>
            <li>Sisa <strong><?= number_format($poin, 0, ',', '.') ?> poin</strong> akan hangus.</li>
          <?php else: ?>
            <li>Sisa poin akan hangus.</li>
          <?php endif; ?>
          <li>Riwayat setoran tetap tersimpan tanpa identitas Anda, untuk pencatatan bank sampah.</li>
        </ul>
      </div>

      <?php if ($pending > 0 && !$errors): ?>
        <div class="alert" role="alert">
          Masih ada <?= $pending ?> transaksi yang diproses. Akun baru bisa dihapus setelah transaksi itu selesai.
        </div>
      <?php endif; ?>

      <?php if ($errors): ?>
        <div class="alert" role="alert">
          <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <div class="field">
        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" required autocomplete="current-password">
      </div>
      <div class="field">
        <label for="konfirmasi">Ketik <strong>HAPUS</strong> untuk konfirmasi</label>
        <input id="konfirmasi" name="konfirmasi" type="text" required autocomplete="off">
      </div>

      <div class="actions">
        <a class="btn btn--white" href="profile.php">Batal</a>
        <button class="btn btn--danger" type="submit" <?= $pending > 0 ? 'disabled' : '' ?>>Hapus Akun Saya</button>
      </div>
    </form>
  </main>
<script src="header/sidebar.js" defer></script>
</body>
</html>