/**
 * Militaria Przemka - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.querySelector('i').classList.toggle('fa-bars');
            this.querySelector('i').classList.toggle('fa-times');
        });
    }
    
    // Carousel functionality
    initCarousel();
    
    // Product Image Gallery
    initProductGallery();
    
    // Add fade-in animations
    addFadeInAnimation();
    
    // Form validation
    initFormValidation();
});

/**
 * Initialize the carousel for featured products
 */
function initCarousel() {
    const carousel = document.querySelector('.product-carousel');
    
    if (!carousel) return;
    
    // Only apply carousel if there are enough items
    const items = carousel.querySelectorAll('.product-card');
    if (items.length <= 4) return;
    
    let currentIndex = 0;
    const itemWidth = items[0].offsetWidth + 30; // Including gap
    const visibleItems = Math.floor(carousel.offsetWidth / itemWidth);
    const totalItems = items.length;
    
    // Create carousel controls
    const controlsContainer = document.createElement('div');
    controlsContainer.className = 'carousel-controls';
    
    const prevBtn = document.createElement('button');
    prevBtn.className = 'carousel-prev';
    prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
    
    const nextBtn = document.createElement('button');
    nextBtn.className = 'carousel-next';
    nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
    
    controlsContainer.appendChild(prevBtn);
    controlsContainer.appendChild(nextBtn);
    
    carousel.parentNode.appendChild(controlsContainer);
    
    // Previous button functionality
    prevBtn.addEventListener('click', function() {
        currentIndex = Math.max(0, currentIndex - 1);
        updateCarousel();
    });
    
    // Next button functionality
    nextBtn.addEventListener('click', function() {
        currentIndex = Math.min(totalItems - visibleItems, currentIndex + 1);
        updateCarousel();
    });
    
    // Update carousel position
    function updateCarousel() {
        const translateValue = -currentIndex * itemWidth;
        carousel.style.transform = `translateX(${translateValue}px)`;
        
        // Update button states
        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex >= totalItems - visibleItems;
        
        prevBtn.style.opacity = prevBtn.disabled ? '0.5' : '1';
        nextBtn.style.opacity = nextBtn.disabled ? '0.5' : '1';
    }
    
    // Initialize carousel position and state
    carousel.style.transition = 'transform 0.5s ease';
    updateCarousel();
    
    // Add touch support for mobile
    let startX = 0;
    let endX = 0;
    
    carousel.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
    });
    
    carousel.addEventListener('touchend', function(e) {
        endX = e.changedTouches[0].clientX;
        
        // Calculate swipe distance
        const diff = startX - endX;
        
        if (Math.abs(diff) > 50) { // Minimum swipe distance
            if (diff > 0) { // Swipe left
                const nextEvent = new Event('click');
                nextBtn.dispatchEvent(nextEvent);
            } else { // Swipe right
                const prevEvent = new Event('click');
                prevBtn.dispatchEvent(prevEvent);
            }
        }
    });
    
    // Auto play carousel
    let autoplayInterval = setInterval(function() {
        if (currentIndex < totalItems - visibleItems) {
            currentIndex++;
        } else {
            currentIndex = 0;
        }
        updateCarousel();
    }, 5000);
    
    // Stop autoplay on hover
    carousel.addEventListener('mouseenter', function() {
        clearInterval(autoplayInterval);
    });
    
    carousel.addEventListener('mouseleave', function() {
        autoplayInterval = setInterval(function() {
            if (currentIndex < totalItems - visibleItems) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateCarousel();
        }, 5000);
    });
    
    // Adjust carousel on window resize
    window.addEventListener('resize', function() {
        const newVisibleItems = Math.floor(carousel.offsetWidth / itemWidth);
        
        if (newVisibleItems !== visibleItems && newVisibleItems > 0) {
            currentIndex = Math.min(currentIndex, totalItems - newVisibleItems);
            updateCarousel();
        }
    });
}

/**
 * Initialize product gallery functionality
 */
function initProductGallery() {
    const mainImage = document.querySelector('.product-gallery img');
    const thumbnails = document.querySelectorAll('.product-thumbnail img');
    
    if (!mainImage || thumbnails.length === 0) return;
    
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Update main image
            mainImage.src = this.src;
            
            // Update active state
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Set first thumbnail as active by default
    thumbnails[0].classList.add('active');
}

/**
 * Add fade-in animation to elements
 */
function addFadeInAnimation() {
    const elements = document.querySelectorAll('.product-card, .category-card, .about-content, .contact-form');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    elements.forEach(element => {
        observer.observe(element);
    });
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    // Create or update error message
                    let errorMsg = field.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                    errorMsg.textContent = 'To pole jest wymagane.';
                } else {
                    field.classList.remove('is-invalid');
                    
                    // Remove error message if exists
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                    
                    // Email validation
                    if (field.type === 'email' && !isValidEmail(field.value)) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        
                        let errorMsg = field.nextElementSibling;
                        if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                            errorMsg = document.createElement('div');
                            errorMsg.className = 'error-message';
                            field.parentNode.insertBefore(errorMsg, field.nextSibling);
                        }
                        errorMsg.textContent = 'Proszę podać poprawny adres email.';
                    }
                    
                    // Password validation for registration
                    if (field.id === 'password' && field.value.length < 8) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        
                        let errorMsg = field.nextElementSibling;
                        if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                            errorMsg = document.createElement('div');
                            errorMsg.className = 'error-message';
                            field.parentNode.insertBefore(errorMsg, field.nextSibling);
                        }
                        errorMsg.textContent = 'Hasło musi mieć co najmniej 8 znaków.';
                    }
                    
                    // Password confirmation
                    if (field.id === 'confirm_password') {
                        const password = document.getElementById('password');
                        if (password && field.value !== password.value) {
                            isValid = false;
                            field.classList.add('is-invalid');
                            
                            let errorMsg = field.nextElementSibling;
                            if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                                errorMsg = document.createElement('div');
                                errorMsg.className = 'error-message';
                                field.parentNode.insertBefore(errorMsg, field.nextSibling);
                            }
                            errorMsg.textContent = 'Hasła nie są identyczne.';
                        }
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const fields = form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            field.addEventListener('blur', function() {
                if (field.hasAttribute('required') && !field.value.trim()) {
                    field.classList.add('is-invalid');
                    
                    let errorMsg = field.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                    errorMsg.textContent = 'To pole jest wymagane.';
                } else {
                    field.classList.remove('is-invalid');
                    
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
        });
    });
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}