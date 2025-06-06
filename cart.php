<?php
/**
 * PIZZERIA ADRIANO - WARENKORB & CHECKOUT (cart.php)
 * 
 * Diese Datei behandelt den Warenkorb und den Checkout-Prozess:
 * - Anzeige der Warenkorb-Inhalte
 * - Entfernen von Artikeln aus dem Warenkorb
 * - Erfassung der Kundendaten
 * - Bestellverarbeitung und CSV-Speicherung
 * - Weiterleitung zur Bestätigungsseite
 * 
 * Wichtige Konzepte:
 * - Array-Manipulation (unset, array_values)
 * - Formularvalidierung
 * - CSV-Dateioperationen
 * - Transaktionale Datenverarbeitung
 */

// Session starten für Warenkorb-Zugriff
session_start();

// Warenkorb initialisieren falls nicht vorhanden
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Variablen für Nachrichten initialisieren
$error = '';
$success = '';

// ARTIKEL AUS WARENKORB ENTFERNEN
// Überprüfung ob eine "Entfernen"-Aktion ausgeführt werden soll
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] == 'remove') {
        $index = $_POST['index'];  // Index des zu entfernenden Artikels
        
        // Überprüfung ob der Index im Warenkorb existiert
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);                    // Artikel entfernen
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Array-Indizes neu organisieren
            $success = 'Artikel wurde entfernt.';
        }
    }
}

// BESTELLUNG AUFGEBEN - Hauptverarbeitungslogik
if ($_POST && isset($_POST['place_order'])) {
    // Kundendaten aus Formular abrufen und bereinigen
    $name = trim($_POST['name']);           // trim() entfernt Leerzeichen am Anfang/Ende
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $zip = trim($_POST['zip']);
    
    // EINGABEVALIDIERUNG
    // Überprüfung aller Pflichtfelder und Email-Format
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($zip)) {
        $error = 'Bitte füllen Sie alle Pflichtfelder aus.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  // PHP-eigene Email-Validierung
        $error = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';    } elseif (empty($_SESSION['cart'])) {
        $error = 'Ihr Warenkorb ist leer.';} else {
        // BESTELLVERARBEITUNG - Alle Validierungen erfolgreich
        
        // Eindeutige Bestellnummer generieren
        // Format: ORD-YYYYMMDD-XXXX (z.B. ORD-20250606-1234)
        $order_number = 'ORD-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
        
        // Gesamtsumme aus Warenkorb berechnen
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['total_price'];  // Alle Einzelpreise addieren
        }
        
        // Bestelldaten als assoziatives Array strukturieren
        // Diese Struktur entspricht den CSV-Spalten in orders.csv
        $order_data = [
            'order_number' => $order_number,
            'date' => date('Y-m-d H:i:s'),     // Aktueller Timestamp
            'customer_name' => $name,
            'customer_email' => $email,
            'customer_phone' => $phone,
            'customer_address' => $address,
            'customer_city' => $city,
            'customer_zip' => $zip,
            'total_amount' => $total,
            'status' => 'pending'                     // Bestellstatus initial auf "pending"
        ];
        
        // CSV-DATEI OPERATIONEN für Hauptbestellung (orders.csv)
        $orders_file = 'data/orders.csv';
        
        // Datenverzeichnis erstellen falls nicht vorhanden
        if (!file_exists('data')) {
            mkdir('data', 0777, true);  // Rekursiv mit Schreibrechten
        }
        
        $file_exists = file_exists($orders_file);
        $fp = fopen($orders_file, 'a');  // 'a' = append mode (anhängen)
        
        // Wenn Datei neu ist, zuerst CSV-Header schreiben
        if (!$file_exists) {
            fputcsv($fp, array_keys($order_data));  // Spaltenüberschriften aus Array-Keys
        }
        
        // Bestelldaten als CSV-Zeile anhängen
        fputcsv($fp, $order_data);
        fclose($fp);
          // CSV-DATEI OPERATIONEN für Bestellpositionen (order_items.csv)
        $items_file = 'data/order_items.csv';
        $file_exists = file_exists($items_file);
        $fp = fopen($items_file, 'a');
        if (!$file_exists) {
            // Header für order_items.csv schreiben
            fputcsv($fp, ['order_number', 'pizza_name', 'size', 'base_price', 'extra_cheese', 'stuffed_crust', 'extra_cost', 'total_price']);
        }
        
        // Alle Warenkorb-Items in CSV schreiben
        // Jeder Artikel wird als separate Zeile mit Bestellnummer verknüpft
        foreach ($_SESSION['cart'] as $item) {
            $item_data = [
                $order_number,                  // Foreign Key zur Hauptbestellung
                $item['pizza_name'],            // Pizza-Name
                $item['size'],                  // Größe
                $item['base_price'],            // Grundpreis
                $item['extra_cheese'],          // Extra Käse (1/0)
                $item['stuffed_crust'],         // Käse im Rand (1/0)
                $item['extra_cost'],            // Zusatzkosten
                $item['total_price']            // Gesamtpreis des Items
            ];
            fputcsv($fp, $item_data);
        }
        fclose($fp);
        
        // BESTELLDATEN für Bestätigungsseite in Session speichern
        // Diese Daten werden auf der confirmation.php angezeigt
        $_SESSION['last_order'] = [
            'order_number' => $order_number,            'customer_data' => $order_data,
            'items' => $_SESSION['cart'],
            'total' => $total
        ];
        
        // Warenkorb leeren nach erfolgreicher Bestellung
        $_SESSION['cart'] = array();
        
        // Weiterleitung zur Bestätigungsseite
        header('Location: confirmation.php');
        exit;  // Wichtig: Script-Ausführung beenden nach Redirect
    }
}

