<h1>Registrierung</h1>
<p>Bitte fülle das folgende Formular aus, um dich zu registrieren.</p>

<div class="form-container">

    <?php
    // Wir prüfen, ob die $errors-Variable (vom AuthController)
    // überhaupt existiert und ob sie nicht leer ist.
    if (isset($errors) && !empty($errors)) {
        // NEU: Wir nutzen unsere CSS-Klassen statt Inline-Style
        echo '<div class="form-message is-error">';
        echo '<strong>Es sind Fehler aufgetreten:</strong>';
        echo '<ul>';
        // Wir gehen jeden Fehler im Array durch und zeigen ihn an
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8'); ?>" method="post">
        
        <div class="form-group">
            <label for="username">Benutzername:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">E-Mail:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">Passwort bestätigen:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        
        <button type="submit">Registrieren</button>
    </form>

</div>