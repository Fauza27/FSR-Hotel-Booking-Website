<?php
$pageTitle = 'Welcome';
$currentPage = 'home';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Experience Luxury & Comfort</h1>
            <p>Book your stay with us and enjoy a world-class hospitality experience in our modern and elegant rooms.</p>
            <a href="<?= APP_URL ?>/rooms" class="btn btn-primary">Explore Rooms</a>
        </div>
    </div>
</section>

<!-- Search Form -->
<section class="container">
    <div class="search-form">
        <form action="<?= APP_URL ?>/rooms" method="GET">
            <div class="search-inputs">
                <div class="form-group">
                    <label for="check_in">Check In</label>
                    <input type="date" id="check_in" name="check_in" class="form-control" min="<?= date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="check_out">Check Out</label>
                    <input type="date" id="check_out" name="check_out" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="adults">Adults</label>
                    <select id="adults" name="adults" class="form-control">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i; ?>"><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="children">Children</label>
                    <select id="children" name="children" class="form-control">
                        <?php for ($i = 0; $i <= 5; $i++): ?>
                            <option value="<?= $i; ?>"><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Check Availability</button>
        </form>
    </div>
</section>

<!-- Featured Rooms -->
<section class="rooms-section">
    <div class="container">
        <div class="section-title">
            <h2>Featured Rooms</h2>
        </div>
        
        <div class="rooms-grid">
            <?php foreach ($featuredRooms as $room): ?>
                <div class="room-card">
                    <div class="room-img">
                        <img src="<?= !empty($room->image_url) ? APP_URL . '/assets/images/rooms/' . $room->image_url : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="<?= $room->room_number; ?>">
                        <div class="room-category"><?= $room->category_name; ?></div>
                    </div>
                    
                    <div class="room-info">
                        <h3 class="room-title"><?= $room->room_number; ?></h3>
                        
                        <div class="room-details">
                            <span><i class="fas fa-user"></i> Max <?= $room->capacity; ?> Persons</span>
                            <span><i class="fas fa-vector-square"></i> <?= $room->size_sqm; ?> sqm</span>
                        </div>
                        
                        <div class="room-price">
                            Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?> <span>/ night</span>
                        </div>
                        
                        <div class="room-footer">
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span>4.5</span>
                            </div>
                            
                            <a href="<?= APP_URL . '/room/view/' . $room->room_id; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= APP_URL ?>/rooms" class="btn btn-secondary">View All Rooms</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="container mb-4">
    <div class="section-title">
        <h2>Why Choose Us</h2>
    </div>
    
    <div class="rooms-grid">
        <div class="room-card">
            <div class="room-info text-center">
                <i class="fas fa-medal" style="font-size: 3rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                <h3 class="room-title">Quality Service</h3>
                <p>We provide top-notch service to ensure your stay is comfortable and memorable.</p>
            </div>
        </div>
        
        <div class="room-card">
            <div class="room-info text-center">
                <i class="fas fa-map-marker-alt" style="font-size: 3rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                <h3 class="room-title">Prime Location</h3>
                <p>Our hotel is located in the heart of the city, close to major attractions and amenities.</p>
            </div>
        </div>
        
        <div class="room-card">
            <div class="room-info text-center">
                <i class="fas fa-utensils" style="font-size: 3rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                <h3 class="room-title">Fine Dining</h3>
                <p>Enjoy exquisite meals prepared by our talented chefs using the finest ingredients.</p>
            </div>
        </div>
        
        <div class="room-card">
            <div class="room-info text-center">
                <i class="fas fa-spa" style="font-size: 3rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                <h3 class="room-title">Wellness Facilities</h3>
                <p>Relax and rejuvenate with our wellness facilities including spa, gym, and pool.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<!-- <section class="container mb-4">
    <div class="section-title">
        <h2>Guest Reviews</h2>
    </div>
    
    <div class="rooms-grid">
        <div class="room-card">
            <div class="room-info">
                <div class="rating mb-2">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <span>5.0</span>
                </div>
                <p class="mb-2">"Amazing experience! The room was spotless, staff were incredibly friendly, and the amenities were top-notch. Will definitely stay here again."</p>
                <div style="display: flex; align-items: center;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; margin-right: 10px;">
                        <img src="<?= APP_URL ?>/assets/images/user1.jpg" alt="Guest" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <strong>John Doe</strong>
                        <small style="display: block; color: var(--medium-text);">Business Traveler</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="room-card">
            <div class="room-info">
                <div class="rating mb-2">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                    <span>4.5</span>
                </div>
                <p class="mb-2">"We had a wonderful family vacation. The hotel's location is perfect, rooms are spacious, and the staff went above and beyond to make our stay special."</p>
                <div style="display: flex; align-items: center;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; margin-right: 10px;">
                        <img src="<?= APP_URL ?>/assets/images/user2.jpg" alt="Guest" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <strong>Jane Smith</strong>
                        <small style="display: block; color: var(--medium-text);">Family Traveler</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="room-card">
            <div class="room-info">
                <div class="rating mb-2">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <span>5.0</span>
                </div>
                <p class="mb-2">"Absolutely stunning hotel! The design is modern yet comfortable. The room service was quick, and the breakfast buffet had an amazing selection."</p>
                <div style="display: flex; align-items: center;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; margin-right: 10px;">
                        <img src="<?= APP_URL ?>/assets/images/user3.jpg" alt="Guest" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <strong>Michael Johnson</strong>
                        <small style="display: block; color: var(--medium-text);">Leisure Traveler</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> -->

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>