<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Database::getInstance()->getConnection();

// Hanya terima request POST (dipicu dari form konfirmasi di list.php)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = isset($_POST['id_produk']) ? (int) $_POST['id_produk'] : 0;

if ($id <= 0) {
    setFlash('error', 'ID produk tidak valid.');
    header('Location: list.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Ambil dulu nama produknya untuk keperluan log aktivitas & pesan flash
    $findStmt = $pdo->prepare('SELECT nama_produk FROM produk WHERE id_produk = :id');
    $findStmt->execute(['id' => $id]);
    $produk = $findStmt->fetch();

    if (!$produk) {
        $pdo->rollBack();
        setFlash('error', 'Produk tidak ditemukan atau sudah dihapus sebelumnya.');
        header('Location: list.php');
        exit;
    }

    // Hapus produk
    $deleteStmt = $pdo->prepare('DELETE FROM produk WHERE id_produk = :id');
    $deleteStmt->execute(['id' => $id]);

    // Catat aktivitas ke log_aktivitas (bonus: transaction + log)
    $logStmt = $pdo->prepare(
        'INSERT INTO log_aktivitas (aksi, keterangan) VALUES (:aksi, :keterangan)'
    );
    $logStmt->execute([
        'aksi'       => 'DELETE_PRODUK',
        'keterangan' => 'Produk "' . $produk['nama_produk'] . '" (ID: ' . $id . ') dihapus.',
    ]);

    $pdo->commit();

    setFlash('success', 'Produk "' . $produk['nama_produk'] . '" berhasil dihapus.');
} catch (Exception $e) {
    $pdo->rollBack();
    setFlash('error', 'Gagal menghapus produk: ' . $e->getMessage());
}

header('Location: list.php');
exit;
