<?php
?>
    </main>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <a href="<?= APP_URL ?>" class="footer-logo">FSR<span>Hotel</span></a>
                    <p class="footer-description">
                        Experience luxury and comfort in our modern hotel. Book your stay with us for an unforgettable experience.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul>
                        <li><a href="<?= APP_URL ?>">Home</a></li>
                        <li><a href="<?= APP_URL ?>/rooms">Rooms</a></li>
                        <li><a href="<?= APP_URL ?>/about">About Us</a></li>
                        <li><a href="<?= APP_URL ?>/contact">Contact</a></li>
                        <!-- <li><a href="<?= APP_URL ?>/privacy-policy">Privacy Policy</a></li>
                        <li><a href="<?= APP_URL ?>/terms">Terms & Conditions</a></li> -->
                    </ul>
                </div>
                
                <div class="footer-contact">
                    <h3 class="footer-title">Contact Us</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt contact-icon"></i>
                            <span>FSR Hotel Street , Samarinda City, Indonesia</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt contact-icon"></i>
                            <span>+62 123 4567 890</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope contact-icon"></i>
                            <span>info@fsrhotel.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y'); ?> FSR Hotel. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>