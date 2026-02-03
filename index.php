<?php
// strona glowna sklepu - lista produktow
// wyswietla wszystkie dostepne produkty z mozliwoscia dodania do koszyka

// laduje plik konfiguracyjny z polaczeniem do bazy i sesja
require_once 'config.php';

// pobieram wszystkie dostepne produkty z bazy danych
// where dostepnosc = true - tylko te ktore sa w sprzedazy
// order by nazwa - sortuje alfabetycznie
$stmt = $pdo->query("SELECT * FROM produkty WHERE dostepnosc = TRUE ORDER BY nazwa");
$produkty = $stmt->fetchAll();

// licze ile produktow jest w koszyku zeby wyswietlic licznik w naglowku
$ilosc_w_koszyku = 0;
if (isset($_SESSION['koszyk'])) {
    // sumuje ilosci wszystkich produktow w koszyku
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
    <!-- viewport dla responsywnosci na urzadzeniach mobilnych -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SHOP_NAME; ?> - Strona główna</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; min-height: 100vh; line-height: 1.6; color: #000000; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .header { background-color: #ffffff; padding: 20px 0; border-bottom: 1px solid #000000; position: sticky; top: 0; z-index: 100; }
        .header .container { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 18px; color: #000000; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; }
        .nav { display: flex; gap: 30px; }
        .nav-link { text-decoration: none; color: #000000; font-weight: 500; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; padding: 8px 0; border-bottom: 2px solid transparent; transition: all 0.2s; }
        .nav-link:hover, .nav-link.active { border-bottom-color: #000000; }
        .koszyk-link { position: relative; }
        .badge { position: absolute; top: -8px; right: -12px; background-color: #000000; color: #ffffff; font-size: 10px; padding: 2px 6px; font-weight: 600; }
        main { padding: 60px 0; }
        main h2 { color: #000000; text-align: center; font-size: 32px; font-weight: 700; margin-bottom: 50px; letter-spacing: -0.5px; }
        .alert { padding: 16px 20px; margin-bottom: 30px; text-align: center; font-size: 14px; border: 1px solid #000000; background-color: #f5f5f5; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        .product-card { background-color: #ffffff; border: 1px solid #e0e0e0; transition: all 0.2s; }
        .product-card:hover { border-color: #000000; }
        .product-image { background-color: #f5f5f5; height: 200px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 20px; border-bottom: 1px solid #e0e0e0; }
        .product-image img { max-width: 140px; max-height: 180px; object-fit: contain; }
        .product-info { padding: 25px; }
        .product-title { font-size: 16px; color: #000000; margin-bottom: 5px; font-weight: 600; }
        .product-category { color: #666666; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .product-desc { color: #666666; font-size: 13px; margin-bottom: 15px; line-height: 1.5; height: 60px; overflow: hidden; }
        .product-price { font-size: 20px; font-weight: 700; color: #000000; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 14px 28px; border: none; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; text-transform: uppercase; letter-spacing: 1px; font-family: inherit; text-align: center; }
        .btn-add { width: 100%; background-color: #000000; color: #ffffff; }
        .btn-add:hover { background-color: #333333; }
        .footer { background-color: #f5f5f5; padding: 30px 0; text-align: center; border-top: 1px solid #e0e0e0; margin-top: 60px; }
        .footer p { color: #666666; font-size: 13px; }
    </style>
</head>
<body>
    <!-- naglowek sklepu z logo i nawigacja -->
    <header class="header">
        <div class="container">
            <!-- nazwa sklepu pobrana ze stalej -->
            <h1 class="logo"><?php echo SHOP_NAME; ?></h1>
            <!-- menu nawigacyjne -->
            <nav class="nav">
                <a href="index.php" class="nav-link active">Produkty</a>
                <!-- link do koszyka z licznikiem produktow -->
                <a href="koszyk.php" class="nav-link koszyk-link">
                    Koszyk 
                    <?php if ($ilosc_w_koszyku > 0): ?>
                        <!-- badge pokazuje ile produktow jest w koszyku -->
                        <span class="badge"><?php echo $ilosc_w_koszyku; ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Nasze książki</h2>
        
        <!-- komunikat o dodaniu produktu do koszyka -->
        <?php if (isset($_GET['dodano'])): ?>
            <div class="alert success">
                Produkt został dodany do koszyka!
            </div>
        <?php endif; ?>

        <!-- siatka produktow - wyswietlam wszystkie ksiazki -->
        <div class="products-grid">
            <?php foreach ($produkty as $produkt): ?>
                <!-- karta pojedynczego produktu -->
                <div class="product-card">
                    <!-- zdjecie produktu -->
                    <div class="product-image">
                        <!-- htmlspecialchars chroni przed atakami xss -->
                        <img src="<?php echo htmlspecialchars($produkt['zdjecie']); ?>" alt="<?php echo htmlspecialchars($produkt['nazwa']); ?>">
                    </div>
                    <div class="product-info">
                        <!-- tytul ksiazki -->
                        <h3 class="product-title"><?php echo htmlspecialchars($produkt['nazwa']); ?></h3>
                        <!-- kategoria -->
                        <p class="product-category"><?php echo htmlspecialchars($produkt['kategoria']); ?></p>
                        <!-- krotki opis -->
                        <p class="product-desc"><?php echo htmlspecialchars($produkt['opis']); ?></p>
                        <!-- cena sformatowana z 2 miejscami po przecinku -->
                        <p class="product-price"><?php echo number_format($produkt['cena'], 2, ',', ' '); ?> zł</p>
                        
                        <!-- formularz do dodawania produktu do koszyka -->
                        <form action="dodaj_do_koszyka.php" method="POST">
                            <!-- ukryte pole z id produktu -->
                            <input type="hidden" name="produkt_id" value="<?php echo $produkt['id']; ?>">
                            <button type="submit" class="btn btn-add">Dodaj do koszyka</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- stopka strony -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 <?php echo SHOP_NAME; ?> - Projekt cząstkowy nr 2</p>
        </div>
    </footer>
</body>
</html>
