<?php
session_start();
//1. DB connection
require_once __DIR__ . '/../src/core/database.php';

//Lade unseren HomeController
require_once __DIR__ . '/../src/controllers/HomeController.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/CourseController.php';

$db = new Database();
$pdo = $db->getConnection();

$homeController = new HomeController($pdo);
$courseController = new CourseController($pdo);
$authController = new AuthController($pdo);

//wenn $pdo null ist, dann fehlerbehandlung fürs erste verlassen wir uns darauf, dass die Config richtig ist

// 2. Routing
// Wir ermitteln den "Basis-Pfad", falls das Projekt in einem Unterordner läuft
// holt sich den Pfad zur php datei, die gerade ausgeführt wird
$scriptName = $_SERVER['SCRIPT_NAME']; // z.B. /projektordner/public/index.php
// schneidet den dateinamen ab, um den basis-pfad zu erhalten  Aus /mein-projekt/public/index.php wird also /mein-projekt/public
$basePath = dirname($scriptName); // z.B. /projektordner/public

// Wenn das Projekt im Hauptverzeichnis (root) läuft, ist der basePath '/'
// In diesem Fall setzen wir ihn auf einen leeren String, damit er beim Suchen nicht stört
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}

// Wir holen die reine URL-Anfrage, z.B. /projekt/public/register
//$_SERVER['REQUEST_URI']: 
//Das ist die komplette Adresse, die der Benutzer in den Browser eingegeben hat, 
//z.B. /mein-projekt/public/login?user=123.
//parse_url(..., PHP_URL_PATH) entfernt dabei alles nach dem ?, also die Query-Parameter

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Wir entfernen den Basis-Pfad von der Anfrage, um den "sauberen" Pfad zu erhalten
// z.B. /register
$path = substr($requestUri, strlen($basePath));

// Fallback, falls der Pfad leer ist (passiert bei Aufruf von /): auf '/' setzen
if ($path === '' || $path === false) {
    $path = '/';
}

// Wir definieren die Basis-Pfade für unsere Views
$viewsPath = __DIR__ . '/../src/views/';

//3. switch Anweisung als Router

switch ($path) {
    case '/':
        // Wir übergeben die Arbeit an die 'index'-Methode
        // unseres Controllers und geben ihm die Pfade mit,
        // die er zum Laden der Views braucht.
        $homeController->index($basePath, $viewsPath);
        break;

    case '/register':
        $authController->register($basePath, $viewsPath);
        break;

    case '/logout':
        $authController->logout($basePath);
        break;

    case '/login':
        $authController->login($basePath, $viewsPath);
        break;
    case '/course':
        $courseController->showCourse($basePath, $viewsPath);
        break;

    case '/lesson':
        $courseController->showLesson($basePath, $viewsPath);
        break;
    case '/check_answer':
        // Diese Route ist speziell: Sie zeigt kein HTML,
        // sondern wird von der checkAnswer-Methode beendet (mit exit;).
        $courseController->checkAnswer();
        break;

    // Einschreibung
    case '/enroll':
        $courseController->enroll($basePath, $viewsPath);
        break;

    case '/search':
        $courseController->showSearchResults($basePath, $viewsPath);
        break;
    case '/my-courses':
        $courseController->showMyCourses($basePath, $viewsPath);
        break;

    case '/impressum':
        // Ruft die showImpressum-Methode im HomeController auf
        $homeController->showImpressum($basePath, $viewsPath);
        break;

    case '/datenschutz':
        // Ruft die showDatenschutz-Methode im HomeController auf
        $homeController->showDatenschutz($basePath, $viewsPath);
        break;

    default:
        // 404 - Seite nicht gefunden
        http_response_code(404);
        require_once $viewsPath . 'partials/header.php';
        echo '<h1>404 - Seite nicht gefunden</h1>';
        require_once $viewsPath . 'partials/footer.php';
        break;
}
