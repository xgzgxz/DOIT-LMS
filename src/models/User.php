<?php

/**
 * Verwaltet Benutzerdaten, einschließlich Erstellung und Authentifizierung.
 */
class User 
{
    /**
     * @var PDO Die Instanz der Datenbankverbindung.
     */
    private $pdo;

    /**
     * Konstruktor für das User-Modell.
     *
     * @param PDO $pdo Eine aktive PDO-Datenbankverbindung.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Erstellt einen neuen Benutzer in der Datenbank.
     *
     * @param string $username Der gewünschte Benutzername.
     * @param string $email Die E-Mail-Adresse des Benutzers.
     * @param string $password Das Klartext-Passwort des Benutzers.
     * @return bool|string True bei Erfolg oder eine Fehlermeldung als String bei einem Fehler.
     */
    public function createUser($username, $email, $password)
    {
        // Passwort für die sichere Speicherung hashen.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password_hash, user_role, created_at) 
                VALUES (?, ?, ?, 'user', NOW())";
                
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username, $email, $hashedPassword]);
            return true;

        } catch (PDOException $e) {
            // Auf einen Fehler wegen doppelten Eintrags prüfen (UNIQUE-Constraint-Verletzung).
            if ($e->getCode() == 23000) {
                return 'E-Mail-Adresse oder Benutzername ist bereits vergeben.';
            }
            
            // Bei anderen Fehlern eine generische Nachricht zurückgeben.
            // In einer echten Anwendung sollte der spezifische Fehler geloggt werden.
            return 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
        }
    }

    /**
     * Überprüft die Anmeldeinformationen eines Benutzers und meldet ihn an.
     *
     * @param string $email Die vom Benutzer angegebene E-Mail.
     * @param string $password Das vom Benutzer angegebene Klartext-Passwort.
     * @return array|false Benutzerdaten als Array bei erfolgreichem Login, andernfalls false.
     */
    public function loginUser($email, $password)
    {
        $sql = "SELECT user_id, username, email, password_hash, user_role 
                FROM users 
                WHERE email = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Überprüfen, ob der Benutzer existiert, und dann das Passwort verifizieren.
            if ($user && password_verify($password, $user['password_hash'])) {
                
                // Bei Erfolg die Benutzerdaten für die Session zurückgeben (ohne den Hash).
                $loggedInUser = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['user_role']
                ];
                
                return $loggedInUser;
            }

            // Wenn der Benutzer nicht gefunden oder das Passwort falsch ist, false zurückgeben.
            return false;

        } catch (PDOException $e) {
            // In einer echten Anwendung die Ausnahme loggen.
            return false;
        }
    }
}