-- =========================================================
-- inventaris_db.sql
-- Skema database untuk Tugas Rutin 8 - CRUD Inventaris
-- Import file ini lewat phpMyAdmin (XAMPP) sebelum menjalankan aplikasi.
-- =========================================================

CREATE DATABASE IF NOT EXISTS inventaris_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE inventaris_db;

-- ---------------------------------------------------------
-- Tabel 1: kategori
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS kategori (
    id_kategori   INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel 2: supplier
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS supplier (
    id_supplier   INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    kontak        VARCHAR(50)  NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel 3: produk (punya 2 Foreign Key: id_kategori & id_supplier)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS produk (
    id_produk    INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk  VARCHAR(150) NOT NULL,
    id_kategori  INT NOT NULL,
    id_supplier  INT NOT NULL,
    stok         INT NOT NULL DEFAULT 0,
    harga        DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_produk_kategori
        FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_produk_supplier
        FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel bonus: log_aktivitas (mencatat aktivitas delete, dipakai fitur transaction/log)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS log_aktivitas (
    id_log      INT AUTO_INCREMENT PRIMARY KEY,
    aksi        VARCHAR(50) NOT NULL,
    keterangan  TEXT NOT NULL,
    waktu       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- SEED DATA (minimal 5 baris per tabel sesuai requirement)
-- =========================================================

INSERT INTO kategori (nama_kategori) VALUES
('Elektronik'),
('Alat Tulis Kantor'),
('Peralatan Rumah Tangga'),
('Komponen Komputer'),
('Furnitur');

INSERT INTO supplier (nama_supplier, kontak) VALUES
('PT Sumber Makmur', '0812-1111-2222'),
('CV Jaya Abadi', '0813-2222-3333'),
('UD Berkah Elektronik', '0821-3333-4444'),
('PT Mitra Komputer', '0822-4444-5555'),
('CV Rumah Tangga Sejahtera', '0856-5555-6666');

INSERT INTO produk (nama_produk, id_kategori, id_supplier, stok, harga) VALUES
('Mouse Wireless Logitech',      4, 4, 25, 125000.00),
('Keyboard Mekanikal RGB',       4, 4, 15, 450000.00),
('Pulpen Standard AE7',          2, 1, 200, 2500.00),
('Buku Tulis 58 Lembar',         2, 2, 150, 5000.00),
('Kipas Angin Berdiri',          3, 5, 10, 350000.00),
('Rice Cooker 1.8L',             3, 5, 8, 275000.00),
('Meja Kerja Minimalis',         5, 3, 5, 850000.00),
('Lampu LED 12 Watt',            1, 3, 40, 35000.00);

INSERT INTO log_aktivitas (aksi, keterangan) VALUES
('SYSTEM', 'Database inventaris_db berhasil diinisialisasi dengan data awal.');
