<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Database::getInstance()->getConnection();

$errors = [];
$old = [
    'nama_produk' => '',
    'id_kategori' => '',
    'id_supplier' => '',
    'stok'        => '',
    'harga'       => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old['nama_produk'] = cleanText($_POST['nama_produk'] ?? '');
    $old['id_kategori'] = $_POST['id_kategori'] ?? '';
    $old['id_supplier'] = $_POST['id_supplier'] ?? '';
    $old['stok']         = $_POST['stok'] ?? '';
    $old['harga']        = $_POST['harga'] ?? '';

    // ---------- Validasi ----------
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
        // Pastikan id_kategori & id_supplier yang dikirim benar-benar ada di database
        $checkKategori = $pdo->prepare('SELECT COUNT(*) FROM kategori WHERE id_kategori = :id');
        $checkKategori->execute(['id' => $old['id_kategori']]);

        $checkSupplier = $pdo->prepare('SELECT COUNT(*) FROM supplier WHERE id_supplier = :id');
        $checkSupplier->execute(['id' => $old['id_supplier']]);

        if ((int) $checkKategori->fetchColumn() === 0) {
            $errors[] = 'Kategori yang dipilih tidak valid.';
        }
        if ((int) $checkSupplier->fetchColumn() === 0) {
            $errors[] = 'Supplier yang dipilih tidak valid.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO produk (nama_produk, id_kategori, id_supplier, stok, harga)
             VALUES (:nama_produk, :id_kategori, :id_supplier, :stok, :harga)'
        );

        $stmt->execute([
            'nama_produk' => $old['nama_produk'],
            'id_kategori' => $old['id_kategori'],
            'id_supplier' => $old['id_supplier'],
            'stok'        => $old['stok'],
            'harga'       => $old['harga'],
        ]);

        setFlash('success', 'Produk "' . $old['nama_produk'] . '" berhasil ditambahkan.');
        header('Location: list.php');
        exit;
    }
}

// Ambil data untuk dropdown
$kategoriList = $pdo->query('SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori')->fetchAll();
$supplierList = $pdo->query('SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier')->fetchAll();

$pageTitle  = 'Tambah Produk';
$activeMenu = 'produk';
$assetPath  = '../';
$basePath   = '../';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>➕ Tambah Produk</h1>
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

    <form method="POST" action="create.php" novalidate>
        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" id="nama_produk" name="nama_produk" value="<?= e($old['nama_produk']) ?>" required>
        </div>

        <div class="form-group">
            <label for="id_kategori">Kategori</label>
            <select id="id_kategori" name="id_kategori" required>
                <option value="">-- Pilih Kategori --</option>
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
                <option value="">-- Pilih Supplier --</option>
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
            <button type="submit">Simpan Produk</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
