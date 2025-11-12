<?php
/**
 * Lesson Page View.
 *
 * This view displays the content of a single lesson, including its title and body.
 * It features a sidebar with a list of all lessons in the same course for easy navigation.
 * If a quiz is associated with the lesson, it is displayed at the bottom.
 *
 * @var int    $lessonId       The ID of the current lesson.
 * @var array  $lesson         Details of the current lesson.
 * @var array  $sidebarLessons All lessons in the current course for the sidebar.
 * @var array|false $quizData  Data for the quiz, or false if none exists.
 * @var string $basePath       The base path for URL generation.
 */
?>
<div class="lesson-layout">

    <aside class="sidebar lesson-sidebar">
        <h3>Lektionen</h3>
        <ul>
            <?php if (!empty($sidebarLessons)): ?>
                <?php foreach ($sidebarLessons as $sidebarLesson):
                    $lessonUrl = htmlspecialchars($basePath . '/lesson?id=' . $sidebarLesson['lesson_id']);
                    $lessonTitle = htmlspecialchars($sidebarLesson['title']);
                    // Determine if the sidebar item is the currently active lesson
                    $isActive = ($sidebarLesson['lesson_id'] == $lessonId);
                    $class = $isActive ? 'active' : '';
                ?>
                    <li class="<?php echo $class; ?>">
                        <a href="<?php echo $lessonUrl; ?>"><?php echo $lessonTitle; ?></a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </aside>

    <article class="lesson-content">
        <h1><?php echo htmlspecialchars($lesson['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($lesson['content'])); ?></p>
        
        <hr>

        <div id="quiz-container" data-check-url="<?php echo htmlspecialchars($basePath . '/check_answer'); ?>">
            <?php if ($quizData): ?>
                <h3>Quiz: <?php echo htmlspecialchars($quizData['question']['question_text']); ?></h3>
                
                <form id="quiz-form">
                    <ul class="quiz-answers">
                        <?php 
                        // Shuffle answers to present them in a random order
                        $shuffledAnswers = $quizData['answers'];
                        shuffle($shuffledAnswers);
                        
                        foreach ($shuffledAnswers as $answer):
                            $answerId = $answer['quizanswer_id'];
                            $answerText = htmlspecialchars($answer['answer_text']);
                        ?>
                            <li>
                                <input type="radio" name="quiz_answer" value="<?php echo $answerId; ?>" id="answer_<?php echo $answerId; ?>">
                                <label for="answer_<?php echo $answerId; ?>"> <?php echo $answerText; ?></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="submit">Antwort prüfen</button>
                </form>
                <div id="quiz-feedback"></div>

            <?php else: ?>
                <p>Für diese Lektion ist kein Quiz verfügbar.</p>
            <?php endif; ?>
        </div>
    </article>
</div>


