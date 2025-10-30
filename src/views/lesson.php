   <div class="lesson-layout">

                <aside class="sidebar lesson-sidebar">
                    <h3>Lektionen</h3>
                    <ul>
                        <?php
                        // 1. SIDEBAR-SCHLEIFE (Die fehlte)
                        if (!empty($sidebarLessons)) {
                            foreach ($sidebarLessons as $sidebarLesson) {
                                $lessonUrl = htmlspecialchars($basePath . '/lesson?id=' . $sidebarLesson['lesson_id']);
                                $lessonTitle = htmlspecialchars($sidebarLesson['title']);
                                // Prüfen, ob dies die aktive Lektion ist
                                $isActive = ($sidebarLesson['lesson_id'] == $lessonId);
                                $class = $isActive ? 'active' : '';

                                echo '<li class="' . $class . '">';
                                echo '<a href="' . $lessonUrl . '">' . $lessonTitle . '</a>';
                                echo '</li>';
                            }
                        }
                        ?>
                    </ul>
                </aside>

                <article class="lesson-content">
                    <?php
                    // 2. LEKTIONS-INHALT (Der fehlte)
                    echo '<h1>' . htmlspecialchars($lesson['title']) . '</h1>';
                    // nl2br wandelt Zeilenumbrüche (Enter) in <br>-Tags um
                    echo nl2br(htmlspecialchars($lesson['content']));
                    ?>
                    
                    <hr>

                    <div id="quiz-container" data-check-url="<?php echo htmlspecialchars($basePath . '/check_answer'); ?>">
                        <?php
                        // 3. QUIZ-ANZEIGE (Die fehlte)
                        if ($quizData) {
                            // Quiz gefunden! Zeige die Frage
                            echo '<h3>Quiz: ' . htmlspecialchars($quizData['question']['question_text']) . '</h3>';
                            
                            echo '<form id="quiz-form">';
                            echo '<ul class="quiz-answers">';
                            
                            shuffle($quizData['answers']);
                            
                            foreach ($quizData['answers'] as $answer) {
                                $answerId = $answer['quizanswer_id'];
                                $answerText = htmlspecialchars($answer['answer_text']);
                                
                                echo '<li>';
                                echo '<input type="radio" name="quiz_answer" value="' . $answerId . '" id="answer_' . $answerId . '">';
                                echo '<label for="answer_' . $answerId . '"> ' . $answerText . '</label>';
                                echo '</li>';
                            }
                            
                            echo '</ul>';
                            echo '<button type="submit">Antwort prüfen</button>';
                            echo '</form>';
                            echo '<div id="quiz-feedback"></div>';

                        } else {
                            // Kein Quiz für diese Lektion gefunden
                            echo '<p>Für diese Lektion ist kein Quiz verfügbar.</p>';
                        }
                        ?>
                    </div>
                </article>
            </div>


