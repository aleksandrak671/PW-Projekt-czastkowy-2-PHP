<?php
// usuwanie produktu z koszyka
// ten plik jest wywolywany gdy uzytkownik kliknie przycisk "usun" przy produkcie

// laduje plik konfiguracyjny z sesja
require_once 'config.php';

// sprawdzam czy podano id produktu w parametrze get
if (isset($_GET['id'])) {
    // rzutuje na int dla bezpieczenstwa
    $produkt_id = (int) $_GET['id'];
    
    // usuwam produkt z koszyka w sesji uzywajac unset()
    if (isset($_SESSION['koszyk'][$produkt_id])) {
        unset($_SESSION['koszyk'][$produkt_id]);
    }
    
    // przekierowuje z powrotem do koszyka z komunikatem
    header('Location: koszyk.php?usunieto=1');
    exit;
}

// jesli nie podano id, przekierowuje do koszyka bez komunikatu
header('Location: koszyk.php');
exit;
