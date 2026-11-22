<?php
session_start();

require_once 'koneksi.php';

// Memastikan pengguna sudah login.
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Mengambil ID pengguna yang sedang login.
$userId = (int) $_SESSION['user_id'];

// Menghitung ringkasan poin milik pengguna.
function getRingkasanPoin(mysqli $conn, int $userId): array
{
    $sql = "SELECT
                COALESCE((
                    SELECT SUM(total_poin)
                    FROM setoran
                    WHERE user_id = ?
                    AND status = 'berhasil'
                ), 0) AS poin_masuk,

                COALESCE((
                    SELECT SUM(poin_dipakai)
                    FROM penukaran_poin
                    WHERE user_id = ?
                    AND status IN ('diproses', 'berhasil')
                ), 0) AS poin_keluar";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $stmt->close();

    $poinMasuk = (int) $data['poin_masuk'];
    $poinKeluar = (int) $data['poin_keluar'];

    return [
        'poin_masuk' => $poinMasuk,
        'poin_keluar' => $poinKeluar,
        'total_poin' => max(0, $poinMasuk - $poinKeluar)
    ];
}


// Mengambil riwayat transaksi pengguna.
function getRiwayatTransaksi(mysqli $conn, int $userId): array
{
    $sql = "
        SELECT *
        FROM (
            SELECT
                CONCAT('S-', s.id) AS kode_transaksi,
                s.waktu_setor AS tanggal,
                CONCAT(
                    'Setoran sampah - ',
                    COALESCE(b.nama, 'Bank Sampah')
                ) AS keterangan,
                'Poin Masuk' AS jenis_transaksi,
                'masuk' AS tipe,
                s.total_poin AS jumlah_poin
            FROM setoran s
            LEFT JOIN bank_sampah b
                ON b.id = s.bank_sampah_id
            WHERE s.user_id = ?
            AND s.status = 'berhasil'

            UNION ALL

            SELECT
                CONCAT('T-', p.id) AS kode_transaksi,
                p.created_at AS tanggal,
                CONCAT(
                    'Penukaran poin - ',
                    p.nama_produk
                ) AS keterangan,
                'Poin Keluar' AS jenis_transaksi,
                'keluar' AS tipe,
                p.poin_dipakai AS jumlah_poin
            FROM penukaran_poin p
            WHERE p.user_id = ?
            AND p.status IN ('diproses', 'berhasil')
        ) AS transaksi
        ORDER BY tanggal DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $data;
}


// Mengambil data ringkasan dan riwayat transaksi.
$ringkasan = getRingkasanPoin($conn, $userId);
$riwayatTransaksi = getRiwayatTransaksi($conn, $userId);

// Menentukan filter riwayat transaksi.
$filter = $_GET['filter'] ?? 'semua';

if (!in_array($filter, ['semua', 'masuk', 'keluar'], true)) {
    $filter = 'semua';
}

// Menyaring transaksi sesuai filter yang dipilih.
$transaksiTampil = $riwayatTransaksi;

