<?php
/**
 * PIZZERIA ADRIANO - BESTELLBESTÄTIGUNG (confirmation.php)
 * 
 * Diese Seite wird nach einer erfolgreichen Bestellung angezeigt.
 * Sie zeigt alle Bestelldetails und Kundeninformationen an.
 * 
 * Wichtige Konzepte:
 * - Session-Daten abrufen und anzeigen
 * - Sicherheitscheck (Umleitung bei fehlenden Daten)
 * - Strukturierte Darstellung von Bestellinformationen
 * - Session-Cleanup nach Anzeige
 */

// Session starten für Zugriff auf Bestelldaten
session_start();

// SICHERHEITSCHECK: Überprüfung ob Bestelldaten vorhanden sind
// Falls keine letzte Bestellung in der Session existiert, zurück zur Startseite
if (!isset($_SESSION['last_order'])) {
    header('Location: index.php');
    exit;
}

// Bestelldaten aus Session abrufen
$order = $_SESSION['last_order'];
?>
<!-- 
    HTML-TEIL DER BESTÄTIGUNGSSEITE
    Anzeige der Bestelldetails und Kundeninformationen
-->
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellung bestätigt - Pizzeria Adriano</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- HEADER mit Navigation -->
    <header>
        <div class="container">
            <h1>🍕 Pizzeria Adriano</h1>
            <nav>
                <a href="index.php">Menu</a>
                <!-- Warenkorb ist jetzt leer (0), da Bestellung abgeschlossen -->
                <a href="cart.php" class="cart-link">Warenkorb (0)</a>
            </nav>
        </div>
    </header>

    <!-- HAUPTINHALT der Bestätigungsseite -->
    <main class="container">
        <!-- Erfolgsbestätigung -->
        <div class="order-confirmation">
            <h2>✅ Bestellung erfolgreich aufgegeben!</h2>
            <p>Vielen Dank für Ihre Bestellung bei Pizzeria Adriano!</p>
            
            <div class="order-number">
                Bestellnummer: <?php echo htmlspecialchars($order['order_number']); ?>
            </div>
            
            <p>Sie erhalten in Kürze eine Bestätigungs-E-Mail an <strong><?php echo htmlspecialchars($order['customer_data']['customer_email']); ?></strong></p>
        </div>

        <div class="cart-summary">
            <h3>Bestelldetails</h3>
            
            <div class="customer-info" style="margin-bottom: 2rem; padding: 1rem; background-color: #f8f9fa; border-radius: 5px;">
                <h4>Lieferadresse:</h4>
                <p>
                    <strong><?php echo htmlspecialchars($order['customer_data']['customer_name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['customer_data']['customer_address']); ?><br>
                    <?php echo htmlspecialchars($order['customer_data']['customer_zip']); ?> <?php echo htmlspecialchars($order['customer_data']['customer_city']); ?><br>
                    Tel: <?php echo htmlspecialchars($order['customer_data']['customer_phone']); ?>
                </p>
            </div>
            
            <h4>Bestellte Artikel:</h4>
            <?php foreach ($order['items'] as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h4><?php echo htmlspecialchars($item['pizza_name']); ?></h4>
                        <p>
                            Größe: <?php echo ucfirst($item['size']); ?> 
                            (€<?php echo number_format($item['base_price'], 2); ?>)
                            <?php if ($item['extra_cheese']): ?>
                                <br>+ Extra Käse (+€1.50)
                            <?php endif; ?>
                            <?php if ($item['stuffed_crust']): ?>
                                <br>+ Käse im Rand (+€2.00)
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="cart-item-price">€<?php echo number_format($item['total_price'], 2); ?></div>
                </div>
            <?php endforeach; ?>
            
            <div class="cart-total">
                <h3>Gesamtsumme: €<?php echo number_format($order['total'], 2); ?></h3>
            </div>
        </div>

        <div class="order-confirmation">
            <h3>Was passiert als Nächstes?</h3>
            <div style="text-align: left; max-width: 600px; margin: 0 auto;">
                <p>📧 <strong>Bestätigung:</strong> Sie erhalten eine E-Mail mit allen Bestelldetails</p>
                <p>👨‍🍳 <strong>Zubereitung:</strong> Unsere Köche bereiten Ihre Pizza frisch zu (ca. 20-25 Min.)</p>
                <p>🚗 <strong>Lieferung:</strong> Die Pizza wird direkt zu Ihnen geliefert (ca. 30-45 Min.)</p>
                <p>📞 <strong>Fragen?</strong> Rufen Sie uns an unter: <strong>+49 123 456789</strong></p>
            </div>
            
            <div style="margin-top: 2rem;">
                <a href="index.php" class="btn btn-primary">Neue Bestellung aufgeben</a>
            </div>
        </div>
    </main>    <footer>
        <div class="container">
            <p>&copy; 2025 Pizzeria Adriano. Alle Rechte vorbehalten.</p>
        </div>
    </footer>

    <?php
    // Clear the last order from session after displaying
    unset($_SESSION['last_order']);
    ?>
</body>
</html>
