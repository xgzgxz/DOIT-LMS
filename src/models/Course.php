<?php
/**
 * Course Model
 * Verantwortlich für alle Datenbankoperationen, die Kurse, Lektionen und Einschreibungen betreffen.
 */
class Course
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ruft alle Kurse aus der Datenbank ab.
     * @return array Ein Array von Kursen.
     */
    public function getAllCourses()
    {
        $sql = "SELECT course_id, title, description FROM courses ORDER BY course_id";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Im Fehlerfall ein leeres Array zurückgeben, um die Anwendung nicht zu unterbrechen.
            return [];
        }
    }

    /**
     * Ruft einen einzelnen Kurs anhand seiner ID ab.
     * @param int $courseId Die ID des Kurses.
     * @return array|false Der Kurs als assoziatives Array oder false, wenn nicht gefunden.
     */
    public function getCourseById($courseId)
    {
        $sql = "SELECT course_id, title, description FROM courses WHERE course_id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$courseId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Ruft alle Lektionen für einen bestimmten Kurs ab.
     * @param int $courseId Die ID des Kurses.
     * @return array Ein Array von Lektionen.
     */
    public function getLessonsByCourseId($courseId)
    {
        $sql = "SELECT lesson_id, title FROM lessons WHERE course_id = ? ORDER BY lesson_id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$courseId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Ruft eine einzelne Lektion anhand ihrer ID ab.
     * @param int $lessonId Die ID der Lektion.
     * @return array|false Die Lektion als assoziatives Array oder false, wenn nicht gefunden.
     */
    public function getLessonById($lessonId)
    {
        $sql = "SELECT lesson_id, title, content, course_id FROM lessons WHERE lesson_id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$lessonId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Überprüft, ob ein Benutzer für einen Kurs eingeschrieben ist.
     * @param int $userId Die ID des Benutzers.
     * @param int $courseId Die ID des Kurses.
     * @return bool True, wenn der Benutzer eingeschrieben ist, sonst false.
     */
    public function isUserEnrolled($userId, $courseId)
    {
        $sql = "SELECT 1 FROM course_user WHERE user_id = ? AND course_id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $courseId]);
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Schreibt einen Benutzer in einen Kurs ein.
     * @param int $userId Die ID des Benutzers.
     * @param int $courseId Die ID des Kurses.
     * @return bool True bei Erfolg, false bei einem Fehler (z.B. Doppeleinschreibung).
     */
    public function enrollUserInCourse($userId, $courseId)
    {
        $sql = "INSERT INTO course_user (user_id, course_id) VALUES (?, ?)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $courseId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Ruft alle Kurse ab, für die ein Benutzer eingeschrieben ist.
     * @param int $userId Die ID des Benutzers.
     * @return array Ein Array der Kurse des Benutzers.
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Sucht Kurse, deren Titel oder Beschreibung einen bestimmten Begriff enthalten.
     * @param string $term Der Suchbegriff.
     * @return array Ein Array der gefundenen Kurse.
     */
    public function searchCoursesByTerm($term)
    {
        $searchTerm = '%' . $term . '%';
        $sql = "SELECT course_id, title, description 
                FROM courses 
                WHERE title LIKE ? OR description LIKE ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}