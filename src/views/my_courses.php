<?php
// $myCourses kommt vom CourseController
?>
<h1>Meine Kurse</h1>

<?php
if (!empty($myCourses)) {
    // ---- Fall 1: User ist in Kursen eingeschrieben ----

    // --- START: NEUES KARTEN-GRID (wie in home.php) ---
    echo '<div class="course-catalog-grid">';

    foreach ($myCourses as $course) {
        // Code ist identisch zu home.php / search_results.php
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
    // ---- Fall 2: User ist in keinen Kursen eingeschrieben ----
    $catalogUrl = htmlspecialchars($basePath . '/', ENT_QUOTES, 'UTF-8');
    echo '<p>Du bist noch für keine Kurse eingeschrieben.</p>';
    echo '<p><a href="' . $catalogUrl . '">Stöbere jetzt im Kurskatalog!</a></p>';
}
?>