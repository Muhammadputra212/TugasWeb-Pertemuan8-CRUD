<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Database::getInstance()->getConnection();

$search = isset($_GET['search']) ? cleanText($_GET['search']) : '';

$sql = 'SELECT produk.id_produk, produk.nama_produk, kategori.nama_kategori,
               supplier.nama_supplier, produk.stok, produk.harga
        FROM produk
        JOIN kategori ON produk.id_kategori = kategori.id_kategori
        JOIN supplier ON produk.id_supplier = supplier.id_supplier';

$params = [];
if ($search !== '') {
    $sql .= ' WHERE produk.nama_produk LIKE :search';
    $params['search'] = '%' . $search . '%';
}
$sql .= ' ORDER BY produk.id_produk ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Set header supaya browser men-download sebagai file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=laporan_inventaris_' . date('Y-m-d_His') . '.csv');

$output = fopen('php://output', 'w');

// BOM supaya Excel membaca karakter UTF-8 (misalnya "Rp") dengan benar
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Header kolom
fputcsv($output, ['ID', 'Nama Produk', 'Kategori', 'Supplier', 'Stok', 'Harga']);

foreach ($rows as $row) {
    fputcsv($output, [
        $row['id_produk'],
        $row['nama_produk'],
        $row['nama_kategori'],
        $row['nama_supplier'],
        $row['stok'],
        $row['harga'],
    ]);
}

fclose($output);
exit;
