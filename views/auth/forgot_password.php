<?php
$pageTitle = 'Forgot Password';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-4 mb-4">
    <div class="auth-container">
        <div class="auth-form">
            <div class="auth-title">
                <h2>Reset Your Password</h2>
                <p>Enter your email address to receive a password reset link</p>
            </div>
            
            <form action="<?= APP_URL ?>/forgot-password" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" value="<?= isset($data['email']) ? $data['email'] : ''; ?>" required>
                    <?php if(isset($data['email_err']) && !empty($data['email_err'])): ?>
                        <small style="color: var(--error-color);"><?= $data['email_err']; ?></small>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
            </form>
            
            <div class="form-switch">
                <p>Remember your password? <a href="<?= APP_URL ?>/login">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>