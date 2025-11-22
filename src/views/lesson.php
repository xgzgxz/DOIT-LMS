<?php
/**
 * Ansicht für eine Lektionsseite.
 *
 * Diese Ansicht zeigt den Inhalt einer einzelnen Lektion an, einschließlich Titel und Text.
 * Sie verfügt über eine Seitenleiste mit einer Liste aller Lektionen desselben Kurses zur einfachen Navigation.
 * Wenn der Lektion ein Quiz zugeordnet ist, wird dieses am Ende angezeigt.
 *
 * @var int    $lessonId       Die ID der aktuellen Lektion.
 * @var array  $lesson         Details der aktuellen Lektion.
 * @var array  $sidebarLessons Alle Lektionen des aktuellen Kurses für die Seitenleiste.
 * @var array|false $quizData  Daten für das Quiz, oder false, falls keines existiert.
 * @var string $basePath       Der Basispfad für die URL-Generierung.
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
                    // Prüfen, ob der Seitenleisten-Eintrag die aktuell aktive Lektion ist
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
                        // Antworten mischen, um sie in zufälliger Reihenfolge darzustellen
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


