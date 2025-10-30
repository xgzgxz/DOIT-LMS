<h1>Anmelden</h1>
<p>Bitte fülle das folgende Formular aus, um dich anzumelden.</p>

<div class="form-container">

    <?php
    // --- START: ANGEPASSTE Statusmeldung (passt jetzt zum AuthController) ---

    // Prüfen, ob in der URL "?status=registered" steht
    if (isset($_GET['status']) && $_GET['status'] === 'registered') {
        echo '<div class="form-message is-success">';
        echo 'Registrierung erfolgreich! Du kannst dich jetzt einloggen.';
        echo '</div>';
    }
    // Prüfen, ob in der URL "?status=logged_out" steht
    if (isset($_GET['status']) && $_GET['status'] === 'logged_out') {
        echo '<div class="form-message is-info">';
        echo 'Du wurdest erfolgreich ausgeloggt. Auf Wiedersehen!';
        echo '</div>';
    }
    // --- ENDE: ANGEPASSTE Statusmeldung ---


    // --- START: NEUER FEHLER-BLOCK ---
    // Hier fangen wir die $errors-Variable auf, die der AuthController
    // bei einem Fehler (z.B. "Passwort falsch") mitschickt.
    if (isset($errors) && !empty($errors)) {
        echo '<div class="form-message is-error">';
        echo '<strong>Login fehlgeschlagen:</strong>';
        echo '<ul>';
        // Wir gehen jeden Fehler im Array durch und zeigen ihn an
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    // --- ENDE: NEUER FEHLER-BLOCK ---
    ?>

    <form action="<?php echo htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'); ?>/login" method="POST">
        <div class="form-group">
            <label for="email">E-Mail:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Anmelden</button>
    </form>
    
</div>