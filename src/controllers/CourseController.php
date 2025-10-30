<?php
// Der CourseController kümmert sich um die Anzeige
// von Kurs-Details und Lektions-Details (inkl. Quiz).

class CourseController
{
    private $pdo;
    private $courseModel; // Speicher für das Course-Model
    private $quizModel;   // Speicher für das Quiz-Model

    /**
     * Der Konstruktor.
     * Wir laden hier BEIDE Models (Course und Quiz),
     * die wir für die Methoden brauchen werden.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        // Models laden
        require_once __DIR__ . '/../models/Course.php';
        require_once __DIR__ . '/../models/Quiz.php';

        // Models instanziieren und speichern
        $this->courseModel = new Course($this->pdo);
        $this->quizModel = new Quiz($this->pdo);
    }

    /**
     * Kümmert sich um die /course Seite (Details + Lektionsliste)
     */
    public function showCourse($basePath, $viewsPath)
    {
        // 1. ID holen
        if (!isset($_GET['id'])) {
            $this->showError(400, 'Fehler: Keine Kurs-ID angegeben', $basePath, $viewsPath);
            return;
        }

        $courseId = (int)$_GET['id'];

        // 2. Daten holen (mit $this->courseModel statt neuem Objekt)
        $course = $this->courseModel->getCourseById($courseId);

        // 3. Prüfen, ob Kurs existiert
        if ($course) {
            // JA! Kurs gefunden
            // --- START: NEUE LOGIK FÜR EINSCHREIBUNG ---

            $isLoggedIn = isset($_SESSION['user']);
            $isEnrolled = false; // Standardmäßig annehmen: nicht eingeschrieben

            // Nur wenn der User eingeloggt ist, prüfen wir, ob er
            // für DIESEN Kurs schon eingeschrieben ist.
            if ($isLoggedIn) {
                $userId = (int)$_SESSION['user']['user_id'];
                $isEnrolled = $this->courseModel->isUserEnrolled($userId, $courseId);
            }
            // 4. Lektionen holen
            $lessons = $this->courseModel->getLessonsByCourseId($courseId);

            // Lade alle Kurse für die Navbar 2
            $allCourses = $this->courseModel->getAllCourses();

            // 5. View laden
            require_once $viewsPath . 'partials/header.php';

            require_once $viewsPath . 'course.php';

            require_once $viewsPath . 'partials/footer.php';
        } else {
            // NEIN! Kurs nicht gefunden
            $this->showError(404, '404 - Kurs nicht gefunden', $basePath, $viewsPath);
        }
    }

    /**
     * Kümmert sich um die /lesson Seite (Inhalt + Sidebar + Quiz)
     * Zeigt die Lektionsdetails an, prüft die Zugriffsberechtigung
     * und lädt das zugehörige Quiz.
     */
    public function showLesson($basePath, $viewsPath)
    {
        // 1. ID holen (unverändert)
        if (!isset($_GET['id'])) {
            $this->showError(400, 'Fehler: Keine Lektions-ID angegeben', $basePath, $viewsPath);
            return;
        }

        $lessonId = (int)$_GET['id'];

        // 2. Lektion holen
        $lesson = $this->courseModel->getLessonById($lessonId);

        // 3. Prüfen, ob Lektion existiert
        if ($lesson) {
            // JA! Lektion gefunden

            // 4. Zugriffskontrolle
            $courseId = $lesson['course_id'];
            $isLoggedIn = isset($_SESSION['user']);
            $isEnrolled = false;

            if ($isLoggedIn) {
                $userId = (int)$_SESSION['user']['user_id'];
                $isEnrolled = $this->courseModel->isUserEnrolled($userId, $courseId);
            }

            if (!$isEnrolled) {
                $loginUrl = htmlspecialchars($basePath . '/login');
                $courseUrl = htmlspecialchars($basePath . '/course?id=' . $courseId);
                $errorMessage = 'Zugriff verweigert. ';

                if ($isLoggedIn) {
                    $errorMessage .= 'Du bist für diesen Kurs nicht eingeschrieben. <br><a href="' . $courseUrl . '">Zurück zur Kurs-Übersicht</a>.';
                } else {
                    $errorMessage .= 'Du musst <a href="' . $loginUrl . '">eingeloggt</a> sein, um diese Lektion zu sehen.';
                }

                $this->showError(403, $errorMessage, $basePath, $viewsPath);
                return;
            }

            // 5. Sidebar-Lektionen holen
            $sidebarLessons = $this->courseModel->getLessonsByCourseId($courseId);

            // 6. Quiz-Daten holen
            $quizData = $this->quizModel->getQuizByLessonId($lessonId);

            // Lade alle Kurse für die Navbar 2
            $allCourses = $this->courseModel->getAllCourses();

            // 7. View laden
            require_once $viewsPath . 'partials/header.php';

            require_once $viewsPath . 'lesson.php';

            // Das Quiz-Skript muss NACH dem HTML, aber VOR dem Footer geladen werden
            echo '<script src="' . htmlspecialchars($basePath . '/js/quiz-handler.js') . '"></script>';
            require_once $viewsPath . 'partials/footer.php';
        } else {
            // NEIN! Lektion nicht gefunden
            $this->showError(404, '404 - Lektion nicht gefunden', $basePath, $viewsPath);
        }
    }

