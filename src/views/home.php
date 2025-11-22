<?php
/**
 * Ansicht für die Startseite.
 *
 * Diese Ansicht zeigt eine personalisierte Willkommensnachricht für eingeloggte Benutzer
 * oder eine allgemeine Begrüßung für Gäste an. Sie zeigt auch einen Katalog aller verfügbaren Kurse.
 *
 * @var array $allCourses Ein Array aller aus der Datenbank abgerufenen Kurse.
 * @var string $basePath Der Basispfad für die URL-Generierung.
 */

if (isset($_SESSION['user'])): ?>
    <h1>Willkommen zurück, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</h1>
    <p>Stöbere in unserem Kurskatalog oder gehe zu "Meine Kurse", um weiterzulernen.</p>
<?php else: ?>
    <h1>Willkommen auf PinguWI - unserer interaktiven Lernplattform</h1>
    <p>Bitte <a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/register">registriere dich</a> oder
        <a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/login">logge dich ein</a>, um mit dem Lernen zu beginnen.
    </p>
<?php endif; ?>

<hr>
<h2>Alle verfügbaren Kurse</h2>

<?php if (!empty($allCourses)): ?>
    <div class="course-catalog-grid">
        <?php foreach ($allCourses as $course):
            $courseUrl = htmlspecialchars($basePath . '/course?id=' . $course['course_id']);
            $courseTitle = htmlspecialchars($course['title']);

            // Beschreibung für die Vorschau kürzen
            $description = htmlspecialchars($course['description']);
            if (strlen($description) > 150) {
                $lastSpace = strrpos(substr($description, 0, 150), ' ');
                $description = ($lastSpace !== false) 
                    ? substr($description, 0, $lastSpace) . '...'
                    : substr($description, 0, 150) . '...';
            }
        ?>
            <article class="course-card">
                <div class="course-card-content">
                    <h3><?php echo $courseTitle; ?></h3>
                    <p><?php echo nl2br($description); ?></p>
                </div>
                <div class="course-card-actions">
                    <a href="<?php echo $courseUrl; ?>" class="button-link">Zum Kurs</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Aktuell sind leider keine Kurse verfügbar.</p>
<?php endif; ?>