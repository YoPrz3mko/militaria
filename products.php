// PROBLEM 1: Standardize database connection
// Replace database.php with:

<?php
// database.php - Consistent database connection
require_once 'config.php';

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Silent success message
    echo "<!-- Database connection successful -->";
} catch(PDOException $e) {
    die("<div class='alert alert-danger'>Database connection failed: " . $e->getMessage() . "</div>");
}
?>

// PROBLEM 2: Fix category.php to match with categories.php
// Replacement for category.php:

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

// PROBLEM 3: Fix product.php to use slugs consistently
// Replacement for product.php:

<?php
require_once 'config.php';
require_once 'database.php';

if (!isset($_GET['slug'])) {
    header("Location: products.php");
    exit();
}

$slug = $_GET['slug'];

try {
    $stmt = $conn->prepare("SELECT p.*, c.name AS category_name 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.category_id 
                           WHERE p.slug = ?");
    $stmt->execute([$slug]);
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
            <img src="images/products/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded" 
                 alt="<?= htmlspecialchars($product['name']) ?>" 
                 onerror="this.src='images/placeholder.jpg'">
        </div>
        <div class="col-md-6">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <p class="h4 text-muted">Category: <?= htmlspecialchars($product['category_name']) ?></p>
            <p class="h3 text-primary"><?= formatPrice($product['price']) ?></p>
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

// PROBLEM 4: Fix products.php for consistency and add category filter
// Replacement for products.php:

<?php
require_once 'config.php';
require_once 'database.php';

try {
    // Get all categories for the filter
    $categoryQuery = "SELECT * FROM categories ORDER BY name";
    $categoryStmt = $conn->query($categoryQuery);
    $categories = $categoryStmt->fetchAll();
    
    // Check if filtering by category
    $categoryFilter = "";
    $params = [];
    
    if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
        $categoryFilter = "WHERE p.category_id = ?";
        $params[] = $_GET['category_id'];
    }
    
    // Get products with optional filter
    $query = "SELECT p.*, c.name AS category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id
              $categoryFilter
              ORDER BY p.name";
              
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

require 'includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">All Products</h1>
    
    <!-- Category Filter -->
    <div class="mb-4">
        <h5>Filter by Category:</h5>
        <div class="btn-group mb-3">
            <a href="products.php" class="btn <?= !isset($_GET['category_id']) ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
            <?php foreach ($categories as $category): ?>
                <a href="products.php?category_id=<?= $category['category_id'] ?>" 
                   class="btn <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['category_id']) ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="images/products/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" 
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         onerror="this.src='images/placeholder.jpg'">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text"><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                        <p class="card-text flex-grow-1"><?= substr(htmlspecialchars($product['description']), 0, 100) ?>...</p>
                        <p class="price h5"><?= formatPrice($product['price']) ?></p>
                        <p class="stock">In Stock: <?= $product['stock'] ?></p>
                        <a href="product.php?slug=<?= $product['slug'] ?>" class="btn btn-primary mt-auto">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No products found in this category.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>