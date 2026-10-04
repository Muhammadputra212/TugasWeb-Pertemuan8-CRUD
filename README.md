# CRUD Inventaris — Tugas Rutin 8

Aplikasi CRUD inventaris sederhana menggunakan **PHP Native + PDO (Singleton Pattern)** dengan database **MySQL**.

## Struktur Folder

```
inventaris-crud/
├── index.php                 # Redirect ke produk/list.php
├── config/
│   └── database.php          # Koneksi PDO dengan Singleton Pattern
├── includes/
│   ├── functions.php         # Helper: flash message, escape output (e())
│   ├── header.php             # Partial navbar
│   └── footer.php             # Partial penutup HTML
├── produk/
│   ├── list.php                # List produk (JOIN kategori + supplier, search, pagination)
│   ├── create.php               # Form tambah produk (dropdown kategori & supplier)
│   ├── edit.php                  # Form edit produk (pre-filled)
│   ├── delete.php                 # Hapus produk (transaction + log aktivitas)
│   └── export.php                  # Export daftar produk ke CSV (bonus)
├── kategori/
│   └── list.php                     # Lihat daftar kategori (read-only)
├── supplier/
│   └── list.php                      # Lihat daftar supplier (read-only)
├── assets/
│   └── style.css                      # Tampilan CSS
└── database/
    └── inventaris_db.sql               # Skema database + data seed
```

## Cara Setup (pakai Laragon)

1. Buka **Laragon**, klik **Start All** (menyalakan Apache/Nginx + MySQL sekaligus).
2. Copy folder `inventaris-crud` ke dalam `C:\laragon\www\`.
3. Buka phpMyAdmin lewat menu **Database** di Laragon (atau ketik `http://localhost/phpmyadmin` di browser), lalu buat/import database:
   - Klik tab **Import** → pilih file `database/inventaris_db.sql` → klik **Go**.
   - Atau buka tab **SQL**, copy-paste seluruh isi file `inventaris_db.sql`, lalu jalankan.
4. Buka browser ke `http://localhost/inventaris-crud/` (atau `http://inventaris-crud.test/` kalau fitur Pretty URL Laragon aktif).

> Kalau MySQL kamu punya password root (bukan default kosong), ubah `$dbPass` di `config/database.php`.

<details>
<summary>Setup pakai XAMPP (alternatif)</summary>

1. Jalankan XAMPP, aktifkan **Apache** dan **MySQL**.
2. Buka `http://localhost/phpmyadmin`, import `database/inventaris_db.sql` seperti di atas.
3. Copy folder `inventaris-crud` ke `C:\xampp\htdocs\`.
4. Buka browser ke `http://localhost/inventaris-crud/`.

</details>

## Alur Penggunaan

1. Halaman utama otomatis menampilkan **daftar produk** (hasil JOIN tabel `produk`, `kategori`, `supplier`).
2. Klik **+ Tambah Produk** untuk menambah data baru lewat form dengan dropdown kategori & supplier.
3. Klik **Edit** pada baris produk untuk mengubah data (form otomatis terisi data lama).
4. Klik **Hapus** untuk menghapus produk — akan muncul konfirmasi browser sebelum data benar-benar terhapus.
5. Gunakan kolom pencarian untuk mencari produk berdasarkan nama.
6. Klik **Export CSV** untuk mengunduh laporan daftar produk.
7. Menu **Kategori** dan **Supplier** di navbar menampilkan data referensi (read-only) beserta jumlah produknya.

## Checklist Requirement Tugas

| # | Requirement | Lokasi Implementasi |
|---|---|---|
| 1 | Database `inventaris_db` dengan 3 tabel + FK | `database/inventaris_db.sql` (`kategori`, `supplier`, `produk`) |
| 2 | Minimal 5 data seed per tabel | `database/inventaris_db.sql` (bagian `INSERT INTO`) |
| 3 | Koneksi PDO dengan Singleton Pattern | `config/database.php` (class `Database`) |
| 4 | Halaman list produk dengan JOIN 2 tabel | `produk/list.php` |
| 5 | Form create dengan dropdown kategori & supplier | `produk/create.php` |
| 6 | Fitur update (form pre-filled) & delete (konfirmasi) | `produk/edit.php`, `produk/delete.php` |
| 7 | SEMUA query input pakai prepared statements | Semua file di `produk/` (pakai `$pdo->prepare()`) |
| 8 | Output HTML pakai `htmlspecialchars()` | `includes/functions.php` (fungsi `e()`), dipakai di semua halaman |
| 9 | Flash message sukses/gagal (redirect pattern) | `includes/functions.php` (`setFlash`/`getFlash`), dipakai setelah create/update/delete |
| 10 | UI rapi | `assets/style.css` (konsisten dengan tema project login sebelumnya) |

## Bonus yang Sudah Ditambahkan

- ✅ **Transaction pada delete + log aktivitas** — `produk/delete.php` memakai `beginTransaction()` / `commit()` / `rollBack()`, setiap penghapusan dicatat ke tabel `log_aktivitas`.
- ✅ **Fitur pencarian** — cari produk berdasarkan nama di `produk/list.php`.
- ✅ **Pagination** — daftar produk dibagi per halaman (5 baris/halaman).
- ✅ **Export laporan** — `produk/export.php` mengunduh daftar produk sebagai file CSV.

## Catatan Keamanan & Praktik Baik

- Semua query ke database menggunakan **prepared statement** (`$pdo->prepare()` + parameter binding) — aman dari SQL Injection.
- Semua output ke HTML melewati fungsi `e()` (pembungkus `htmlspecialchars()`) — aman dari XSS.
- `id_kategori` dan `id_supplier` yang dikirim lewat form divalidasi ulang ke database sebelum disimpan, supaya tidak bisa diisi ID sembarangan.
- Relasi Foreign Key menggunakan `ON DELETE RESTRICT`, sehingga kategori/supplier yang masih dipakai produk tidak bisa dihapus langsung dari database (mencegah data produk jadi "yatim").
