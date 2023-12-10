<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Function to update total price in real-time
        function updateTotalPrice(index) {
            var quantity = parseInt($('#quantity_' + index).val()) || 0;
            var price = parseFloat($('#price_' + index).text().replace('$', '')) || 0;
            var total = quantity * price;
            $('#total_' + index).text('$' + total.toFixed(2));

            // Calculate the overall total price
            var overallTotal = 0;
            $('.total-price').each(function() {
                overallTotal += parseFloat($(this).text().replace('$', '')) || 0;
            });
            $('#overall-total').text('Overall Total: $' + overallTotal.toFixed(2));
        }

        // Function to calculate the initial total price
        function calculateInitialTotal() {
            var overallTotal = 0;
            $('.total-price').each(function() {
                overallTotal += parseFloat($(this).text().replace('$', '')) || 0;
            });
            $('#overall-total').text('Overall Total: $' + overallTotal.toFixed(2));
        }

        // Call the initial total calculation on page load
        $(document).ready(function() {
            calculateInitialTotal();
        });
    </script>
</head>
<body>

    <h1>Shopping Cart</h1>

    <?php
    session_start();

    // Include database connection file
    require_once 'db_connection.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
            // Add product to the cart
            $product_id = $_POST['product_id'];
            $query = "SELECT * FROM products WHERE id = $product_id";
            $result = mysqli_query($conn, $query);

            if ($result && $row = mysqli_fetch_assoc($result)) {
                // Check if the product is already in the cart
                $found = false;
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $row['id']) {
                        $item['quantity'] += 1;
                        $found = true;
                        break;
                    }
                }

                // If not found, add it to the cart
                if (!$found) {
                    $row['quantity'] = 1;
                    $_SESSION['cart'][] = $row;
                }

                echo "<p>Product '{$row['name']}' added to cart</p>";
            } else {
                echo "<p>Product not found</p>";
            }
        } elseif (isset($_POST['update_quantity']) && isset($_POST['cart_index']) && isset($_POST['quantity'])) {
            // Update quantity of a cart item
            $cart_index = $_POST['cart_index'];
            $quantity = (int)$_POST['quantity'];

            if ($quantity > 0) {
                $_SESSION['cart'][$cart_index]['quantity'] = $quantity;
            } else {
                // Remove the item if the quantity is 0 or less
                unset($_SESSION['cart'][$cart_index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the array
            }
        } elseif (isset($_POST['remove_from_cart']) && isset($_POST['cart_index'])) {
            // Remove item from cart
            $cart_index = $_POST['cart_index'];
            unset($_SESSION['cart'][$cart_index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the array
        }
    }

    // Display cart contents
    if (!empty($_SESSION['cart'])) {
        echo "<h2>Cart Contents</h2>";

        foreach ($_SESSION['cart'] as $key => $item) {
            echo "<div class='cart-item'>";
            echo "<h3>{$item['name']}</h3>";
            echo "<p>{$item['description']}</p>";
            echo "<p>Price: <span id='price_$key'>\${$item['price']}</span></p>";

            // Add form for updating quantity and removing item
            echo "<form action='cart.php' method='post'>";
            echo "<input type='hidden' name='cart_index' value='$key'>";
            echo "<label for='quantity'>Quantity:</label>";
            echo "<input type='number' id='quantity_$key' name='quantity' value='{$item['quantity']}' min='1' onchange='updateTotalPrice($key)'>";
            echo "<input type='submit' name='update_quantity' value='Update'>";
            echo "<input type='submit' name='remove_from_cart' value='Remove'>";
            echo "</form>";

            // Display total price for each item
            $totalPrice = $item['quantity'] * $item['price'];
            echo "<p>Total: <span id='total_$key' class='total-price'>\${$totalPrice}</span></p>";

            echo "</div>";
        }

        // Display overall total price
        echo "<p id='overall-total'>Overall Total: \$0.00</p>";
        // Add a button to redirect back to the product page
        echo "<form action='index.php' method='get'>";
        echo "<input type='submit' value='Back to Product Page'>";
        echo "</form>";

        echo "<form action='payment.php' method='get'>";
        echo "<input type='submit' value='Proceed to Payment'>";
        echo "</form>";
    } else {
        echo "<p>Cart is empty</p>";

        echo "<form action='index.php' method='get'>";
        echo "<input type='submit' value='Back to Product Page'>";
        echo "</form>";
    }

    // Close database connection
    mysqli_close($conn);
    ?>

</body>
</html>