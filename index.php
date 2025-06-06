<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Pizza data
$pizzas = [
    ['id' => 1, 'name' => 'Margherita', 'image' => 'pizza-margherita-nach-italienischer-art.jpg', 'price_small' => 8.50, 'price_medium' => 10.50, 'price_large' => 12.50],
    ['id' => 2, 'name' => 'Salami Mozzarella', 'image' => 'pizza-salami-mozzarella.jpg', 'price_small' => 9.50, 'price_medium' => 11.50, 'price_large' => 13.50],
    ['id' => 3, 'name' => 'Hawaii', 'image' => 'pizza-hawai.jpg', 'price_small' => 9.00, 'price_medium' => 11.00, 'price_large' => 13.00],
    ['id' => 4, 'name' => 'Tonno', 'image' => 'pizza-tonno.jpg', 'price_small' => 10.00, 'price_medium' => 12.00, 'price_large' => 14.00],
    ['id' => 5, 'name' => 'Capricciosa', 'image' => 'pizza-capricciosa.jpg', 'price_small' => 10.50, 'price_medium' => 12.50, 'price_large' => 14.50],
    ['id' => 6, 'name' => 'Al Funghi e Pancetta', 'image' => 'pizza-al-funghi-e-pancetta.jpg', 'price_small' => 11.00, 'price_medium' => 13.00, 'price_large' => 15.00]
];

// Handle form submission
if ($_POST) {
    $pizza_id = $_POST['pizza_id'];
    $size = $_POST['size'];
    $extra_cheese = isset($_POST['extra_cheese']) ? 1 : 0;
    $stuffed_crust = isset($_POST['stuffed_crust']) ? 1 : 0;
    
    // Find pizza
    $pizza = null;
    foreach ($pizzas as $p) {
        if ($p['id'] == $pizza_id) {
            $pizza = $p;
            break;
        }
    }
    
    if ($pizza) {
        $base_price = $pizza['price_' . $size];
        $extra_cost = 0;
        if ($extra_cheese) $extra_cost += 1.50;
        if ($stuffed_crust) $extra_cost += 2.00;
        
        $item = [
            'pizza_id' => $pizza_id,
            'pizza_name' => $pizza['name'],
            'size' => $size,
            'base_price' => $base_price,
            'extra_cheese' => $extra_cheese,
            'stuffed_crust' => $stuffed_crust,
            'extra_cost' => $extra_cost,
            'total_price' => $base_price + $extra_cost,
            'quantity' => 1
        ];
        
        $_SESSION['cart'][] = $item;
        $message = "Pizza wurde zum Warenkorb hinzugefügt!";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Pizza Bestellung - Pizzeria Adrian</title>
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
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>        <section class="hero">
            <h2>Willkommen bei Pizzeria Adrian!</h2>
            <p>Bestellen Sie jetzt Ihre liebste Pizza - frisch aus dem Ofen!</p>
        </section>

        <section class="pizza-menu">
            <h2>Unsere Pizzen</h2>
            <div class="pizza-grid">
                <?php foreach ($pizzas as $pizza): ?>
                    <div class="pizza-card">
                        <img src="img/<?php echo $pizza['image']; ?>" alt="<?php echo $pizza['name']; ?>">
                        <div class="pizza-info">
                            <h3><?php echo $pizza['name']; ?></h3>
                            <div class="prices">
                                <span>Klein: €<?php echo number_format($pizza['price_small'], 2); ?></span>
                                <span>Mittel: €<?php echo number_format($pizza['price_medium'], 2); ?></span>
                                <span>Groß: €<?php echo number_format($pizza['price_large'], 2); ?></span>
                            </div>
                            
                            <form method="POST" class="order-form">
                                <input type="hidden" name="pizza_id" value="<?php echo $pizza['id']; ?>">
                                
                                <div class="form-group">
                                    <label>Größe:</label>
                                    <select name="size" required>
                                        <option value="small">Klein (€<?php echo number_format($pizza['price_small'], 2); ?>)</option>
                                        <option value="medium" selected>Mittel (€<?php echo number_format($pizza['price_medium'], 2); ?>)</option>
                                        <option value="large">Groß (€<?php echo number_format($pizza['price_large'], 2); ?>)</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="extra_cheese">
                                        Extra Käse (+€1.50)
                                    </label>
                                </div>
                                
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="stuffed_crust">
                                        Käse im Rand (+€2.00)
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">In den Warenkorb</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>    <footer>
        <div class="container">
            <p>&copy; 2025 Pizzeria Adrian. Alle Rechte vorbehalten.</p>
        </div>
    </footer>
</body>
</html>
