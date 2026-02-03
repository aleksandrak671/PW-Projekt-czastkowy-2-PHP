<?php
// strona potwierdzenia zamowienia
// wyswietla podsumowanie zlozonego zamowienia i tresc emaila

// laduje plik konfiguracyjny
require_once 'config.php';

// sprawdzam czy jest zapisane zamowienie do wyswietlenia
// jesli nie ma, przekierowuje na strone glowna
if (!isset($_SESSION['ostatnie_zamowienie'])) {
    header('Location: index.php');
    exit;
}

// pobieram dane zamowienia z sesji
$zamowienie = $_SESSION['ostatnie_zamowienie'];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- kodowanie i viewport -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SHOP_NAME; ?> - Potwierdzenie zamówienia</title>
    <link rel="stylesheet" href="style-new.css">
</head>
<body>
    <!-- naglowek -->
    <header class="header">
        <div class="container">
            <h1 class="logo"><?php echo SHOP_NAME; ?></h1>
            <nav class="nav">
                <a href="index.php" class="nav-link">Produkty</a>
                <a href="koszyk.php" class="nav-link">Koszyk</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <!-- sekcja potwierdzenia -->
        <div class="confirmation">
            <!-- naglowek z numerem zamowienia -->
            <div class="confirmation-header">
                <h2>Zamówienie zostało złożone!</h2>
                <p class="order-number">Numer zamówienia: <strong><?php echo htmlspecialchars($zamowienie['numer']); ?></strong></p>
            </div>

            <!-- dane zamawiajacego -->
            <div class="confirmation-details">
                <h3>Dane zamawiającego:</h3>
                <p><strong>Imię i nazwisko:</strong> <?php echo htmlspecialchars($zamowienie['dane_klienta']['imie']); ?></p>
                <p><strong>E-mail:</strong> <?php echo htmlspecialchars($zamowienie['dane_klienta']['email']); ?></p>
                <p><strong>Telefon:</strong> <?php echo htmlspecialchars($zamowienie['dane_klienta']['telefon']); ?></p>
                <p><strong>Adres dostawy:</strong><br>
                    <?php echo htmlspecialchars($zamowienie['dane_klienta']['ulica']); ?><br>
                    <?php echo htmlspecialchars($zamowienie['dane_klienta']['kod_pocztowy']); ?> <?php echo htmlspecialchars($zamowienie['dane_klienta']['miasto']); ?>
                </p>
            </div>

            <!-- lista zamowionych produktow -->
            <div class="confirmation-products">
                <h3>Zamówione produkty:</h3>
                <table class="summary-table">
                    <?php foreach ($zamowienie['produkty'] as $produkt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($produkt['nazwa']); ?> (x<?php echo $produkt['ilosc']; ?>)</td>
                            <td><?php echo number_format($produkt['cena_czesciowa'], 2, ',', ' '); ?> zł</td>
                        </tr>
                    <?php endforeach; ?>
                    <!-- suma do zaplaty -->
                    <tr class="total-row">
                        <td><strong>RAZEM DO ZAPŁATY:</strong></td>
                        <td><strong><?php echo number_format($zamowienie['suma'], 2, ',', ' '); ?> zł</strong></td>
                    </tr>
                </table>
            </div>

            <!-- podglad tresci emaila ktory zostal wyslany do sklepu -->
            <div class="email-preview">
                <h3>Treść wysłanego zamówienia:</h3>
                <pre><?php echo htmlspecialchars($zamowienie['email_tresc']); ?></pre>
            </div>

            <!-- przycisk powrotu do sklepu -->
            <div class="confirmation-actions">
                <a href="index.php" class="btn btn-primary">Kontynuuj zakupy</a>
            </div>
        </div>
    </main>

    <!-- stopka -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 <?php echo SHOP_NAME; ?> - Projekt cząstkowy nr 2</p>
        </div>
    </footer>
</body>
</html>
<?php
// moglbym usunac zamowienie z sesji po wyswietleniu
// ale zostawiam zeby mozna bylo odswiezyc strone
// unset($_SESSION['ostatnie_zamowienie']);
?>
