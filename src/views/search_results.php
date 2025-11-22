<?php
/**
 * Ansicht für die Suchergebnisseite.
 *
 * Diese Ansicht zeigt die Ergebnisse der Suchanfrage eines Benutzers an. Sie zeigt ein
 * Raster von Kursen, die mit dem Suchbegriff übereinstimmen. Wenn keine Kurse gefunden
 * werden oder kein Suchbegriff angegeben wurde, wird eine entsprechende Meldung angezeigt.
 *
 * @var array  $courses    Ein Array von Kursen, die mit dem Suchbegriff übereinstimmen.
 * @var string $searchTerm Die ursprüngliche Suchanfrage des Benutzers.
 * @var string $basePath   Der Basispfad für die URL-Generierung.
 */

$safeTerm = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
?>

<h1>Suchergebnisse für "<?php echo $safeTerm; ?>"</h1>

<?php if (!empty($courses)): ?>
    <p>Folgende Kurse wurden gefunden:</p>
    
    <div class="course-catalog-grid">
        <?php foreach ($courses as $course):
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
    <?php if (!empty($searchTerm)): ?>
        <p>Leider wurden keine Kurse für den Begriff "<?php echo $safeTerm; ?>" gefunden.</p>
    <?php else: ?>
        <p>Bitte gib einen Suchbegriff in die Suchleiste ein.</p>
    <?php endif; ?>
<?php endif; ?>

<p><a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/">Zurück zur Startseite</a></p>