if ($filter !== 'semua') {
    $transaksiTampil = array_values(array_filter(
        $riwayatTransaksi,
        function ($transaksi) use ($filter) {
            return $transaksi['tipe'] === $filter;
        }
    ));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi Poin - RecyclePoint</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="riwayat_transaksi.css">
</head>

<body class="bg-cream">

    <?php include 'header/top_nav.php'; ?>

    <main class="page rt-page">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="Breadcrumb">
            Dashboard / Riwayat Poin
        </nav>

        <!-- Judul halaman -->
        <header class="rt-heading">
            <h1 class="page__title">Riwayat Transaksi Poin</h1>
            <p>Pantau seluruh aktivitas masuk dan keluarnya poin kamu.</p>
        </header>

        <!-- Ringkasan poin -->
        <section class="rt-summary" aria-label="Ringkasan poin">

            <article class="rt-card rt-card--total">
                <div class="rt-card__icon">★</div>
                <div>
                    <p>Total Poin</p>
                    <strong><?= number_format($ringkasan['total_poin'], 0, ',', '.') ?></strong>
                    <span>Poin tersedia</span>
                </div>
            </article>

            <article class="rt-card">
                <div class="rt-card__icon rt-card__icon--in">↗</div>
                <div>
                    <p>Total Poin Masuk</p>
                    <strong class="rt-in">+<?= number_format($ringkasan['poin_masuk'], 0, ',', '.') ?></strong>
                    <span>Akumulasi poin masuk</span>
                </div>
            </article>

            <article class="rt-card">
                <div class="rt-card__icon rt-card__icon--out">↙</div>
                <div>
                    <p>Total Poin Keluar</p>
                    <strong class="rt-out">-<?= number_format($ringkasan['poin_keluar'], 0, ',', '.') ?></strong>
                    <span>Akumulasi poin digunakan</span>
                </div>
            </article>

        </section>

        <!-- Riwayat transaksi -->
        <section class="rt-history">

            <div class="rt-section-heading">
                <div>
                    <h2>Riwayat Transaksi</h2>
                    <p>Daftar aktivitas transaksi poin kamu.</p>
                </div>
            </div>

            <!-- Filter transaksi -->
            <div class="rt-tabs" role="group" aria-label="Filter transaksi">

                <a href="riwayat_transaksi.php?filter=semua"
                    class="rt-tab <?= $filter === 'semua' ? 'active' : '' ?>">
                     Semua
                </a>

                <a href="riwayat_transaksi.php?filter=masuk"
                    class="rt-tab <?= $filter === 'masuk' ? 'active' : '' ?>">
                     Poin Masuk
                </a>

                <a href="riwayat_transaksi.php?filter=keluar"
                    class="rt-tab <?= $filter === 'keluar' ? 'active' : '' ?>">
                     Poin Keluar
                </a>

            </div>

            <p class="rt-status" id="rtStatus" role="status" aria-live="polite"></p>

            <!-- Tabel transaksi -->
            <div class="rt-table-wrapper">

                <table class="rt-table">

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Jenis Transaksi</th>
                            <th>Jumlah Poin</th>
                        </tr>
                    </thead>

                    
                    <tbody id="rtTableBody">

                        <?php foreach ($transaksiTampil as $transaksi): ?>
                            <?php
                            $isMasuk = $transaksi['tipe'] === 'masuk';
                            $tanggal = date('d M Y', strtotime($transaksi['tanggal']));
                            $jumlahPoin = (int) $transaksi['jumlah_poin'];
                            ?>

                            <tr data-type="<?= htmlspecialchars($transaksi['tipe'], ENT_QUOTES, 'UTF-8') ?>">
                                <td><?= htmlspecialchars($tanggal, ENT_QUOTES, 'UTF-8') ?></td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($transaksi['keterangan'], ENT_QUOTES, 'UTF-8') ?>
                                    </strong>
                                    <small>
                                        <?= htmlspecialchars($transaksi['kode_transaksi'], ENT_QUOTES, 'UTF-8') ?>
                                    </small>
                                </td>

                                <td>
                                    <span class="rt-type <?= $isMasuk ? 'rt-type--in' : 'rt-type--out' ?>">
                                        <?= htmlspecialchars($transaksi['jenis_transaksi'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>

                                <td class="rt-amount <?= $isMasuk ? 'rt-amount--in' : 'rt-amount--out' ?>">
                                    <?= $isMasuk ? '+' : '-' ?><?= number_format($jumlahPoin, 0, ',', '.') ?> Poin
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

                <?php if (empty($transaksiTampil)): ?>
                    <p class="rt-empty" id="rtEmpty">
                        Belum ada transaksi pada kategori ini.
                    </p>
                <?php endif; ?>

            </div>

        </section>

    </main>

    <script src="header/sidebar.js" defer></script>

</body>
</html