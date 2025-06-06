<?php
/**
 * PIZZERIA ADRIANO - HAUPTSEITE (index.php)
 * 
 * Diese Datei stellt die Hauptseite der Pizza-Bestellanwendung dar.
 * Hier können Kunden Pizzen auswählen, konfigurieren und zum Warenkorb hinzufügen.
 * 
 * Wichtige Konzepte:
 * - PHP Session Management für Warenkorb-Persistierung
 * - Dynamische Formularverarbeitung
 * - Array-basierte Datenstrukturen
 * - Sicherheitsfunktionen (htmlspecialchars)
 */

// Session starten - ermöglicht das Speichern von Daten zwischen Seitenaufrufen
session_start();

// Warenkorb initialisieren falls noch nicht vorhanden
// Die Session wird verwendet, um den Warenkorb über mehrere Seitenaufrufe zu erhalten
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Pizza-Datenbank als assoziatives Array
// In einer realen Anwendung würden diese Daten aus einer Datenbank kommen
$pizzas = [
    ['id' => 1, 'name' => 'Margherita', 'image' => 'pizza-margherita-nach-italienischer-art.jpg', 'price_small' => 8.50, 'price_medium' => 10.50, 'price_large' => 12.50],
    ['id' => 2, 'name' => 'Salami Mozzarella', 'image' => 'pizza-salami-mozzarella.jpg', 'price_small' => 9.50, 'price_medium' => 11.50, 'price_large' => 13.50],
    ['id' => 3, 'name' => 'Hawaii', 'image' => 'pizza-hawai.jpg', 'price_small' => 9.00, 'price_medium' => 11.00, 'price_large' => 13.00],
    ['id' => 4, 'name' => 'Tonno', 'image' => 'pizza-tonno.jpg', 'price_small' => 10.00, 'price_medium' => 12.00, 'price_large' => 14.00],
    ['id' => 5, 'name' => 'Capricciosa', 'image' => 'pizza-capricciosa.jpg', 'price_small' => 10.50, 'price_medium' => 12.50, 'price_large' => 14.50],
    ['id' => 6, 'name' => 'Al Funghi e Pancetta', 'image' => 'pizza-al-funghi-e-pancetta.jpg', 'price_small' => 11.00, 'price_medium' => 13.00, 'price_large' => 15.00]
];

// FORMULARVERARBEITUNG - Hinzufügen zum Warenkorb
// Überprüfung ob POST-Daten gesendet wurden (Formular wurde abgeschickt)
if ($_POST) {
    // Formulardaten sicher abrufen
    $pizza_id = $_POST['pizza_id'];          // ID der ausgewählten Pizza
    $size = $_POST['size'];                  // Gewählte Größe (small, medium, large)
    $extra_cheese = isset($_POST['extra_cheese']) ? 1 : 0;   // Checkbox-Wert (1 oder 0)
    $stuffed_crust = isset($_POST['stuffed_crust']) ? 1 : 0; // Checkbox-Wert (1 oder 0)
    
    // Pizza in der Datenstruktur finden
    // Durchsuche das $pizzas Array nach der entsprechenden ID
    $pizza = null;
    foreach ($pizzas as $p) {
        if ($p['id'] == $pizza_id) {
            $pizza = $p;
            break; // Sobald gefunden, Schleife verlassen
        }
    }
    
    // Wenn Pizza gefunden wurde, Warenkorb-Item erstellen
    if ($pizza) {
        // Grundpreis basierend auf gewählter Größe ermitteln
        $base_price = $pizza['price_' . $size];
        
        // Zusatzkosten für Extras berechnen
        $extra_cost = 0;
        if ($extra_cheese) $extra_cost += 1.50;    // Extra Käse kostet 1,50€
        if ($stuffed_crust) $extra_cost += 2.00;   // Käse im Rand kostet 2,00€
        
        // Warenkorb-Item als assoziatives Array erstellen
        // Diese Struktur enthält alle wichtigen Informationen für die spätere Verarbeitung
        $item = [
            'pizza_id' => $pizza_id,                           // Referenz zur Pizza
            'pizza_name' => $pizza['name'],                    // Name für Anzeige
            'size' => $size,                                   // Größe für Anzeige
            'base_price' => $base_price,                       // Grundpreis            'extra_cheese' => $extra_cheese,                   // Extra Käse (ja/nein)
            'stuffed_crust' => $stuffed_crust,                 // Käse im Rand (ja/nein)
            'extra_cost' => $extra_cost,                       // Zusatzkosten
            'total_price' => $base_price + $extra_cost,        // Gesamtpreis
            'quantity' => 1                                    // Menge (vorerst immer 1)
        ];
        
        // Item zum Warenkorb hinzufügen (Session-Array)
        // Der [] Operator fügt das Element am Ende des Arrays hinzu
        $_SESSION['cart'][] = $item;
        
        // Erfolgsmeldung setzen (wird später im HTML angezeigt)
        $message = "Pizza wurde zum Warenkorb hinzugefügt!";
    }
}
?>
<!-- 
    HTML-TEIL DER ANWENDUNG
    Ab hier beginnt die Benutzeroberfläche der Anwendung.
    Das HTML wird mit PHP-Code vermischt (eingebettetes PHP).
