<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Product Page</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

    <a href="cart.php" style="text-decoration: none; color: black;">
        <i class="fas fa-shopping-cart" style="font-size: 24px; margin: 10px;"></i>
    </a>

    <h1>Product Page</h1>

    <?php
    // Include database connection file
    require_once 'db_connection.php';

    // Fetch products from the database
    $query = "SELECT * FROM products";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='product'>";
            echo "<h3>{$row['name']}</h3>";
            echo "<p>{$row['description']}</p>";
            echo "<p>Price: \${$row['price']}</p>";
            echo "<form action='cart.php' method='post'>";
            echo "<input type='hidden' name='product_id' value='{$row['id']}'>";
            echo "<input type='submit' name='add_to_cart' value='Add to Cart'>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<p>No products available</p>";
    }

    // Close database connection
    mysqli_close($conn);
    ?>

</body>
</html>
