<?php
// formularz skladania zamowienia
// uzytkownik podaje tutaj dane do wysylki

// laduje plik konfiguracyjny
require_once 'config.php';

// sprawdzam czy koszyk nie jest pusty
// jesli jest, przekierowuje do strony koszyka
if (!isset($_SESSION['koszyk']) || empty($_SESSION['koszyk'])) {
    header('Location: koszyk.php');
    exit;
}

// pobieram produkty z koszyka zeby wyswietlic podsumowanie
$produkty_w_koszyku = [];
$suma_calkowita = 0;

// pobieram id produktow z koszyka
$ids = array_keys($_SESSION['koszyk']);
// tworze placeholdery dla prepared statement
$placeholders = str_repeat('?,', count($ids) - 1) . '?';
$stmt = $pdo->prepare("SELECT * FROM produkty WHERE id IN ($placeholders)");
$stmt->execute($ids);
$produkty_z_bazy = $stmt->fetchAll();

// lacze dane z bazy z ilosciami z sesji
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
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- kodowanie i viewport -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SHOP_NAME; ?> - Złóż zamówienie</title>
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
        <h2>Formularz zamówienia</h2>

        <!-- uklad dwukolumnowy: podsumowanie i formularz -->
        <div class="order-layout">
            <!-- lewa kolumna - podsumowanie zamowienia -->
            <div class="order-summary">
                <h3>Podsumowanie zamówienia</h3>
                <table class="summary-table">
                    <!-- wyswietlam kazdy produkt z koszyka -->
                    <?php foreach ($produkty_w_koszyku as $produkt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($produkt['nazwa']); ?> (x<?php echo $produkt['ilosc']; ?>)</td>
                            <td><?php echo number_format($produkt['cena_czesciowa'], 2, ',', ' '); ?> zł</td>
                        </tr>
                    <?php endforeach; ?>
                    <!-- wiersz z suma -->
                    <tr class="total-row">
                        <td><strong>RAZEM:</strong></td>
                        <td><strong><?php echo number_format($suma_calkowita, 2, ',', ' '); ?> zł</strong></td>
                    </tr>
                </table>
            </div>

            <!-- prawa kolumna - formularz danych klienta -->
            <div class="order-form">
                <h3>Dane do wysyłki</h3>
                <!-- formularz wysyla dane do wyslij_zamowienie.php -->
                <form action="wyslij_zamowienie.php" method="POST">
                    <!-- imie i nazwisko -->
                    <div class="form-group">
                        <label for="imie" class="required">Imię i nazwisko</label>
                        <input type="text" id="imie" name="imie" required placeholder="Jan Kowalski">
                    </div>

                    <!-- email -->
                    <div class="form-group">
                        <label for="email" class="required">Adres e-mail</label>
                        <input type="email" id="email" name="email" required placeholder="jan@example.com">
                    </div>

                    <!-- telefon -->
                    <div class="form-group">
                        <label for="telefon" class="required">Telefon</label>
                        <input type="tel" id="telefon" name="telefon" required placeholder="123 456 789">
                    </div>

                    <!-- adres -->
                    <div class="form-group">
                        <label for="ulica" class="required">Ulica i numer domu/mieszkania</label>
                        <input type="text" id="ulica" name="ulica" required placeholder="ul. Przykładowa 10/5">
                    </div>

                    <!-- kod pocztowy z walidacja formatu -->
                    <div class="form-group">
                        <label for="kod_pocztowy" class="required">Kod pocztowy</label>
                        <input type="text" id="kod_pocztowy" name="kod_pocztowy" required placeholder="00-000" pattern="[0-9]{2}-[0-9]{3}">
                    </div>

                    <!-- miasto -->
                    <div class="form-group">
                        <label for="miasto" class="required">Miasto</label>
                        <input type="text" id="miasto" name="miasto" required placeholder="Warszawa">
                    </div>

                    <!-- opcjonalne uwagi -->
                    <div class="form-group">
                        <label for="uwagi">Uwagi do zamówienia</label>
                        <textarea id="uwagi" name="uwagi" placeholder="Dodatkowe informacje (opcjonalne)"></textarea>
                    </div>

                    <!-- przyciski -->
                    <div class="form-actions">
                        <a href="koszyk.php" class="btn btn-secondary">← Wróć do koszyka</a>
                        <button type="submit" class="btn btn-primary">Wyślij zamówienie</button>
                    </div>
                </form>
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
