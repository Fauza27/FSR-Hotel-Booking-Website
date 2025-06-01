<?php
$pageTitle = 'Our Rooms';
$currentPage = 'rooms';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<!-- Rooms Banner -->
<section class="hero" style="padding: 2rem 0;">
    <div class="container">
        <div class="hero-content">
            <h1>Our Rooms</h1>
            <p>Choose from our selection of comfortable and luxurious rooms.</p>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="container">
    <div class="search-form">
        <form action="<?= APP_URL ?>/rooms" method="GET">
            <div class="search-inputs">
                <div class="form-group">
                    <label for="check_in">Check In</label>
                    <input type="date" id="check_in" name="check_in" class="form-control" min="<?= date('Y-m-d'); ?>" value="<?= isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="check_out">Check Out</label>
                    <input type="date" id="check_out" name="check_out" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" value="<?= isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="adults">Adults</label>
                    <select id="adults" name="adults" class="form-control">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i; ?>" <?= (isset($_GET['adults']) && $_GET['adults'] == $i) ? 'selected' : ($i == 1 && !isset($_GET['adults']) ? 'selected' : ''); ?>><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="children">Children</label>
                    <select id="children" name="children" class="form-control">
                        <?php for ($i = 0; $i <= 5; $i++): ?>
                            <option value="<?= $i; ?>" <?= (isset($_GET['children']) && $_GET['children'] == $i) ? 'selected' : ($i == 0 && !isset($_GET['children']) ? 'selected' : ''); ?>><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category">Room Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category->category_id; ?>" <?= (isset($_GET['category']) && $_GET['category'] == $category->category_id) ? 'selected' : ''; ?>><?= $category->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Search Available Rooms</button>
        </form>
    </div>
</section>

<!-- Rooms List -->
<section class="rooms-section">
    <div class="container">
        <?php if (empty($rooms)): ?>
            <div class="alert alert-info">
                <p>No rooms available for the selected dates. Please try different dates or criteria.</p>
            </div>
        <?php else: ?>
            <div class="section-title">
                <h2>Available Rooms</h2>
            </div>
            
            <div class="rooms-grid">
                <?php foreach ($rooms as $room): ?>
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
                                
                                <a href="<?= APP_URL . '/room/view/' . $room->room_id; ?><?= isset($_GET['check_in']) ? '?check_in=' . $_GET['check_in'] . '&check_out=' . $_GET['check_out'] . '&adults=' . $_GET['adults'] . '&children=' . $_GET['children'] : ''; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>