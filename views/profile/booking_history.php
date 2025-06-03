<?php
// Set up the view by including header
$pageTitle = 'My Booking History';
$currentPage = 'profile';
$activeMenu = 'booking_history'; // Variabel selalu diset dengan nilai yang benar
include_once(VIEW_PATH . 'layouts/header.php');
?>
<div class="container mt-4 mb-4 profile-section">
    <aside class="profile-sidebar">
        <div class="profile-avatar">
            <img src="<?= isset($user->avatar) && $user->avatar ? APP_URL . '/assets/images/' . $user->avatar : APP_URL . '/assets/images/user-placeholder.png'; ?>" alt="Avatar">
        </div>
        <div class="profile-name">
            <h4><?= $user->full_name; ?></h4>
        </div>
        <nav class="profile-menu">
            <a href="<?= APP_URL; ?>/profile" class="<?= $activeMenu == 'profile' ? 'active' : ''; ?>">Profile</a>
            <a href="<?= APP_URL; ?>/profile/bookings" class="<?= $activeMenu == 'booking_history' ? 'active' : ''; ?>">Booking History</a>
            <a href="<?= APP_URL; ?>/profile/edit" class="<?= $activeMenu == 'edit' ? 'active' : ''; ?>">Edit Profile</a>
        </nav>
    </aside>
    <section class="profile-content">
        <h2 class="mb-3">My Booking History</h2>
        <div class="booking-history">
            <?php if (!isset($bookings)): ?>
                <div class="alert alert-danger">Booking data not found.</div>
            <?php elseif (empty($bookings)): ?>
                <div class="alert alert-info">You have no bookings yet.</div>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card">
                        <div class="booking-card-header">
                            <h4>Booking #<?= $booking->booking_id; ?></h4>
                            <div class="booking-status status-<?= $booking->status; ?>"><?= ucfirst($booking->status); ?></div>
                        </div>
                        <div class="booking-details">
                            <div class="booking-room-img" style="max-width:120px;">
                                <img src="<?= !empty($booking->image_url) ? APP_URL . '/assets/images/rooms/' . $booking->image_url : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="<?= $booking->room_number; ?>">
                            </div>
                            <div class="booking-info">
                                <div class="booking-info-item">
                                    <strong>Room:</strong> <span><?= $booking->room_number; ?> (<?= $booking->category_name; ?>)</span>
                                </div>
                                <div class="booking-info-item">
                                    <strong>Check-in:</strong> <span><?= date('d M Y', strtotime($booking->check_in_date)); ?></span>
                                </div>
                                <div class="booking-info-item">
                                    <strong>Check-out:</strong> <span><?= date('d M Y', strtotime($booking->check_out_date)); ?></span>
                                </div>
                                <div class="booking-info-item">
                                    <strong>Total:</strong> <span>Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span>
                                </div>
                                <div class="booking-info-item">
                                    <strong>Status:</strong> <span class="status-<?= $booking->status; ?>"><?= ucfirst($booking->status); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="booking-card-footer">
                            <a href="<?= APP_URL; ?>/booking/details/<?= $booking->booking_id; ?>" class="btn btn-primary btn-sm">View Details</a>
                            <?php if (in_array($booking->status, ['pending','confirmed'])): ?>
                                <a href="<?= APP_URL; ?>/booking/cancel/<?= $booking->booking_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this booking?');">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>