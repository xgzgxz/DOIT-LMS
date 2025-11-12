<?php
/**
 * Login Page View.
 *
 * This view displays the login form and handles various status messages,
 * such as successful registration, logout confirmation, and login errors.
 *
 * @var array|null $errors   An array of error messages if login fails.
 * @var string     $basePath The base path for URL generation.
 */
?>
<h1>Anmelden</h1>
<p>Bitte fülle das folgende Formular aus, um dich anzumelden.</p>

<div class="form-container">

    <?php // Display status messages based on URL parameters ?>
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

    <?php // Display login errors if they exist ?>
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