<?php
$pageTitle = 'Booking Details';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-4 mb-4">
    <div class="room-detail">
        <div class="room-detail-main">
            <h2 class="mb-3">Booking Details</h2>
            <div class="booking-details-section mb-4">
                <h3 class="feature-title">Booking Information</h3>
                <div class="room-card">
                    <div class="booking-details">
                        <div class="booking-room-img">
                            <img src="<?= !empty($room->image_url) ? APP_URL . '/assets/images/rooms/' . $room->image_url : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="<?= $room->room_number; ?>">
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
                                <strong>Status:</strong>
                                <span class="status-<?= $booking->status; ?>"><?= ucfirst($booking->status); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="booking-card-footer">
                        <div class="booking-price">
                            Total: Rp <?= number_format($booking->total_price, 0, ',', '.'); ?>
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
