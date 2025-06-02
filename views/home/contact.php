<?php
$pageTitle = 'Contact Us';
$currentPage = 'contact';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<section class="container mt-4 mb-4">
    <div class="section-title">
        <h1>Contact Us</h1>
    </div>
    <div class="room-card" style="max-width:700px;margin:0 auto;">
        <div class="room-info">
            <p>Jika Anda memiliki pertanyaan, kritik, atau saran, silakan hubungi kami melalui form di bawah ini atau kontak langsung ke hotel kami.</p>
            <form action="<?= APP_URL ?>/contact" method="POST" class="mb-3">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
            <div style="margin-top:2rem;">
                <h4>Hotel Contact</h4>
                <p><i class="fas fa-map-marker-alt"></i> FSR Hotel Street , Samarinda City, Indonesia</p>
                <p><i class="fas fa-phone-alt"></i> +62 123 4567 890</p>
                <p><i class="fas fa-envelope"></i> info@fsrhotel.com</p>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>
