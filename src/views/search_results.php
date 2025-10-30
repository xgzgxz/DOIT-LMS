<?php
// $courses und $searchTerm kommen vom CourseController

// Wir müssen den Suchbegriff "entschärfen", bevor wir ihn
// auf der Seite ausgeben, um XSS-Angriffe zu verhindern.
$safeTerm = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
?>

<h1>Suchergebnisse für "<?php echo $safeTerm; ?>"</h1>

<?php
if (!empty($courses)) {
    // ---- Fall 1: Kurse wurden gefunden ----
    echo '<p>Folgende Kurse wurden gefunden:</p>';
    
    // --- START: NEUES KARTEN-GRID (wie in home.php) ---
    echo '<div class="course-catalog-grid">';

    foreach ($courses as $course) {
        $courseUrl = htmlspecialchars($basePath . '/course?id=' . $course['course_id']);
        $courseTitle = htmlspecialchars($course['title']);
        
        // Beschreibung kürzen
        $description = htmlspecialchars($course['description']);
        if (strlen($description) > 150) {
            $lastSpace = strrpos(substr($description, 0, 150), ' ');
            $description = ($lastSpace !== false) ? substr($description, 0, $lastSpace) . '...' : substr($description, 0, 150) . '...';
        }
        
        // Karten-Struktur
        echo '<article class="course-card">';
        echo '<div class="course-card-content">';
        echo '<h3>' . $courseTitle . '</h3>'; 
        echo '<p>' . nl2br($description) . '</p>';
        echo '</div>'; 
        echo '<div class="course-card-actions">';
        echo '<a href="' . $courseUrl . '" class="button-link">Zum Kurs</a>';
        echo '</div>'; 
        echo '</article>';
    }
    
    echo '</div>'; // Ende .course-catalog-grid
    // --- ENDE: NEUES KARTEN-GRID ---
    
} else {
    // ---- Fall 2: Keine Kurse gefunden ----
    if (!empty($searchTerm)) {
        echo '<p>Leider wurden keine Kurse für den Begriff "' . $safeTerm . '" gefunden.</p>';
    } else {
        echo '<p>Bitte gib einen Suchbegriff in die Suchleiste ein.</p>';
    }
}
?>

<p><a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/">Zurück zur Startseite</a></p>