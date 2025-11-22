<?php
/**
 * Ansicht für die Registrierungsseite.
 *
 * Diese Ansicht zeigt das Benutzerregistrierungsformular an. Falls Validierungsfehler
 * vom Controller zurückgegeben werden, werden diese oben im Formular angezeigt.
 *
 * @var array|null $errors   Ein Array mit Validierungsfehlermeldungen.
 * @var string     $basePath Der Basispfad für die URL-Generierung.
 */
?>
<h1>Registrierung</h1>
<p>Bitte fülle das folgende Formular aus, um dich zu registrieren.</p>

<div class="form-container">

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="form-message is-error">
            <strong>Es sind Fehler aufgetreten:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

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