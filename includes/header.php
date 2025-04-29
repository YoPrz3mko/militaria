<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Teko:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-top">
            <div class="container">
                <div class="contact-info">
                    <span><i class="fas fa-phone"></i> +48 123 456 789</span>
                    <span><i class="fas fa-envelope"></i> info@militariaprzemka.com</span>
                </div>
                <div class="user-actions">
                    <?php if (is_logged_in()): ?>
                        <span>Witaj, <?php echo $_SESSION['username']; ?></span>
                        <a href="account.php">Moje konto</a>
                        <a href="logout.php">Wyloguj</a>
                    <?php else: ?>
                        <a href="login.php">Logowanie</a>
                        <a href="register.php">Rejestracja</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="header-main">
            <div class="container">
                <div class="logo">
                    <a href="index.php">
                        <img src="images/logo.png" alt="Militaria Przemka Logo">
                        <span>Militaria Przemka</span>
                    </a>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.php">Strona główna</a></li>
                        <li class="dropdown">
                            <a href="products.php">Produkty</a>
                            <div class="dropdown-content">
                                <?php
                                $conn = connectDB();
                                $sql = "SELECT * FROM categories";
                                $result = $conn->query($sql);
                                
                                while ($row = $result->fetch_assoc()) {
                                    echo '<a href="category.php?slug=' . $row['slug'] . '">' . $row['name'] . '</a>';
                                }
                                
                                $conn->close();
                                ?>
                            </div>
                        </li>
                        <li><a href="contact.php">Kontakt</a></li>
                    </ul>
                </nav>
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>
    
    <main>
        <?php echo display_message(); ?>