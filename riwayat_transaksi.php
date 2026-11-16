
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
                    <strong>1.250</strong>
                    <span>Poin tersedia</span>
                </div>
            </article>

            <article class="rt-card">
                <div class="rt-card__icon rt-card__icon--in">↗</div>
                <div>
                    <p>Total Poin Masuk</p>
                    <strong class="rt-in">+85</strong>
                    <span>Akumulasi poin masuk</span>
                </div>
            </article>

            <article class="rt-card">
                <div class="rt-card__icon rt-card__icon--out">↙</div>
                <div>
                    <p>Total Poin Keluar</p>
                    <strong class="rt-out">-15</strong>
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

                <button class="rt-tab active"
                        type="button"
                        data-filter="semua"
                        aria-pressed="true">
                    Semua
                </button>

                <button class="rt-tab"
                        type="button"
                        data-filter="masuk"
                        aria-pressed="false">
                    Poin Masuk
                </button>

                <button class="rt-tab"
                        type="button"
                        data-filter="keluar"
                        aria-pressed="false">
                    Poin Keluar
                </button>

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

                        <tr data-type="masuk">
                            <td>12 Nov 2026</td>
                            <td>
                                <strong>Setor Sampah Plastik</strong>
                                <small>Bank Sampah Asri</small>
                            </td>
                            <td>
                                <span class="rt-type rt-type--in">Poin Masuk</span>
                            </td>
                            <td class="rt-amount rt-amount--in">+20 Poin</td>
                        </tr>

                        <tr data-type="keluar">
                            <td>9 Nov 2026</td>
                            <td>
                                <strong>Penukaran Poin</strong>
                                <small>Top-up Pulsa</small>
                            </td>
                            <td>
                                <span class="rt-type rt-type--out">Poin Keluar</span>
                            </td>
                            <td class="rt-amount rt-amount--out">-15 Poin</td>
                        </tr>

                        <tr data-type="masuk">
                            <td>5 Nov 2026</td>
                            <td>
                                <strong>Setor Sampah Plastik</strong>
                                <small>Bank Sampah Harapan</small>
                            </td>
                            <td>
                                <span class="rt-type rt-type--in">Poin Masuk</span>
                            </td>
                            <td class="rt-amount rt-amount--in">+10 Poin</td>
                        </tr>

                        <tr data-type="masuk">
                            <td>1 Nov 2026</td>
                            <td>
                                <strong>Setor Sampah Plastik</strong>
                                <small>Bank Sampah Bumi Hijau</small>
                            </td>
                            <td>
                                <span class="rt-type rt-type--in">Poin Masuk</span>
                            </td>
                            <td class="rt-amount rt-amount--in">+5 Poin</td>
                        </tr>

                    </tbody>

                </table>

                <p class="rt-empty" id="rtEmpty" hidden>
                    Belum ada transaksi pada kategori ini.
                </p>

            </div>

        </section>

    </main>

    <script src="header/sidebar.js" defer></script>
    <script src="riwayat_transaksi.js" defer></script>

</body>
</html