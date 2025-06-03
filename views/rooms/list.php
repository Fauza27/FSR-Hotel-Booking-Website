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
                    <input type="date" id="check_in" name="check_in" class="form-control" min="<?= date('Y-m-d'); ?>" value="<?= htmlspecialchars(isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d')); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="check_out">Check Out</label>
                    <input type="date" id="check_out" name="check_out" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" value="<?= htmlspecialchars(isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'))); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="adults">Adults</label>
                    <select id="adults" name="adults" class="form-control">
                        <?php 
                        $selectedAdults = isset($_GET['adults']) ? intval($_GET['adults']) : 1;
                        for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i; ?>" <?= ($selectedAdults == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="children">Children</label>
                    <select id="children" name="children" class="form-control">
                        <?php 
                        $selectedChildren = isset($_GET['children']) ? intval($_GET['children']) : 0;
                        for ($i = 0; $i <= 5; $i++): ?>
                            <option value="<?= $i; ?>" <?= ($selectedChildren == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category">Room Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php 
                        $selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : '';
                        foreach ($categories as $category): ?>
                            <option value="<?= $category->category_id; ?>" <?= ($selectedCategory == $category->category_id) ? 'selected' : ''; ?>><?= htmlspecialchars($category->name); ?></option>
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
                <p>No rooms available for the selected dates or criteria. Please try different dates or criteria.</p>
            </div>
        <?php else: ?>
            <div class="section-title">
                <h2>Available Rooms</h2>
            </div>
            
            <div class="rooms-grid">
                <?php foreach ($rooms as $room): ?>
                    <div class="room-card">
                        <div class="room-img">
                            
                            <img src="<?= !empty($room->image_url) ? APP_URL . '/assets/images/rooms/' . $room->image_url : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="<?= htmlspecialchars($room->room_number); ?>">
                            <!-- <img src="<?= $imageUrl; ?>" alt="<?= htmlspecialchars($room->room_number); ?>"> -->
                            <div class="room-category"><?= htmlspecialchars($room->category_name); ?></div>
                        </div>
                        
                        <div class="room-info">
                            <h3 class="room-title"><?= htmlspecialchars($room->room_number); ?></h3>
                            
                            <div class="room-details">
                                <span><i class="fas fa-user"></i> Max <?= htmlspecialchars($room->capacity); ?> Persons</span>
                                <span><i class="fas fa-vector-square"></i> <?= htmlspecialchars($room->size_sqm); ?> sqm</span>
                            </div>
                            
                            <div class="room-price">
                                Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?> <span>/ night</span>
                            </div>
                            
                            <div class="room-footer">
                                <div class="rating">
                                    <?php
                                    // Logika untuk menampilkan rating kamar jika tersedia
                                    // Misalkan $room->average_rating dan $room->total_reviews juga di-fetch dari model
                                    // Jika tidak, ini hanya placeholder
                                    $avgRating = $room->average_rating ?? 0; // Ambil dari objek room jika ada
                                    $revCount = $room->total_reviews ?? 0;  // Ambil dari objek room jika ada
                                    ?>
                                    <?php if ($revCount > 0): ?>
                                        <?php for ($k = 1; $k <= 5; $k++): ?>
                                            <?php if ($k <= floor($avgRating)): ?>
                                                <i class="fas fa-star"></i>
                                            <?php elseif ($k - 0.5 <= $avgRating): ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <span><?= number_format($avgRating, 1); ?> (<?= $revCount; ?>)</span>
                                    <?php else: ?>
                                        <span style="font-size: 0.9em; color: #777;">No reviews yet</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php
                                $queryParams = [];
                                if (isset($_GET['check_in'])) $queryParams['check_in'] = $_GET['check_in'];
                                if (isset($_GET['check_out'])) $queryParams['check_out'] = $_GET['check_out'];
                                if (isset($_GET['adults'])) $queryParams['adults'] = $_GET['adults'];
                                if (isset($_GET['children'])) $queryParams['children'] = $_GET['children'];
                                $queryString = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
                                ?>
                                <a href="<?= APP_URL . '/room/view/' . $room->room_id . $queryString; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>