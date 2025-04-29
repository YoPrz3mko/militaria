<?php
// database.php - Ensure this is correct
$host = 'localhost';
$dbname = 'militaria_przemka';
$username = 'root';
$password = ''; // Add your password if you have one

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<!-- Database connection successful -->"; // Silent debug message
} catch(PDOException $e) {
    die("<div class='alert alert-danger'>Database connection failed: " . $e->getMessage() . "</div>");
}
?>