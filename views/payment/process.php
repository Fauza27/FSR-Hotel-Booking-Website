<?php
$pageTitle = 'Payment Process';
$currentPage = 'profile';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-4 mb-4">
    <div class="room-card" style="max-width:600px;margin:0 auto;">
        <div class="room-info">
            <h2 class="mb-3">Payment for Booking #<?= $booking->booking_id; ?></h2>
            <p><strong>Room:</strong> <?= $room->room_number; ?> (<?= $room->category_name; ?>)</p>
            <p><strong>Check-in:</strong> <?= date('d M Y', strtotime($booking->check_in_date)); ?></p>
            <p><strong>Check-out:</strong> <?= date('d M Y', strtotime($booking->check_out_date)); ?></p>
            <p><strong>Total Amount:</strong> <span style="color:var(--accent-color);font-weight:600;">Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span></p>
            <hr>
            <form action="<?= APP_URL; ?>/payment/process/<?= $booking->booking_id; ?>" method="POST">
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="">-- Select Payment Method --</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Pay at Hotel (Cash)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3">Pay Now</button>
            </form>
        </div>
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>
