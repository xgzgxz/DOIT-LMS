<?php
/**
 * Ansicht für die Seite "Meine Kurse".
 *
 * Diese Ansicht zeigt ein Raster aller Kurse an, für die der aktuell eingeloggte
 * Benutzer eingeschrieben ist. Wenn der Benutzer für keine Kurse eingeschrieben ist,
 * wird er aufgefordert, den Kurskatalog zu durchsuchen.
 *
 * @var array  $myCourses Ein Array der Kurse, für die der Benutzer eingeschrieben ist.
 * @var string $basePath  Der Basispfad für die URL-Generierung.
 */
?>
<h1>Meine Kurse</h1>

<?php if (!empty($myCourses)): ?>
    <div class="course-catalog-grid">
        <?php foreach ($myCourses as $course):
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
    <p>Du bist noch für keine Kurse eingeschrieben.</p>
    <p><a href="<?php echo htmlspecialchars($basePath . '/', ENT_QUOTES, 'UTF-8'); ?>">Stöbere jetzt im Kurskatalog!</a></p>
<?php endif; ?>