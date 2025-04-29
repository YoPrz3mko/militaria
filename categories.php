<?php
require_once 'database.php';

try {
    $query = "SELECT * FROM categories";
    $stmt = $conn->query($query);
    $categories = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

require 'includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Product Categories</h1>
    
    <div class="row">
        <?php foreach ($categories as $category): ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="card-title"><?= htmlspecialchars($category['name']) ?></h2>
                    <p class="card-text flex-grow-1"><?= htmlspecialchars($category['description']) ?></p>
                    <a href="products.php?category_id=<?= $category['category_id'] ?>" class="btn btn-primary mt-auto">View Products</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>