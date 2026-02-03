<?php
// strona koszyka - wyswietla zawartosc koszyka
// tutaj uzytkownik moze zmienic ilosc produktow lub je usunac

// laduje plik konfiguracyjny z polaczeniem do bazy i sesja
require_once 'config.php';

// tablica na produkty w koszyku i zmienna na sume
$produkty_w_koszyku = [];
$suma_calkowita = 0;

// sprawdzam czy w koszyku sa jakies produkty
if (isset($_SESSION['koszyk']) && !empty($_SESSION['koszyk'])) {
    
    // pobieram klucze tablicy koszyka - to sa id produktow
    $ids = array_keys($_SESSION['koszyk']);
    
    // pobieram dane produktow z bazy uzywajac where in
    // str_repeat tworzy odpowiednia liczbe placeholderow (?,?,?)
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM produkty WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produkty_z_bazy = $stmt->fetchAll();
    
    // lacze dane z bazy z ilosciami z sesji
    foreach ($produkty_z_bazy as $produkt) {
        // pobieram ilosc z sesji
        $ilosc = $_SESSION['koszyk'][$produkt['id']];
        // obliczam cene czesciowa (cena x ilosc)
        $cena_czesciowa = $produkt['cena'] * $ilosc;
        
        // dodaje produkt do tablicy z wszystkimi potrzebnymi danymi
        $produkty_w_koszyku[] = [
            'id' => $produkt['id'],
            'nazwa' => $produkt['nazwa'],
            'cena' => $produkt['cena'],
            'ilosc' => $ilosc,
            'cena_czesciowa' => $cena_czesciowa
        ];
        
        // dodaje do sumy calkowitej
        $suma_calkowita += $cena_czesciowa;
    }
}

// licze produkty do wyswietlenia w naglowku (badge)
$ilosc_w_koszyku = 0;
if (isset($_SESSION['koszyk'])) {
    foreach ($_SESSION['koszyk'] as $ilosc) {
        $ilosc_w_koszyku += $ilosc;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- kodowanie utf-8 dla polskich znakow -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SHOP_NAME; ?> - Koszyk</title>
    <link rel="stylesheet" href="style-new.css">
</head>
<body>
    <!-- naglowek z nawigacja -->
    <header class="header">
        <div class="container">
            <h1 class="logo"><?php echo SHOP_NAME; ?></h1>
            <nav class="nav">
                <a href="index.php" class="nav-link">Produkty</a>
                <!-- aktywny link do koszyka -->
                <a href="koszyk.php" class="nav-link active koszyk-link">
                    Koszyk 
                    <?php if ($ilosc_w_koszyku > 0): ?>
                        <span class="badge"><?php echo $ilosc_w_koszyku; ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Twój koszyk</h2>

        <!-- komunikaty o wykonanych akcjach -->
        <?php if (isset($_GET['usunieto'])): ?>
            <div class="alert success">Produkt został usunięty z koszyka.</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['zaktualizowano'])): ?>
            <div class="alert success">Ilość została zaktualizowana.</div>
        <?php endif; ?>

        <?php if (empty($produkty_w_koszyku)): ?>
            <!-- komunikat gdy koszyk jest pusty -->
            <div class="empty-cart">
                <p>Twój koszyk jest pusty</p>
                <a href="index.php" class="btn btn-primary">Przejdź do zakupów</a>
            </div>
        <?php else: ?>
            <!-- tabela z produktami w koszyku -->
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Cena jednostkowa</th>
                        <th>Ilość</th>
                        <th>Cena częściowa</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- petla wyswietlajaca kazdy produkt -->
                    <?php foreach ($produkty_w_koszyku as $produkt): ?>
                        <tr>
                            <td class="product-name"><?php echo htmlspecialchars($produkt['nazwa']); ?></td>
                            <td><?php echo number_format($produkt['cena'], 2, ',', ' '); ?> zł</td>
                            <td>
                                <!-- formularz do zmiany ilosci -->
                                <form action="aktualizuj_koszyk.php" method="POST" class="quantity-form">
                                    <input type="hidden" name="produkt_id" value="<?php echo $produkt['id']; ?>">
                                    <input type="number" name="ilosc" value="<?php echo $produkt['ilosc']; ?>" min="1" max="99" class="quantity-input">
                                    <button type="submit" class="btn btn-small">Zmień</button>
                                </form>
                            </td>
                            <td class="partial-price"><?php echo number_format($produkt['cena_czesciowa'], 2, ',', ' '); ?> zł</td>
                            <td>
                                <!-- link do usuniecia produktu -->
                                <a href="usun_z_koszyka.php?id=<?php echo $produkt['id']; ?>" class="btn btn-danger">Usuń</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <!-- wiersz z suma calkowita -->
                    <tr class="total-row">
                        <td colspan="3"><strong>SUMA:</strong></td>
                        <td colspan="2" class="total-price"><strong><?php echo number_format($suma_calkowita, 2, ',', ' '); ?> zł</strong></td>
                    </tr>
                </tfoot>
            </table>

            <!-- przyciski akcji -->
            <div class="cart-actions">
                <a href="index.php" class="btn btn-secondary">← Kontynuuj zakupy</a>
                <a href="zamowienie.php" class="btn btn-primary">Złóż zamówienie →</a>
            </div>
        <?php endif; ?>
    </main>

    <!-- stopka -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 <?php echo SHOP_NAME; ?> - Projekt cząstkowy nr 2</p>
        </div>
    </footer>
</body>
</html>
