<?php
$pageTitle = 'Payment Success';
$currentPage = 'profile';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-5 mb-5 text-center">
    <div style="font-size:5rem;color:var(--success-color);margin-bottom:1rem;">
        <i class="fas fa-check-circle"></i>
    </div>
    <h1 class="mb-2">Payment Successful!</h1>
    <p class="mb-3" style="color:var(--medium-text);font-size:1.2rem;">
        Thank you for your payment. Your booking has been confirmed.<br>
        <strong>Booking ID:</strong> <?= $booking->booking_id; ?><br>
        <strong>Room:</strong> <?= $room->room_number; ?> (<?= $room->category_name; ?>)<br>
        <strong>Check-in:</strong> <?= date('d M Y', strtotime($booking->check_in_date)); ?>,
        <strong>Check-out:</strong> <?= date('d M Y', strtotime($booking->check_out_date)); ?>
    </p>
    <a href="<?= APP_URL; ?>/profile/bookings" class="btn btn-primary">View My Bookings</a>
    <a href="<?= APP_URL; ?>" class="btn btn-secondary ml-2">Back to Home</a>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>
