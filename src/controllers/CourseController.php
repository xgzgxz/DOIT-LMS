<?php
/**
 * CourseController
 * Verantwortlich für die Anzeige von Kurs- und Lektionsdetails sowie die Verwaltung von Einschreibungen.
 */
class CourseController
{
    private $pdo;
    private $courseModel;
    private $quizModel;

    /**
     * Konstruktor. Initialisiert die Datenbankverbindung und lädt die benötigten Models.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        require_once __DIR__ . '/../models/Course.php';
        require_once __DIR__ . '/../models/Quiz.php';

        $this->courseModel = new Course($this->pdo);
        $this->quizModel = new Quiz($this->pdo);
    }

    /**
     * Zeigt die Detailseite eines Kurses inklusive aller Lektionen an.
     */
    public function showCourse($basePath, $viewsPath)
    {
        if (!isset($_GET['id'])) {
            $this->showError(400, 'Fehler: Keine Kurs-ID angegeben', $basePath, $viewsPath);
            return;
        }

        $courseId = (int)$_GET['id'];
        $course = $this->courseModel->getCourseById($courseId);

        if ($course) {
            $isLoggedIn = isset($_SESSION['user']);
            $isEnrolled = false;

            if ($isLoggedIn) {
                $userId = (int)$_SESSION['user']['user_id'];
                $isEnrolled = $this->courseModel->isUserEnrolled($userId, $courseId);
            }
            
            $lessons = $this->courseModel->getLessonsByCourseId($courseId);
            $allCourses = $this->courseModel->getAllCourses();

            require_once $viewsPath . 'partials/header.php';
            require_once $viewsPath . 'course.php';
            require_once $viewsPath . 'partials/footer.php';
        } else {
            $this->showError(404, '404 - Kurs nicht gefunden', $basePath, $viewsPath);
        }
    }

    /**
     * Zeigt eine einzelne Lektion an, prüft die Zugriffsberechtigung und lädt das Quiz.
     */
    public function showLesson($basePath, $viewsPath)
    {
        if (!isset($_GET['id'])) {
            $this->showError(400, 'Fehler: Keine Lektions-ID angegeben', $basePath, $viewsPath);
            return;
        }

        $lessonId = (int)$_GET['id'];
        $lesson = $this->courseModel->getLessonById($lessonId);

        if ($lesson) {
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

            $sidebarLessons = $this->courseModel->getLessonsByCourseId($courseId);
            $quizData = $this->quizModel->getQuizByLessonId($lessonId);
            $allCourses = $this->courseModel->getAllCourses();

            require_once $viewsPath . 'partials/header.php';
            require_once $viewsPath . 'lesson.php';
            echo '<script src="' . htmlspecialchars($basePath . '/js/quiz-handler.js') . '"></script>';
            require_once $viewsPath . 'partials/footer.php';
        } else {
            $this->showError(404, '404 - Lektion nicht gefunden', $basePath, $viewsPath);
        }
    }

    /**
     * Verarbeitet eine AJAX-Anfrage zur Überprüfung einer Quiz-Antwort.
     */
    public function checkAnswer()
    {
        header('Content-Type: application/json');
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true);

        if (isset($data['answer_id'])) {
            $answerId = (int)$data['answer_id'];
            $isCorrect = $this->quizModel->checkAnswerById($answerId);
            echo json_encode(['correct' => $isCorrect]);
        } else {
            echo json_encode(['correct' => false, 'error' => 'Keine Antwort-ID gesendet.']);
        }
        exit;
    }

    /**
     * Verarbeitet die Einschreibung eines Benutzers in einen Kurs.
     */
    public function enroll($basePath, $viewsPath)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . $basePath . '/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = (int)$_POST['course_id'];
            $userId = (int)$_SESSION['user']['user_id'];

            $success = $this->courseModel->enrollUserInCourse($userId, $courseId);

            if ($success) {
                header('Location: ' . $basePath . '/course?id=' . $courseId . '&status=enrolled');
            } else {
                header('Location: ' . $basePath . '/course?id=' . $courseId . '&status=error');
            }
            exit;
        } else {
            header('Location: ' . $basePath . '/');
            exit;
        }
    }

    /**
     * Zeigt eine generische Fehlerseite an.
     */
    private function showError($statusCode, $message, $basePath, $viewsPath)
    {
        http_response_code($statusCode);
        $allCourses = $this->courseModel->getAllCourses();
        require_once $viewsPath . 'partials/header.php';
        echo '<h1>' . $message . '</h1>';
        echo '<p><a href="' . htmlspecialchars($basePath) . '/">Zurück zur Startseite</a></p>';
        require_once $viewsPath . 'partials/footer.php';
    }

    /**
     * Zeigt die Suchergebnisse für Kurse an.
     */
    public function showSearchResults($basePath, $viewsPath)
    {
        $searchTerm = trim($_GET['query'] ?? '');
        $courses = [];

        if (!empty($searchTerm)) {
            $courses = $this->courseModel->searchCoursesByTerm($searchTerm);
        }

        $allCourses = $this->courseModel->getAllCourses();

        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'search_results.php';
        require_once $viewsPath . 'partials/footer.php';
    }

    /**
     * Zeigt die Seite "Meine Kurse" für den eingeloggten Benutzer an.
     */
    public function showMyCourses($basePath, $viewsPath)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . $basePath . '/login');
            exit;
        }

        $userId = (int)$_SESSION['user']['user_id'];
        $myCourses = $this->courseModel->getCoursesByUserId($userId);
        $allCourses = $this->courseModel->getAllCourses();

        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'my_courses.php';
        require_once $viewsPath . 'partials/footer.php';
    }
}
