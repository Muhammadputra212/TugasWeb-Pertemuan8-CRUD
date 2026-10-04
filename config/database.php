<?php
/**
 * database.php
 * Koneksi database menggunakan PDO dengan Singleton Pattern.
 * Pastikan hanya ada SATU koneksi PDO yang dipakai di seluruh aplikasi.
 */

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    // --- Ubah kredensial di sini sesuai setup XAMPP/MySQL kamu ---
    private string $host    = '127.0.0.1';
    private string $dbName  = 'inventaris_db';
    private string $dbUser  = 'root';
    private string $dbPass  = '';
    private string $charset = 'utf8mb4';

    /**
     * Constructor dibuat PRIVATE supaya class ini tidak bisa di-instantiate
     * langsung dari luar (ciri khas Singleton Pattern).
     */
    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // pakai prepared statement asli dari MySQL
        ];

        try {
            $this->pdo = new PDO($dsn, $this->dbUser, $this->dbPass, $options);
        } catch (PDOException $e) {
            die('Koneksi database gagal: ' . $e->getMessage() .
                '<br>Pastikan MySQL (XAMPP) sudah berjalan dan database "inventaris_db" sudah di-import.');
        }
    }

    /**
     * Satu-satunya cara mendapatkan instance Database.
     * Kalau belum pernah dibuat, baru dibuat sekali; selanjutnya pakai yang sudah ada.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Ambil objek PDO untuk dipakai query (prepare, execute, dll).
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Cegah object ini di-clone atau di-unserialize (menjaga aturan Singleton)
    private function __clone() {}

    public function __wakeup()
    {
        throw new Exception('Tidak boleh unserialize instance Singleton.');
    }
}
