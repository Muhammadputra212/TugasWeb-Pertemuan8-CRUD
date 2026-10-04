<?php
/**
 * header.php
 * Partial navbar yang di-include di setiap halaman.
 * Variabel $pageTitle dan $activeMenu diisi oleh halaman pemanggil SEBELUM include file ini.
 */
$pageTitle  = $pageTitle  ?? 'Inventaris';
$activeMenu = $activeMenu ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle) ?> — CRUD Inventaris</title>
    <link rel="stylesheet" href="<?= $assetPath ?? '' ?>assets/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">📦 CRUD Inventaris</div>
        <div class="navbar-links">
            <a href="<?= $basePath ?? '' ?>produk/list.php" class="<?= $activeMenu === 'produk' ? 'active' : '' ?>">Produk</a>
            <a href="<?= $basePath ?? '' ?>kategori/list.php" class="<?= $activeMenu === 'kategori' ? 'active' : '' ?>">Kategori</a>
            <a href="<?= $basePath ?? '' ?>supplier/list.php" class="<?= $activeMenu === 'supplier' ? 'active' : '' ?>">Supplier</a>
        </div>
    </nav>
    <div class="wrap">
