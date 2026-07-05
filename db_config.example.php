<?php
/**
 * PRIMER KONFIGURACIJE
 * -------------------------------------------------------------
 * Kopiraj ovaj fajl u "db_config.php" i popuni svoje podatke.
 * Pravi "db_config.php" NE ide na GitHub (nalazi se u .gitignore).
 */

// --- Baza podataka ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'job');            // naziv baze
define('DB_USER', 'root');           // MySQL korisnik
define('DB_PASS', '');               // MySQL lozinka
define('DB_CHARSET', 'utf8mb4');

// --- Adresa aplikacije (bez ovoga aktivacioni/reset linkovi ne rade) ---
define('BASE_URL', 'http://localhost/job_portal/');

// --- SMTP (slanje e-mail poruka preko PHPMailer-a) ---
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@gmail.com');   // tvoj e-mail
define('SMTP_PASS', 'your_app_password');      // Gmail App Password
define('SMTP_FROM', 'your_email@gmail.com');
define('SMTP_FROM_NAME', 'IT Job Portal');

// --- PDO konekcija ---
function getDbConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed.']));
        }
    }
    return $pdo;
}
