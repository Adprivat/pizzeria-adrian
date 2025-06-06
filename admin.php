<?php
// Simple admin page to view orders (in a real application, this would need proper authentication)

$orders = [];
$order_items = [];

// Read orders
if (file_exists('data/orders.csv')) {
    $handle = fopen('data/orders.csv', 'r');
    $headers = fgetcsv($handle); // Skip header
    while (($data = fgetcsv($handle)) !== FALSE) {
        $orders[] = array_combine($headers, $data);
    }
    fclose($handle);
}

// Read order items
if (file_exists('data/order_items.csv')) {
    $handle = fopen('data/order_items.csv', 'r');
    $headers = fgetcsv($handle); // Skip header
    while (($data = fgetcsv($handle)) !== FALSE) {
        $order_items[] = array_combine($headers, $data);
    }
    fclose($handle);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bestellungen</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: white;
        }
        th, td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .order-details {
            background: white;
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>🍕 Admin - Bestellverwaltung</h1>
            <nav>
                <a href="index.php">Zurück zur Website</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Alle Bestellungen</h2>
        
        <?php if (empty($orders)): ?>
            <div class="cart-summary">
                <p>Noch keine Bestellungen vorhanden.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Bestellnummer</th>
                        <th>Datum</th>
                        <th>Kunde</th>
                        <th>E-Mail</th>
                        <th>Telefon</th>
                        <th>Adresse</th>
                        <th>Gesamtsumme</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($order['date']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['customer_address']); ?><br>
                                <?php echo htmlspecialchars($order['customer_zip']); ?> <?php echo htmlspecialchars($order['customer_city']); ?>
                            </td>
                            <td><strong>€<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Bestelldetails</h2>
            <?php 
            $current_order = '';
            foreach ($order_items as $item): 
                if ($current_order != $item['order_number']):
                    if ($current_order != '') echo '</div>';
                    $current_order = $item['order_number'];
            ?>
                    <div class="order-details">
                        <h3>Bestellung: <?php echo htmlspecialchars($item['order_number']); ?></h3>
            <?php endif; ?>
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
            <?php if ($current_order != '') echo '</div>'; ?>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Pizzeria Adrian. Alle Rechte vorbehalten.</p>
        </div>
    </footer>
</body>
</html>
