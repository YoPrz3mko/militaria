<?php
require_once 'config.php';

// Check if user is already logged in
if (is_logged_in()) {
    redirect('index.php');
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    $conn = connectDB();
    
    // Check if username exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Set success message
            set_message('Zalogowano pomyślnie!', 'success');
            
            // Redirect to home page
            redirect('index.php');
        } else {
            $error = "Niepoprawne hasło";
        }
    } else {
        $error = "Użytkownik o podanej nazwie nie istnieje";
    }
    
    $conn->close();
}

$page_title = "Logowanie - " . SITE_NAME;
include 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-container fade-in">
            <h1>Logowanie</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Nazwa użytkownika</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Hasło</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn" style="width: 100%;">Zaloguj się</button>
                </div>
            </form>
            
            <div class="auth-links">
                <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>