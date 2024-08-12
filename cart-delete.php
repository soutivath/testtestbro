<?php
session_start();
include 'config.php';


if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT) !== false) {
    $productId = intval($_GET['id']); 

    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        $_SESSION['message'] = 'Cart delete success';
    } else {
        $_SESSION['message'] = 'Product not found in cart';
    }
} else {
    $_SESSION['message'] = 'Invalid product ID';
}


header('Location: ' . $base_url . '/cart.php');
exit();
?>
