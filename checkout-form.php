<?php
session_start();
include 'config.php';

$now = date('Y-m-d H:i:s');
$query = mysqli_query($conn, "INSERT INTO orders (order_date, fullname, email, tel, grand_total) 
VALUES 
('{$now}', '{$_POST['fullname']}', '{$_POST['email']}', '{$_POST['tel']}','{$_POST['grand_total']}')") or die('Query failed');
if($query){
  
    $last_id = mysqli_insert_id($conn);
    foreach ($_SESSION['cart'] as $productId => $productQty) {
        die(var_dump ($productId));
        $product_name = $_POST['product'][$productId]['name'];
        $price = $_POST['product'][$productId]['price'];
        $total = $price * $productQty;
        
        die(var_dump($product_name));
       mysqli_query($conn, "INSERT INTO order_datails (order_id, product_id, product_name, price, quantity, total)
        VALUES 
        ('{$last_id}', '{$productId}', '{$product_name}', '{$price}','{$productQty}','{$total}')") or die('Query failed');

    }
    
    
    unset($_SESSION['cart']);
    $_SESSION['message'] = 'Cart order success!!!';
    header('location: ' . $base_url . '/checkout-success.php');

}else{
    $_SESSION['message'] = 'Checkout not complete!!!';
    header('location: ' . $base_url . '/checkout-success.php');
}
