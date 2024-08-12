<?php
session_start();
include 'config.php';

$productIds = [];
foreach ($_SESSION['cart'] ?? [] as $cartId => $cartQty) {
    $productIds[] = intval($cartId);  
}

$rows = 0;
$query = null;
if (count($productIds) > 0) {    $idsPlaceholder = implode(',', array_fill(0, count($productIds), '?'));
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
<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Checkout</title>

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

        <h4 class="mb-3">Checkout</h4>
        <form action="<?php echo $base_url; ?>/checkout-form.php" method="post">
            <div class="row g-5">
                <div class="col-md-6 col-lg-7">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label class="form-label">Fullname</label>
                            <input type="text" name="fullname" class="form-control" placeholder="" value="" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Tel</label>
                            <input type="text" name="tel" class="form-control" placeholder="" value="" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="" value="" required>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="text-end">
                        <a href="<?php echo $base_url; ?>/product-list.php" class="btn btn-secondary btn-lg">Back to Products</a>
                        <button class="btn btn-primary btn-lg" type="submit">Continue to checkout</button>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary">Your cart</span>
                        <span class="badge bg-primary rounded-pill"><?php echo $rows; ?></span>
                    </h4>

                    <?php if ($rows > 0): ?>
                        <ul class="list-group mb-3">
                            <?php $grand_total = 0; ?>
                            <?php while ($product = mysqli_fetch_assoc($result)): ?>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0"><?php echo htmlspecialchars($product['product_name']); ?> (<?php echo htmlspecialchars($_SESSION['cart'][$product['id']]); ?>)</h6>
                                        <small class="text-body-secondary"><?php echo nl2br(htmlspecialchars($product['detail'])); ?></small>
                                        <input type="hidden" name="product[<?php echo $product['id']; ?>][price]" value="<?php echo htmlspecialchars($product['price']); ?>">
                                        <input type="hidden" name="product[<?php echo $product['id']; ?>][name]" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    </div>
                                    <span class="text-body-secondary">k<?php echo number_format($_SESSION['cart'][$product['id']] * $product['price'], 2); ?></span>
                                </li>
                                <?php $grand_total += $_SESSION['cart'][$product['id']] * $product['price']; ?>
                            <?php endwhile; ?>
                            <li class="list-group-item d-flex justify-content-between bg-body-tertiary">
                                <div class="text-success">
                                    <h6 class="my-0">Grand Total</h6>
                                    <small>amount</small>
                                </div>
                                <span class="text-success"><strong>k<?php echo number_format($grand_total, 2); ?></strong></span>
                            </li>
                        </ul>
                        <input type="hidden" name="grand_total" value="<?php echo htmlspecialchars($grand_total); ?>">
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">Your cart is empty.</div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
    <script src="<?php echo $base_url; ?>/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
