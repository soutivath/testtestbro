<?php
session_start();
include 'config.php';


if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {

    if (isset($_POST['product']) && is_array($_POST['product'])) {
        foreach ($_SESSION['cart'] as $productId => $productQty) {

            if (isset($_POST['product'][$productId]['quantity']) && filter_var($_POST['product'][$productId]['quantity'], FILTER_VALIDATE_INT) !== false) {
                $_SESSION['cart'][$productId] = (int) $_POST['product'][$productId]['quantity'];
            }
        }
        $_SESSION['message'] = 'Cart update success';
    } else {
        $_SESSION['message'] = 'No product data received';
    }
} else {
    $_SESSION['message'] = 'Cart not found';
}


header('Location: ' . $base_url . '/cart.php');
exit();
?>