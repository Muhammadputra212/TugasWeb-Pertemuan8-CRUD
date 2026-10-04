<?php
/**
 * functions.php
 * Kumpulan fungsi bantu: session/flash message, escape output, dan utilitas kecil lainnya.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escape output ke HTML supaya aman dari XSS.
 * Dipakai di SEMUA tempat yang menampilkan data dari database/input user.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Simpan flash message ke session, akan tampil SEKALI di halaman berikutnya
 * (dipakai setelah redirect pada pola create/update/delete).
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type, // 'success' atau 'error'
        'message' => $message,
    ];
}

/**
 * Ambil flash message (kalau ada) lalu langsung hapus dari session
 * supaya tidak muncul lagi saat halaman di-refresh.
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render HTML flash message (dipanggil di bagian atas halaman).
 */
function renderFlash(): void
{
    $flash = getFlash();
    if ($flash) {
        $class = $flash['type'] === 'success' ? 'alert-success' : 'alert-error';
        echo '<div class="alert ' . $class . '">' . e($flash['message']) . '</div>';
    }
}

/**
 * Bersihkan teks dari spasi berlebih (validasi dasar sebelum masuk ke query).
 */
function cleanText(string $value): string
{
    return trim($value);
}

/**
 * Format angka ke format Rupiah, misalnya 125000 -> "Rp 125.000"
 */
function formatRupiah($angka): string
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}
