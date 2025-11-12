<?php

/**
 * Manages user data, including creation and authentication.
 */
class User 
{
    /**
     * @var PDO The database connection instance.
     */
    private $pdo;

    /**
     * Constructor for the User model.
     *
     * @param PDO $pdo An active PDO database connection.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Creates a new user in the database.
     *
     * @param string $username The desired username.
     * @param string $email The user's email address.
     * @param string $password The user's plain-text password.
     * @return bool|string True on success, or an error message string on failure.
     */
    public function createUser($username, $email, $password)
    {
        // Hash the password for secure storage.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password_hash, user_role, created_at) 
                VALUES (?, ?, ?, 'user', NOW())";
                
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username, $email, $hashedPassword]);
            return true;

        } catch (PDOException $e) {
            // Check for a duplicate entry error (UNIQUE constraint violation).
            if ($e->getCode() == 23000) {
                return 'E-Mail-Adresse oder Benutzername ist bereits vergeben.';
            }
            
            // For other errors, return a generic message.
            // In a real application, log the specific error.
            return 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
        }
    }

    /**
     * Verifies user credentials and logs them in.
     *
     * @param string $email The email provided by the user.
     * @param string $password The plain-text password provided by the user.
     * @return array|false User data as an array on successful login, otherwise false.
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

            // Verify user existence and then the password.
            if ($user && password_verify($password, $user['password_hash'])) {
                
                // On success, return user data for session storage (excluding the hash).
                $loggedInUser = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['user_role']
                ];
                
                return $loggedInUser;
            }

            // If user not found or password incorrect, return false.
            return false;

        } catch (PDOException $e) {
            // In a real application, log the exception.
            return false;
        }
    }
}