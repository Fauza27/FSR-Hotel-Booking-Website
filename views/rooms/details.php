<?php
$pageTitle = $room->room_number . ' - ' . $room->category_name;
$currentPage = 'rooms';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<style>
    /* CSS untuk bintang rating */
    .star-rating { 
        color: #ffc107; 
        display: inline-block;
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
    
    .rating-stars { 
        display: inline-block;
        unicode-bidi: bidi-override; 
        direction: rtl;
        margin: 10px 0;
    }
    
    .rating-stars input[type="radio"] { 
        display: none; 
    }
    
    .rating-stars label {
        font-size: 2em;
        color: #ddd;
        cursor: pointer;
        padding: 0 0.1em;
        transition: color 0.2s ease;
    }
    
    .rating-stars input[type="radio"]:checked ~ label,
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #ffc107;
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
</style>

<!-- Room Details -->
<div class="container mt-4 mb-4">
    <div class="room-detail">
        <div class="room-detail-main">
            <!-- Room Gallery -->
            <div class="room-gallery">
                <div class="main-image">
                    <img src="<?= !empty($roomImages) && count($roomImages) > 0 ? APP_URL . '/assets/images/rooms/' . $roomImages[0]->image_url : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" 
                         alt="<?= htmlspecialchars($room->room_number); ?>" 
                         id="main-room-image">
                </div>
                
                <?php if (!empty($roomImages) && count($roomImages) > 1): ?>
                    <div class="thumbnails">
                        <?php foreach ($roomImages as $index => $image): ?>
                            <div class="thumbnail <?= $index === 0 ? 'active' : ''; ?>" 
                                 data-image="<?= APP_URL . '/assets/images/rooms/' . $image->image_url; ?>">
                                <img src="<?= APP_URL . '/assets/images/rooms/' . $image->image_url; ?>" 
                                     alt="<?= htmlspecialchars($room->room_number); ?> - image <?= $index + 1; ?>">
                            </div>
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
                <p><?= nl2br(htmlspecialchars($room->description)); ?></p>
                <p class="mt-2">Category: <strong><?= htmlspecialchars($room->category_name); ?></strong></p>
                <p><?= nl2br(htmlspecialchars($room->category_description)); ?></p>
            </div>

            <!-- Reviews Section -->
            <div class="room-reviews mt-4">
                <h3 class="feature-title">Guest Reviews (<?= $totalReviews ?>)</h3>
                
                <?php if ($totalReviews > 0): ?>
                    <div class="average-rating">
                        <p><strong>Average Rating:</strong> 
                            <span class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $averageRating): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($i - 0.5 <= $averageRating): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </span>
                            <strong><?= number_format($averageRating, 1); ?> out of 5</strong> (<?= $totalReviews ?> review<?= $totalReviews > 1 ? 's' : ''; ?>)
                        </p>
                    </div>
                <?php endif; ?>

                <div class="review-list mt-3">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <h5><?= htmlspecialchars($review->user_fullname ?: $review->user_username); ?></h5>
                                <div class="star-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?= $i <= $review->rating ? '' : ' far'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="review-meta">Reviewed on: <?= date('F j, Y', strtotime($review->created_at)); ?></p>
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
                    <?php if (isset($eligibleBookingForReview) && $eligibleBookingForReview): ?>
                        <div class="review-form">
                            <h3 class="feature-title">Leave a Review</h3>
                            <form action="<?= APP_URL; ?>/review/create" method="POST">
                                <input type="hidden" name="room_id" value="<?= $room->room_id; ?>">
                                <input type="hidden" name="booking_id" value="<?= $eligibleBookingForReview->booking_id; ?>">
                                
                                <div class="form-group">
                                    <label for="rating">Your Rating:</label>
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
                            <p><i class="fas fa-info-circle"></i> You can review this room after completing a booking and haven't reviewed it yet.</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <p><i class="fas fa-sign-in-alt"></i> 
                           <a href="<?= APP_URL; ?>/login?redirect=<?= urlencode(APP_URL . '/room/view/' . $room->room_id); ?>">Login</a> 
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
                        <?php for ($i = 1; $i <= $room->capacity; $i++): ?>
                            <option value="<?= $i; ?>" <?= (isset($_GET['adults']) && $_GET['adults'] == $i) ? 'selected' : ($i == 1 && !isset($_GET['adults']) ? 'selected' : ''); ?>>
                                <?= $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="children">Children</label>
                    <select id="children" name="children" class="form-control">
                        <?php for ($i = 0; $i <= 5; $i++): ?>
                            <option value="<?= $i; ?>" <?= (isset($_GET['children']) && $_GET['children'] == $i) ? 'selected' : ($i == 0 && !isset($_GET['children']) ? 'selected' : ''); ?>>
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
<?php if (!empty($similarRooms)): ?>
<section class="rooms-section">
    <div class="container">
        <div class="section-title">
            <h2>Similar Rooms</h2>
        </div>
        
        <div class="rooms-grid">
            <?php foreach ($similarRooms as $sRoom): ?>
                <?php if ($sRoom->room_id != $room->room_id): ?>
                    <div class="room-card">
                        <div class="room-img">
                            <img src="<?= !empty($sRoom->image_url) ? APP_URL . '/assets/images/rooms/' . $sRoom->image_url : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" 
                                 alt="<?= htmlspecialchars($sRoom->room_number); ?>">
                            <div class="room-category"><?= htmlspecialchars($sRoom->category_name); ?></div>
                        </div>
                        
                        <div class="room-info">
                            <h3 class="room-title"><?= htmlspecialchars($sRoom->room_number); ?></h3>
                            
                            <div class="room-details">
                                <span><i class="fas fa-user"></i> Max <?= $sRoom->capacity; ?> Persons</span>
                                <span><i class="fas fa-vector-square"></i> <?= $sRoom->size_sqm; ?> sqm</span>
                            </div>
                            
                            <div class="room-price">
                                Rp <?= number_format($sRoom->price_per_night, 0, ',', '.'); ?> <span>/ night</span>
                            </div>
                            
                            <div class="room-footer">
                                <div class="rating">
                                    <?php 
                                    // Jika Anda memiliki data rating untuk similar rooms, gunakan di sini
                                    // Untuk sementara menggunakan placeholder
                                    ?>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span>4.5</span>
                                </div>
                                
                                <a href="<?= APP_URL . '/room/view/' . $sRoom->room_id; ?><?= isset($_GET['check_in']) ? '?check_in=' . htmlspecialchars($_GET['check_in']) . '&check_out=' . htmlspecialchars($_GET['check_out']) . '&adults=' . htmlspecialchars($_GET['adults']) . '&children=' . htmlspecialchars($_GET['children']) : ''; ?>" 
                                   class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const numNightsElement = document.getElementById('num-nights');
    const totalPriceElement = document.getElementById('total-price');
    const totalPriceInput = document.getElementById('total-price-input');
    const pricePerNight = <?= $room->price_per_night; ?>;
    
    // Price calculation function
    function updatePricing() {
        if (!checkInInput || !checkOutInput) return;
        
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);

        // Basic date validation
        if (isNaN(checkIn.getTime()) || isNaN(checkOut.getTime()) || checkOut <= checkIn) {
            numNightsElement.textContent = '0';
            totalPriceElement.textContent = 'Rp 0';
            totalPriceInput.value = 0;
            return;
        }

        const timeDiff = checkOut.getTime() - checkIn.getTime();
        const nightCount = Math.ceil(timeDiff / (1000 * 3600 * 24));
        
        if (nightCount > 0) {
            numNightsElement.textContent = nightCount;
            const totalPrice = nightCount * pricePerNight;
            totalPriceElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice);
            totalPriceInput.value = totalPrice;
        } else {
            numNightsElement.textContent = '0';
            totalPriceElement.textContent = 'Rp 0';
            totalPriceInput.value = 0;
        }
    }
    
    // Check-in date change event
    if (checkInInput && checkOutInput) {
        checkInInput.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            
            const year = nextDay.getFullYear();
            const month = (nextDay.getMonth() + 1).toString().padStart(2, '0');
            const day = nextDay.getDate().toString().padStart(2, '0');
            
            checkOutInput.min = `${year}-${month}-${day}`;
            
            // Update check-out date if it's invalid
            if (new Date(checkOutInput.value) <= checkInDate) {
                checkOutInput.value = `${year}-${month}-${day}`;
            }
            
            updatePricing();
        });
        
        checkOutInput.addEventListener('change', updatePricing);
        updatePricing(); // Initialize pricing
    }
    
    // Thumbnail gallery functionality
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('main-room-image');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Remove active class from all thumbnails
                thumbnails.forEach(thumb => thumb.classList.remove('active'));
                
                // Add active class to clicked thumbnail
                this.classList.add('active');
                
                // Update main image
                const newImageSrc = this.getAttribute('data-image');
                if (newImageSrc) {
                    mainImage.src = newImageSrc;
                }
            });
        });
    }
    
    // Rating stars functionality for review form
    const ratingStars = document.querySelectorAll('.rating-stars input[type="radio"]');
    const ratingLabels = document.querySelectorAll('.rating-stars label');
    
    ratingLabels.forEach(label => {
        label.addEventListener('mouseenter', function() {
            const rating = this.getAttribute('for').replace('star', '');
            highlightStars(rating);
        });
        
        label.addEventListener('mouseleave', function() {
            const checkedRating = document.querySelector('.rating-stars input[type="radio"]:checked');
            if (checkedRating) {
                highlightStars(checkedRating.value);
            } else {
                highlightStars(0);
            }
        });
    });
    
    ratingStars.forEach(star => {
        star.addEventListener('change', function() {
            highlightStars(this.value);
        });
    });
    
    function highlightStars(rating) {
        ratingLabels.forEach((label, index) => {
            const starValue = 5 - index; // Stars are in reverse order
            if (starValue <= rating) {
                label.style.color = '#ffc107';
            } else {
                label.style.color = '#ddd';
            }
        });
    }
});
</script>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>