<?php
// cart.php - Shopping cart page
require_once 'config.php';
require_once 'database.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add product to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($quantity < 1) $quantity = 1;
    
    try {
        // Get product info
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            // Check stock availability
            if ($product['stock'] < $quantity) {
                $quantity = $product['stock']; // Limit to available stock
                set_message("Only {$quantity} items are available for this product", "warning");
            }
            
            // Check if product already in cart
            if (isset($_SESSION['cart'][$product_id])) {
                // Update quantity
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                
                // Check for stock limit again
                if ($_SESSION['cart'][$product_id]['quantity'] > $product['stock']) {
                    $_SESSION['cart'][$product_id]['quantity'] = $product['stock'];
                    set_message("You've reached the maximum available quantity for this product", "warning");
                } else {
                    set_message("Cart updated successfully", "success");
                }
            } else {
                // Add new product
                $_SESSION['cart'][$product_id] = [
                    'product_id' => $product_id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image' => $product['image']
                ];
                set_message("Product added to cart", "success");
            }
        }
    } catch(PDOException $e) {
        set_message("Error adding product to cart: " . $e->getMessage(), "danger");
    }
    
    // Redirect back to product page if came from there
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'product.php') !== false) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

// Remove product from cart
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    set_message("Product removed from cart", "success");
    header("Location: cart.php");
    exit;
}

// Update cart quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $product_id = (int)$product_id;
        $quantity = (int)$quantity;
        
        if ($quantity < 1) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            // Verify stock
            $stmt = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if ($product && $quantity <= $product['stock']) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else if ($product) {
                $_SESSION['cart'][$product_id]['quantity'] = $product['stock'];
                set_message("Quantity adjusted to match available stock", "warning");
            }
        }
    }
    set_message("Cart updated successfully", "success");
    header("Location: cart.php");
    exit;
}

// Clear the entire cart
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    set_message("Cart has been cleared", "success");
    header("Location: cart.php");
    exit;
}

// Calculate totals
$total_items = 0;
$total_price = 0;

foreach ($_SESSION['cart'] as $item) {
    $total_items += $item['quantity'];
    $total_price += $item['price'] * $item['quantity'];
}

// Include the header
$page_title = "Shopping Cart - " . SITE_NAME;
require 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Shopping Cart</h1>
    
    <?php echo display_message(); ?>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="products.php">Continue shopping</a>.
        </div>
    <?php else: ?>
        <form action="cart.php" method="post">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php 
                                    // Determine the correct image path
                                    $img_path = "images/products/{$item['image']}";
                                    // Fallback to placeholder if image doesn't exist
                                    if (!file_exists($img_path)) {
                                        // Try the category directories
                                        if (file_exists("img/dluga/{$item['image']}")) {
                                            $img_path = "img/dluga/{$item['image']}";
                                        } elseif (file_exists("img/krotka/{$item['image']}")) {
                                            $img_path = "img/krotka/{$item['image']}";
                                        } else {
                                            $img_path = "images/placeholder.jpg";
                                        }
                                    }
                                    ?>
                                    <img src="<?php echo $img_path; ?>" class="img-thumbnail me-3" style="width: 80px;" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                </div>
                            </td>
                            <td><?php echo formatPrice($item['price']); ?></td>
                            <td width="150">
                                <input type="number" name="quantity[<?php echo $product_id; ?>]" class="form-control" value="<?php echo $item['quantity']; ?>" min="1">
                            </td>
                            <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                            <td>
                                <a href="cart.php?remove=<?php echo $product_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?')">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th><?php echo formatPrice($total_price); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            
            <div class="d-flex justify-content-between mb-5">
                <div>
                    <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="cart.php?clear=1" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</a>
                </div>
                <div>
                    <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>