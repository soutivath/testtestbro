<?php
session_start();
include 'config.php';

if (!empty($_GET['id'])) {

    $product_id = intval($_GET['id']);
    

    $query = mysqli_query($conn, "SELECT id FROM prodeucts WHERE id = $product_id LIMIT 1");
    
    if (mysqli_num_rows($query) > 0) {

        if (!isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = 1;
        } else {

            $_SESSION['cart'][$product_id] += 1;
        }
        $_SESSION['message'] = 'Cart added successfully';
    } else {
        $_SESSION['message'] = 'Product not found';
    }
}


header('Location: ' . $base_url . '/product-list.php');
exit();