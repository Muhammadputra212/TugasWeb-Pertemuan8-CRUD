<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Database::getInstance()->getConnection();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    setFlash('error', 'ID produk tidak valid.');
    header('Location: list.php');
    exit;
}

// Ambil data produk yang mau diedit (prepared statement)
$stmt = $pdo->prepare('SELECT * FROM produk WHERE id_produk = :id');
$stmt->execute(['id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    setFlash('error', 'Produk tidak ditemukan.');
    header('Location: list.php');
    exit;
}

$errors = [];

// Nilai awal form = data produk dari database (form pre-filled)
$old = [
    'nama_produk' => $produk['nama_produk'],
    'id_kategori' => $produk['id_kategori'],
    'id_supplier' => $produk['id_supplier'],
    'stok'        => $produk['stok'],
    'harga'       => $produk['harga'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old['nama_produk'] = cleanText($_POST['nama_produk'] ?? '');
    $old['id_kategori'] = $_POST['id_kategori'] ?? '';
    $old['id_supplier'] = $_POST['id_supplier'] ?? '';
    $old['stok']         = $_POST['stok'] ?? '';
    $old['harga']        = $_POST['harga'] ?? '';

    if ($old['nama_produk'] === '') {
        $errors[] = 'Nama produk wajib diisi.';
    }
    if ($old['id_kategori'] === '' || !ctype_digit((string) $old['id_kategori'])) {
        $errors[] = 'Kategori wajib dipilih.';
    }
    if ($old['id_supplier'] === '' || !ctype_digit((string) $old['id_supplier'])) {
        $errors[] = 'Supplier wajib dipilih.';
    }
    if ($old['stok'] === '' || !ctype_digit((string) $old['stok'])) {
        $errors[] = 'Stok harus berupa angka bulat (0 atau lebih).';
    }
    if ($old['harga'] === '' || !is_numeric($old['harga']) || (float) $old['harga'] < 0) {
        $errors[] = 'Harga harus berupa angka dan tidak boleh negatif.';
    }

    if (empty($errors)) {
        $updateStmt = $pdo->prepare(
            'UPDATE produk
             SET nama_produk = :nama_produk,
                 id_kategori = :id_kategori,
                 id_supplier = :id_supplier,
                 stok = :stok,
                 harga = :harga
             WHERE id_produk = :id'
        );

        $updateStmt->execute([
            'nama_produk' => $old['nama_produk'],
            'id_kategori' => $old['id_kategori'],
            'id_supplier' => $old['id_supplier'],
            'stok'        => $old['stok'],
            'harga'       => $old['harga'],
            'id'          => $id,
        ]);

        setFlash('success', 'Produk "' . $old['nama_produk'] . '" berhasil diperbarui.');
        header('Location: list.php');
        exit;
    }
}

$kategoriList = $pdo->query('SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori')->fetchAll();
$supplierList = $pdo->query('SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier')->fetchAll();

$pageTitle  = 'Edit Produk';
$activeMenu = 'produk';
$assetPath  = '../';
$basePath   = '../';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>✏️ Edit Produk</h1>
    <a href="list.php" class="btn btn-secondary">&larr; Kembali</a>
</div>

<div class="panel narrow">

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $id ?>" novalidate>
        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" id="nama_produk" name="nama_produk" value="<?= e($old['nama_produk']) ?>" required>
        </div>

        <div class="form-group">
            <label for="id_kategori">Kategori</label>
            <select id="id_kategori" name="id_kategori" required>
                <?php foreach ($kategoriList as $kategori): ?>
                    <option value="<?= (int) $kategori['id_kategori'] ?>"
                        <?= (string) $old['id_kategori'] === (string) $kategori['id_kategori'] ? 'selected' : '' ?>>
                        <?= e($kategori['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_supplier">Supplier</label>
            <select id="id_supplier" name="id_supplier" required>
                <?php foreach ($supplierList as $supplier): ?>
                    <option value="<?= (int) $supplier['id_supplier'] ?>"
                        <?= (string) $old['id_supplier'] === (string) $supplier['id_supplier'] ? 'selected' : '' ?>>
                        <?= e($supplier['nama_supplier']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" min="0" value="<?= e((string) $old['stok']) ?>" required>
        </div>

        <div class="form-group">
            <label for="harga">Harga (Rp)</label>
            <input type="number" id="harga" name="harga" min="0" step="0.01" value="<?= e((string) $old['harga']) ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit">Simpan Perubahan</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
