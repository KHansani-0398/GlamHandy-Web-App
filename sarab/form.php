<?php
session_start();

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $pa    = trim($_POST['PASS']);
    $redirect = isset($_POST['redirect']) ? trim($_POST['redirect']) : '';

    require_once 'config.php';

    $safe_email = mysqli_real_escape_string($con, $email);
    // Check if user with given Email exists
    $query = mysqli_query($con, "SELECT * FROM users WHERE Email='$safe_email'");
    $nor = mysqli_num_rows($query);

    if ($nor < 1) {
        $err_url = 'login.php?error=email' . (!empty($redirect) ? '&redirect=' . urlencode($redirect) : '');
        header('Location: ' . $err_url);
        exit();
    } else {
        $rec = mysqli_fetch_assoc($query);

        // Check if Password matches
        if ($rec['Password'] !== $pa) {
            $err_url = 'login.php?error=password' . (!empty($redirect) ? '&redirect=' . urlencode($redirect) : '');
            header('Location: ' . $err_url);
            exit();
        }

        $_SESSION['email'] = $rec["Email"];

        if (strtolower($rec["Email"]) == "admin@gmail.com") {
            header('Location: Admin.php');
            exit();
        } else {
            // If user attempted to add a product before logging in, return them to catalog with that product modal open
            if (isset($_SESSION['pending_product_id']) && intval($_SESSION['pending_product_id']) > 0) {
                $product_id = intval($_SESSION['pending_product_id']);
                unset($_SESSION['pending_product_id']);
                unset($_SESSION['pending_quantity']);
                header("Location: catalog.php?open_product=$product_id");
                exit();
            }

            // Redirect to return page if provided and safe, otherwise user profile page
            if (!empty($redirect) && !preg_match('/^https?:\/\//i', $redirect)) {
                header('Location: ' . $redirect);
            } else {
                header('Location: User.php');
            }
            exit();
        }
    }
}
?>
