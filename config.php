<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'militaria_przemka');
define('DB_USER', 'root');
define('DB_PASS', '');

// Website Configuration
define('SITE_NAME', 'Militaria Przemka');
define('SITE_URL', 'http://localhost/militaria_przemka');
define('ADMIN_EMAIL', 'admin@militariaprzemka.com');

// Connect to database
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set proper charset
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Session start
session_start();

// Helper Functions
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function display_message() {
    if(isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $status = $_SESSION['status'] ?? 'info';
        unset($_SESSION['message']);
        unset($_SESSION['status']);
        
        return "<div class='alert alert-{$status}'>{$message}</div>";
    }
    return "";
}

function set_message($message, $status = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['status'] = $status;
}

function formatPrice($price) {
    return number_format($price, 2) . ' PLN';
}
?>