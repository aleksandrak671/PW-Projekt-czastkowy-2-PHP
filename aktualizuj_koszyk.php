<?php
// aktualizacja ilosci produktu w koszyku
// ten plik jest wywolywany gdy uzytkownik zmieni ilosc i kliknie "zmien"

// laduje plik konfiguracyjny z sesja
require_once 'config.php';

// sprawdzam czy formularz zostal wyslany metoda post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // pobieram id produktu i nowa ilosc z formularza
    // uzywam ?? 0 jako wartosc domyslna jesli pole nie istnieje
    $produkt_id = (int) ($_POST['produkt_id'] ?? 0);
    $ilosc = (int) ($_POST['ilosc'] ?? 1);
    
    // walidacja ilosci - minimum 1, maksimum 99
    // zapobiega to podaniu ujemnych lub zbyt duzych wartosci
    if ($ilosc < 1) $ilosc = 1;
    if ($ilosc > 99) $ilosc = 99;
    
    // aktualizuje ilosc w koszyku jesli produkt tam jest
    if (isset($_SESSION['koszyk'][$produkt_id])) {
        $_SESSION['koszyk'][$produkt_id] = $ilosc;
    }
    
    // przekierowuje z powrotem do koszyka z komunikatem
    header('Location: koszyk.php?zaktualizowano=1');
    exit;
}

// jesli nie bylo post, przekierowuje do koszyka
header('Location: koszyk.php');
exit;
