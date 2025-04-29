<?php
// checkout.php - Checkout process
require_once 'config.php';
require_once 'database.php';

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    set_message("Your cart is empty. Please add products before checkout.", "warning");
    header("Location: products.php");
    exit;
}

// Redirect if not logged in
if (!is_logged_in()) {
    set_message("Please log in or register to complete your purchase.", "info");
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];
$conn = connectDB();
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Process the checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $first_name = isset($_POST['first_name']) ? sanitize_input($_POST['first_name']) : $user['first_name'];
    $last_name = isset($_POST['last_name']) ? sanitize_input($_POST['last_name']) : $user['last_name'];
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : $user['email'];
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $city = sanitize_input($_POST['city']);
    $postal_code = sanitize_input($_POST['postal_code']);
    $country = sanitize_input($_POST['country']);
    $payment_method = sanitize_input($_POST['payment_method']);
    
    // Calculate order total
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address, payment_method) VALUES (?, ?, 'pending', ?, ?)");
        $shipping_address = "$address, $city, $postal_code, $country";
        $stmt->bind_param("idss", $user_id, $total_amount, $shipping_address, $payment_method);
        $stmt->execute();
        
        $order_id = $conn->insert_id;
        
        // Insert order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $stmt->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
            $stmt->execute();
            
            // Update product stock
            $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
            $update_stmt->bind_param("ii", $item['quantity'], $product_id);
            $update_stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        
        // Clear the cart
        $_SESSION['cart'] = [];
        
        // Set success message
        set_message("Your order has been placed successfully. Order ID: #$order_id", "success");
        
        // Redirect to thank you page
        header("Location: thank_you.php?order_id=$order_id");
        exit;
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        set_message("Error processing your order: " . $e->getMessage(), "danger");
    }
}

// Calculate totals for display
$total_items = 0;
$subtotal = 0;
$shipping = 15.00; // Fixed shipping cost
$tax_rate = 0.05; // 5% tax

foreach ($_SESSION['cart'] as $item) {
    $total_items += $item['quantity'];
    $subtotal += $item['price'] * $item['quantity'];
}

$tax = $subtotal * $tax_rate;
$total = $subtotal + $tax + $shipping;

$page_title = "Checkout - " . SITE_NAME;
require 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Checkout</h1>
    
    <?php echo display_message(); ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <form action="checkout.php" method="post" id="checkout-form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo $user['address'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="country">Country</label>
                                <select class="form-control" id="country" name="country" required>
                                    <option value="">Select...</option>
                                    <option value="USA">United States</option>
                                    <option value="Poland">Poland</option>
                                    <option value="Germany">Germany</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="France">France</option>
                                    <option value="Canada">Canada</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr class="mb-4">
                        
                        <h5 class="mb-3">Payment Method</h5>
                        
                        <div class="d-block my-3">
                            <div class="custom-control custom-radio">
                                <input id="credit" name="payment_method" type="radio" class="custom-control-input" value="credit_card" checked required>
                                <label class="custom-control-label" for="credit">Credit Card</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="paypal" name="payment_method" type="radio" class="custom-control-input" value="paypal" required>
                                <label class="custom-control-label" for="paypal">PayPal</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="bank_transfer" name="payment_method" type="radio" class="custom-control-input" value="bank_transfer" required>
                                <label class="custom-control-label" for="bank_transfer">Bank Transfer</label>
                            </div>
                        </div>
                        
                        <div id="credit-card-details" class="payment-details">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cc_name">Name on card</label>
                                    <input type="text" class="form-control" id="cc_name" placeholder="Full name as displayed on card">
                                    <small class="text-muted">Full name as displayed on card</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cc_number">Credit card number</label>
                                    <input type="text" class="form-control" id="cc_number" placeholder="XXXX XXXX XXXX XXXX">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="cc_expiration">Expiration</label>
                                    <input type="text" class="form-control" id="cc_expiration" placeholder="MM/YY">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="cc_cvv">CVV</label>
                                    <input type="text" class="form-control" id="cc_cvv" placeholder="XXX">
                                </div>
                            </div>
                        </div>
                        
                        <div id="paypal-details" class="payment-details" style="display: none;">
                            <div class="alert alert-info">
                                You will be redirected to PayPal to complete your payment after submitting your order.
                            </div>
                        </div>
                        
                        <div id="bank-transfer-details" class="payment-details" style="display: none;">
                            <div class="alert alert-info">
                                <p>Please make a transfer to the following bank account:</p>
                                <p>
                                    <strong>Bank:</strong> Example Bank<br>
                                    <strong>Account Number:</strong> 1234567890<br>
                                    <strong>Account Holder:</strong> Militaria Przemka<br>
                                    <strong>Reference:</strong> Your order number (will be generated after submission)
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span class="text-muted"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                            </li>
                        <?php endforeach; ?>
                        
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Subtotal</span>
                            <strong><?php echo formatPrice($subtotal); ?></strong>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Tax (5%)</span>
                            <strong><?php echo formatPrice($tax); ?></strong>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Shipping</span>
                            <strong><?php echo formatPrice($shipping); ?></strong>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total</span>
                            <strong><?php echo formatPrice($total); ?></strong>
                        </li>
                    </ul>
                    
                    <button class="btn btn-success btn-lg btn-block mt-3" type="submit" form="checkout-form">Place Order</button>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Need Help?</h5>
                    <p class="card-text">If you have any questions about your order, please contact our customer service.</p>
                    <a href="contact.php" class="btn btn-outline-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method switcher
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentDetails = document.querySelectorAll('.payment-details');
    
    paymentMethods.forEach(function(method) {
        method.addEventListener('change', function() {
            // Hide all payment details
            paymentDetails.forEach(function(detail) {
                detail.style.display = 'none';
            });
            
            // Show selected payment method details
            const selectedMethod = document.getElementById(this.value + '-details');
            if (selectedMethod) {
                selectedMethod.style.display = 'block';
            }
        });
    });
});
</script>

<?php require 'includes/footer.php'; ?>