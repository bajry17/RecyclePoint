<?php
$bankSampah = [
    [
        'nama' => 'Bank Sampah Asri',
        'alamat' => 'Dago, Bandung',
        'jarak' => '1 km',
        'jarak_angka' => 1
    ],
    [
        'nama' => 'Bank Sampah Harapan',
        'alamat' => 'Lebak Gede, Bandung',
        'jarak' => '1,2 km',
        'jarak_angka' => 1.2
    ],
    [
        'nama' => 'Bank Sampah Bumi Hijau',
        'alamat' => 'Lebak Siliwangi, Bandung',
        'jarak' => '1,6 km',
        'jarak_angka' => 1.6
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Bank Sampah - RecyclePoint</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="daftar_lokasi.css">
</head>

<body class="bg-cream">

    <?php include 'header/top_nav.php'; ?>

    <main class="page dl-page">

        <div class="dl-breadcrumb">
            <a href="dashboard.php">Dashboard</a>
            <span>/</span>
            <span>Daftar & Lokasi</span>
        </div>

        <section class="dl-heading">
            <h1>Daftar Bank Sampah</h1>
            <p>“Temukan bank sampah terdekat dan mulai setor sampahmu.”</p>
        </section>

        <section class="dl-search-section">

            <div class="dl-search-box">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m16 16 4 4"></path>
                </svg>

                <input
                    type="text"
                    id="searchBank"
                    placeholder="Cari nama atau lokasi bank sampah"
                    autocomplete="off"
                >
            </div>

            <div class="dl-filter-wrapper">
                <button type="button" class="dl-filter-button" id="filterButton">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 7h9"></path>
                        <path d="M17 7h3"></path>
                        <circle cx="15" cy="7" r="2"></circle>
                        <path d="M4 17h3"></path>
                        <path d="M11 17h9"></path>
                        <circle cx="9" cy="17" r="2"></circle>
                    </svg>
                    Filter
                </button>

                <div class="dl-filter-menu" id="filterMenu" hidden>
                    <p>Urutkan berdasarkan</p>
                    <button type="button" data-sort="default">Urutan awal</button>
                    <button type="button" data-sort="distance">Jarak terdekat</button>
                    <button type="button" data-sort="name">Nama A-Z</button>
                </div>
            </div>

        </section>

        <section class="dl-location-section">

            <div class="dl-location-heading">
                <h2>Bank Sampah Terdekat</h2>
                <span id="locationCount">
                    <?= count($bankSampah); ?> lokasi ditemukan
                </span>
            </div>

            <div class="dl-location-panel" id="locationList">

                <?php foreach ($bankSampah as $bank): ?>

                    <?php
                    $mapsQuery = $bank['nama'] . ', ' . $bank['alamat'];
                    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($mapsQuery);
                    ?>

                    <article
                        class="dl-location-card"
                        data-name="<?= htmlspecialchars($bank['nama']); ?>"
                        data-address="<?= htmlspecialchars($bank['alamat']); ?>"
                        data-distance="<?= $bank['jarak_angka']; ?>"
                    >

                        <div class="dl-recycle-icon">
                            <span>♻</span>
                        </div>

                        <div class="dl-location-info">
                            <h3><?= htmlspecialchars($bank['nama']); ?></h3>
                            <p><?= htmlspecialchars($bank['alamat']); ?></p>
                            <span><?= htmlspecialchars($bank['jarak']); ?> dari lokasimu</span>
                        </div>

                        <a
                            href="<?= htmlspecialchars($mapsUrl, ENT_QUOTES, 'UTF-8'); ?>"
                            class="dl-maps-button"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            Lihat Maps
                            <span class="dl-maps-icon">↗</span>
                        </a>

                    </article>

                <?php endforeach; ?>

                <p class="dl-empty-message" id="emptyMessage" hidden>
                    Bank sampah yang kamu cari tidak ditemukan.
                </p>

            </div>

        </section>

    </main>

    <script src="header/sidebar.js" defer></script>
    <script src="daftar_lokasi.js" defer></script>

</body>
</html>