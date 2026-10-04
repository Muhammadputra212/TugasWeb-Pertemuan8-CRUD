<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Database::getInstance()->getConnection();

// Tampilkan kategori beserta jumlah produk di tiap kategori (JOIN + GROUP BY)
$sql = 'SELECT kategori.id_kategori, kategori.nama_kategori, COUNT(produk.id_produk) AS jumlah_produk
        FROM kategori
        LEFT JOIN produk ON produk.id_kategori = kategori.id_kategori
        GROUP BY kategori.id_kategori, kategori.nama_kategori
        ORDER BY kategori.nama_kategori';

$kategoriList = $pdo->query($sql)->fetchAll();

$pageTitle  = 'Daftar Kategori';
$activeMenu = 'kategori';
$assetPath  = '../';
$basePath   = '../';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>🏷️ Daftar Kategori</h1>
</div>

<div class="panel">
    <?php renderFlash(); ?>
    <p style="color:#64748b; font-size:13px; margin-bottom:16px;">
        Kategori dipakai sebagai pilihan dropdown saat menambah/mengedit produk.
    </p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Produk</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kategoriList as $kategori): ?>
                    <tr>
                        <td><?= e((string) $kategori['id_kategori']) ?></td>
                        <td><?= e($kategori['nama_kategori']) ?></td>
                        <td><span class="badge"><?= e((string) $kategori['jumlah_produk']) ?> produk</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
