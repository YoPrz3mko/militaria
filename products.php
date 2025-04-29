<?php
require_once 'database.php';

try {
    $query = "SELECT p.*, c.name AS category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id";
    $stmt = $conn->query($query);
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

require 'includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Militaria Products</h1>
    
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="images/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text"><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                    <p class="card-text flex-grow-1"><?= substr(htmlspecialchars($product['description']), 0, 100) ?>...</p>
                    <p class="price h5">$<?= number_format($product['price'], 2) ?></p>
                    <p class="stock">In Stock: <?= $product['stock'] ?></p>
                    <a href="product.php?product_id=<?= $product['product_id'] ?>" class="btn btn-primary mt-auto">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>