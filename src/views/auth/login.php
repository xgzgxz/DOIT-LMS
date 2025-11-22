<?php
/**
 * Ansicht für die Anmeldeseite.
 *
 * Diese Ansicht zeigt das Anmeldeformular an und behandelt verschiedene Statusmeldungen,
 * wie z.B. erfolgreiche Registrierung, Abmeldebestätigung und Anmeldefehler.
 *
 * @var array|null $errors   Ein Array mit Fehlermeldungen, falls die Anmeldung fehlschlägt.
 * @var string     $basePath Der Basispfad für die URL-Generierung.
 */
?>
<h1>Anmelden</h1>
<p>Bitte fülle das folgende Formular aus, um dich anzumelden.</p>

<div class="form-container">

    <?php // Statusmeldungen basierend auf URL-Parametern anzeigen ?>
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'registered'): ?>
            <div class="form-message is-success">
                Registrierung erfolgreich! Du kannst dich jetzt einloggen.
            </div>
        <?php elseif ($_GET['status'] === 'logged_out'): ?>
            <div class="form-message is-info">
                Du wurdest erfolgreich ausgeloggt. Auf Wiedersehen!
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php // Anmeldefehler anzeigen, falls vorhanden ?>
    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="form-message is-error">
            <strong>Login fehlgeschlagen:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

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