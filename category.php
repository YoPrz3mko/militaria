<?php
require_once 'config.php';
require_once 'database.php';

// Verify that slug parameter exists
if (!isset($_GET['slug'])) {
    header('Location: categories.php');
    exit();
}

$slug = $_GET['slug'];

try {
    // Get category first
    $stmt = $conn->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    $category = $stmt->fetch();
    
    if (!$category) {
        header('Location: categories.php');
        exit();
    }
    
    // Get products in this category
    $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ?");
    $stmt->execute([$category['category_id']]);
    $products = $stmt->fetchAll();
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

require 'includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4"><?= htmlspecialchars($category['name']) ?></h1>
    <p class="lead"><?= htmlspecialchars($category['description']) ?></p>
    
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="images/products/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='images/placeholder.jpg'">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text flex-grow-1"><?= substr(htmlspecialchars($product['description']), 0, 100) ?>...</p>
                    <p class="price h5"><?= formatPrice($product['price']) ?></p>
                    <p class="stock">In Stock: <?= $product['stock'] ?></p>
                    <a href="product.php?slug=<?= htmlspecialchars($product['slug']) ?>" class="btn btn-primary mt-auto">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>