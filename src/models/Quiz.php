<?php
// Die Klasse 'Quiz' kümmert sich um alle Datenbank-Aktionen,
// die mit Quizzen zu tun haben.

class Quiz
{
    // Private Variable für die Datenbankverbindung
    private $pdo;

    // Der Konstruktor, der die $pdo-Verbindung entgegennimmt
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Holt die Quizfrage und die zugehörigen Antworten für eine Lektions-ID.
     *
     * @param int $lessonId Die ID der Lektion.
     * @return array|false Ein Array mit Frage und Antworten, oder false.
     */
    public function getQuizByLessonId($lessonId)
    {
        $sqlQuestion = "SELECT quizquestion_id, question_text FROM quiz_questions WHERE lesson_id = ?";
        
        try {
            $stmtQuestion = $this->pdo->prepare($sqlQuestion);
            $stmtQuestion->execute([$lessonId]);
            $question = $stmtQuestion->fetch(PDO::FETCH_ASSOC);

            if ($question) {
                // Frage gefunden, jetzt die Antworten holen.
                $quizQuestionId = $question['quizquestion_id'];
                $sqlAnswers = "SELECT quizanswer_id, answer_text, is_correct
                               FROM quiz_answers 
                               WHERE quizquestion_id = ?";

                $stmtAnswers = $this->pdo->prepare($sqlAnswers);
                $stmtAnswers->execute([$quizQuestionId]);
                $answers = $stmtAnswers->fetchAll(PDO::FETCH_ASSOC);

                // Frage und Antworten zusammenbauen und zurückgeben.
                return [
                    'question' => $question,
                    'answers' => $answers
                ];
            } else {
                // Keine Frage für diese Lektion gefunden.
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * Überprüft eine einzelne Antwort-ID auf ihre Korrektheit.
     *
     * @param int $answerId Die ID der ausgewählten Antwort.
     * @return bool True, wenn die Antwort korrekt ist, sonst false.
     */
    public function checkAnswerById($answerId)
    {
        $sql = "SELECT is_correct FROM quiz_answers WHERE quizanswer_id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$answerId]);
            // fetchColumn() holt nur den Wert der Spalte 'is_correct' (0 oder 1).
            $result = $stmt->fetchColumn();
            return (bool)$result;
        } catch (PDOException $e) {
            return false;
        }
    }
}