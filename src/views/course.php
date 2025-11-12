<?php
/**
 * Course Detail Page View.
 *
 * This view displays the details for a specific course, including its description,
 * a list of lessons, and enrollment options. It also features a sidebar
 * with a list of all available courses.
 *
 * @var array $course       Details of the current course.
 * @var array $lessons      Lessons belonging to the current course.
 * @var array $allCourses   All courses for the sidebar navigation.
 * @var bool  $isLoggedIn   User's login status.
 * @var bool  $isEnrolled   User's enrollment status for this course.
 * @var string $basePath    The base path for URL generation.
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
                    // Determine if the sidebar item is the currently active course
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
        
        <?php // Display feedback messages based on URL status parameters ?>
        <?php if (isset($_GET['status']) && $_GET['status'] === 'enrolled'): ?>
            <div class="form-message is-success">
                <strong>Du wurdest erfolgreich für diesen Kurs eingeschrieben!</strong>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="form-message is-error">
                <strong>Bei der Einschreibung ist ein Fehler aufgetreten.</strong>
            </div>
        <?php endif; ?>

        <?php // Display enrollment status and actions ?>
        <?php if ($isLoggedIn): ?>
            <?php if ($isEnrolled): ?>
                <?php // Show enrollment confirmation, but hide it if the user just enrolled to avoid redundancy. ?>
                <?php if (!isset($_GET['status']) || $_GET['status'] !== 'enrolled'): ?>
                    <p><strong>Du bist für diesen Kurs eingeschrieben.</strong></p>
                <?php endif; ?>
            <?php else: ?>
                <?php // Show enrollment button for logged-in, non-enrolled users. ?>
                <form action="<?php echo htmlspecialchars($basePath . '/enroll'); ?>" method="POST">
                    <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                    <button type="submit">Jetzt für diesen Kurs einschreiben</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <?php // Prompt non-logged-in users to log in. ?>
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