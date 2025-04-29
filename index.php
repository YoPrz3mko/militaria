<?php
require_once 'config.php';

// Get featured products
$conn = connectDB();
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        WHERE p.featured = 1 
        LIMIT 4";
$result = $conn->query($sql);
$featured_products = [];

while ($row = $result->fetch_assoc()) {
    $featured_products[] = $row;
}

// Get all categories
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
$categories = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

$conn->close();

// Page title
$page_title = "Home - " . SITE_NAME;
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Militaria Przemka</h1>
        <p>Profesjonalny sklep z bronią - Twój godny zaufania partner</p>
        <a href="products.php" class="btn">Zobacz naszą ofertę</a>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products">
    <div class="container">
        <h2>Polecana broń</h2>
        <div class="product-carousel">
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (file_exists('images/products/' . $product['image'])): ?>
                            <img src="images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <?php else: ?>
                            <img src="images/placeholder.jpg" alt="<?php echo $product['name']; ?>">
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="category"><?php echo $product['category_name']; ?></p>
                        <p class="price"><?php echo formatPrice($product['price']); ?></p>
                        <a href="product.php?slug=<?php echo $product['slug']; ?>" class="btn">Zobacz szczegóły</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories">
    <div class="container">
        <h2>Kategorie produktów</h2>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <div class="category-image" style="background-image: url('images/categories/<?php echo $category['slug']; ?>.jpg');">
                        <div class="category-overlay"></div>
                        <h3><?php echo $category['name']; ?></h3>
                    </div>
                    <a href="category.php?slug=<?php echo $category['slug']; ?>" class="btn">Zobacz produkty</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about">
    <div class="container">
        <div class="about-content">
            <h2>O Militaria Przemka</h2>
            <p>Militaria Przemka to sklep specjalizujący się w sprzedaży najwyższej jakości broni. Oferujemy szeroki wybór broni krótkiej i długiej od renomowanych producentów.</p>
            <p>Zapewniamy profesjonalne doradztwo oraz kompleksową obsługę klienta. Wszystkie nasze produkty posiadają niezbędne certyfikaty i atesty.</p>
            <a href="contact.php" class="btn">Skontaktuj się z nami</a>
        </div>
        <div class="about-image">
            <img src="images/store.jpg" alt="Militaria Przemka Store">
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>