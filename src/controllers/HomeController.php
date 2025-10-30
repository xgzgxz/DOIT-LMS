<?php
// Die Klasse HomeController kümmert sich um alle Anfragen,
// die "statische" Seiten wie die Startseite oder das Impressum (später) betreffen.

class HomeController
{
    // Wir brauchen wieder einen privaten Speicher für die Datenbankverbindung
    private $pdo;
    private $courseModel;

    /**
     * Der Konstruktor. Wird aufgerufen, wenn ein HomeController-Objekt
     * erstellt wird (z.B. new HomeController($pdo))
     */
    public function __construct($pdo)
    {
        // Die übergebene $pdo-Verbindung in unserem privaten Speicher ablegen
        $this->pdo = $pdo;
        require_once __DIR__ . '/../models/Course.php';
        $this->courseModel = new Course($this->pdo);
    }

    /**
    * Diese Methode kümmert sich um die Darstellung der Startseite (Kurs-Katalog).
     */
    public function index($basePath, $viewsPath)
    {
        // 1. Alle Kurse für die Übersicht holen
        $allCourses = $this->courseModel->getAllCourses();

        // 2. Die Views laden
        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'home.php';
        require_once $viewsPath . 'partials/footer.php';
    }

        /**
     * Zeigt die statische Impressum-Seite an.
     */
    public function showImpressum($basePath, $viewsPath)
    {
       // $allCourses für den Header laden, damit die Kurs-Navigation funktioniert.
        $allCourses = $this->courseModel->getAllCourses();

        // Views laden: Header, Impressum-View, Footer
        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'impressum.php';
        require_once $viewsPath . 'partials/footer.php';
    }

    /**
     * Zeigt die statische Datenschutz-Seite an.
     */
    public function showDatenschutz($basePath, $viewsPath)
    {
        // $allCourses für den Header laden
        $allCourses = $this->courseModel->getAllCourses();

        // Views laden: Header, unsere neue Datenschutz-View, Footer
        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'datenschutz.php';
        require_once $viewsPath . 'partials/footer.php';
    }
}
?>