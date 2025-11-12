<?php
/**
 * Database
 * Stellt eine Verbindung zur Datenbank her und gibt die PDO-Instanz zurück.
 */
class Database {
    private $host = '127.0.0.1';
    private $port = 3306;
    private $dbname = 'doit_lpdb';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    private $pdo;

    /**
     * Konstruktor. Baut die Datenbankverbindung auf.
     */
    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Im Produktivbetrieb sollte hier ein Logging stattfinden, anstatt die Anwendung zu beenden.
            die("Datenbankverbindung konnte nicht hergestellt werden.");
        }
    }

    /**
     * Gibt die aktive PDO-Verbindung zurück.
     */
    public function getConnection() {
        return $this->pdo;
    }
}
?>