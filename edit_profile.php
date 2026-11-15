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
</head>
<body class="bg-cream">

  <?php include 'header/top_nav.php' ?>

  <main class="page">
    <nav class="breadcrumb" aria-label="Breadcrumb">Dashboard/Profil/Edit Profil</nav>
    <h1 class="page__title">Edit Profil</h1>

    <form class="card edit" action="profile.php" onsubmit="return false">
      <div class="edit__photo">
        <div class="photo">
          <div class="photo__img" aria-hidden="true">
            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
          </div>
          <span class="photo__cam" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linejoin="round"><path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/></svg>
          </span>
        </div>
        <label class="btn btn--white" for="foto">Unggah Foto Baru</label>
        <input id="foto" class="sr-only" type="file" accept="image/png,image/jpeg">
      </div>

      <div class="edit__form">
        <h2>Informasi Pribadi</h2>
        <div class="form-grid">
          <div class="field"><label for="nama">Nama Lengkap</label><input id="nama" name="nama" type="text" value="Dudung Somat" autocomplete="name"></div>
          <div class="field"><label for="panggilan">Nama Panggilan</label><input id="panggilan" name="panggilan" type="text" value="Dudung" autocomplete="nickname"></div>
          <div class="field"><label for="hp">Nomor Telepon</label><input id="hp" name="hp" type="tel" value="081287939220" autocomplete="tel"></div>
          <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="dudung123@gmail.com" autocomplete="email"></div>
          <div class="field field--full"><label for="alamat">Alamat Rumah</label><input id="alamat" name="alamat" type="text" value="Jl. Melati No. 24" autocomplete="street-address"></div>
          <div class="field"><label for="provinsi">Provinsi</label>
            <select id="provinsi" name="provinsi"><option>Jawa Barat</option><option>DKI Jakarta</option><option>Jawa Tengah</option><option>Jawa Timur</option><option>Banten</option></select></div>
          <div class="field"><label for="kota">Kota/Kabupaten</label>
            <select id="kota" name="kota"><option>Bandung</option><option>Bekasi</option><option>Bogor</option><option>Depok</option><option>Cimahi</option></select></div>
          <div class="field"><label for="kecamatan">Kecamatan</label>
            <select id="kecamatan" name="kecamatan"><option>Coblong</option><option>Cicendo</option><option>Lengkong</option><option>Sukajadi</option><option>Cibeunying Kidul</option></select></div>
          <div class="field"><label for="pos">Kode pos</label><input id="pos" name="pos" type="text" inputmode="numeric" value="40132" autocomplete="postal-code"></div>
        </div>
        <div class="actions">
          <a class="btn btn--white" href="profile.php">Batal</a>
          <button class="btn btn--orange" type="submit">Simpan</button>
        </div>
      </div>
    </form>
  </main>

</body>
</html>