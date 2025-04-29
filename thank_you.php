<?php
// thank_you.php - Order confirmation page
require_once 'config.php';
require_once 'database.php';

// Redirect if no order ID provided
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = (int)$_GET['order_id'];

// Only show order details if user is logged in
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    $conn = connectDB();
    
    // Get order information
    $sql = "SELECT o.*, u.email 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.order_id = ? AND o.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Order not found or doesn't belong to this user
        header("Location: index.php");
        exit;
    }
    
    $order = $result->fetch_assoc();
    
    // Get order items
    $sql = "SELECT oi.*, p.name, p.image 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.product_id 
            WHERE oi.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();
    $order_items = [];
    
    while ($item = $items_result->fetch_assoc()) {
        $order_items[] = $item;
    }
}

$page_title = "Thank You - " . SITE_NAME;
require 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="card-title mb-4">Thank You for Your Order!</h1>
                    
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <p class="lead">Your order #<?php echo $order_id; ?> has been placed successfully.</p>
                    <p>A confirmation email has been sent to <?php echo isset($order) ? htmlspecialchars($order['email']) : 'your registered email address'; ?>.</p>
                    
                    <?php if (isset($order)): ?>
                        <div class="mt-4">
                            <h5>Order Details</h5>
                            <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($order['created_at'])); ?></p>
                            <p><strong>Order Status:</strong> <span class="badge bg-primary"><?php echo ucfirst($order['status']); ?></span></p>
                            <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                        </div>
                        
                        <?php if (!empty($order_items)): ?>
                            <div class="mt-4">
                                <h5>Ordered Products</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($order_items as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                    <td><?php echo formatPrice($item['price']); ?></td>
                                                    <td><?php echo $item['quantity']; ?></td>
                                                    <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">Total:</th>
                                                <th><?php echo formatPrice($order['total_amount']); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($order['payment_method'] == 'bank_transfer'): ?>
                            <div class="alert alert-info mt-4">
                                <h5>Payment Instructions</h5>
                                <p>Please complete your payment by transferring the total amount to:</p>
                                <p>
                                    <strong>Bank:</strong> Example Bank<br>
                                    <strong>Account Number:</strong> 1234567890<br>
                                    <strong>Account Holder:</strong> Militaria Przemka<br>
                                    <strong>Reference:</strong> Order #<?php echo $order_id; ?>
                                </p>
                                <p>Your order will be processed after we receive your payment.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="mt-5">
                        <a href="index.php" class="btn btn-primary">Return to Home Page</a>
                        <?php if (is_logged_in()): ?>
                            <a href="account.php" class="btn btn-outline-primary">View Your Account</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>