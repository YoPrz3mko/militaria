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