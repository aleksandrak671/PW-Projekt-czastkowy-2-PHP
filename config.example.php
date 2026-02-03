<?php
/**
 * plik konfiguracyjny sklepu internetowego
 * 
 * INSTRUKCJA:
 * 1. skopiuj ten plik jako config.php
 * 2. uzupelnij dane dostepowe do swojej bazy danych
 */

// rozpoczecie sesji - potrzebna do koszyka
session_start();

// dane dostepowe do bazy danych - UZUPELNIJ WLASNYMI DANYMI
define('DB_HOST', 'localhost');              // adres serwera mysql
define('DB_NAME', 'projekt2_sklep');         // nazwa bazy danych
define('DB_USER', 'twoj_uzytkownik');        // nazwa uzytkownika mysql
define('DB_PASS', 'twoje_haslo');            // haslo do mysql

// polaczenie z baza danych uzywajac pdo
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

// stale konfiguracyjne sklepu - MOZESZ ZMIENIC
define('SHOP_EMAIL', 'sklep@example.com');   // email na zamowienia
define('SHOP_NAME', 'Księgarnia Literacka'); // nazwa sklepu
