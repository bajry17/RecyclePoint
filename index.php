
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

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Permintaan tidak valid.');
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email dan kata sandi wajib diisi.';
    } else {
        $stmt = $conn->prepare(
            'SELECT id, nama_panggilan, password_hash, role FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = (int) $user['id'];
            $_SESSION['nama']      = $user['nama_panggilan'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['csrf']      = bin2hex(random_bytes(32));

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Email atau kata sandi salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - RecyclePoint</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="login.css">
  <style>
    .login-error{background:#fdecea;color:#b42318;border:1px solid #f5c2c0;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:14px}
  </style>
</head>
<body>

  <main class="login-page">

    <div class="circle circle--top"></div>
    <div class="circle circle--bottom"></div>
    <div class="circle circle--orange"></div>

    <a href="index.php" class="login-brand">
      <img src="images/logo.png" alt="Logo RecyclePoint">
    </a>

    <section class="login-content">

      <div class="login-intro">
        <h1>Setor sampah,<br>panen poin.</h1>
        <p>
          Masuk kembali dan jadikan setiap setoranmu
          lebih bernilai.
        </p>
      </div>

      <div class="login-card">
        <h2>Login</h2>

        <?php if ($error): ?>
          <div class="login-error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="index.php" method="post">
          <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

          <div class="login-field">
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Masukkan email"
              value="<?= e($email) ?>"
              required
            >
          </div>

          <div class="login-field">
            <label for="password">Kata Sandi</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Masukkan kata sandi"
              required
            >
          </div>

          <button type="submit" class="login-button">
            Login
          </button>

        </form>

        <p class="login-register">
          Belum punya akun?
          <a href="register.php">Register</a>
        </p>
      </div>

    </section>
  </main>

</body>
</html>
