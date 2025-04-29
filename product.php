<?php
require_once 'database.php';

if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['product_id'];

try {
    $stmt = $conn->prepare("SELECT p.*, c.name AS category_name 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.category_id 
                           WHERE p.product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header("Location: products.php");
        exit();
    }
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

require 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="images/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="col-md-6">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <p class="h4 text-muted">Category: <?= htmlspecialchars($product['category_name']) ?></p>
            <p class="h3 text-primary">$<?= number_format($product['price'], 2) ?></p>
            <p class="h5">Stock: <?= $product['stock'] ?></p>
            <hr>
            <p class="lead"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            
            <form action="cart.php" method="post" class="mt-4">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <div class="form-group row">
                    <label for="quantity" class="col-sm-2 col-form-label">Quantity:</label>
                    <div class="col-sm-3">
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               value="1" min="1" max="<?= $product['stock'] ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg mt-3">Add to Cart</button>
            </form>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>