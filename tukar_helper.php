<?php
/**
 * tukar_helper.php
 * Dipakai bersama oleh: marketpoin, tukarpulsa, tukarewallet,
 * konfirmasidaurulang, konfirmasipulsa, konfirmasiewallet.
 * Letakkan di folder yang sama dengan koneksi.php.
 */
declare(strict_types=1);

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sama seperti profile.php: belum login -> ke halaman login
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/koneksi.php';   // menyediakan $conn (mysqli)

if (!function_exists('e')) {
    function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
}

$user_id = (int) $_SESSION['user_id'];

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Daftar aplikasi e-wallet (hanya 2, jadi cukup di sini; dipakai tukarewallet & konfirmasiewallet)
const EWALLET_APP = ['Gopay', 'Dana'];

/* ---------- pesan sekali tampil ---------- */
function ambil_flash(): string
{
    $f = (string) ($_SESSION['flash'] ?? '');
    unset($_SESSION['flash']);
    return $f;
}

function ambil_flash_error(): string
{
    $f = (string) ($_SESSION['flash_error'] ?? '');
    unset($_SESSION['flash_error']);
    return $f;
}

/* ---------- data user ---------- */
// Saldo poin = setoran berhasil - penukaran (diproses/berhasil), dihitung oleh view v_ringkasan_user
function poin_user(mysqli $conn, int $uid): int
{
    $st = $conn->prepare('SELECT poin FROM v_ringkasan_user WHERE user_id = ?');
    $st->bind_param('i', $uid);
    $st->execute();
    $r = $st->get_result()->fetch_assoc();
    $st->close();
    return (int) ($r['poin'] ?? 0);
}

function data_user(mysqli $conn, int $uid): array
{
    $st = $conn->prepare('SELECT no_telepon, alamat_rumah, kecamatan, kota, provinsi, kode_pos FROM users WHERE id = ?');
    $st->bind_param('i', $uid);
    $st->execute();
    $r = $st->get_result()->fetch_assoc();
    $st->close();
    return $r ?: [];
}

// Hasil kosong ('') kalau alamat rumah belum diisi. Nilai "0" (isian tidak valid) dilewati.
function alamat_lengkap(array $u): string
{
    $bagian = [];
    foreach (['alamat_rumah', 'kecamatan', 'kota', 'provinsi'] as $k) {
        $v = trim((string) ($u[$k] ?? ''));
        if ($v !== '' && $v !== '0') {
            $bagian[] = $v;
        }
    }
    if (!$bagian) {
        return '';
    }
    $teks = implode(', ', $bagian);
    $pos  = trim((string) ($u['kode_pos'] ?? ''));
    return $pos !== '' ? $teks . ' ' . $pos : $teks;
}

// Hanya path relatif yang aman. Kalau kosong / mencurigakan -> placeholder.
function foto_aman(?string $f): string
{
    $f = trim((string) $f);
    if ($f === '' || strpos($f, '..') !== false || $f[0] === '/' || preg_match('~^([a-z]+:)?//~i', $f)) {
        return 'images/Placeholder.png';
    }
    return $f;
}

