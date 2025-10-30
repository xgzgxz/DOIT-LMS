<?php
// $allCourses (alle Kurse) kommt jetzt IMMER vom HomeController

if (isset($_SESSION['user'])) {
    // ---- Fall 1: Benutzer IST eingeloggt ----
    // Wir zeigen einen personalisierten Willkommensgruß
?>
    <h1>Willkommen zurück, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</h1>
    <p>Stöbere in unserem Kurskatalog oder gehe zu "Meine Kurse", um weiterzulernen.</p>

<?php
} else {
    // ---- Fall 2: Benutzer ist NICHT eingeloggt (Gast) ----
?>
    <h1>Willkommen auf unserem LMS!</h1>
    <p>Bitte <a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/register">registriere dich</a> oder
        <a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/login">logge dich ein</a>, um mit dem Lernen zu beginnen.
    </p>
<?php
}
?>

<hr>
<h2>Alle verfügbaren Kurse</h2>

<?php
// Dieser Teil (der Katalog) wird jetzt IMMER angezeigt
if (!empty($allCourses)) {
    
    // --- START: ÄNDERUNG ---
    // Wir ersetzen <ul> durch unser neues Grid-Container-Div
    echo '<div class="course-catalog-grid">';

    foreach ($allCourses as $course) {
        $courseUrl = htmlspecialchars($basePath . '/course?id=' . $course['course_id']);
        $courseTitle = htmlspecialchars($course['title']);
        
        // NEU: Beschreibung kürzen für die Vorschau
        $description = htmlspecialchars($course['description']);
        if (strlen($description) > 150) {
            // Finde das letzte Leerzeichen vor dem 150. Zeichen
            $lastSpace = strrpos(substr($description, 0, 150), ' ');
            if ($lastSpace !== false) {
                $description = substr($description, 0, $lastSpace) . '...';
            } else {
                // Falls kein Leerzeichen gefunden wurde (sehr langes Wort)
                $description = substr($description, 0, 150) . '...';
            }
        }
        
        // --- START: NEUE KARTEN-STRUKTUR (ersetzt <li>) ---
        echo '<article class="course-card">';
        
        // 1. Der Hauptinhalt der Karte
        echo '<div class="course-card-content">';
        // Wir nutzen <h3> für den Titel, das ist semantisch korrekt
        echo '<h3>' . $courseTitle . '</h3>'; 
        echo '<p>' . nl2br($description) . '</p>';
        echo '</div>'; // Ende .course-card-content
        
        // 2. Der "Action"-Bereich mit dem Button
        echo '<div class="course-card-actions">';
        // Wir nutzen unsere neue .button-link Klasse für den <a>-Tag
        echo '<a href="' . $courseUrl . '" class="button-link">Zum Kurs</a>';
        echo '</div>'; // Ende .course-card-actions

        echo '</article>'; // Ende .course-card
        // --- ENDE: NEUE KARTEN-STRUKTUR ---
    }
    
    echo '</div>'; // Ende .course-catalog-grid
    // --- ENDE: ÄNDERUNG ---

} else {
    echo '<p>Aktuell sind leider keine Kurse verfügbar.</p>';
}
?>