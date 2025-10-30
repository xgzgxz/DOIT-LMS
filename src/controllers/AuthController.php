<?php
// Die Klasse AuthController kümmert sich um alles, was mit
// Authentifizierung (Benutzern und Rechten) zu tun hat: Login, Logout, Registrierung.

class AuthController
{
    private $pdo;
    private $userModel;
    private $courseModel;

    /**
     * Der Konstruktor.
     * Wir übergeben $pdo und laden DIREKT das User-Model,
     * da wir es in fast jeder Methode brauchen.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        // Wir laden das User-Model und erstellen eine Instanz
        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User($this->pdo);

        require_once __DIR__ . '/../models/Course.php';
        $this->courseModel = new Course($this->pdo);
    }

    /**
     * Kümmert sich um die Registrierung (Anzeige und Verarbeitung)
     */
    public function register($basePath, $viewsPath)
    {
        $errors = []; // Array für Fehlermeldungen

        // --- Logik für POST (Formular wurde gesendet) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Daten aus POST holen
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            // 2. Validierung
            if (empty($username)) {
                $errors[] = 'Benutzername ist erforderlich.';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Gültige E-Mail-Adresse ist erforderlich.';
            }
            if (strlen($password) < 8) {
                // Mindestlänge auf 8 erhöht
                $errors[] = 'Passwort muss mindestens 8 Zeichen lang sein.';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                // Prüft, ob ein Großbuchstabe (A-Z) enthalten ist 
                $errors[] = 'Passwort muss mindestens einen Großbuchstaben enthalten.';
            }
            if (!preg_match('/[0-9]/', $password)) {
                // Prüft, ob eine Ziffer (0-9) enthalten ist 
                $errors[] = 'Passwort muss mindestens eine Ziffer enthalten.';
            }
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                // Prüft auf ein Sonderzeichen (jedes Zeichen, das KEIN Buchstabe und KEINE Zahl ist) 
                $errors[] = 'Passwort muss mindestens ein Sonderzeichen enthalten.';
            }
            if ($password !== $passwordConfirm) {
                $errors[] = 'Die Passwörter stimmen nicht überein.';
            }

            // 3. Wenn KEINE Fehler aufgetreten sind...
            if (empty($errors)) {
                // ...versuche, den Benutzer zu erstellen (mit unserem Model)
                $result = $this->userModel->createUser($username, $email, $password);

                if ($result === true) {
                    // Erfolg! Weiterleiten zur Login-Seite mit Meldung
                    header('Location: ' . $basePath . '/login?status=registered');
                    exit;
                } else {
                    // Fehler von createUser() (z.B. "E-Mail vergeben")
                    $errors[] = $result;
                }
            }
        }

        // Lade alle Kurse für die Navbar 2, BEVOR der Header geladen wird
        $allCourses = $this->courseModel->getAllCourses();

        // --- Logik für GET (Seite wird normal aufgerufen) ---
        // (oder wenn POST-Validierung fehlgeschlagen ist)
        // Wir laden die View und übergeben ihr die $errors (ggf. leer)
        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'auth/register.php';
        require_once $viewsPath . 'partials/footer.php';
    }

    /**
     * Kümmert sich um den Login (Anzeige und Verarbeitung)
     */
    public function login($basePath, $viewsPath)
    {
        $errors = []; // Array für Fehlermeldungen

        // --- Logik für POST (Formular wurde gesendet) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $errors[] = 'E-Mail und Passwort sind erforderlich.';
            } else {
                // Model-Methode aufrufen
                $user = $this->userModel->loginUser($email, $password);

                if ($user) {
                    // ERFOLG!
                    // 1. Session-ID regenerieren (Schutz vor Session Fixation)
                    session_regenerate_id(true);

                    // 2. Benutzerdaten in die Session speichern
                    $_SESSION['user'] = $user;

                    // 3. Weiterleiten zur Startseite
                    header('Location: ' . $basePath . '/');
                    exit;
                } else {
                    // FEHLER
                    $errors[] = 'E-Mail oder Passwort ist ungültig.';
                }
            }
        }

        // Lade alle Kurse für die Navbar 2, BEVOR der Header geladen wird
        $allCourses = $this->courseModel->getAllCourses();

        // --- Logik für GET (Seite wird normal aufgerufen) ---
        require_once $viewsPath . 'partials/header.php';
        require_once $viewsPath . 'auth/login.php';
        require_once $viewsPath . 'partials/footer.php';
    }

    /**
     * Kümmert sich um den Logout
     */
   public function logout($basePath)
    {
            // Prüfen, ob überhaupt ein Benutzer eingeloggt ist
        if (isset($_SESSION['user'])) {
            
            // ---- FALL 1: Benutzer ist eingeloggt ----
            // Wir führen die normale Logout-Prozedur durch

            // 1. Session-Daten löschen
            session_unset();

            // 2. Session zerstören
            session_destroy();

            // 3. (Optional) Cookie löschen, falls verwendet
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            // 4. Zur Login-Seite MIT Meldung weiterleiten
            header('Location: ' . $basePath . '/login?status=logged_out');
            exit;

        } else {
            
            // ---- FALL 2: Benutzer ist NICHT eingeloggt (Gast) ----
            // Wir leiten einfach nur zur Login-Seite um,
            // OHNE die Session zu zerstören (gibt ja keine)
            // und OHNE die "Ausgeloggt"-Meldung.
            
            header('Location: ' . $basePath . '/login');
            exit;
        }
    }
}
?>