/* ---------- produk daur ulang ---------- */
// $id > 0 : produk dengan id itu (kalau aktif). $id = 0 : produk aktif pertama (untuk tampilan contoh).
function produk_daur_ulang(mysqli $conn, int $id = 0): ?array
{
    if ($id > 0) {
        $st = $conn->prepare("SELECT id, nama, harga_poin, ongkir_poin, stok, foto
                                FROM produk_poin
                               WHERE id = ? AND tipe = 'produk_daur_ulang' AND aktif = 1");
        $st->bind_param('i', $id);
    } else {
        $st = $conn->prepare("SELECT id, nama, harga_poin, ongkir_poin, stok, foto
                                FROM produk_poin
                               WHERE tipe = 'produk_daur_ulang' AND aktif = 1
                               ORDER BY id LIMIT 1");
    }
    $st->execute();
    $r = $st->get_result()->fetch_assoc();
    $st->close();
    return $r ?: null;
}

/* ---------- pulsa & e-wallet ---------- */
function daftar_nominal(mysqli $conn, string $tipe): array
{
    $st = $conn->prepare('SELECT id, nominal, harga_poin FROM produk_poin
                           WHERE tipe = ? AND aktif = 1 AND nominal IS NOT NULL
                           ORDER BY nominal');
    $st->bind_param('s', $tipe);
    $st->execute();
    $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    $st->close();
    return $rows;
}

function produk_nominal(mysqli $conn, string $tipe, int $nominal): ?array
{
    $st = $conn->prepare('SELECT id, nama, nominal, harga_poin FROM produk_poin
                           WHERE tipe = ? AND aktif = 1 AND nominal = ? LIMIT 1');
    $st->bind_param('si', $tipe, $nominal);
    $st->execute();
    $r = $st->get_result()->fetch_assoc();
    $st->close();
    return $r ?: null;
}

// Nominal terkecil yang aktif (untuk tampilan contoh di halaman konfirmasi)
function nominal_default(mysqli $conn, string $tipe): ?array
{
    $st = $conn->prepare('SELECT id, nama, nominal, harga_poin FROM produk_poin
                           WHERE tipe = ? AND aktif = 1 AND nominal IS NOT NULL
                           ORDER BY nominal LIMIT 1');
    $st->bind_param('s', $tipe);
    $st->execute();
    $r = $st->get_result()->fetch_assoc();
    $st->close();
    return $r ?: null;
}

function peta_operator(mysqli $conn): array
{
    $peta = [];
    $res = $conn->query('SELECT prefix, operator FROM operator_prefix');
    while ($r = $res->fetch_assoc()) {
        $peta[$r['prefix']] = $r['operator'];
    }
    return $peta;
}

function operator_dari_nomor(mysqli $conn, string $nomor): ?string
{
    $prefix = substr($nomor, 0, 4);
    $st = $conn->prepare('SELECT operator FROM operator_prefix WHERE prefix = ?');
    $st->bind_param('s', $prefix);
    $st->execute();
    $r = $st->get_result()->fetch_assoc();
    $st->close();
    return $r['operator'] ?? null;
}

/* ---------- simpan penukaran (satu transaksi) ----------
 * $tipe   : produk_daur_ulang | pulsa | ewallet
 * $extra  : nomor_tujuan, penyedia, alamat_pengiriman (sesuai tipe)
 * Harga, ongkir, dan stok SELALU diambil dari database, bukan dari form.
 * Mengembalikan ['ok' => bool, 'pesan' => string, 'kode' => string]
 */
function simpan_penukaran(mysqli $conn, int $uid, int $produkId, string $tipe, array $extra = [], int $percobaan = 0): array
{
    $nomor    = $extra['nomor_tujuan'] ?? null;
    $penyedia = $extra['penyedia'] ?? null;
    $alamat   = $extra['alamat_pengiriman'] ?? null;

    try {
        $conn->begin_transaction();

        // Kunci baris user: dua permintaan serentak tidak bisa memakai poin yang sama
        $st = $conn->prepare('SELECT id FROM users WHERE id = ? FOR UPDATE');
        $st->bind_param('i', $uid);
        $st->execute();
        $ada = $st->get_result()->fetch_assoc();
        $st->close();
        if (!$ada) {
            throw new RuntimeException('Akun tidak ditemukan.');
        }

        $st = $conn->prepare('SELECT id, nama, tipe, harga_poin, ongkir_poin, stok
                                FROM produk_poin WHERE id = ? AND aktif = 1 FOR UPDATE');
        $st->bind_param('i', $produkId);
        $st->execute();
        $p = $st->get_result()->fetch_assoc();
        $st->close();
        if (!$p || $p['tipe'] !== $tipe) {
            throw new RuntimeException('Produk tidak tersedia.');
        }
        if ($tipe === 'produk_daur_ulang' && (int) $p['stok'] < 1) {
            throw new RuntimeException('Stok produk habis.');
        }

        $ongkir = $tipe === 'produk_daur_ulang' ? (int) $p['ongkir_poin'] : 0;
        $total  = (int) $p['harga_poin'] + $ongkir;

        if (poin_user($conn, $uid) < $total) {
            throw new RuntimeException('Poin kamu belum cukup untuk penukaran ini.');
        }

        // Kode: TKR-YYYYMMDD-NNN
        $awal = 'TKR-' . date('Ymd') . '-';
        $like = $awal . '%';
        $st = $conn->prepare('SELECT COALESCE(MAX(CAST(SUBSTRING(kode, -3) AS UNSIGNED)), 0) AS n
                                FROM penukaran_poin WHERE kode LIKE ?');
        $st->bind_param('s', $like);
        $st->execute();
        $n = (int) ($st->get_result()->fetch_assoc()['n'] ?? 0);
        $st->close();
        $kode = $awal . str_pad((string) ($n + 1), 3, '0', STR_PAD_LEFT);

        $nama = (string) $p['nama'];
        $st = $conn->prepare("INSERT INTO penukaran_poin
            (kode, user_id, produk_id, nama_produk, jumlah, poin_dipakai, status,
             nomor_tujuan, penyedia, biaya_ongkir, alamat_pengiriman)
            VALUES (?, ?, ?, ?, 1, ?, 'diproses', ?, ?, ?, ?)");
        $st->bind_param('siisissis', $kode, $uid, $produkId, $nama, $total, $nomor, $penyedia, $ongkir, $alamat);
        $st->execute();
        $st->close();

        if ($tipe === 'produk_daur_ulang') {
            $st = $conn->prepare('UPDATE produk_poin SET stok = stok - 1 WHERE id = ? AND stok >= 1');
            $st->bind_param('i', $produkId);
            $st->execute();
            $habis = $st->affected_rows < 1;
            $st->close();
            if ($habis) {
                throw new RuntimeException('Stok produk habis.');
            }
        }

        $conn->commit();
        return ['ok' => true, 'pesan' => '', 'kode' => $kode];
    } catch (RuntimeException $ex) {
        $conn->rollback();
        return ['ok' => false, 'pesan' => $ex->getMessage(), 'kode' => ''];
    } catch (mysqli_sql_exception $ex) {
        $conn->rollback();
        // 1062 = kode bentrok dengan transaksi lain di detik yang sama -> coba lagi
        if ($ex->getCode() === 1062 && $percobaan < 3) {
            return simpan_penukaran($conn, $uid, $produkId, $tipe, $extra, $percobaan + 1);
        }
        error_log('simpan_penukaran gagal: ' . $ex->getMessage());
        return ['ok' => false, 'pesan' => 'Penukaran gagal disimpan. Coba lagi.', 'kode' => ''];
    }
}