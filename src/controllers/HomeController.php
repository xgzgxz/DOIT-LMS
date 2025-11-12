<?php
/**
 * HomeController
 * Verantwortlich für die Anzeige statischer Seiten wie Startseite, Impressum und Datenschutz.
 */
class HomeController
{
    private $pdo;
    private $courseModel;

    /**
     * Konstruktor. Initialisiert die Datenbankverbindung und lädt das Course-Model.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once __DIR__ . '/../models/Course.php';
        $this->courseModel = new Course($this->pdo);
    }

    /**
     * Zeigt die Startseite mit dem Kurskatalog an.
     */
    public function index($basePath, $viewsPath)
    {
        $allCourses = $this->courseModel->getAllCourses();

        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'home.php';
        require_once $viewsPath . 'partials/footer.php';
    }

    /**
     * Zeigt die statische Impressum-Seite an.
     */
    public function showImpressum($basePath, $viewsPath)
    {
        $allCourses = $this->courseModel->getAllCourses();

        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'impressum.php';
        require_once $viewsPath . 'partials/footer.php';
    }

    /**
     * Zeigt die statische Datenschutz-Seite an.
     */
    public function showDatenschutz($basePath, $viewsPath)
    {
        $allCourses = $this->courseModel->getAllCourses();

        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'datenschutz.php';
        require_once $viewsPath . 'partials/footer.php';
    }
}
?>