<?php

/**
 * Manages all database interactions related to quizzes.
 */
class Quiz
{
    /**
     * @var PDO The database connection instance.
     */
    private $pdo;

    /**
     * Constructor for the Quiz model.
     *
     * @param PDO $pdo An active PDO database connection.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Fetches the quiz question and its corresponding answers for a given lesson ID.
     *
     * @param int $lessonId The ID of the lesson.
     * @return array|false An array containing the question and answers, or false if not found.
     */
    public function getQuizByLessonId($lessonId)
    {
        $sqlQuestion = "SELECT quizquestion_id, question_text FROM quiz_questions WHERE lesson_id = ?";
        
        try {
            $stmtQuestion = $this->pdo->prepare($sqlQuestion);
            $stmtQuestion->execute([$lessonId]);
            $question = $stmtQuestion->fetch(PDO::FETCH_ASSOC);

            if ($question) {
                $quizQuestionId = $question['quizquestion_id'];
                $sqlAnswers = "SELECT quizanswer_id, answer_text, is_correct
                               FROM quiz_answers 
                               WHERE quizquestion_id = ?";

                $stmtAnswers = $this->pdo->prepare($sqlAnswers);
                $stmtAnswers->execute([$quizQuestionId]);
                $answers = $stmtAnswers->fetchAll(PDO::FETCH_ASSOC);

                return [
                    'question' => $question,
                    'answers' => $answers
                ];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // In a real application, log the exception.
            return false;
        }
    }
    
    /**
     * Checks if a given answer ID corresponds to a correct answer.
     *
     * @param int $answerId The ID of the selected answer.
     * @return bool True if the answer is correct, otherwise false.
     */
    public function checkAnswerById($answerId)
    {
        $sql = "SELECT is_correct FROM quiz_answers WHERE quizanswer_id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$answerId]);
            $result = $stmt->fetchColumn();
            return (bool)$result;
        } catch (PDOException $e) {
            // In a real application, log the exception.
            return false;
        }
    }
}