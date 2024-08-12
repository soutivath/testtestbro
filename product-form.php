<?php
session_start();
include 'config.php';


$product_name = trim($_POST['product_name']);
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$detail = trim($_POST['detail']);
$image_name = $_FILES['profile_image']['name'];
$image_tmp = $_FILES['profile_image']['tmp_name'];
$folder = 'upload_image/';
$image_location = $folder . $image_name;


if (empty($_POST['id'])) {

    $query = mysqli_query($conn, "INSERT INTO prodeucts (product_name, price, profile_image, detail) VALUES ('$product_name', '$price', '$image_name', '$detail')") or die('Query failed: ' . mysqli_error($conn));
} else {

    $product_id = intval($_POST['id']);
    $query_product = mysqli_query($conn, "SELECT * FROM prodeucts WHERE id='$product_id'");
    $result = mysqli_fetch_assoc($query_product);

 
    if (empty($image_name)) {
        $image_name = $result['profile_image'];
    } else {
        if (!empty($result['profile_image']) && file_exists($folder . $result['profile_image'])) {
            unlink($folder . $result['profile_image']);
        }
    }


    $query = mysqli_query($conn, "UPDATE prodeucts SET product_name='$product_name', price='$price', profile_image='$image_name', detail='$detail' WHERE id='$product_id'") or die('Query failed: ' . mysqli_error($conn));
}

mysqli_close($conn);

if ($query) {
 
    if (!empty($image_tmp)) {
        move_uploaded_file($image_tmp, $image_location);
    }

    $_SESSION['message'] = 'Product saved successfully';
    header('Location: ' . $base_url . '/index.php');
} else {
    $_SESSION['message'] = 'Product could not be saved!';
    header('Location: ' . $base_url . '/index.php');
}
exit();
?>