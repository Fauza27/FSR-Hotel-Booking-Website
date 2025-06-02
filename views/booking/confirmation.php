<?php
// views/booking/confirmation.php
$pageTitle = 'Booking Confirmation';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-4 mb-4">
    <div class="room-detail">
        <div class="room-detail-main">
            <div class="alert alert-success">
                <h4><i class="fas fa-check-circle"></i> Booking Created Successfully!</h4>
                <p>Your booking request has been received. Please proceed to payment to confirm your reservation.</p>
            </div>
            
            <div class="booking-details-section mb-4">
                <h3 class="feature-title">Booking Details</h3>
                <div class="booking-card">
                    <div class="booking-card-header">
                        <h4>Booking #<?= $booking->booking_id; ?></h4>
                        <div class="booking-status status-<?= $booking->status; ?>"><?= ucfirst($booking->status); ?></div>
                    </div>
                    
                    <div class="booking-details">
                        <div class="booking-room-img">
                            <img src="<?= !empty($roomImages) && count($roomImages) > 0 ? APP_URL . '/assets/images/rooms/' . $roomImages[0]->image_url : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="<?= $room->room_number; ?>">
                        </div>
                        
                        <div class="booking-info">
                            <div class="booking-info-item">
                                <strong>Room:</strong>
                                <span><?= $room->room_number; ?> (<?= $room->category_name; ?>)</span>
                            </div>
                            
                            <div class="booking-info-item">
                                <strong>Check-in Date:</strong>
                                <span><?= date('d M Y', strtotime($booking->check_in_date)); ?></span>
                            </div>
                            
                            <div class="booking-info-item">
                                <strong>Check-out Date:</strong>
                                <span><?= date('d M Y', strtotime($booking->check_out_date)); ?></span>
                            </div>
                            
                            <div class="booking-info-item">
                                <strong>Duration:</strong>
                                <span><?= $nights; ?> night<?= $nights > 1 ? 's' : ''; ?></span>
                            </div>
                            
                            <div class="booking-info-item">
                                <strong>Guests:</strong>
                                <span><?= $booking->adults; ?> Adult<?= $booking->adults > 1 ? 's' : ''; ?><?= $booking->children > 0 ? ', ' . $booking->children . ' Child' . ($booking->children > 1 ? 'ren' : '') : ''; ?></span>
                            </div>
                            
                            <div class="booking-info-item">
                                <strong>Booking Date:</strong>
                                <span><?= date('d M Y H:i', strtotime($booking->created_at)); ?></span>
                            </div>
                            
                            <div class="booking-info-item">
                                <strong>Identity Document:</strong>
                                <span><i class="fas fa-file-alt"></i> Uploaded</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="booking-card-footer">
                        <div class="booking-price">
                            Total: Rp <?= number_format($booking->total_price, 0, ',', '.'); ?>
                        </div>
                        
                        <div>
                            <a href="<?= APP_URL; ?>/booking/cancel/<?= $booking->booking_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel Booking</a>
                            <a href="<?= APP_URL; ?>/payment/process/<?= $booking->booking_id; ?>" class="btn btn-primary">Pay Now</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="guest-info-section mb-4">
                <h3 class="feature-title">Guest Information</h3>
                <div class="room-card">
                    <div class="room-info">
                        <div class="booking-info">
                            <div class="booking-info-item">
                                <strong>Full Name:</strong>
                                <span><?= $user->full_name; ?></span>
                            </div>
                            
                            <div class="booking-info-item">
                                <strong>Email:</strong>
                                <span><?= $user->email; ?></span>
                            </div>
                            
                            <div class="booking-info-item">
                                <strong>Phone:</strong>
                                <span><?= $user->phone; ?></span>
                            </div>
                            
                            <div class="booking-info-item">
                                <strong>Address:</strong>
                                <span><?= $user->address; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="payment-info-section">
                <h3 class="feature-title">Payment Information</h3>
                <div class="room-card">
                    <div class="room-info">
                        <p>To confirm your booking, please complete the payment. You can pay using the following methods:</p>
                        <ul style="list-style-type: disc; margin-left: 20px;">
                            <li>Credit Card</li>
                            <li>Bank Transfer</li>
                            <li>Cash at hotel reception (upon arrival)</li>
                        </ul>
                        <p class="mt-3">Please note that your booking will be cancelled automatically if payment is not completed within 24 hours.</p>
                        <div class="text-center mt-3">
                            <a href="<?= APP_URL; ?>/payment/process/<?= $booking->booking_id; ?>" class="btn btn-primary">Proceed to Payment</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="room-sidebar">
            <h3 class="sidebar-title">Booking Summary</h3>
            
            <div class="total-info">
                <div class="total-row">
                    <span>Room Type:</span>
                    <span><?= $room->category_name; ?></span>
                </div>
                <div class="total-row">
                    <span>Room Number:</span>
                    <span><?= $room->room_number; ?></span>
                </div>
                <div class="total-row">
                    <span>Check-in:</span>
                    <span><?= date('d M Y', strtotime($booking->check_in_date)); ?></span>
                </div>
                <div class="total-row">
                    <span>Check-out:</span>
                    <span><?= date('d M Y', strtotime($booking->check_out_date)); ?></span>
                </div>
                <div class="total-row">
                    <span>Nights:</span>
                    <span><?= $nights; ?></span>
                </div>
                <div class="total-row">
                    <span>Price per night:</span>
                    <span>Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></span>
                </div>
                <div class="total-row grand-total">
                    <span>Total:</span>
                    <span>Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <p style="color: var(--medium-text);">Booking ID: <?= $booking->booking_id; ?></p>
                <p style="color: var(--medium-text);">Status: <span class="status-<?= $booking->status; ?>"><?= ucfirst($booking->status); ?></span></p>
            </div>
            
            <div class="text-center mt-3">
                <a href="<?= APP_URL; ?>/profile/bookings" class="btn btn-secondary">View All Bookings</a>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>