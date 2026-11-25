<?php
session_start();

// 1. Check if User is Logged In
if (!isset($_SESSION['email'])) {
    if (isset($_POST['Product_ID']) || isset($_GET['product_id'])) {
        $pid = isset($_POST['Product_ID']) ? intval($_POST['Product_ID']) : intval($_GET['product_id']);
        $qty = isset($_POST['Quantity']) ? max(1, intval($_POST['Quantity'])) : 1;
        $_SESSION['pending_product_id'] = $pid;
        $_SESSION['pending_quantity']   = $qty;
        header('Location: login.php?notice=login_required&product_id=' . $pid . '&redirect=' . urlencode('catalog.php?open_product=' . $pid));
        exit();
    }
    $redirect_param = isset($_POST['redirect']) ? $_POST['redirect'] : 'catalog.php';
    header('Location: login.php?error=login_required&redirect=' . urlencode($redirect_param));
    exit();
}

require_once 'config.php';

// 2. Handle Add to Cart / Place Order Submit
if (isset($_POST['place_order']) || isset($_POST['Product_ID'])) {
    $email = mysqli_real_escape_string($con, $_SESSION['email']);
    
    // Get User_ID from users table using session email
    $user_id = 1;
    $user_query = mysqli_query($con, "SELECT * FROM users WHERE Email='$email'");
    if ($user_query && mysqli_num_rows($user_query) > 0) {
        $u_row = mysqli_fetch_assoc($user_query);
        if (isset($u_row['User_ID'])) {
            $user_id = $u_row['User_ID'];
        } elseif (isset($u_row['id'])) {
            $user_id = $u_row['id'];
        } elseif (isset($u_row['ID'])) {
            $user_id = $u_row['ID'];
        }
    }

    $product_id = intval($_POST['Product_ID']);
    $quantity   = isset($_POST['Quantity']) ? max(1, intval($_POST['Quantity'])) : 1;

    // Get Product Price from products table
    $prod_query = mysqli_query($con, "SELECT Price_LKR FROM products WHERE Product_ID='$product_id'");
    if ($prod_query && mysqli_num_rows($prod_query) > 0) {
        $p_row = mysqli_fetch_assoc($prod_query);
        $unit_price = floatval(preg_replace('/[^0-9.]/', '', $p_row['Price_LKR']));
    } else {
        $unit_price = 100.00;
    }

    // Calculate Total Price
    $total_price = $unit_price * $quantity;

    // Detect exact price column name in user's 'orders' table
    $price_col = 'Total_Price_(LKR)';
    $show_cols = mysqli_query($con, "SHOW COLUMNS FROM orders");
    if ($show_cols) {
        while ($c_row = mysqli_fetch_assoc($show_cols)) {
            $field = $c_row['Field'];
            if (in_array($field, ['Total_Price_(LKR)', 'Total_Price_LKR', 'Total_Price', 'Total_Amount', 'Price_LKR', 'Price', 'Amount', 'Total'])) {
                $price_col = $field;
                break;
            }
        }
    }

    // Insert Order into MySQL database table
    $insert_sql = "INSERT INTO `orders` (`User_ID`, `Product_ID`, `Quantity`, `$price_col`, `Status`) 
                   VALUES ('$user_id', '$product_id', '$quantity', '$total_price', 'Pending')";
    
    $run = mysqli_query($con, $insert_sql);

    if ($run) {
        header('Location: user.php?order=success');
        exit();
    } else {
        header('Location: catalog.php?order=failed');
        exit();
    }
} else {
    header('Location: catalog.php');
    exit();
}
?>
