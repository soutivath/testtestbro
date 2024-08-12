<?php
session_start();
include 'config.php';

// Initialize an empty array for product IDs
$productIds = [];
foreach ($_SESSION['cart'] ?? [] as $cartId => $cartQty) {
    $productIds[] = intval($cartId);  // Ensure IDs are integers to prevent SQL injection
}

$rows = 0;
if (count($productIds) > 0) {
    // Prepare and execute the query using prepared statements to prevent SQL injection
    $idsPlaceholder = implode(',', array_fill(0, count($productIds), '?'));
    $query = mysqli_prepare($conn, "SELECT * FROM prodeucts WHERE id IN ($idsPlaceholder)");
    if ($query) {
        mysqli_stmt_bind_param($query, str_repeat('i', count($productIds)), ...$productIds);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        $rows = mysqli_num_rows($result);
    } else {
        die('Query preparation failed: ' . mysqli_error($conn));
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>

    <link href="<?php echo $base_url; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>/assets/fontawesome/css/fontawesome.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>/assets/fontawesome/css/brands.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>/assets/fontawesome/css/solid.min.css" rel="stylesheet">
</head>

<body class="bg-body-tertiary">
    <?php include 'include/menu.php'; ?>
    <div class="container" style="margin-top: 30px;">
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>  
        <?php endif; ?>

        <h4>Cart</h4>
        <div class="row">
            <div class="col-12">
                <form action="<?php echo $base_url; ?>/cart-update.php" method="post">
                    <table class="table table-bordered border-info">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Image</th>
                                <th>Product Name</th>
                                <th style="width: 200px;">Price</th>
                                <th style="width: 100px;">Quantity</th>
                                <th style="width: 200px;">Total</th>
                                <th style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($rows > 0): ?>
                                <?php 
                                $cartTotal = 0;
                                while ($product = mysqli_fetch_assoc($result)): 
                                    $productId = $product['id'];
                                    $quantity = $_SESSION['cart'][$productId];
                                    $totalPrice = $product['price'] * $quantity;
                                    $cartTotal += $totalPrice;
                                ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($product['profile_image'])): ?>
                                                <img src="<?php echo $base_url; ?>/upload_image/<?php echo htmlspecialchars($product['profile_image']); ?>" width="100" alt="Product Image">
                                            <?php else: ?>
                                                <img src="<?php echo $base_url; ?>/assets/images/no-image.jpg" width="100" alt="Product Image">
                                            <?php endif; ?>    
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['product_name']); ?>
                                            <div>
                                                <small class="text-muted"><?php echo nl2br(htmlspecialchars($product['detail'])); ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($product['price'], 2); ?></td>
                                        <td>
                                            <input type="number" name="product[<?php echo $product['id']; ?>][quantity]" value="<?php echo htmlspecialchars($_SESSION['cart'][$product['id']]); ?>" class="form-control">
                                        </td>
                                        <td><?php echo number_format($totalPrice, 2); ?></td>
                                        <td>
                                            <a onclick="return confirm('Are you sure you want to delete?');" role="button" href="<?php echo $base_url; ?>/product-delete.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr>
                                    <td colspan="6" class="text-end">
                                        <button type="submit" class="btn btn-lg btn-success">Update Cart</button>
                                        <a href="<?php echo $base_url; ?>/checkout.php" class="btn btn-lg btn-primary">Checkout Order</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total Cart Price:</td>
                                    <td colspan="2"><?php echo number_format($cartTotal, 2); ?> kip</td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6"><h4 class="text-center text-danger">ບໍ່ມີສິນຄ້າໃນກະຕ່າ</h4></td>
                                </tr>
                            <?php endif; ?>   
                        </tbody>
                    </table>
                </form>
            </div>       
        </div>
    </div>

    <script src="<?php echo $base_url; ?>assets/js/bootstrap.min.js"></script>
</body>

</html>