// WARENKORB-GESAMTSUMME berechnen für Anzeige
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['total_price'];
}
?>
<!-- 
    HTML-TEIL DER WARENKORB-SEITE
    Anzeige des Warenkorbs und Checkout-Formular
-->
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warenkorb - Pizzeria Adriano</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>    <!-- HEADER mit Navigation -->
    <header>
        <div class="container">
            <h1>🍕 Pizzeria Adriano</h1>
            <nav>
                <a href="index.php">Menu</a>
                <!-- Dynamische Warenkorb-Anzeige -->
                <a href="cart.php" class="cart-link">Warenkorb (<?php echo count($_SESSION['cart']); ?>)</a>
            </nav>
        </div>
    </header>

    <!-- HAUPTINHALT der Warenkorb-Seite -->    <main class="container">
        <h2>Ihr Warenkorb</h2>
        
        <!-- NACHRICHTEN-ANZEIGE - Bedingte Ausgabe von Fehlern und Erfolg -->
        <?php if ($error): ?>
            <!-- XSS-Schutz: htmlspecialchars() verhindert Code-Injection -->
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- WARENKORB-INHALT - Unterschiedliche Anzeige je nach Warenkorb-Status -->
        <?php if (empty($_SESSION['cart'])): ?>
            <!-- LEERER WARENKORB - Fallback-Anzeige -->            <!-- LEERER WARENKORB - Fallback-Anzeige -->
            <div class="cart-summary">
                <p>Ihr Warenkorb ist leer.</p>
                <a href="index.php" class="btn btn-primary">Zurück zum Menu</a>
            </div>
        <?php else: ?>
            <!-- GEFÜLLTER WARENKORB - Anzeige aller Items mit Entfernen-Option -->
            <div class="cart-summary">
                <h3>Bestellübersicht</h3>
                
                <!-- WARENKORB-SCHLEIFE - Iteration durch alle Cart-Items -->
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="cart-item">
                        <!-- ITEM-INFORMATIONEN - Pizza-Details und Zusätze -->
                        <div class="cart-item-info">
                            <!-- XSS-Schutz bei Ausgabe von Benutzerdaten -->
                            <h4><?php echo htmlspecialchars($item['pizza_name']); ?></h4>
                            <p>
                                <!-- ucfirst() macht ersten Buchstaben groß -->
                                Größe: <?php echo ucfirst($item['size']); ?> 
                                (€<?php echo number_format($item['base_price'], 2); ?>)
                                
                                <!-- Bedingte Anzeige der Zusatzoptionen -->
                                <?php if ($item['extra_cheese']): ?>
                                    <br>+ Extra Käse (+€1.50)
                                <?php endif; ?>
                                <?php if ($item['stuffed_crust']): ?>
                                    <br>+ Käse im Rand (+€2.00)
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <!-- ITEM-AKTIONEN - Preis und Entfernen-Button -->
                        <div class="cart-item-actions">
                            <!-- number_format() formatiert Preis mit 2 Dezimalstellen -->
                            <div class="cart-item-price">€<?php echo number_format($item['total_price'], 2); ?></div>
                            
                            <!-- ENTFERNEN-FORMULAR - POST-Request zum Löschen -->
                            <form method="POST" style="display: inline;">
                                <!-- Hidden Fields für Aktion und Array-Index -->
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                <button type="submit" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">Entfernen</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- GESAMTSUMME - Berechnung und Anzeige -->
                <div class="cart-total">
                    <h3>Gesamtsumme: €<?php echo number_format($cart_total, 2); ?></h3>
                </div>
            </div>            <!-- KUNDEN-FORMULAR - Checkout-Bereich für Bestelldaten -->
            <div class="customer-form">
                <h3>Ihre Daten</h3>
                <form method="POST">
                    <!-- Hidden Field signalisiert Bestellabsendung -->
                    <input type="hidden" name="place_order" value="1">
                    
                    <!-- ERSTE ZEILE - Name und E-Mail nebeneinander -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <!-- STICKY FORMS - Wert bleibt nach Validierungsfehlern erhalten -->
                            <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail *</label>
                            <!-- HTML5 type="email" für Client-seitige Validierung -->
                            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <!-- ZWEITE ZEILE - Telefon und PLZ nebeneinander -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Telefon *</label>
                            <!-- HTML5 type="tel" für bessere mobile Tastatur -->
                            <input type="tel" id="phone" name="phone" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="zip">PLZ *</label>
                            <input type="text" id="zip" name="zip" required value="<?php echo isset($_POST['zip']) ? htmlspecialchars($_POST['zip']) : ''; ?>">
                        </div>
                    </div>
                    
                    <!-- ADRESSE - Volle Breite -->
                    <div class="form-group">
                        <label for="address">Adresse *</label>
                        <input type="text" id="address" name="address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                    </div>
                    
                    <!-- STADT - Volle Breite -->
                    <div class="form-group">
                        <label for="city">Stadt *</label>
                        <input type="text" id="city" name="city" required value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                    </div>
                    
                    <!-- BUTTON-BEREICH - Zurück und Bestellen -->
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="index.php" class="btn btn-secondary" style="margin-right: 1rem;">Zurück zum Menu</a>
                        <!-- Dynamischer Preis im Button-Text -->
                        <button type="submit" class="btn btn-success">Jetzt bestellen (€<?php echo number_format($cart_total, 2); ?>)</button>
                    </div>
                </form>
            </div>        <?php endif; ?>
    </main>

    <!-- FOOTER - Standard-Fußbereich -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Pizzeria Adriano. Alle Rechte vorbehalten.</p>
        </div>
    </footer>
</body>
</html>
