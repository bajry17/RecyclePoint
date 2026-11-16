
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
</head>
<body>

  <main class="register-page">

    <div class="register-circle register-circle--top"></div>
    <div class="register-circle register-circle--bottom"></div>

    <section class="register-card">

      <h1>Register</h1>

      <form action="index.html" method="get">

        <div class="register-grid">

          <div class="register-field">
            <label for="nama">Nama lengkap</label>
            <input
              type="text"
              id="nama"
              name="nama"
              placeholder="Dudung Somat"
              required
            >
          </div>

          <div class="register-field">
            <label for="panggilan">Nama panggilan</label>
            <input
              type="text"
              id="panggilan"
              name="panggilan"
              placeholder="Dudung"
              required
            >
          </div>

          <div class="register-field">
            <label for="hp">No. HP</label>
            <input
              type="tel"
              id="hp"
              name="hp"
              placeholder="081287939220"
              required
            >
          </div>

          <div class="register-field">
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="dudung123@gmail.com"
              required
            >
          </div>

          <div class="register-field register-field--full">
            <label for="alamat">Alamat</label>
            <input
              type="text"
              id="alamat"
              name="alamat"
              placeholder="Jl. Melati No. 24"
              required
            >
          </div>

          <div class="register-field">
            <label for="provinsi">Provinsi</label>
            <select id="provinsi" name="provinsi" required>
              <option value="">Pilih Provinsi</option>
              <option value="Jawa Barat" selected>Jawa Barat</option>
              <option value="DKI Jakarta">DKI Jakarta</option>
              <option value="Jawa Tengah">Jawa Tengah</option>
              <option value="Jawa Timur">Jawa Timur</option>
              <option value="Banten">Banten</option>
            </select>
          </div>

          <div class="register-field">
            <label for="kota">Kota/kabupaten</label>
            <select id="kota" name="kota" required>
              <option value="Bandung" selected>Bandung</option>
            </select>
          </div>

          <div class="register-field">
            <label for="kecamatan">Kecamatan</label>
            <select id="kecamatan" name="kecamatan" required>
              <option value="Coblong" selected>Coblong</option>
            </select>
          </div>

          <div class="register-field">
            <label for="kodepos">Kode pos</label>
            <input
              type="text"
              id="kodepos"
              name="kodepos"
              placeholder="40132"
              inputmode="numeric"
              required
            >
          </div>

          <div class="register-field">
            <label for="password">Kata sandi</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="••••••••"
              required
            >
          </div>

          <div class="register-field">
            <label for="konfirmasi">Konfirmasi kata sandi</label>
            <input
              type="password"
              id="konfirmasi"
              name="konfirmasi"
              placeholder="••••••••"
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
        <a href="index.html">Login</a>
      </p>

    </section>
  </main>

  <script>
    const wilayah = {
      "Jawa Barat": {
        "Bandung": ["Coblong", "Cicendo", "Lengkong", "Sukajadi", "Cibeunying Kidul"],
        "Bekasi": ["Bekasi Timur", "Bekasi Barat", "Bekasi Utara"],
        "Bogor": ["Bogor Tengah", "Bogor Barat", "Bogor Utara"],
        "Depok": ["Beji", "Cimanggis", "Pancoran Mas"],
        "Cimahi": ["Cimahi Utara", "Cimahi Tengah", "Cimahi Selatan"]
      },
      "DKI Jakarta": {
        "Jakarta Selatan": ["Kebayoran Baru", "Tebet", "Cilandak"],
        "Jakarta Barat": ["Cengkareng", "Kebon Jeruk", "Grogol Petamburan"],
        "Jakarta Timur": ["Cakung", "Duren Sawit", "Kramat Jati"],
        "Jakarta Utara": ["Kelapa Gading", "Tanjung Priok"],
        "Jakarta Pusat": ["Menteng", "Gambir", "Tanah Abang"]
      },
      "Jawa Tengah": {
        "Semarang": ["Banyumanik", "Tembalang", "Semarang Barat"],
        "Surakarta": ["Banjarsari", "Laweyan", "Jebres"],
        "Magelang": ["Magelang Utara", "Magelang Selatan"]
      },
      "Jawa Timur": {
        "Surabaya": ["Wonokromo", "Gubeng", "Tegalsari"],
        "Malang": ["Klojen", "Lowokwaru", "Blimbing"],
        "Kediri": ["Mojoroto", "Kota", "Pesantren"]
      },
      "Banten": {
        "Tangerang": ["Cipondoh", "Karawaci", "Ciledug"],
        "Tangerang Selatan": ["Pamulang", "Serpong", "Ciputat"],
        "Serang": ["Serang", "Cipocok Jaya", "Kasemen"]
      }
    };

    const provinsi = document.getElementById("provinsi");
    const kota = document.getElementById("kota");
    const kecamatan = document.getElementById("kecamatan");

    function isiKota(pilihanKota = "") {
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

      isiKecamatan();
    }

    function isiKecamatan(pilihanKecamatan = "") {
      const daftarKecamatan =
        wilayah[provinsi.value]?.[kota.value] || [];

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

    provinsi.addEventListener("change", () => {
      isiKota();
    });

    kota.addEventListener("change", () => {
      isiKecamatan();
    });

    // Pilihan awal mengikuti contoh pada desain.
    isiKota("Bandung");
    isiKecamatan("Coblong");

    // Pemeriksaan sederhana konfirmasi password.
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
