<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Database::getInstance()->getConnection();

$sql = 'SELECT supplier.id_supplier, supplier.nama_supplier, supplier.kontak,
               COUNT(produk.id_produk) AS jumlah_produk
        FROM supplier
        LEFT JOIN produk ON produk.id_supplier = supplier.id_supplier
        GROUP BY supplier.id_supplier, supplier.nama_supplier, supplier.kontak
        ORDER BY supplier.nama_supplier';

$supplierList = $pdo->query($sql)->fetchAll();

$pageTitle  = 'Daftar Supplier';
$activeMenu = 'supplier';
$assetPath  = '../';
$basePath   = '../';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>🚚 Daftar Supplier</h1>
</div>

<div class="panel">
    <?php renderFlash(); ?>
    <p style="color:#64748b; font-size:13px; margin-bottom:16px;">
        Supplier dipakai sebagai pilihan dropdown saat menambah/mengedit produk.
    </p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Supplier</th>
                    <th>Kontak</th>
                    <th>Jumlah Produk</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($supplierList as $supplier): ?>
                    <tr>
                        <td><?= e((string) $supplier['id_supplier']) ?></td>
                        <td><?= e($supplier['nama_supplier']) ?></td>
                        <td><?= e($supplier['kontak']) ?></td>
                        <td><span class="badge"><?= e((string) $supplier['jumlah_produk']) ?> produk</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
