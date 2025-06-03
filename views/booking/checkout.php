<?php
$pageTitle = 'Booking Checkout';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-4 mb-4">
    <div class="room-detail">
        <div class="room-detail-main">
            <h2 class="mb-3">Booking Checkout</h2>
            
            <div class="booking-details-section mb-4">
                <h3 class="feature-title">Booking Details</h3>
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
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="payment-form-section">
                <h3 class="feature-title">Payment Information</h3>
                <div class="room-card">
                    <div class="payment-methods mb-3">
                        <h4 class="mb-2">Select Payment Method</h4>
                        <div class="form-group">
                            <label style="display: flex; align-items: center; cursor: pointer; margin-bottom: 1rem;">
                                <input type="radio" name="payment_method" value="credit_card" style="margin-right: 0.7rem;" checked>
                                <i class="fas fa-credit-card" style="margin-right: 0.5rem; color: var(--accent-color);"></i>
                                <span>Credit / Debit Card</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; cursor: pointer; margin-bottom: 1rem;">
                                <input type="radio" name="payment_method" value="bank_transfer" style="margin-right: 0.7rem;">
                                <i class="fas fa-university" style="margin-right: 0.5rem; color: var(--accent-color);"></i>
                                <span>Bank Transfer</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="payment_method" value="cash" style="margin-right: 0.7rem;">
                                <i class="fas fa-money-bill-wave" style="margin-right: 0.5rem; color: var(--accent-color);"></i>
                                <span>Pay at Hotel (Cash)</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Credit Card Form (shown by default) -->
                    <div id="credit-card-form">
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" class="form-control" placeholder="XXXX XXXX XXXX XXXX" maxlength="19">
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="card_holder">Card Holder Name</label>
                                <input type="text" id="card_holder" name="card_holder" class="form-control" placeholder="Name on card">
                            </div>
                            
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY" maxlength="5">
                            </div>
                            
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" class="form-control" placeholder="XXX" maxlength="4">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bank Transfer Form (hidden by default) -->
                    <div id="bank-transfer-form" style="display: none;">
                        <div class="alert alert-info">
                            <p><strong>Bank Account Details:</strong></p>
                            <p>Bank Name: Example Bank</p>
                            <p>Account Name: PurpleStay Hotel</p>
                            <p>Account Number: 1234567890</p>
                            <p>Reference: BOOK-<?= $booking->booking_id; ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="transfer_proof">Upload Transfer Proof</label>
                            <input type="file" id="transfer_proof" name="transfer_proof" class="form-control" accept="image/*,.pdf">
                            <small style="color: var(--medium-text);">Accepted formats: JPG, PNG, PDF. Max size: 2MB</small>
                        </div>
                    </div>
                    
                    <!-- Cash Payment Form (hidden by default) -->
                    <div id="cash-form" style="display: none;">
                        <div class="alert alert-info">
                            <p>You've selected to pay at the hotel. Your booking will be held for 24 hours.</p>
                            <p>Please note that payment will be required upon check-in. Failure to arrive or cancel within 24 hours will result in booking cancellation.</p>
                        </div>
                    </div>
                    
                    <div class="total-info">
                        <div class="total-row">
                            <span>Room Rate:</span>
                            <span>Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?> x <?= $nights; ?> night<?= $nights > 1 ? 's' : ''; ?></span>
                        </div>
                        <?php if(isset($taxes) && $taxes > 0): ?>
                        <div class="total-row">
                            <span>Taxes & Fees:</span>
                            <span>Rp <?= number_format($taxes, 0, ',', '.'); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="total-row grand-total">
                            <span>Total Amount:</span>
                            <span>Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" id="complete-payment-btn" class="btn btn-primary btn-block">Complete Payment</button>
                        <a href="<?= APP_URL; ?>/booking/cancel/<?= $booking->booking_id; ?>" class="btn btn-secondary btn-block mt-2" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel Booking</a>
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
                    <span>Guests:</span>
                    <span><?= $booking->adults + $booking->children; ?></span>
                </div>
                <div class="total-row">
                    <span>Price per night:</span>
                    <span>Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></span>
                </div>
                <?php if(isset($taxes) && $taxes > 0): ?>
                <div class="total-row">
                    <span>Taxes & Fees:</span>
                    <span>Rp <?= number_format($taxes, 0, ',', '.'); ?></span>
                </div>
                <?php endif; ?>
                <div class="total-row grand-total">
                    <span>Total:</span>
                    <span>Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <p style="color: var(--medium-text);">Booking ID: <?= $booking->booking_id; ?></p>
                <p style="color: var(--medium-text);">Status: <span class="status-<?= $booking->status; ?>"><?= ucfirst($booking->status); ?></span></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle payment forms based on selected payment method
        const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
        const creditCardForm = document.getElementById('credit-card-form');
        const bankTransferForm = document.getElementById('bank-transfer-form');
        const cashForm = document.getElementById('cash-form');
        
        paymentMethodInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Hide all forms first
                creditCardForm.style.display = 'none';
                bankTransferForm.style.display = 'none';
                cashForm.style.display = 'none';
                
                // Show the selected form
                if (this.value === 'credit_card') {
                    creditCardForm.style.display = 'block';
                } else if (this.value === 'bank_transfer') {
                    bankTransferForm.style.display = 'block';
                } else if (this.value === 'cash') {
                    cashForm.style.display = 'block';
                }
            });
        });
        
        // Format credit card number with spaces
        const cardNumberInput = document.getElementById('card_number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                // Remove all non-digit characters
                let value = this.value.replace(/\D/g, '');
                
                // Add spaces every 4 digits
                if (value.length > 0) {
                    value = value.match(/.{1,4}/g).join(' ');
                }
                
                // Update the input value
                this.value = value;
            });
        }
        
        // Format expiry date with slash
        const expiryDateInput = document.getElementById('expiry_date');
        if (expiryDateInput) {
            expiryDateInput.addEventListener('input', function(e) {
                // Remove all non-digit characters
                let value = this.value.replace(/\D/g, '');
                
                // Format as MM/YY
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                
                // Update the input value
                this.value = value;
            });
        }
        
        // Complete payment button
        const completePaymentBtn = document.getElementById('complete-payment-btn');
        if (completePaymentBtn) {
            completePaymentBtn.addEventListener('click', function() {
                // Get selected payment method
                const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
                
                // Validate based on payment method
                if (selectedPaymentMethod === 'credit_card') {
                    const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
                    const cardHolder = document.getElementById('card_holder').value;
                    const expiryDate = document.getElementById('expiry_date').value;
                    const cvv = document.getElementById('cvv').value;
                    
                    if (!cardNumber || cardNumber.length < 16) {
                        alert('Please enter a valid card number');
                        return;
                    }
                    
                    if (!cardHolder) {
                        alert('Please enter the card holder name');
                        return;
                    }
                    
                    if (!expiryDate || expiryDate.length < 5) {
                        alert('Please enter a valid expiry date');
                        return;
                    }
                    
                    if (!cvv || cvv.length < 3) {
                        alert('Please enter a valid CVV');
                        return;
                    }
                } else if (selectedPaymentMethod === 'bank_transfer') {
                    const transferProof = document.getElementById('transfer_proof').files;
                    
                    if (transferProof.length === 0) {
                        alert('Please upload transfer proof');
                        return;
                    }
                }
                
                // Simulate payment processing
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                // Redirect to success page after 2 seconds (simulating processing)
                setTimeout(() => {
                    window.location.href = '<?= APP_URL; ?>/payment/success/<?= $booking->booking_id; ?>';
                }, 2000);
            });
        }
    });
</script>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>