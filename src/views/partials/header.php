<?php
/**
 * Globales Header-Partial.
 *
 * Diese Datei enthält den oberen Teil des HTML-Dokuments, einschließlich des <head>-Abschnitts
 * und der Hauptnavigationsleiste. Sie steuert die Anzeige verschiedener Navigationslinks
 * basierend auf dem Authentifizierungsstatus des Benutzers (eingeloggt oder Gast).
 *
 * @var string     $basePath   Der Basispfad für die URL-Generierung, wird für Links und Assets verwendet.
 * @var array|null $allCourses Ein Array aller verfügbaren Kurse für die sekundäre Navigationsleiste.
 */
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lernplattform für WI</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/css/style.css">
</head>

<body>

<nav>
    <div class="nav-main-content">
        <div class="nav-left">
            <a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/" class="logo-link">
                <img src="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/img/logo.png" alt="PinguWI Logo" class="logo responsive-logo">
            </a>
            <ul>
                <li><a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/" id="project-name">PinguWI</a></li>
            </ul>
        </div>

        <div class="nav-right">
            <div class="search-bar">
                <form action="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/search" method="GET">
                    <label for="search-query">Kurse suchen:</label>
                    <input type="search" id="search-query" name="query" placeholder="Suchbegriff">
                    <button type="submit">Suchen</button>
                </form>
            </div>

            <ul>
                <?php if (isset($_SESSION['user'])): ?>
                    <?php // Links für eingeloggte Benutzer ?>
                    <li><a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/my-courses">Meine Kurse</a></li>
                    <li><a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/logout">Logout</a></li>
                    <li class="user-info">
                        Hallo, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!
                    </li>
                <?php else: ?>
                    <?php // Links für Gäste ?>
                    <li><a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/login">Login</a></li>
                    <li><a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/register">Registrieren</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<nav class="nav-courses" id="nav-courses-bar">
    <button id="burger-menu-button" class="burger-menu-button">
        ☰ Menü
    </button>
    <ul>
        <?php if (isset($allCourses) && !empty($allCourses)): ?>
            <?php foreach ($allCourses as $navCourse):
                $courseUrl = htmlspecialchars($basePath . '/course?id=' . $navCourse['course_id']);
                $courseTitle = htmlspecialchars($navCourse['title']);
            ?>
                <li>
                    <a href="<?php echo $courseUrl; ?>"><?php echo $courseTitle; ?></a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Keine Kurse gefunden.</li>
        <?php endif; ?>
    </ul>
</nav>

<main>