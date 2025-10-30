<?php

class Database {

    private$host = '127.0.0.1';
    private $port = 3306;
    private $dbname = 'doit_lpdb';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    private $pdo;

    public function __construct() {

    try {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
        $this->pdo = new PDO($dsn, $this->username, $this->password);

        // Fehlerbehandlung aktivieren
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Datenbankverbindung konnte nicht hergestellt werden. Bitte versuchen Sie es später erneut.");
    }
}
    public function getConnection() {
        return $this->pdo;
    }
}
?>