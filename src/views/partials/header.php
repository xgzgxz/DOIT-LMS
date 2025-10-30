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
                <?php
                // isset() prüft, ob die Variable $_SESSION['user'] existiert.
                if (isset($_SESSION['user'])) {
                    // ---- Fall 1: Benutzer IST eingeloggt ----
                ?>
                    <li><a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/my-courses">Meine Kurse</a></li>
                    <li><a href="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/logout">Logout</a></li>
                    <li class="user-info">
                        Hallo, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!
                    </li>

                <?php
                } else {
                    // ---- Fall 2: Benutzer ist NICHT eingeloggt (Gast) ----
                ?>
                    <li><a href="<?php echo $basePath; ?>/login">Login</a></li>
                    <li><a href="<?php echo $basePath; ?>/register">Registrieren</a></li>
                <?php
                }
                // --- ENDE DER LOGIK ---
                ?>
            </ul>

        </div> </div> </nav>

    <nav class="nav-courses" id="nav-courses-bar">
        
        <button id="burger-menu-button" class="burger-menu-button">
            ☰ Menü
        </button>

        <ul>
            
            <?php
            // Wir prüfen, ob die Variable $allCourses überhaupt existiert 
            // und ob sie Kurse enthält. (Sicher ist sicher)
            if (isset($allCourses) && !empty($allCourses)) {
                
                // Wir durchlaufen alle Kurse
         foreach ($allCourses as $navCourse) {
                    $courseUrl = htmlspecialchars($basePath . '/course?id=' . $navCourse['course_id']);
                    $courseTitle = htmlspecialchars($navCourse['title']);
                    
                    
                    // Wir erstellen einen Link für jeden Kurs
                    echo '<li>';
                    echo '<a href="' . $courseUrl . '">' . $courseTitle . '</a>';
                    echo '</li>';
                }
            } else {
                // Fallback, falls mal was schiefgeht
                echo '<li>Keine Kurse gefunden.</li>';
            }
            ?>
            
        </ul>
    </nav>

    <main>