    /**
     * Bearbeitet die AJAX-Anfrage vom Quiz-Handler.
     * Liest JSON, prüft die Antwort und gibt JSON zurück.
     */
    public function checkAnswer()
    {
        // 1. Sicherstellen, dass wir JSON zurücksenden
        header('Content-Type: application/json');

        // 2. Die rohen POST-Daten (den JSON-String) lesen
        $jsonInput = file_get_contents('php://input');

        // 3. Den JSON-String in ein PHP-Array umwandeln
        //    Der 'true' Parameter sorgt dafür, dass wir ein Array bekommen,
        //    kein stdClass-Objekt.
        $data = json_decode($jsonInput, true);

        // 4. Prüfen, ob die answer_id da ist
        if (isset($data['answer_id'])) {
            $answerId = (int)$data['answer_id'];

            // 5. Das Quiz-Model (das im Konstruktor geladen wurde) nutzen,
            //    um die Antwort zu prüfen. Das gibt true or false zurück.
            $isCorrect = $this->quizModel->checkAnswerById($answerId);

            // 6. Eine JSON-Antwort erstellen und senden
            echo json_encode(['correct' => $isCorrect]);
        } else {
            // 7. Fehler, falls keine ID gesendet wurde
            //    Wir senden 'false', damit das Quiz nicht blockiert.
            echo json_encode(['correct' => false, 'error' => 'Keine Antwort-ID gesendet.']);
        }

        // WICHTIG: 'exit' nach einer AJAX-Antwort,
        // damit kein HTML (z.B. vom Footer) mitgesendet wird.
        exit;
    }
    /**
     * Kümmert sich um die Einschreibung (den POST-Request vom Button)
     */
    public function enroll($basePath, $viewsPath)
    {
        // 1. Sicherheit: Ist der Benutzer überhaupt eingeloggt?
        if (!isset($_SESSION['user'])) {
            // Wenn nicht, sofort zum Login schicken
            header('Location: ' . $basePath . '/login');
            exit;
        }

        // 2. Sicherheit: Nur POST-Requests erlauben
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 3. Daten holen
            $courseId = (int)$_POST['course_id'];
            $userId = (int)$_SESSION['user']['user_id'];

            // 4. Model-Funktion aufrufen
            $success = $this->courseModel->enrollUserInCourse($userId, $courseId);

            // 5. Nach der Aktion zurück zur Kurs-Seite leiten.
            if ($success) {
                header('Location: ' . $basePath . '/course?id=' . $courseId . '&status=enrolled');
            } else {
                // Bei Fehler auch zurück, aber mit Fehlermeldung
                header('Location: ' . $basePath . '/course?id=' . $courseId . '&status=error');
            }
            exit;
        } else {
            // Kein POST? Zurück zur Startseite.
            header('Location: ' . $basePath . '/');
            exit;
        }
    }

    /**
     * Eine private Hilfsfunktion, um Fehlerseiten anzuzeigen
     */
    private function showError($statusCode, $message, $basePath, $viewsPath)
    {
        http_response_code($statusCode);
        // Lade alle Kurse für die Navbar 2
        $allCourses = $this->courseModel->getAllCourses();
        require_once $viewsPath . 'partials/header.php';
        echo '<h1>' . $message . '</h1>';
        echo '<p><a href="' . htmlspecialchars($basePath) . '/">Zurück zur Startseite</a></p>';
        require_once $viewsPath . 'partials/footer.php';
    }
    /**
     * Kümmert sich um die /search Seite (Suchergebnisse)
     */
    public function showSearchResults($basePath, $viewsPath)
    {
        // 1. Den Suchbegriff aus der URL holen (z.B. /search?query=php)
        //    Wir nutzen trim(), um Leerzeichen am Anfang/Ende zu entfernen.
        $searchTerm = trim($_GET['query'] ?? '');

        // 2. Die Variable für die Ergebnisse vorbereiten
        $courses = [];

       // 3. Nur wenn der Suchbegriff nicht leer ist, die Suche ausführen.
        if (!empty($searchTerm)) {
            $courses = $this->courseModel->searchCoursesByTerm($searchTerm);
        }

        // Lade alle Kurse für die Navbar 2
        $allCourses = $this->courseModel->getAllCourses();

        // 4. Die View laden und die Ergebnisse übergeben.
        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'search_results.php';
        require_once $viewsPath . 'partials/footer.php';
    }
    /**
     * Zeigt die "Meine Kurse"-Seite für eingeloggte Benutzer an.
     */
    public function showMyCourses($basePath, $viewsPath)
    {
        // 1. Sicherheit: Ist der Benutzer überhaupt eingeloggt?
        if (!isset($_SESSION['user'])) {
            // Wenn nicht, sofort zum Login schicken
            header('Location: ' . $basePath . '/login');
            exit;
        }

        // 2. Benutzer-ID holen
        $userId = (int)$_SESSION['user']['user_id'];

        // 3. Model-Funktion aufrufen
        $myCourses = $this->courseModel->getCoursesByUserId($userId);
        $allCourses = $this->courseModel->getAllCourses();

        // 4. Die View-Datei laden und die Kursliste übergeben
        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'my_courses.php';
        require_once $viewsPath . 'partials/footer.php';
    }
}
