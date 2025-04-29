<?php
require_once 'config.php';

// Check if user is already logged in
if (is_logged_in()) {
    redirect('index.php');
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    
    $errors = [];
    
    // Validate username
    if (strlen($username) < 3) {
        $errors[] = "Nazwa użytkownika musi mieć co najmniej 3 znaki";
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Podany adres email jest niepoprawny";
    }
    
    // Validate password
    if (strlen($password) < 8) {
        $errors[] = "Hasło musi mieć co najmniej 8 znaków";
    }
    
    // Validate password confirmation
    if ($password !== $confirm_password) {
        $errors[] = "Hasła nie są identyczne";
    }
    
    // If no validation errors
    if (empty($errors)) {
        $conn = connectDB();
        
        // Check if username already exists
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Nazwa użytkownika jest już zajęta";
        } else {
            // Check if email already exists
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Adres email jest już przypisany do innego konta";
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO users (username, email, password, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $username, $email, $hashed_password, $first_name, $last_name);
                
                if ($stmt->execute()) {
                    // Set success message
                    set_message('Rejestracja zakończona pomyślnie! Możesz się teraz zalogować.', 'success');
                    
                    // Redirect to login page
                    redirect('login.php');
                } else {
                    $errors[] = "Wystąpił błąd podczas rejestracji. Spróbuj ponownie.";
                }
            }
        }
        
        $conn->close();
    }
}

$page_title = "Rejestracja - " . SITE_NAME;
include 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-container fade-in">
            <h1>Rejestracja</h1>
            
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="username">Nazwa użytkownika *</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo isset($username) ? $username : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Hasło *</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <small class="form-text text-muted">Hasło musi mieć co najmniej 8 znaków</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Potwierdź hasło *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <small class="form-text text-muted">Wprowadź ponownie swoje hasło</small>
                </div>

                <div class="form-group">
                    <label for="first_name">Imię *</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo isset($first_name) ? $first_name : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Nazwisko *</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo isset($last_name) ? $last_name : ''; ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Zarejestruj się</button>
            </form>

            <p class="mt-3">Masz już konto? <a href="login.php">Zaloguj się tutaj</a></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
