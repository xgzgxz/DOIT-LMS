<?php
/**
 * AuthController
 * Verantwortlich für die Authentifizierung: Registrierung, Login und Logout.
 */
class AuthController
{
    private $pdo;
    private $userModel;
    private $courseModel;

    /**
     * Konstruktor. Initialisiert die Datenbankverbindung und lädt die benötigten Models.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User($this->pdo);

        require_once __DIR__ . '/../models/Course.php';
        $this->courseModel = new Course($this->pdo);
    }

    /**
     * Zeigt das Registrierungsformular an und verarbeitet die Eingaben.
     */
    public function register($basePath, $viewsPath): void
    {
        $errors = [];

        // Verarbeitet die Formulardaten bei einer POST-Anfrage.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            // Validierung der Eingaben
            if (empty($username)) {
                $errors[] = 'Benutzername ist erforderlich.';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Gültige E-Mail-Adresse ist erforderlich.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Passwort muss mindestens 8 Zeichen lang sein.';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'Passwort muss mindestens einen Großbuchstaben enthalten.';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'Passwort muss mindestens eine Ziffer enthalten.';
            }
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                // Prüft auf ein Sonderzeichen.
                $errors[] = 'Passwort muss mindestens ein Sonderzeichen enthalten.';
            }
            if ($password !== $passwordConfirm) {
                $errors[] = 'Die Passwörter stimmen nicht überein.';
            }

            // Wenn die Validierung erfolgreich war, wird der Benutzer erstellt.
            if (empty($errors)) {
                $result = $this->userModel->createUser($username, $email, $password);

                if ($result === true) {
                    // Erfolgreich erstellt, Weiterleitung zur Login-Seite.
                    header('Location: ' . $basePath . '/login?status=registered');
                    exit;
                } else {
                    // Fehler bei der Erstellung (z.B. E-Mail bereits vergeben).
                    $errors[] = $result;
                }
            }
        }

        // Lädt alle Kurse für die Navigation, bevor der Header gerendert wird.
        $allCourses = $this->courseModel->getAllCourses();

        // Rendert die Registrierungs-View.
        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'auth/register.php';
        require_once $viewsPath . 'partials/footer.php';
    }

    /**
     * Zeigt das Login-Formular an und verarbeitet die Anmeldedaten.
     */
    public function login($basePath, $viewsPath)
    {
        $errors = [];

        // Verarbeitet die Formulardaten bei einer POST-Anfrage.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $errors[] = 'E-Mail und Passwort sind erforderlich.';
            } else {
                $user = $this->userModel->loginUser($email, $password);

                if ($user) {
                    // Session-ID zum Schutz vor Session Fixation regenerieren.
                    session_regenerate_id(true);

                    // Benutzerdaten in der Session speichern.
                    $_SESSION['user'] = $user;

                    // Weiterleitung zum Dashboard/Startseite.
                    header('Location: ' . $basePath . '/');
                    exit;
                } else {
                    $errors[] = 'E-Mail oder Passwort ist ungültig.';
                }
            }
        }

        // Lädt alle Kurse für die Navigation, bevor der Header gerendert wird.
        $allCourses = $this->courseModel->getAllCourses();

        // Rendert die Login-View.
        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'auth/login.php';
        require_once $viewsPath . 'partials/footer.php';
    }

    /**
     * Loggt den aktuellen Benutzer aus und beendet die Session.
     */
   public function logout($basePath)
    {
        if (isset($_SESSION['user'])) {
            // Session-Daten und -Cookie löschen.
            session_unset();
            session_destroy();

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            // Weiterleitung zur Login-Seite mit Statusmeldung.
            header('Location: ' . $basePath . '/login?status=logged_out');
            exit;

        } else {
            // Wenn kein Benutzer eingeloggt ist, einfach zur Login-Seite weiterleiten.
            header('Location: ' . $basePath . '/login');
            exit;
        }
    }
}
?>