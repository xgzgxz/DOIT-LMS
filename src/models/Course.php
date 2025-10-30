<?php

// Die Klasse 'Course' kümmert sich um alle Datenbank-Aktionen,
// die mit Kursen zu tun haben.
class Course
{
    // Private Variable für die Datenbankverbindung
    private $pdo;

    // Der Konstruktor, der die $pdo-Verbindung entgegennimmt
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Holt ALLE Kurse aus der Datenbank.
     * @return array Ein Array mit allen Kurs-Datensätzen.
     */
    public function getAllCourses()
    {
        $sql = "SELECT course_id, title, description FROM courses ORDER BY course_id";

        try {
            // Da keine Benutzereingaben verwendet werden, kann 'query()' genutzt werden.
            $stmt = $this->pdo->query($sql);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $courses;
        } catch (PDOException $e) {
            // Bei einem Fehler geben wir ein leeres Array zurück, damit die Seite nicht abstürzt.
            return [];
        }
    }
    /**
     * Holt einen einzelnen Kurs anhand seiner ID aus der Datenbank.
     *
     * @param int $courseId Die ID des Kurses, der gesucht wird.
     * @return array|false Der Kurs-Datensatz oder false, wenn nicht gefunden.
     */
    public function getCourseById($courseId)
    {
        $sql = "SELECT course_id, title, description FROM courses WHERE course_id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$courseId]);
            // fetch() wird verwendet, da nur eine Zeile erwartet wird.
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            return $course;
        } catch (PDOException $e) {
            // Bei einem Datenbankfehler 'false' zurückgeben.
            return false;
        }
    }

    /**
     * Holt alle Lektionen, die zu einer bestimmten Kurs-ID gehören.
     * @param int $courseId Die ID des Kurses, dessen Lektionen gesucht werden.
     * @return array Ein Array mit allen Lektions-Datensätzen (oder ein leeres Array).
     */

    public function getLessonsByCourseId($courseId)
    {
        $sql = "SELECT lesson_id, title FROM lessons WHERE course_id = ? ORDER BY lesson_id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$courseId]);
            // fetchAll() gibt bei keinen Treffern ein leeres Array zurück.
            $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $lessons;
        } catch (PDOException $e) {
            // Bei einem DB-Fehler ein leeres Array zurückgeben.
            return [];
        }
    }

    /**
     * Holt eine einzelne Lektion anhand ihrer ID aus der Datenbank.
     * @param int $lessonId Die ID der Lektion, die gesucht wird.
     * @return array|false Die Lektion als Array oder false, wenn nicht gefunden.
     */
    public function getLessonById($lessonId)
    {
        $sql = "SELECT lesson_id, title, content, course_id FROM lessons WHERE lesson_id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$lessonId]);
            $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
            return $lesson;
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * Prüft, ob ein Benutzer bereits für einen Kurs eingeschrieben ist.
     *
     * @param int $userId Die ID des Benutzers.
     * @param int $courseId Die ID des Kurses.
     * @return bool True, wenn eingeschrieben, sonst false.
     */
    public function isUserEnrolled($userId, $courseId)
    {
        $sql = "SELECT 1 FROM course_user WHERE user_id = ? AND course_id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $courseId]);
            // fetchColumn() gibt den Wert der ersten Spalte oder false zurück.
            $isEnrolled = $stmt->fetchColumn();
            return (bool)$isEnrolled;
        } catch (PDOException $e) {
            return false; // Im Fehlerfall sicherheitshalber 'false' annehmen
        }
    }

    /**
     * Schreibt einen Benutzer für einen Kurs ein (trägt ihn in course_user ein).
     *
     * @param int $userId Die ID des Benutzers.
     * @param int $courseId Die ID des Kurses.
     * @return bool True bei Erfolg, false bei Fehler.
     */
    public function enrollUserInCourse($userId, $courseId)
    {
        $sql = "INSERT INTO course_user (user_id, course_id) VALUES (?, ?)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $courseId]);
            return true;
        } catch (PDOException $e) {
            // Fehler, z.B. bei doppelter Einschreibung (Duplicate entry).
            return false;
        }
    }
    /**
     * Holt alle Kurse, für die ein bestimmter Benutzer eingeschrieben ist.
     *
     * @param int $userId Die ID des Benutzers.
     * @return array Ein Array mit den Kurs-Datensätzen.
     */
    public function getCoursesByUserId($userId)
    {
        $sql = "SELECT c.course_id, c.title, c.description 
                FROM courses c
                JOIN course_user cu ON c.course_id = cu.course_id
                WHERE cu.user_id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            $userCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $userCourses;
        } catch (PDOException $e) {
            return []; // Leeres Array bei Fehler
        }
    }
    /**
     * Durchsucht Kurse nach einem Suchbegriff in Titel ODER Beschreibung.
     *
     * @param string $term Der Suchbegriff.
     * @return array Ein Array mit den gefundenen Kursen.
     */
    public function searchCoursesByTerm($term)
    {
        // Wildcards (%) für die LIKE-Suche hinzufügen.
        $searchTerm = '%' . $term . '%';
        $sql = "SELECT course_id, title, description 
                FROM courses 
                WHERE title LIKE ? OR description LIKE ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            // Den Suchbegriff für beide Platzhalter übergeben.
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return []; // Leeres Array bei Fehler
        }
    }
}