<?php
// Diese Datei ist die "View" für die Kurs-Detailseite.
// Sie bekommt vom CourseController die Variablen:
// $course (Details des aktuellen Kurses)
// $lessons (Lektionen des aktuellen Kurses)
// $allCourses (Alle Kurse für die Sidebar - NEU DANK NAVBAR 2)
// $isLoggedIn, $isEnrolled, $basePath
?>
<div class="lesson-layout">

    <aside class="sidebar">
        <h3>Alle Kurse</h3>
        <ul>
            <?php
            // Wir prüfen, ob $allCourses (vom Controller) Kurse enthält
            if (!empty($allCourses)) {

                // Wir durchlaufen alle Kurse
                foreach ($allCourses as $sidebarCourse) {
                    $courseUrl = htmlspecialchars($basePath . '/course?id=' . $sidebarCourse['course_id']);
                    $courseTitle = htmlspecialchars($sidebarCourse['title']);

                    // --- Aktiven Status prüfen ---
                    // Wir prüfen, ob der Kurs in der Schleife ($sidebarCourse)
                    // derselbe ist wie der Kurs, den wir gerade anzeigen ($course).
                    $isActive = ($sidebarCourse['course_id'] == $course['course_id']);
                    $class = $isActive ? 'active' : '';

                    // Wir geben das List-Item mit der (evtl. leeren) Klasse aus
                    echo '<li class="' . $class . '">';
                    echo '<a href="' . $courseUrl . '">' . $courseTitle . '</a>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
    </aside>

    <article class="lesson-content">

        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
        <?php

        if (isset($_GET['status']) && $_GET['status'] === 'enrolled') {
            // NEU: Wir nutzen unsere .form-message Klasse
            echo '<div class="form-message is-success">';
            echo '<strong>Du wurdest erfolgreich für diesen Kurs eingeschrieben!</strong>';
            echo '</div>';
        }
        if (isset($_GET['status']) && $_GET['status'] === 'error') {
            // NEU: Wir nutzen unsere .form-message Klasse
            echo '<div class="form-message is-error">';
            echo '<strong>Bei der Einschreibung ist ein Fehler aufgetreten.</strong>';
            echo '</div>';
        }

        // Jetzt die 3 Fälle anzeigen:
        if ($isLoggedIn) {
            if ($isEnrolled) {
                // Fall 1:

                // Wir prüfen, ob die 'status=enrolled' Meldung NICHT gesetzt ist.
                // Nur wenn sie NICHT gesetzt ist, zeigen wir die Standard-Meldung
                // "Du bist bereits eingeschrieben" an.
                // Das verhindert die doppelte Meldung direkt nach der Einschreibung.
                if (!isset($_GET['status']) || $_GET['status'] !== 'enrolled') {
                    echo '<p><strong>Du bist für diesen Kurs eingeschrieben.</strong></p>';
                }
            } else {
                // Fall 2: Eingeloggt, aber NICHT eingeschrieben
                // Wir zeigen den Button als Formular
        ?>
                <form action="<?php echo htmlspecialchars($basePath . '/enroll'); ?>" method="POST">
                    <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                    <button type="submit">Jetzt für diesen Kurs einschreiben</button>
                </form>
        <?php
            }
        } else {
            // Fall 3: Nicht eingeloggt
            $loginUrl = htmlspecialchars($basePath . '/login');
            echo '<p>Bitte <a href="' . $loginUrl . '">einloggen</a>, um dich für diesen Kurs einzuschreiben.</p>';
        }
        echo '<hr>';
        echo '<h2>Lektionen in diesem Kurs</h2>';

        if (!empty($lessons)) {
            echo '<ul>';
            foreach ($lessons as $lesson) {
                $lessonUrl = htmlspecialchars($basePath . '/lesson?id=' . $lesson['lesson_id']);
                $lessonTitle = htmlspecialchars($lesson['title']);
                echo '<li><a href="' . $lessonUrl . '">' . $lessonTitle . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Für diesen Kurs sind noch keine Lektionen verfügbar.</p>';
        }
        ?>

    </article>
</div>