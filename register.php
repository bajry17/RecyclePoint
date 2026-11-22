
<?php
session_start();

require_once 'koneksi.php';

function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$errors = [];
$old = [
    'nama'      => '',
    'panggilan' => '',
    'hp'        => '',
    'email'     => '',
    'role'      => 'masyarakat',
    'alamat'    => '',
    'provinsi'  => 'DKI Jakarta',
    'kota'      => 'Jakarta Pusat',
    'kecamatan' => 'Menteng',
    'kodepos'   => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Permintaan tidak valid.');
    }

    foreach ($old as $key => $default) {
        $old[$key] = trim($_POST[$key] ?? $default);
    }

    $password   = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if ($old['nama'] === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    }
    if ($old['panggilan'] === '') {
        $errors[] = 'Nama panggilan wajib diisi.';
    }
    if ($old['hp'] === '' || !preg_match('/^[0-9+\-\s]{9,16}$/', $old['hp'])) {
        $errors[] = 'Nomor HP tidak valid.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }
    if (!in_array($old['role'], ['masyarakat', 'pengrajin'], true)) {
        $errors[] = 'Role yang dipilih tidak valid.';
    }
    if ($old['alamat'] === '') {
        $errors[] = 'Alamat wajib diisi.';
    }
    if ($old['provinsi'] === '' || $old['kota'] === '' || $old['kecamatan'] === '') {
        $errors[] = 'Data wilayah (provinsi, kota, kecamatan) wajib dipilih.';
    }
    if (!preg_match('/^[0-9]{5}$/', $old['kodepos'])) {
        $errors[] = 'Kode pos harus terdiri dari 5 digit angka.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Kata sandi minimal 8 karakter.';
    }
    if ($password !== $konfirmasi) {
        $errors[] = 'Konfirmasi kata sandi tidak sesuai.';
    }

    if (empty($errors)) {
        $cekStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $cekStmt->bind_param('s', $old['email']);
        $cekStmt->execute();
        $sudahAda = $cekStmt->get_result()->fetch_assoc();
        $cekStmt->close();

        if ($sudahAda) {
            $errors[] = 'Email sudah terdaftar. Silakan gunakan email lain atau login.';
        }
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $insertStmt = $conn->prepare(
            'INSERT INTO users 
            (nama_lengkap, nama_panggilan, no_telepon, email, role, alamat_rumah, provinsi, kota, kecamatan, kode_pos, password_hash) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $insertStmt->bind_param(
            'sssssssssss',
            $old['nama'],
            $old['panggilan'],
            $old['hp'],
            $old['email'],
            $old['role'],
            $old['alamat'],
            $old['provinsi'],
            $old['kota'],
            $old['kecamatan'],
            $old['kodepos'],
            $passwordHash
        );

        $insertStmt->execute();
        $insertStmt->close();

        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        header('Location: index.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - RecyclePoint</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="register.css">
  <style>
    .register-error {
      background: #fdecea;
      color: #b42318;
      border: 1px solid #f5c2c0;
      border-radius: 10px;
      padding: 12px 16px;
      margin-bottom: 18px;
      font-size: 14px;
    }
    .register-error ul {
      margin: 0;
      padding-left: 18px;
    }
  </style>
</head>
<body>

  <main class="register-page">

    <div class="register-circle register-circle--top"></div>
    <div class="register-circle register-circle--bottom"></div>

    <section class="register-card">

      <h1>Register</h1>

      <?php if (!empty($errors)): ?>
        <div class="register-error" role="alert">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="register.php" method="post">
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

        <div class="register-grid">

          <div class="register-field">
            <label for="nama">Nama lengkap</label>
            <input
              type="text"
              id="nama"
              name="nama"
              placeholder="Masukkan nama lengkap"
              value="<?= e($old['nama']) ?>"
              required
            >
          </div>

          <div class="register-field">
            <label for="panggilan">Nama panggilan</label>
            <input
              type="text"
              id="panggilan"
              name="panggilan"
              placeholder="Masukkan nama panggilan"
              value="<?= e($old['panggilan']) ?>"
              required
            >
          </div>

          <div class="register-field">
            <label for="hp">No. HP</label>
            <input
              type="tel"
              id="hp"
              name="hp"
              placeholder="Masukkan no. hp"
              value="<?= e($old['hp']) ?>"
              required
            >
          </div>

          <div class="register-field">
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Masukkan email"
              value="<?= e($old['email']) ?>"
              required
            >
          </div>

          <div class="register-field">
            <label for="role">Role</label>
            <select id="role" name="role" required>
              <option value="masyarakat" <?= $old['role'] === 'masyarakat' ? 'selected' : '' ?>>Masyarakat</option>
              <option value="pengrajin" <?= $old['role'] === 'pengrajin' ? 'selected' : '' ?>>Pengrajin</option>
            </select>
          </div>

          <div class="register-field">
            <label for="alamat">Alamat</label>
            <input
              type="text"
              id="alamat"
              name="alamat"
              placeholder="Masukkan alamat"
              value="<?= e($old['alamat']) ?>"
              required
            >
          </div>

          <div class="register-field">
            <label for="provinsi">Provinsi</label>
            <select id="provinsi" name="provinsi" required>
              <option value="DKI Jakarta" <?= $old['provinsi'] === 'DKI Jakarta' ? 'selected' : '' ?>>DKI Jakarta</option>
            </select>
          </div>

          <div class="register-field">
            <label for="kota">Kota/kabupaten</label>
            <select id="kota" name="kota" required></select>
          </div>

          <div class="register-field">
            <label for="kecamatan">Kecamatan</label>
            <select id="kecamatan" name="kecamatan" required></select>
          </div>

          <div class="register-field">
            <label for="kodepos">Kode pos</label>
            <input
              type="text"
              id="kodepos"
              name="kodepos"
              placeholder="Masukkan kode pos"
              inputmode="numeric"
              maxlength="5"
              value="<?= e($old['kodepos']) ?>"
              required
            >
          </div>

          <div class="register-field">
            <label for="password">Kata sandi</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Masukkan kata sandi"
              minlength="8"
              required
            >
          </div>

          <div class="register-field">
            <label for="konfirmasi">Konfirmasi kata sandi</label>
            <input
              type="password"
              id="konfirmasi"
              name="konfirmasi"
              placeholder="Masukkan ulang kata sandi"
              minlength="8"
              required
            >
          </div>

        </div>

        <button type="submit" class="register-button">
          Register
        </button>

      </form>

      <p class="register-login">
        Sudah punya akun?
        <a href="index.php">Login</a>
      </p>

    </section>
  </main>

  <script>
    const wilayah = {
      "DKI Jakarta": {
        "Jakarta Selatan": ["Kebayoran Baru", "Tebet", "Cilandak"],
        "Jakarta Barat": ["Cengkareng", "Kebon Jeruk", "Grogol Petamburan"],
        "Jakarta Timur": ["Cakung", "Duren Sawit", "Kramat Jati"],
        "Jakarta Utara": ["Kelapa Gading", "Tanjung Priok"],
        "Jakarta Pusat": ["Menteng", "Gambir", "Tanah Abang"]
      }
    };

    const provinsi = document.getElementById("provinsi");
    const kota = document.getElementById("kota");
    const kecamatan = document.getElementById("kecamatan");

    function isiKota(pilihanKota = "", pilihanKecamatan = "") {
      const daftarKota = wilayah[provinsi.value] || {};
      kota.innerHTML = "";

      Object.keys(daftarKota).forEach(namaKota => {
        const option = document.createElement("option");
        option.value = namaKota;
        option.textContent = namaKota;
        kota.appendChild(option);
      });

      if (pilihanKota && daftarKota[pilihanKota]) {
        kota.value = pilihanKota;
      }

      isiKecamatan(pilihanKecamatan);
    }

    function isiKecamatan(pilihanKecamatan = "") {
      const daftarKecamatan = wilayah[provinsi.value]?.[kota.value] || [];
      kecamatan.innerHTML = "";

      daftarKecamatan.forEach(namaKecamatan => {
        const option = document.createElement("option");
        option.value = namaKecamatan;
        option.textContent = namaKecamatan;
        kecamatan.appendChild(option);
      });

      if (pilihanKecamatan && daftarKecamatan.includes(pilihanKecamatan)) {
        kecamatan.value = pilihanKecamatan;
      }
    }

    provinsi.addEventListener("change", () => isiKota());
    kota.addEventListener("change", () => isiKecamatan());

    isiKota(<?= json_encode($old['kota']) ?>, <?= json_encode($old['kecamatan']) ?>);

    document.querySelector("form").addEventListener("submit", function(event) {
      const password = document.getElementById("password").value;
      const konfirmasi = document.getElementById("konfirmasi").value;

      if (password !== konfirmasi) {
        event.preventDefault();
        alert("Konfirmasi kata sandi tidak sesuai!");
      }
    });
  </script>

</body>
</html>
