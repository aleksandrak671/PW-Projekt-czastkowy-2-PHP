<?php
// wysylanie zamowienia emailem
// ten plik przetwarza formularz zamowienia i wysyla email do sklepu

// laduje plik konfiguracyjny
require_once 'config.php';

// sprawdzam czy formularz zostal wyslany metoda post
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: zamowienie.php');
    exit;
}

// sprawdzam czy koszyk nie jest pusty
if (!isset($_SESSION['koszyk']) || empty($_SESSION['koszyk'])) {
    header('Location: koszyk.php');
    exit;
}

// pobieram i waliduje dane z formularza
// trim() usuwa biale znaki z poczatku i konca
$imie = trim($_POST['imie'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefon = trim($_POST['telefon'] ?? '');
$ulica = trim($_POST['ulica'] ?? '');
$kod_pocztowy = trim($_POST['kod_pocztowy'] ?? '');
$miasto = trim($_POST['miasto'] ?? '');
$uwagi = trim($_POST['uwagi'] ?? '');

// tablica na bledy walidacji
$errors = [];

// walidacja pol - sprawdzam czy nie sa puste
if (empty($imie)) $errors[] = 'Imie i nazwisko jest wymagane.';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Podaj prawidlowy adres e-mail.';
if (empty($telefon)) $errors[] = 'Telefon jest wymagany.';
if (empty($ulica)) $errors[] = 'Ulica jest wymagana.';
if (empty($kod_pocztowy)) $errors[] = 'Kod pocztowy jest wymagany.';
if (empty($miasto)) $errors[] = 'Miasto jest wymagane.';

// jesli sa bledy, zapisuje je w sesji i przekierowuje
if (!empty($errors)) {
    $_SESSION['order_errors'] = $errors;
    header('Location: zamowienie.php');
    exit;
}

// pobieram produkty z koszyka
$produkty_w_koszyku = [];
$suma_calkowita = 0;

$ids = array_keys($_SESSION['koszyk']);
$placeholders = str_repeat('?,', count($ids) - 1) . '?';
$stmt = $pdo->prepare("SELECT * FROM produkty WHERE id IN ($placeholders)");
$stmt->execute($ids);
$produkty_z_bazy = $stmt->fetchAll();

foreach ($produkty_z_bazy as $produkt) {
    $ilosc = $_SESSION['koszyk'][$produkt['id']];
    $cena_czesciowa = $produkt['cena'] * $ilosc;
    
    $produkty_w_koszyku[] = [
        'nazwa' => $produkt['nazwa'],
        'cena' => $produkt['cena'],
        'ilosc' => $ilosc,
        'cena_czesciowa' => $cena_czesciowa
    ];
    
    $suma_calkowita += $cena_czesciowa;
}

// tworze tresc emaila
// generuje unikalny numer zamowienia: ZAM-data-losowa liczba
$numer_zamowienia = 'ZAM-' . date('Ymd') . '-' . rand(1000, 9999);

// buduje tresc emaila z danymi klienta i produktami
$tresc_email = "NOWE ZAMOWIENIE - {$numer_zamowienia}\n";
$tresc_email .= "======================================\n\n";
$tresc_email .= "DANE KLIENTA:\n";
$tresc_email .= "Imie i nazwisko: {$imie}\n";
$tresc_email .= "E-mail: {$email}\n";
$tresc_email .= "Telefon: {$telefon}\n";
$tresc_email .= "Adres dostawy:\n";
$tresc_email .= "  {$ulica}\n";
$tresc_email .= "  {$kod_pocztowy} {$miasto}\n";
if (!empty($uwagi)) {
    $tresc_email .= "Uwagi: {$uwagi}\n";
}
$tresc_email .= "\n======================================\n";
$tresc_email .= "ZAMOWIONE PRODUKTY:\n\n";

// dodaje liste produktow do emaila
foreach ($produkty_w_koszyku as $produkt) {
    $tresc_email .= "- {$produkt['nazwa']}\n";
    $tresc_email .= "  Cena: " . number_format($produkt['cena'], 2, ',', ' ') . " zl x {$produkt['ilosc']} = ";
    $tresc_email .= number_format($produkt['cena_czesciowa'], 2, ',', ' ') . " zl\n\n";
}

$tresc_email .= "======================================\n";
$tresc_email .= "SUMA DO ZAPLATY: " . number_format($suma_calkowita, 2, ',', ' ') . " zl\n";
$tresc_email .= "======================================\n";
$tresc_email .= "\nZamowienie zlozone: " . date('d.m.Y H:i:s') . "\n";

// przygotowuje naglowki emaila
$temat = "Nowe zamowienie: {$numer_zamowienia}";
$naglowki = "From: {$email}\r\n";
$naglowki .= "Reply-To: {$email}\r\n";
$naglowki .= "Content-Type: text/plain; charset=UTF-8\r\n";

// probuje wyslac email
// mail() moze nie dzialac na localhost bez konfiguracji serwera smtp
// @ tlumi bledy - nawet jesli email sie nie wysle, strona zadziala
$email_wyslany = @mail(SHOP_EMAIL, $temat, $tresc_email, $naglowki);

// zapisuje dane zamowienia do sesji zeby wyswietlic potwierdzenie
$_SESSION['ostatnie_zamowienie'] = [
    'numer' => $numer_zamowienia,
    'produkty' => $produkty_w_koszyku,
    'suma' => $suma_calkowita,
    'dane_klienta' => [
        'imie' => $imie,
        'email' => $email,
        'telefon' => $telefon,
        'ulica' => $ulica,
        'kod_pocztowy' => $kod_pocztowy,
        'miasto' => $miasto
    ],
    'email_tresc' => $tresc_email  // zapisuje tresc emaila do podgladu
];

// czyszcze koszyk po zlozeniu zamowienia
unset($_SESSION['koszyk']);

// przekierowuje do strony potwierdzenia
header('Location: potwierdzenie.php');
exit;
