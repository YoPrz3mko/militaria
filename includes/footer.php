</main>

    <footer>
        <div class="footer-top">
            <div class="container">
                <div class="footer-columns">
                    <div class="footer-column">
                        <h3>Militaria Przemka</h3>
                        <p>Profesjonalny sklep z bronią oferujący szeroki wybór broni krótkiej i długiej od renomowanych producentów.</p>
                        <div class="social-icons">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="footer-column">
                        <h3>Szybkie linki</h3>
                        <ul>
                            <li><a href="index.php">Strona główna</a></li>
                            <li><a href="products.php">Produkty</a></li>
                            <li><a href="contact.php">Kontakt</a></li>
                            <?php if (is_logged_in()): ?>
                                <li><a href="account.php">Moje konto</a></li>
                                <li><a href="logout.php">Wyloguj</a></li>
                            <?php else: ?>
                                <li><a href="login.php">Logowanie</a></li>
                                <li><a href="register.php">Rejestracja</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Kategorie</h3>
                        <ul>
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT * FROM categories";
                            $result = $conn->query($sql);
                            
                            while ($row = $result->fetch_assoc()) {
                                echo '<li><a href="category.php?slug=' . $row['slug'] . '">' . $row['name'] . '</a></li>';
                            }
                            
                            $conn->close();
                            ?>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Kontakt</h3>
                        <ul class="contact-info">
                            <li><i class="fas fa-map-marker-alt"></i> ul. Przykładowa 123, 00-000 Warszawa</li>
                            <li><i class="fas fa-phone"></i> +48 123 456 789</li>
                            <li><i class="fas fa-envelope"></i> info@militariaprzemka.com</li>
                            <li><i class="fas fa-clock"></i> Pon-Pt: 9:00 - 17:00</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> Militaria Przemka. Wszelkie prawa zastrzeżone.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>