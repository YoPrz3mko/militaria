<?php
require 'includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Contact Us</h1>
    
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success">Thank you for your message! We'll contact you soon.</div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <form action="process_contact.php" method="post">
                <div class="form-group mb-3">
                    <label for="name">Your Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="form-group mb-3">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="form-group mb-3">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                
                <div class="form-group mb-3">
                    <label for="message">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Contact Information</h5>
                    <p class="card-text">
                        <strong>Email:</strong> contact@militariashop.com<br>
                        <strong>Phone:</strong> +1 (123) 456-7890<br>
                        <strong>Address:</strong> 123 Military Ave, History Town, HT 12345
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>