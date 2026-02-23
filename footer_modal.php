<style>
    /* Footer Specific Styles para bumagay sa Dark Theme */
    .footer-section {
        background-color: #1a1a1a; /* Dark background */
        color: #b0b0b0;           /* Muted text */
        padding: 60px 0 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        font-family: 'Manrope', sans-serif;
    }
    
    .footer-brand {
        font-family: 'Playfair Display', serif;
        color: #fff;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 20px;
        display: block;
        text-decoration: none;
    }

    .footer-heading {
        color: #bfa065; /* Gold accent */
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 20px;
    }

    .footer-link {
        color: #b0b0b0;
        text-decoration: none;
        display: block;
        margin-bottom: 10px;
        transition: 0.3s;
        font-size: 0.9rem;
    }

    .footer-link:hover {
        color: #bfa065; /* Gold hover */
        padding-left: 5px;
    }

    .social-icon {
        color: #fff;
        background: rgba(255,255,255,0.05);
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 10px;
        transition: 0.3s;
        text-decoration: none;
    }

    .social-icon:hover {
        background: #bfa065;
        color: #000;
        transform: translateY(-3px);
    }

    .copyright {
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        margin-top: 50px;
        padding-top: 20px;
        font-size: 0.8rem;
        text-align: center;
    }
</style>

<footer class="footer-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-5 mb-lg-0">
                <a href="index.php" class="footer-brand">VISITEASE</a>
                <p style="max-width: 300px; line-height: 1.6;">
                    Dedicated to preserving the legacy and history of Mayor Pedro S. Tolentino. A place where history is felt, not just seen.
                </p>
                <div class="mt-4">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="footer-heading">Explore</h5>
                <a href="index.php" class="footer-link">Home</a>
                <a href="exhibits.php" class="footer-link">Exhibits</a>
                <a href="gallery.php" class="footer-link">Gallery</a>
                <a href="about.php" class="footer-link">About Us</a>
            </div>

            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="footer-heading">Visit</h5>
                <a href="book.php" class="footer-link">Book Tickets</a>
                <a href="check_status.php" class="footer-link">Track Status</a>
                <a href="#" class="footer-link">Rules & Regulations</a>
                <a href="#" class="footer-link">FAQs</a>
            </div>

            <div class="col-lg-4">
                <h5 class="footer-heading">Contact</h5>
                <p class="mb-2"><i class="fas fa-map-marker-alt me-2" style="color: #bfa065;"></i> Cultural District, Batangas City</p>
                <p class="mb-2"><i class="fas fa-phone me-2" style="color: #bfa065;"></i> (043) 123-4567</p>
                <p class="mb-2"><i class="fas fa-envelope me-2" style="color: #bfa065;"></i> info@tolentinomuseum.ph</p>
            </div>
        </div>

        <div class="copyright">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Pedro S. Tolentino Museum. All Rights Reserved.</p>
        </div>
    </div>
</footer>