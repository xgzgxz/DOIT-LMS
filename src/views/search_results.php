<?php
/**
 * Search Results Page View.
 *
 * This view displays the results of a user's search query. It shows a grid
 * of courses that match the search term. If no courses are found, or if no
 * search term was provided, it displays an appropriate message.
 *
 * @var array  $courses    An array of courses matching the search term.
 * @var string $searchTerm The user's original search query.
 * @var string $basePath   The base path for URL generation.
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
            
            // Truncate description for preview
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