<?php
session_start();


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$error = '';
$success = '';


if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] == 'remove') {
        $index = $_POST['index'];
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); 
            $success = 'Artikel wurde entfernt.';
        }
    }
}


if ($_POST && isset($_POST['place_order'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $zip = trim($_POST['zip']);
    
    
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($zip)) {
        $error = 'Bitte füllen Sie alle Pflichtfelder aus.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    } elseif (empty($_SESSION['cart'])) {
        $error = 'Ihr Warenkorb ist leer.';
    } else {
        
        $order_number = 'ORD-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
        
        
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['total_price'];
        }
        
        
        $order_data = [
            'order_number' => $order_number,
            'date' => date('Y-m-d H:i:s'),
            'customer_name' => $name,
            'customer_email' => $email,
            'customer_phone' => $phone,
            'customer_address' => $address,
            'customer_city' => $city,
            'customer_zip' => $zip,
            'total_amount' => $total,
            'status' => 'pending'
        ];
        
        
        $orders_file = 'data/orders.csv';
        if (!file_exists('data')) {
            mkdir('data', 0777, true);
        }
        
        $file_exists = file_exists($orders_file);
        $fp = fopen($orders_file, 'a');
        
        if (!$file_exists) {
            
            fputcsv($fp, array_keys($order_data));
        }
        
        fputcsv($fp, $order_data);
        fclose($fp);
        
        
        $items_file = 'data/order_items.csv';
        $fp = fopen($items_file, 'a');
        
        $file_exists = file_exists($items_file);
        if (!$file_exists) {
            
            fputcsv($fp, ['order_number', 'pizza_name', 'size', 'base_price', 'extra_cheese', 'stuffed_crust', 'extra_cost', 'total_price']);
        }
        
        foreach ($_SESSION['cart'] as $item) {
            $item_data = [
                $order_number,
                $item['pizza_name'],
                $item['size'],
                $item['base_price'],
                $item['extra_cheese'],
                $item['stuffed_crust'],
                $item['extra_cost'],
                $item['total_price']
            ];
            fputcsv($fp, $item_data);
        }
        fclose($fp);
        
        
        $_SESSION['last_order'] = [
            'order_number' => $order_number,
            'customer_data' => $order_data,
            'items' => $_SESSION['cart'],
            'total' => $total
        ];
        
        // Clear cart
        $_SESSION['cart'] = array();
        
        // Redirect to confirmation
        header('Location: confirmation.php');
        exit;
    }
}

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['total_price'];
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warenkorb - Pizzeria Adrian</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🍕 Pizzeria Adrian</h1>
            <nav>
                <a href="index.php">Menu</a>
                <a href="cart.php" class="cart-link">Warenkorb (<?php echo count($_SESSION['cart']); ?>)</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Ihr Warenkorb</h2>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="cart-summary">
                <p>Ihr Warenkorb ist leer.</p>
                <a href="index.php" class="btn btn-primary">Zurück zum Menu</a>
            </div>
        <?php else: ?>
            <div class="cart-summary">
                <h3>Bestellübersicht</h3>
                
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
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
                        <div class="cart-item-actions">
                            <div class="cart-item-price">€<?php echo number_format($item['total_price'], 2); ?></div>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                <button type="submit" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">Entfernen</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="cart-total">
                    <h3>Gesamtsumme: €<?php echo number_format($cart_total, 2); ?></h3>
                </div>
            </div>

            <div class="customer-form">
                <h3>Ihre Daten</h3>
                <form method="POST">
                    <input type="hidden" name="place_order" value="1">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail *</label>
                            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Telefon *</label>
                            <input type="tel" id="phone" name="phone" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="zip">PLZ *</label>
                            <input type="text" id="zip" name="zip" required value="<?php echo isset($_POST['zip']) ? htmlspecialchars($_POST['zip']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Adresse *</label>
                        <input type="text" id="address" name="address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="city">Stadt *</label>
                        <input type="text" id="city" name="city" required value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                    </div>
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="index.php" class="btn btn-secondary" style="margin-right: 1rem;">Zurück zum Menu</a>
                        <button type="submit" class="btn btn-success">Jetzt bestellen (€<?php echo number_format($cart_total, 2); ?>)</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Pizzeria Adrian. Alle Rechte vorbehalten.</p>
        </div>
    </footer>
</body>
</html>
