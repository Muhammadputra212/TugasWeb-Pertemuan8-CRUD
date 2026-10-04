<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Database::getInstance()->getConnection();

// ---------- Pencarian (bonus) ----------
$search = isset($_GET['search']) ? cleanText($_GET['search']) : '';

// ---------- Pagination (bonus) ----------
$limit       = 5; // jumlah baris per halaman
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset      = ($currentPage - 1) * $limit;

// Hitung total data (untuk total halaman), menghormati filter pencarian
if ($search !== '') {
    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM produk WHERE nama_produk LIKE :search');
    $countStmt->execute(['search' => '%' . $search . '%']);
} else {
    $countStmt = $pdo->query('SELECT COUNT(*) FROM produk');
}
$totalRows  = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalRows / $limit));

// ---------- Query utama: JOIN produk + kategori + supplier ----------
$sql = 'SELECT produk.id_produk, produk.nama_produk, produk.stok, produk.harga,
               kategori.nama_kategori, supplier.nama_supplier
        FROM produk
        JOIN kategori ON produk.id_kategori = kategori.id_kategori
        JOIN supplier ON produk.id_supplier = supplier.id_supplier';

$params = [];
if ($search !== '') {
    $sql .= ' WHERE produk.nama_produk LIKE :search';
    $params['search'] = '%' . $search . '%';
}

$sql .= ' ORDER BY produk.id_produk DESC LIMIT :limit OFFSET :offset';

$stmt = $pdo->prepare($sql);
if ($search !== '') {
    $stmt->bindValue(':search', $params['search'], PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produkList = $stmt->fetchAll();

$pageTitle  = 'Daftar Produk';
$activeMenu = 'produk';
$assetPath  = '../';
$basePath   = '../';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>📦 Daftar Produk</h1>
    <div style="display:flex; gap:10px;">
        <a href="export.php?search=<?= urlencode($search) ?>" class="btn btn-secondary">⬇️ Export CSV</a>
        <a href="create.php" class="btn">+ Tambah Produk</a>
    </div>
</div>

<div class="panel">
    <?php renderFlash(); ?>

    <form method="GET" action="list.php" class="search-bar">
        <input type="search" name="search" placeholder="Cari nama produk..." value="<?= e($search) ?>">
        <button type="submit" class="btn-secondary">Cari</button>
        <?php if ($search !== ''): ?>
            <a href="list.php" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>

    <?php if (empty($produkList)): ?>
        <div class="empty-state">
            Tidak ada produk ditemukan<?= $search !== '' ? ' untuk pencarian "' . e($search) . '"' : '' ?>.
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produkList as $produk): ?>
                        <tr>
                            <td><?= e((string) $produk['id_produk']) ?></td>
                            <td><?= e($produk['nama_produk']) ?></td>
                            <td><span class="badge"><?= e($produk['nama_kategori']) ?></span></td>
                            <td><?= e($produk['nama_supplier']) ?></td>
                            <td><?= e((string) $produk['stok']) ?></td>
                            <td><?= formatRupiah($produk['harga']) ?></td>
                            <td>
                                <div class="row-actions">
                                    <a href="edit.php?id=<?= (int) $produk['id_produk'] ?>" class="btn btn-secondary btn-small">Edit</a>
                                    <form method="POST" action="delete.php" onsubmit="return confirm('Yakin mau hapus produk \'<?= e($produk['nama_produk']) ?>\'? Tindakan ini tidak bisa dibatalkan.');">
                                        <input type="hidden" name="id_produk" value="<?= (int) $produk['id_produk'] ?>">
                                        <button type="submit" class="btn-danger btn-small">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="list.php?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                       class="<?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
