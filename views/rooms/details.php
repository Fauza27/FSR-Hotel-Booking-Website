<?php
// Pastikan variabel ini sudah di-escape di controller jika berasal dari input pengguna,
// namun untuk judul halaman, biasanya aman jika berasal dari data database yang terpercaya.
$pageTitle = htmlspecialchars($room->room_number) . ' - ' . htmlspecialchars($room->category_name);
$currentPage = 'rooms';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<style>
    /* CSS untuk bintang rating */
    .star-rating { 
        color: #ffc107; /* Warna bintang terisi */
        display: inline-block;
    }
    .star-rating .far.fa-star { /* Bintang kosong (outline) */
        color: #e4e5e9; /* Warna yang lebih soft untuk bintang kosong */
    }
    .star-rating .fas.fa-star-half-alt { /* Bintang setengah */
        color: #ffc107;
    }
    
    .review-list .review-item {
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 15px;
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
    }
    
    .review-list .review-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .review-meta { 
        font-size: 0.9em; 
        color: #777;
        margin-bottom: 10px;
    }
    
    .review-form .form-group label { 
        font-weight: bold; 
    }
    
    /* Rating stars di form */
    .rating-stars { 
        display: inline-block;
        unicode-bidi: bidi-override; 
        direction: rtl; /* Bintang dari kanan ke kiri */
        margin: 10px 0;
    }
    
    .rating-stars input[type="radio"] { 
        display: none; /* Sembunyikan radio button asli */
    }
    
    .rating-stars label {
        font-size: 2em;
        color: #ddd; /* Warna bintang default (kosong) */
        cursor: pointer;
        padding: 0 0.1em;
        transition: color 0.2s ease;
    }
    
    /* Styling saat radio button di-check atau label di-hover */
    .rating-stars input[type="radio"]:checked ~ label, /* Semua label setelah yang di-check */
    .rating-stars label:hover, /* Label yang di-hover */
    .rating-stars label:hover ~ label { /* Semua label setelah yang di-hover */
        color: #ffc107; /* Warna bintang aktif/hover */
    }
    
    .average-rating {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #ffc107;
    }
    
    .review-form {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .thumbnails {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    .thumbnail {
        width: 100px; 
        height: 75px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.3s;
        overflow: hidden; /* Pastikan gambar tidak keluar dari border */
    }
    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Gambar akan mengisi area tanpa distorsi */
    }
    .thumbnail.active {
        border-color: #007bff; 
    }
</style>

<!-- Room Details -->
<div class="container mt-4 mb-4">
    <div class="room-detail">
        <div class="room-detail-main">
            <!-- Room Gallery -->
            <div class="room-gallery">
                <div class="main-image">
                    <?php 
                    $mainImageUrl = APP_URL . '/assets/images/room-placeholder.jpg'; // Default
                    // Ambil gambar pertama dari $roomImages jika ada dan image_url tidak kosong
                    if (!empty($roomImages) && isset($roomImages[0]->image_url) && !empty(trim($roomImages[0]->image_url))) {
                        $mainImageUrl = APP_URL . '/assets/images/rooms/' . htmlspecialchars($roomImages[0]->image_url);
                    }
                    ?>
                    <img src="<?= $mainImageUrl; ?>" 
                         alt="<?= htmlspecialchars($room->room_number); ?>" 
                         id="main-room-image">
                </div>
                
                <?php if (!empty($roomImages) && count($roomImages) > 1): ?>
                    <div class="thumbnails">
                        <?php foreach ($roomImages as $index => $image): ?>
                            <?php if (isset($image->image_url) && !empty(trim($image->image_url))): // Pastikan image_url ada dan tidak kosong ?>
                            <div class="thumbnail <?= $index === 0 ? 'active' : ''; ?>" 
                                 data-image="<?= APP_URL . '/assets/images/rooms/' . htmlspecialchars($image->image_url); ?>">
                                <img src="<?= APP_URL . '/assets/images/rooms/' . htmlspecialchars($image->image_url); ?>" 
                                     alt="<?= htmlspecialchars($room->room_number); ?> - image <?= $index + 1; ?>">
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Room Features -->
            <div class="room-features">
                <h3 class="feature-title">Room Features</h3>
                <div class="feature-list">
                    <?php if (!empty($roomFacilities)): ?>
                        <?php foreach ($roomFacilities as $facility): ?>
                            <div class="feature-item">
                                <i class="fas fa-check-circle feature-icon"></i>
                                <span><?= htmlspecialchars($facility->name); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No specific facilities listed for this room.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Room Description -->
            <div class="room-description">
                <h3 class="feature-title">Room Description</h3>
                <p><?= nl2br(htmlspecialchars($room->description ?? 'No description available.')); ?></p>
                <p class="mt-2">Category: <strong><?= htmlspecialchars($room->category_name ?? 'N/A'); ?></strong></p>
                <p><?= nl2br(htmlspecialchars($room->category_description ?? '')); ?></p>
            </div>

            <!-- Reviews Section -->
            <div class="room-reviews mt-4">
                <h3 class="feature-title">Guest Reviews (<?= $totalReviews ?>)</h3>
                
                <?php if ($totalReviews > 0): ?>
                    <div class="average-rating">
                        <p><strong>Average Rating:</strong> 
                            <span class="star-rating">
                                <?php for ($k = 1; $k <= 5; $k++): ?>
                                    <?php if ($k <= floor($averageRating)): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($k - 0.5 <= $averageRating): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </span>
                            <strong><?= number_format($averageRating, 1); ?> out of 5</strong> (based on <?= $totalReviews ?> review<?= $totalReviews > 1 ? 's' : ''; ?>)
                        </p>
                    </div>
                <?php endif; ?>

                <div class="review-list mt-3">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <h5><?= htmlspecialchars($review->user_fullname ?: ($review->user_username ?: 'Anonymous')); ?></h5>
                                <div class="star-rating">
                                    <?php for ($j = 1; $j <= 5; $j++): ?>
                                        <i class="<?= ($j <= $review->rating) ? 'fas' : 'far'; ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="review-meta">Reviewed on: <?= htmlspecialchars(date('F j, Y', strtotime($review->created_at))); ?></p>
                                <p><?= nl2br(htmlspecialchars($review->comment)); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No reviews yet for this room. Be the first to review!</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Review Form Section -->
            <div class="add-review-form mt-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php 
                    // Variabel $userCanReview dan $eligibleBookingIdForReview harusnya sudah di-set di controller
                    // jika kode controller sebelumnya sudah diterapkan.
                    if (isset($userCanReview) && $userCanReview && isset($eligibleBookingIdForReview) && $eligibleBookingIdForReview !== null): 
                    ?>
                        <div class="review-form">
                            <h3 class="feature-title">Leave a Review</h3>
                            <form action="<?= APP_URL; ?>/review/create" method="POST">
                                <input type="hidden" name="room_id" value="<?= $room->room_id; ?>">
                                <input type="hidden" name="booking_id" value="<?= $eligibleBookingIdForReview; ?>">
                                
                                <div class="form-group">
                                    <label for="rating-input">Your Rating:</label> <!-- ID di sini sebaiknya unik jika 'rating' dipakai di tempat lain -->
                                    <div class="rating-stars">
                                        <input type="radio" id="star5" name="rating" value="5" required/>
                                        <label for="star5" title="5 stars">★</label>
                                        <input type="radio" id="star4" name="rating" value="4" />
                                        <label for="star4" title="4 stars">★</label>
                                        <input type="radio" id="star3" name="rating" value="3" />
                                        <label for="star3" title="3 stars">★</label>
                                        <input type="radio" id="star2" name="rating" value="2" />
                                        <label for="star2" title="2 stars">★</label>
                                        <input type="radio" id="star1" name="rating" value="1" />
                                        <label for="star1" title="1 star">★</label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="comment">Your Review:</label>
                                    <textarea name="comment" id="comment" rows="4" class="form-control" 
                                              placeholder="Share your experience with this room..." required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary mt-2">Submit Review</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p><i class="fas fa-info-circle"></i> You can review this room after completing a booking for it and if you haven't reviewed that specific booking yet.</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <p><i class="fas fa-sign-in-alt"></i> 
                           <a href="<?= APP_URL; ?>/login?redirect=<?= urlencode(APP_URL . $_SERVER['REQUEST_URI']); // Perbaikan: $_SERVER['REQUEST_URI'] sudah termasuk query string ?>">Login</a> 
                           to leave a review after your stay.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Room Sidebar - Booking Form -->
        <div class="room-sidebar">
            <h3 class="sidebar-title">Book This Room</h3>
            <div class="price-tag">
                Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?> <span>/ night</span>
            </div>
            
            <form action="<?= APP_URL; ?>/booking/create" method="POST" class="booking-form" enctype="multipart/form-data">
                <input type="hidden" name="room_id" value="<?= $room->room_id; ?>">
                
                <div class="form-group">
                    <label for="check_in">Check In</label>
                    <input type="date" id="check_in" name="check_in" class="form-control" 
                           min="<?= date('Y-m-d'); ?>" 
                           value="<?= isset($_GET['check_in']) ? htmlspecialchars($_GET['check_in']) : date('Y-m-d'); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="check_out">Check Out</label>
                    <input type="date" id="check_out" name="check_out" class="form-control" 
                           min="<?= date('Y-m-d', strtotime('+1 day')); ?>" 
                           value="<?= isset($_GET['check_out']) ? htmlspecialchars($_GET['check_out']) : date('Y-m-d', strtotime('+1 day')); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="adults">Adults</label>
                    <select id="adults" name="adults" class="form-control">
                        <?php 
                        $selectedAdults = isset($_GET['adults']) ? intval($_GET['adults']) : 1;
                        $maxAdults = $room->capacity ?? 1; // Default ke 1 jika capacity tidak ada
                        for ($i = 1; $i <= $maxAdults; $i++): ?>
                            <option value="<?= $i; ?>" <?= ($selectedAdults == $i) ? 'selected' : ''; ?>>
                                <?= $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="children">Children</label>
                    <select id="children" name="children" class="form-control">
                        <?php 
                        $selectedChildren = isset($_GET['children']) ? intval($_GET['children']) : 0;
                        for ($i = 0; $i <= 5; $i++): // Anda bisa menyesuaikan batas atas anak-anak jika perlu ?>
                            <option value="<?= $i; ?>" <?= ($selectedChildren == $i) ? 'selected' : ''; ?>>
                                <?= $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="identity_file">Upload KTP <span style="color: var(--error-color);">*</span></label>
                    <input type="file" id="identity_file" name="identity_file" class="form-control" 
                           accept="image/jpeg,image/png,image/jpg,application/pdf" required>
                    <small style="color: var(--medium-text);">Format yang diizinkan: JPG, JPEG, PNG, PDF. Maks: 2MB</small>
                </div>
                
                <div class="total-info">
                    <div class="total-row">
                        <span>Price per night:</span>
                        <span>Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Number of nights:</span>
                        <span id="num-nights">1</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total:</span>
                        <span id="total-price">Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></span>
                        <input type="hidden" name="total_price" id="total-price-input" value="<?= $room->price_per_night; ?>">
                    </div>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button type="submit" class="btn btn-primary btn-block mt-3">Book Now</button>
                <?php else: ?>
                    <a href="<?= APP_URL; ?>/login?redirect=<?= urlencode(APP_URL . $_SERVER['REQUEST_URI']); ?>" 
                       class="btn btn-primary btn-block mt-3">Login to Book</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<!-- Similar Rooms -->
<?php if (!empty($similarRooms) && count(array_filter($similarRooms, function($sr) use ($room) { return $sr->room_id != $room->room_id; })) > 0 ): ?>
<section class="rooms-section">
    <div class="container">
        <div class="section-title">
            <h2>Similar Rooms</h2>
        </div>
        
        <div class="rooms-grid">
            <?php $similarRoomDisplayedCount = 0; ?>
            <?php foreach ($similarRooms as $sRoom): ?>
                <?php if ($sRoom->room_id != $room->room_id && $similarRoomDisplayedCount < 3): ?>
                    <div class="room-card">
                        <div class="room-img">
                            <img src="<?= !empty($sRoom->primary_image_url) ? APP_URL . '/assets/images/rooms/' . htmlspecialchars($sRoom->primary_image_url) : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" 
                                 alt="<?= htmlspecialchars($sRoom->room_number); ?>">
                            <div class="room-category"><?= htmlspecialchars($sRoom->category_name); ?></div>
                        </div>
                        
                        <div class="room-info">
                            <h3 class="room-title"><?= htmlspecialchars($sRoom->room_number); ?></h3>
                            
                            <div class="room-details">
                                <span><i class="fas fa-user"></i> Max <?= htmlspecialchars($sRoom->capacity); ?> Persons</span>
                                <span><i class="fas fa-vector-square"></i> <?= htmlspecialchars($sRoom->size_sqm); ?> sqm</span>
                            </div>
                            
                            <div class="room-price">
                                Rp <?= number_format($sRoom->price_per_night, 0, ',', '.'); ?> <span>/ night</span>
                            </div>
                            
                            <div class="room-footer">
                                <div class="rating">
                                    <?php /* Anda perlu logika untuk mendapatkan rating similar rooms jika ada */ ?>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span>4.5</span>
                                </div>
                                <?php
                                $currentParams = [];
                                if (isset($_GET['check_in'])) $currentParams['check_in'] = $_GET['check_in'];
                                if (isset($_GET['check_out'])) $currentParams['check_out'] = $_GET['check_out'];
                                if (isset($_GET['adults'])) $currentParams['adults'] = $_GET['adults'];
                                if (isset($_GET['children'])) $currentParams['children'] = $_GET['children'];
                                $similarRoomQueryString = !empty($currentParams) ? '?' . http_build_query($currentParams) : '';
                                ?>
                                <a href="<?= APP_URL . '/room/view/' . $sRoom->room_id . $similarRoomQueryString; ?>" 
                                   class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php $similarRoomDisplayedCount++; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const numNightsElement = document.getElementById('num-nights');
    const totalPriceElement = document.getElementById('total-price');
    const totalPriceInput = document.getElementById('total-price-input');
    const pricePerNight = parseFloat(<?= $room->price_per_night ?? 0; ?>); // Default ke 0 jika tidak ada
    
    function updatePricing() {
        if (!checkInInput || !checkOutInput || !numNightsElement || !totalPriceElement || !totalPriceInput) {
            console.error('One or more pricing elements are missing for booking form.');
            return;
        }
        
        const checkInDateStr = checkInInput.value;
        const checkOutDateStr = checkOutInput.value;

        if (!checkInDateStr || !checkOutDateStr) {
            numNightsElement.textContent = '0';
            totalPriceElement.textContent = 'Rp 0';
            if (totalPriceInput) totalPriceInput.value = 0;
            return;
        }
        
        const checkIn = new Date(checkInDateStr);
        const checkOut = new Date(checkOutDateStr);

        if (isNaN(checkIn.getTime()) || isNaN(checkOut.getTime()) || checkOut <= checkIn) {
            numNightsElement.textContent = '0';
            totalPriceElement.textContent = 'Rp 0';
            if (totalPriceInput) totalPriceInput.value = 0;
            return;
        }

        const timeDiff = checkOut.getTime() - checkIn.getTime();
        const nightCount = Math.max(0, Math.ceil(timeDiff / (1000 * 3600 * 24)));
        
        numNightsElement.textContent = nightCount;
        const total = nightCount * pricePerNight;
        totalPriceElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        if (totalPriceInput) totalPriceInput.value = total;
    }
    
    if (checkInInput && checkOutInput) {
        checkInInput.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            if (isNaN(checkInDate.getTime())) {
                 updatePricing(); // Reset harga jika tanggal tidak valid
                 return;
            }

            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            
            const year = nextDay.getFullYear();
            const month = (nextDay.getMonth() + 1).toString().padStart(2, '0');
            const day = nextDay.getDate().toString().padStart(2, '0');
            
            const minCheckoutDate = `${year}-${month}-${day}`;
            checkOutInput.min = minCheckoutDate;
            
            if (new Date(checkOutInput.value) <= checkInDate) {
                checkOutInput.value = minCheckoutDate;
            }
            updatePricing();
        });
        
        checkOutInput.addEventListener('change', updatePricing);
        
        if (checkInInput.value && checkOutInput.value) {
            updatePricing(); // Initialize pricing on page load if dates are pre-filled
        }
    }
    
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('main-room-image');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                thumbnails.forEach(thumb => thumb.classList.remove('active'));
                this.classList.add('active');
                const newImageSrc = this.getAttribute('data-image');
                if (newImageSrc) {
                    mainImage.src = newImageSrc;
                }
            });
        });
    }
    
    // CSS handles rating stars hover and checked state. JavaScript for this can be removed if CSS is sufficient.
    // Jika Anda ingin JavaScript untuk mengubah warna secara dinamis (misal, saat load dari database):
    const initiallyCheckedStar = document.querySelector('.rating-stars input[type="radio"]:checked');
    if (initiallyCheckedStar) {
        // CSS :checked ~ label should handle this.
        // If you need JS to set it explicitly (e.g. if loaded value needs to be set):
        // highlightStars(initiallyCheckedStar.value); 
    }
});
</script>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>