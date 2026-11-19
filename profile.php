<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profil Saya – RecyclePoint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="profile.css">
</head>
<body class="bg-cream">

  <?php include 'header/top_nav.php' ?>

  <main class="page">
    <nav class="breadcrumb" aria-label="Breadcrumb">Dashboard/Profil</nav>
    <h1 class="page__title">Profil Saya</h1>

    <section class="card hero">
      <div class="hero__avatar" aria-hidden="true">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
      </div>
      <div class="hero__info">
        <h2 class="hero__name">Dudung <span class="badge">Masyarakat</span></h2>
        <p class="hero__since">Bergabung Sejak Oktober 2026</p>
      </div>
      <a href="index.php">
        <button class="btn btn--danger" type="button">
          <svg style="margin-right:10px" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M9 21H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h4M16 17l5-5-5-5M21 12H9"/>
          </svg>
          <span>Logout</span>
        </button>
      </a>
    </section>

    <div class="layout">
      <section class="card info" aria-labelledby="info-title">
        <div class="card__head">
          <h2 id="info-title">Informasi Pribadi</h2>
          <a class="btn btn--orange btn--sm" href="edit_profile.php">Edit Profil</a>
        </div>
        <dl class="fields">
          <div><dt>Nama Lengkap</dt><dd>Dudung Somat</dd></div>
          <div><dt>Nama Panggilan</dt><dd>Dudung</dd></div>
          <div><dt>Nomor Telepon</dt><dd>081287939220</dd></div>
          <div><dt>Email</dt><dd>dudung123@gmail.com</dd></div>
          <div class="fields__full"><dt>Alamat Rumah</dt><dd>Jl. Melati No. 24, Coblong, Kota Bandung, Jawa Barat 40132</dd></div>
        </dl>
      </section>

      <aside class="card settings" aria-labelledby="set-title">
        <h2 id="set-title">Pengaturan Akun</h2>
        <ul class="menu">
          <li><a href="#">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/></svg>
            <span>Keamanan Akun</span><i class="chev" aria-hidden="true"></i></a></li>
          <li><a href="#">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 0 0 4 0"/></svg>
            <span>Notifikasi</span><i class="chev" aria-hidden="true"></i></a></li>
          <li><a href="#">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.7.4-1 .9-1 1.7M12 17h.01"/></svg>
            <span>Pusat Bantuan</span><i class="chev" aria-hidden="true"></i></a></li>
          <li><a href="#" class="danger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
            <span>Hapus Akun</span><i class="chev" aria-hidden="true"></i></a></li>
        </ul>
      </aside>
    </div>
  </main>
<script src="header/sidebar.js" defer></script>
</body>
</html>