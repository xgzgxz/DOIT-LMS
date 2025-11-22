<?php
/**
 * Ansicht für die Kurs-Detailseite.
 *
 * Diese Ansicht zeigt die Details für einen bestimmten Kurs an, einschließlich seiner Beschreibung,
 * einer Liste von Lektionen und Einschreibungsoptionen. Sie verfügt außerdem über eine Seitenleiste
 * mit einer Liste aller verfügbaren Kurse.
 *
 * @var array $course       Details des aktuellen Kurses.
 * @var array $lessons      Lektionen, die zum aktuellen Kurs gehören.
 * @var array $allCourses   Alle Kurse für die Navigation in der Seitenleiste.
 * @var bool  $isLoggedIn   Login-Status des Benutzers.
 * @var bool  $isEnrolled   Einschreibungsstatus des Benutzers für diesen Kurs.
 * @var string $basePath    Der Basispfad für die URL-Generierung.
 */
?>
<div class="lesson-layout">

    <aside class="sidebar">
        <h3>Alle Kurse</h3>
        <ul>
            <?php if (!empty($allCourses)): ?>
                <?php foreach ($allCourses as $sidebarCourse):
                    $courseUrl = htmlspecialchars($basePath . '/course?id=' . $sidebarCourse['course_id']);
                    $courseTitle = htmlspecialchars($sidebarCourse['title']);
                    // Bestimmen, ob der Seitenleisten-Eintrag der aktuell aktive Kurs ist
                    $isActive = ($sidebarCourse['course_id'] == $course['course_id']);
                    $class = $isActive ? 'active' : '';
                ?>
                    <li class="<?php echo $class; ?>">
                        <a href="<?php echo $courseUrl; ?>"><?php echo $courseTitle; ?></a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </aside>

    <article class="lesson-content">

        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
        
        <?php // Feedback-Nachrichten basierend auf URL-Statusparametern anzeigen ?>
        <?php if (isset($_GET['status']) && $_GET['status'] === 'enrolled'): ?>
            <div class="form-message is-success">
                <strong>Du wurdest erfolgreich für diesen Kurs eingeschrieben!</strong>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="form-message is-error">
                <strong>Bei der Einschreibung ist ein Fehler aufgetreten.</strong>
            </div>
        <?php endif; ?>

        <?php // Einschreibestatus und Aktionen anzeigen ?>
        <?php if ($isLoggedIn): ?>
            <?php if ($isEnrolled): ?>
                <?php // Einschreibebestätigung anzeigen, aber ausblenden, wenn sich der Benutzer gerade erst eingeschrieben hat, um Redundanz zu vermeiden. ?>
                <?php if (!isset($_GET['status']) || $_GET['status'] !== 'enrolled'): ?>
                    <p><strong>Du bist für diesen Kurs eingeschrieben.</strong></p>
                <?php endif; ?>
            <?php else: ?>
                <?php // Einschreibebutton für eingeloggte, nicht eingeschriebene Benutzer anzeigen. ?>
                <form action="<?php echo htmlspecialchars($basePath . '/enroll'); ?>" method="POST">
                    <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                    <button type="submit">Jetzt für diesen Kurs einschreiben</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <?php // Nicht eingeloggte Benutzer auffordern, sich einzuloggen. ?>
            <p>Bitte <a href="<?php echo htmlspecialchars($basePath . '/login'); ?>">einloggen</a>, um dich für diesen Kurs einzuschreiben.</p>
        <?php endif; ?>
        
        <hr>
        <h2>Lektionen in diesem Kurs</h2>

        <?php if (!empty($lessons)): ?>
            <ul>
                <?php foreach ($lessons as $lesson): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($basePath . '/lesson?id=' . $lesson['lesson_id']); ?>">
                            <?php echo htmlspecialchars($lesson['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Für diesen Kurs sind noch keine Lektionen verfügbar.</p>
        <?php endif; ?>

    </article>
</div>