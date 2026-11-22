<?php
session_start();

require_once 'koneksi.php';

//Untuk mengamankan teks yang ditampilkan ke halaman.
function e($value): string 
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
} 

// FUngsi untuk mengambil daftar bank sampah yang aktif 
function getBankSampah(mysqli $conn, string $keyword = '', string $sort = 'default'): array
{
    $sql = "SELECT id, nama, alamat, provinsi, kota, kecamatan, kode_pos, latitude, longitude, no_telepon, jam_operasional, foto, status 
    FROM bank_sampah
    WHERE status = 'aktif'";

    if ($keyword !== '') {
        $sql .= " AND (
        nama LIKE ? 
        OR alamat LIKE ?
        OR kecamatan LIKE ?
        OR kota LIKE ?
        )";
    }

    //Mengurutkan data
    $orderBy = ($sort === 'name') ? 'nama ASC' : 'id ASC';
    $sql .= " ORDER BY " . $orderBy;

    $stmt = $conn->prepare($sql);

    if ($keyword !== '') {
        $search = "%" . $keyword . "%";
        $stmt->bind_param("ssss", $search, $search, $search, $search); 
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $data;
}

//Memastikan pengguna sudah login
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Mengambil kata kunci pencarian dan pilihan pengurutan.
$keyword = trim($_GET['keyword'] ?? '');
$sort = $_GET['sort'] ?? 'default';

// Memastikan pilihan pengurutan sesuai dengan pilihan yang tersedia.
if (!in_array($sort, ['default', 'name'], true)) {
    $sort = 'default';
}

// Mengambil daftar bank sampah berdasarkan pencarian dan pengurutan.
$bankSampah = getBankSampah($conn, $keyword, $sort);

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

<form method="GET" action="daftar_lokasi.php" class="dl-search-section">

    <div class="dl-search-box">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m16 16 4 4"></path>
        </svg>

        <input
            type="text"
            name="keyword"
            placeholder="Cari nama atau lokasi bank sampah"
            value="<?= e($keyword) ?>"
        >
    </div>

    <div class="dl-filter-wrapper">
        <select name="sort" class="dl-filter-button">
            <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>
                Urutan awal
            </option>

            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>
                Nama A-Z
            </option>
        </select>
    </div>

    <button type="submit" class="dl-filter-button">
        Cari
    </button>

</form>

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
    // Menentukan lokasi yang akan dibuka di Google Maps.
    if (
        !empty($bank['latitude']) &&
        !empty($bank['longitude'])
    ) {
        $mapsQuery = $bank['latitude'] . ',' . $bank['longitude'];
    } else {
        $mapsQuery = $bank['nama'] . ',' . $bank['alamat'];
    }

    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query='
        . rawurlencode($mapsQuery);
    ?>

    <div class="dl-location-card">

        <div class="dl-recycle-icon">
            <span>♻</span>
        </div>

        <div class="dl-location-info">
            <h3><?= e($bank['nama']) ?></h3>
            <p><?= e($bank['alamat']) ?></p>
            <span>Jam operasional: <?= e($bank['jam_operasional'] ?: 'Belum tersedia') ?></span>
        </div>

        <a
            href="<?= e($mapsUrl) ?>"
            class="dl-maps-button"
            target="_blank"
            rel="noopener noreferrer"
        >
            <span class="dl-maps-icon">↗</span>
            Lihat Maps
        </a>

    </div>

<?php endforeach; ?>

<p
    class="dl-empty-message"
    id="emptyMessage"
    <?= !empty($bankSampah) ? 'hidden' : '' ?>
>
    Bank sampah yang kamu cari tidak ditemukan.
</p>
</div>


            </div>

        </section>

    </main>

    <script src="header/sidebar.js" defer></script>
    <script src="daftar_lokasi.js" defer></script>

</body>
</html