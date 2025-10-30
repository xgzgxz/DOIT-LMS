<?php
//Hier definieren wir die User-Klasse
//Funktionen: erstellen, finden, aktualisieren der User in der Datenbank

class User 
{
//eine private Variable, um die Datenbankverbindung (das $pdo Objekt) zu speichern
// private: nur Funktionen *innerhalb* der Klasse können darauf zugreifen
    private $pdo;

    /**
     * Der Konstruktor der User-Klasse
     * Wird aufgerufen, wenn ein neues User-Objekt erstellt wird
     * z.B. mit new User($pdo);
     * wir "injizieren" die Datenbankverbindung, damit die Klasse sie nutzen kann
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
        /**
        * Erstellt einen neuen Benutzer in der Datenbank
        * nimmt die (bereits validierten) Daten aus dem Formular entgegen
        */
    public function createUser($username, $email, $password)
    {
        // 1. Passwort hashen
        // password_hash() erstellt einen sicheren Hash, den man nicht einfach zurückrechnen kann.
        //PASSWORD_DEFAULT ist der aktuell empfohlene, starke Algorithmus.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


// 2. SQL-Query vorbereiten
        // Die Fragezeichen (?) sind Platzhalter. Das nennt man "Prepared Statement".
        // Es ist die WICHTIGSTE Methode, um SQL-Injection-Angriffe zu verhindern.
        $sql = "INSERT INTO users (username, email, password_hash, user_role, created_at) 
                VALUES (?, ?, ?, 'user', NOW())";
                
// 3. Fehlerbehandlung mit try...catch
        // Wir versuchen, den Befehl auszuführen. Wenn die Datenbank einen Fehler wirft
        // (z.B. weil die E-Mail schon existiert), fangen (catchen) wir ihn ab.
        try {
            // Das SQL-Statement "vorbereiten"
            $stmt = $this->pdo->prepare($sql);
            
            // Das Statement "ausführen" und die Platzhalter (?) mit unseren Daten füllen.
            // Die Reihenfolge muss exakt stimmen:
            // 1. ? -> $username
            // 2. ? -> $email
            // 3. ? -> $hashedPassword
            $stmt->execute([$username, $email, $hashedPassword]);

            // Wenn alles geklappt hat, geben wir 'true' (wahr) zurück.
            return true;

        } catch (PDOException $e) {
            // Ein Fehler ist aufgetreten.
            // Der häufigste Fehler hier ist "Duplicate entry" (Code 23000),
            // weil die E-Mail oder der Benutzername bereits vergeben ist.
            // (Unsere Datenbank hat UNIQUE-Constraints auf 'username' und 'email') [cite: 5, 6]
            if ($e->getCode() == 23000) {
                return 'E-Mail-Adresse oder Benutzername ist bereits vergeben.';
            }
            
            // Für alle anderen, unerwarteten Fehler:
            // (Im echten Leben würden wir den $e->getMessage() in ein Log-File schreiben,
            // aber nicht dem Benutzer zeigen, da er technische Details verraten könnte.)
            return 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
        }
    }
    /**
     * Überprüft die Anmeldeinformationen eines Benutzers.
     *
     * @param string $email Die vom Benutzer eingegebene E-Mail.
     * @param string $password Das vom Benutzer eingegebene Klartext-Passwort.
     * @return array|false Gibt die Benutzerdaten (als Array) bei Erfolg zurück, sonst false.
     */
    public function loginUser($email, $password)
    {
        // 1. Benutzer anhand der E-Mail suchen
        // Wir holen uns den Benutzer aus der DB, der DIESE E-Mail-Adresse hat.
        // Wir brauchen unbedingt den gespeicherten 'password_hash'.
        $sql = "SELECT user_id, username, email, password_hash, user_role 
                FROM users 
                WHERE email = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);

            // fetch() holt uns die EINE Zeile, die gefunden wurde.
            // Wenn keine Zeile gefunden wurde (E-Mail existiert nicht), ist $user 'false'.
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Prüfen, ob ein Benutzer gefunden wurde
            if ($user) {
                
                // 3. Passwort verifizieren
                // $user ist vorhanden, jetzt MUSS das Passwort geprüft werden.
                // password_verify() ist die Magie:
                // Es nimmt das Klartext-Passwort ($password)
                // und vergleicht es mit dem Hash aus der Datenbank ($user['password_hash']).
                // Es gibt true (Passwort stimmt) oder false (Passwort falsch) zurück.
                
                if (password_verify($password, $user['password_hash'])) {
                    // ---- ERFOLG! ----
                    // E-Mail gefunden UND Passwort stimmt überein.
                    // Wir geben die Benutzerdaten (ohne den Hash!) zurück.
                    
                    // Wir erstellen ein "sauberes" Array, das wir in die Session legen können.
                    $loggedInUser = [
                        'user_id' => $user['user_id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['user_role']
                    ];
                    
                    return $loggedInUser;
                }
            }

            // ---- FEHLER ----
            // Entweder wurde der Benutzer (E-Mail) nicht gefunden (if $user war false)
            // ODER das Passwort war falsch (if password_verify war false).
            // In beiden Fällen geben wir 'false' zurück.
            // (Wir sagen absichtlich nicht, WAS falsch war - das ist sicherer).
            return false;

        } catch (PDOException $e) {
            // Bei einem DB-Fehler auch 'false' zurückgeben und den Fehler loggen
            // (Fürs Logging könnten wir hier z.B. error_log($e->getMessage()) einfügen)
            return false;
        }
    }
}

?>