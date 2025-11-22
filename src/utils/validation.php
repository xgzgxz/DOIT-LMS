<?php

/**
 * Diese Datei ist für die Zentralisierung der gesamten Validierungslogik vorgesehen.
 *
 * TODO: Die Validierungslogik, die sich derzeit im AuthController.php befindet,
 * in diese Datei auslagern (Refactoring). Dies verbessert die Trennung der
 * Zuständigkeiten (Separation of Concerns) und macht die Validierungsfunktionen
 * in der gesamten Anwendung wiederverwendbar.
 *
 * Beispiel:
 * function validateRegistration(array $data): array
 * {
 *     $errors = [];
 *     // ... Validierungsprüfungen ...
 *     return $errors;
 * }
 */