-->
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <!-- Responsive Design für mobile Geräte -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza Bestellung - Pizzeria Adriano</title>
    <!-- Einbindung der externen CSS-Datei für das Styling -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- HEADER-BEREICH mit Navigation -->
    <header>
        <div class="container">
            <h1>🍕 Pizzeria Adriano</h1>
            <nav>
                <a href="index.php">Menu</a>
                <!-- Dynamische Anzeige der Warenkorb-Anzahl mit PHP -->
                <a href="cart.php" class="cart-link">Warenkorb (<?php echo count($_SESSION['cart']); ?>)</a>
            </nav>
        </div>    </header>

    <!-- HAUPTINHALT der Seite -->
    <main class="container">
        <!-- Bedingte Anzeige der Erfolgsmeldung -->
        <!-- Nur wenn $message gesetzt ist, wird die Meldung angezeigt -->
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- HERO-SEKTION mit Willkommensnachricht -->
        <section class="hero">
            <h2>Willkommen bei Pizzeria Adriano!</h2>
            <p>Bestellen Sie jetzt Ihre liebste Pizza - frisch aus dem Ofen!</p>
        </section>

        <!-- PIZZA-MENU Sektion -->
        <section class="pizza-menu">
            <h2>Unsere Pizzen</h2>
            <!-- Grid-Layout für responsive Pizza-Karten -->
            <div class="pizza-grid">
                <!-- FOREACH-SCHLEIFE: Iteration über alle Pizzen im Array -->
                <?php foreach ($pizzas as $pizza): ?>
                    <div class="pizza-card">
                        <!-- Dynamische Bildeinbindung mit Pizza-Namen als Alt-Text -->
                        <img src="img/<?php echo $pizza['image']; ?>" alt="<?php echo $pizza['name']; ?>">
                        <div class="pizza-info">
                            <!-- Pizza-Name sicher ausgeben (XSS-Schutz) -->
                            <h3><?php echo htmlspecialchars($pizza['name']); ?></h3>
                            <div class="prices">
                                <!-- Preisanzeige mit number_format für korrekte Währungsformatierung -->
                                <span>Klein: €<?php echo number_format($pizza['price_small'], 2); ?></span>                                <span>Mittel: €<?php echo number_format($pizza['price_medium'], 2); ?></span>
                                <span>Groß: €<?php echo number_format($pizza['price_large'], 2); ?></span>
                            </div>
                            
                            <!-- BESTELLFORMULAR für jede Pizza -->
                            <!-- Das Formular sendet Daten per POST an dieselbe Seite -->
                            <form method="POST" class="order-form">
                                <!-- Hidden Field: Pizza-ID zur Identifikation im Backend -->
                                <input type="hidden" name="pizza_id" value="<?php echo $pizza['id']; ?>">
                                
                                <!-- Größenauswahl per Dropdown -->
                                <div class="form-group">
                                    <label>Größe:</label>
                                    <select name="size" required>
                                        <option value="small">Klein (€<?php echo number_format($pizza['price_small'], 2); ?>)</option>
                                        <option value="medium" selected>Mittel (€<?php echo number_format($pizza['price_medium'], 2); ?>)</option>
                                        <option value="large">Groß (€<?php echo number_format($pizza['price_large'], 2); ?>)</option>
                                    </select>
                                </div>
                                
                                <!-- Checkbox für Extra Käse -->
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="extra_cheese">                                        Extra Käse (+€1.50)
                                    </label>
                                </div>
                                
                                <!-- Checkbox für Käse im Rand -->
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="stuffed_crust">
                                        Käse im Rand (+€2.00)
                                    </label>
                                </div>
                                
                                <!-- Submit-Button zum Hinzufügen in den Warenkorb -->
                                <button type="submit" class="btn btn-primary">In den Warenkorb</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?> <!-- Ende der Pizza-Schleife -->            </div>
        </section>
    </main>

    <!-- FOOTER-BEREICH -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Pizzeria Adriano. Alle Rechte vorbehalten.</p>
        </div>
    </footer>
</body>
</html>
