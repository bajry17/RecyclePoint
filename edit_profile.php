<?php
session_start();

// Asumsi: koneksi.php mendefinisikan $conn (mysqli); login mengisi $_SESSION['user_id'].
require_once 'koneksi.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];

function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

// ---------------------------------------------------------------------
// Data wilayah: provinsi => kota => [kecamatan]. Sama dengan halaman Register.
// users.provinsi/kota/kecamatan menyimpan nama (VARCHAR), bukan id.
// Tambahkan wilayah lain di sini (dan di halaman Register) bila perlu.
// ---------------------------------------------------------------------
$wilayah = [
    'DKI Jakarta' => [
        'Jakarta Selatan' => ['Kebayoran Baru', 'Tebet', 'Cilandak'],
        'Jakarta Barat'   => ['Cengkareng', 'Kebon Jeruk', 'Grogol Petamburan'],
        'Jakarta Timur'   => ['Cakung', 'Duren Sawit', 'Kramat Jati'],
        'Jakarta Utara'   => ['Kelapa Gading', 'Tanjung Priok'],
        'Jakarta Pusat'   => ['Menteng', 'Gambir', 'Tanah Abang'],
    ],
];

// CSRF token
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Ambil data user saat ini
$stmt = $conn->prepare(
    'SELECT nama_lengkap, nama_panggilan, email, no_telepon, foto,
            alamat_rumah, provinsi, kota, kecamatan, kode_pos
       FROM users WHERE id = ? LIMIT 1'
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$errors = [];
$form = [
    'nama'       => $user['nama_lengkap'],
    'panggilan'  => $user['nama_panggilan'],
    'hp'         => $user['no_telepon'],
    'email'      => $user['email'],
    'alamat'     => $user['alamat_rumah'] ?? '',
    'provinsi'   => $user['provinsi'] ?? '',
    'kota'       => $user['kota'] ?? '',
    'kecamatan'  => $user['kecamatan'] ?? '',
    'pos'        => $user['kode_pos'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- CSRF ---
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Permintaan tidak valid.');
    }

    // --- Ambil input ---
    foreach (['nama', 'panggilan', 'hp', 'email', 'alamat', 'provinsi', 'kota', 'kecamatan', 'pos'] as $k) {
        $form[$k] = trim($_POST[$k] ?? '');
    }

    // --- Validasi ---
    if ($form['nama'] === '' || mb_strlen($form['nama']) > 120) {
        $errors[] = 'Nama lengkap wajib diisi (maksimal 120 karakter).';
    }
    if ($form['panggilan'] === '' || mb_strlen($form['panggilan']) > 60) {
        $errors[] = 'Nama panggilan wajib diisi (maksimal 60 karakter).';
    }
    if (!preg_match('/^\+?[0-9]{9,15}$/', $form['hp'])) {
        $errors[] = 'Nomor telepon harus 9-15 digit angka.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($form['email']) > 150) {
        $errors[] = 'Format email tidak valid.';
    }
    if (mb_strlen($form['alamat']) > 255) {
        $errors[] = 'Alamat rumah maksimal 255 karakter.';
    }
    if ($form['pos'] !== '' && !preg_match('/^[0-9]{5}$/', $form['pos'])) {
        $errors[] = 'Kode pos harus 5 digit angka.';
    }
    // Wilayah bertingkat: kota harus milik provinsi, kecamatan harus milik kota
    if ($form['provinsi'] !== '' && !isset($wilayah[$form['provinsi']])) {
        $errors[] = 'Provinsi tidak valid.';
    } elseif ($form['kota'] !== '' && !isset($wilayah[$form['provinsi']][$form['kota']])) {
        $errors[] = 'Kota/Kabupaten tidak sesuai dengan provinsi.';
    } elseif ($form['kecamatan'] !== '' && !in_array($form['kecamatan'], $wilayah[$form['provinsi']][$form['kota']] ?? [], true)) {
        $errors[] = 'Kecamatan tidak sesuai dengan kota/kabupaten.';
    }

    // Email harus unik (kecuali milik sendiri)
    if (!$errors) {
        $cek = $conn->prepare('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
        $cek->bind_param('si', $form['email'], $userId);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $errors[] = 'Email sudah digunakan akun lain.';
        }
        $cek->close();
    }

    // --- Upload foto (opsional) ---
    $fotoBaru = null;
    if (!$errors && isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $f = $_FILES['foto'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Gagal mengunggah foto.';
        } elseif ($f['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ukuran foto maksimal 2 MB.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
            $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
            if (!isset($extMap[$mime])) {
                $errors[] = 'Foto harus berformat JPG atau PNG.';
            } else {
                $dir = __DIR__ . '/uploads/foto';
                if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
                    $errors[] = 'Folder upload tidak dapat dibuat.';
                } else {
                    $nama = 'u' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $extMap[$mime];
                    if (move_uploaded_file($f['tmp_name'], $dir . '/' . $nama)) {
                        $fotoBaru = 'uploads/foto/' . $nama;
                    } else {
                        $errors[] = 'Foto tidak dapat disimpan.';
                    }
                }
            }
        }
    }

    // --- Simpan ---
    if (!$errors) {
        $alamat = $form['alamat'] !== '' ? $form['alamat'] : null;
        $prov   = $form['provinsi']  !== '' ? $form['provinsi']  : null;
        $kota   = $form['kota']      !== '' ? $form['kota']      : null;
        $kec    = $form['kecamatan'] !== '' ? $form['kecamatan'] : null;
        $pos    = $form['pos']       !== '' ? $form['pos']             : null;
        $foto   = $fotoBaru ?? $user['foto'];

        try {
            $upd = $conn->prepare(
                'UPDATE users
                    SET nama_lengkap = ?, nama_panggilan = ?, email = ?, no_telepon = ?,
                        alamat_rumah = ?, provinsi = ?, kota = ?, kecamatan = ?,
                        kode_pos = ?, foto = ?
                  WHERE id = ?'
            );
            $upd->bind_param(
                'ssssssssssi',
                $form['nama'], $form['panggilan'], $form['email'], $form['hp'],
                $alamat, $prov, $kota, $kec, $pos, $foto, $userId
            );
            $upd->execute();
            $upd->close();

            // Hapus foto lama jika diganti
            if ($fotoBaru && $user['foto'] && strpos($user['foto'], 'uploads/foto/') === 0) {
                @unlink(__DIR__ . '/' . $user['foto']);
            }

            $_SESSION['flash'] = 'Profil berhasil diperbarui.';
            header('Location: profile.php');
            exit;
        } catch (mysqli_sql_exception $ex) {
            // Batalkan foto baru bila query gagal
            if ($fotoBaru) {
                @unlink(__DIR__ . '/' . $fotoBaru);
            }
            $errors[] = ($ex->getCode() === 1062)
                ? 'Email sudah digunakan akun lain.'
                : 'Terjadi kesalahan saat menyimpan data. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Profil – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
  <style>
    .alert{background:#fdecea;color:#b42318;border:1px solid #f5c2c0;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:14px}
    .alert ul{margin:0;padding-left:18px}
    .photo__img img{width:100%;height:100%;object-fit:cover;border-radius:50%}
  </style>
</head>
<body class="bg-cream">

  <?php include 'header/top_nav.php' ?>

  <main class="page">
    <nav class="breadcrumb" aria-label="Breadcrumb">Dashboard/Profil/Edit Profil</nav>
    <h1 class="page__title">Edit Profil</h1>

    <form class="card edit" action="edit_profile.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

      <div class="edit__photo">
        <div class="photo">
          <div class="photo__img" <?= $user['foto'] ? '' : 'aria-hidden="true"' ?>>
            <?php if ($user['foto']): ?>
              <img src="<?= e($user['foto']) ?>" alt="Foto profil">
            <?php else: ?>
              <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
            <?php endif; ?>
          </div>
          <span class="photo__cam" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linejoin="round"><path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/></svg>
          </span>
        </div>
        <label class="btn btn--white" for="foto">Unggah Foto Baru</label>
        <input id="foto" name="foto" class="sr-only" type="file" accept="image/png,image/jpeg">
      </div>

      <div class="edit__form">
        <h2>Informasi Pribadi</h2>

        <?php if ($errors): ?>
          <div class="alert" role="alert">
            <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
          </div>
        <?php endif; ?>

        <div class="form-grid">
          <div class="field"><label for="nama">Nama Lengkap</label><input id="nama" name="nama" type="text" value="<?= e($form['nama']) ?>" maxlength="120" required autocomplete="name"></div>
          <div class="field"><label for="panggilan">Nama Panggilan</label><input id="panggilan" name="panggilan" type="text" value="<?= e($form['panggilan']) ?>" maxlength="60" required autocomplete="nickname"></div>
          <div class="field"><label for="hp">Nomor Telepon</label><input id="hp" name="hp" type="tel" value="<?= e($form['hp']) ?>" maxlength="20" required autocomplete="tel"></div>
          <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="<?= e($form['email']) ?>" maxlength="150" required autocomplete="email"></div>
          <div class="field field--full"><label for="alamat">Alamat Rumah</label><input id="alamat" name="alamat" type="text" value="<?= e($form['alamat']) ?>" maxlength="255" autocomplete="street-address"></div>

          <?php
            // Opsi awal dirender di server agar tetap benar tanpa JavaScript
            $daftarKota = $wilayah[$form['provinsi']] ?? [];
            $daftarKec  = $daftarKota[$form['kota']] ?? [];
          ?>
          <div class="field"><label for="provinsi">Provinsi</label>
            <select id="provinsi" name="provinsi">
              <option value="">Pilih provinsi</option>
              <?php foreach (array_keys($wilayah) as $nm): ?>
                <option value="<?= e($nm) ?>" <?= $form['provinsi'] === $nm ? 'selected' : '' ?>><?= e($nm) ?></option>
              <?php endforeach; ?>
            </select></div>
          <div class="field"><label for="kota">Kota/Kabupaten</label>
            <select id="kota" name="kota">
              <option value="">Pilih kota/kabupaten</option>
              <?php foreach (array_keys($daftarKota) as $nm): ?>
                <option value="<?= e($nm) ?>" <?= $form['kota'] === $nm ? 'selected' : '' ?>><?= e($nm) ?></option>
              <?php endforeach; ?>
            </select></div>
          <div class="field"><label for="kecamatan">Kecamatan</label>
            <select id="kecamatan" name="kecamatan">
              <option value="">Pilih kecamatan</option>
              <?php foreach ($daftarKec as $nm): ?>
                <option value="<?= e($nm) ?>" <?= $form['kecamatan'] === $nm ? 'selected' : '' ?>><?= e($nm) ?></option>
              <?php endforeach; ?>
            </select></div>
          <div class="field"><label for="pos">Kode pos</label><input id="pos" name="pos" type="text" inputmode="numeric" maxlength="5" value="<?= e($form['pos']) ?>" autocomplete="postal-code"></div>
        </div>
        <div class="actions">
          <a class="btn btn--white" href="profile.php">Batal</a>
          <button class="btn btn--orange" type="submit">Simpan</button>
        </div>
      </div>
    </form>
  </main>
<script>
  const wilayah = <?= json_encode($wilayah, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

  const provinsi  = document.getElementById("provinsi");
  const kota      = document.getElementById("kota");
  const kecamatan = document.getElementById("kecamatan");

  function isiOpsi(select, daftar, placeholder) {
    select.innerHTML = "";
    const kosong = document.createElement("option");
    kosong.value = "";
    kosong.textContent = placeholder;
    select.appendChild(kosong);
    daftar.forEach(nama => {
      const option = document.createElement("option");
      option.value = nama;
      option.textContent = nama;
      select.appendChild(option);
    });
  }

  function isiKota() {
    isiOpsi(kota, Object.keys(wilayah[provinsi.value] || {}), "Pilih kota/kabupaten");
    isiKecamatan();
  }

  function isiKecamatan() {
    const daftar = (wilayah[provinsi.value] || {})[kota.value] || [];
    isiOpsi(kecamatan, daftar, "Pilih kecamatan");
  }

  provinsi.addEventListener("change", isiKota);
  kota.addEventListener("change", isiKecamatan);
</script>
<script src="header/sidebar.js" defer></script>
</body>
</html>