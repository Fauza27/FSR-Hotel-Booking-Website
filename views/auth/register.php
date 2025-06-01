<?php
$pageTitle = 'Register';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-4 mb-4">
    <div class="auth-container">
        <div class="auth-form" style="max-width: 600px;">
            <div class="auth-title">
                <h2>Create an Account</h2>
                <p>Fill in the form below to register</p>
            </div>
            
            <form action="<?= APP_URL ?>/register" method="POST" id="register-form">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" value="<?= isset($data['username']) ? $data['username'] : ''; ?>" required>
                        <?php if(isset($data['username_err']) && !empty($data['username_err'])): ?>
                            <small style="color: var(--error-color);"><?= $data['username_err']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="<?= isset($data['email']) ? $data['email'] : ''; ?>" required>
                        <?php if(isset($data['email_err']) && !empty($data['email_err'])): ?>
                            <small style="color: var(--error-color);"><?= $data['email_err']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Enter your full name" value="<?= isset($data['full_name']) ? $data['full_name'] : ''; ?>" required>
                    <?php if(isset($data['full_name_err']) && !empty($data['full_name_err'])): ?>
                        <small style="color: var(--error-color);"><?= $data['full_name_err']; ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" value="<?= isset($data['phone']) ? $data['phone'] : ''; ?>" required>
                    <?php if(isset($data['phone_err']) && !empty($data['phone_err'])): ?>
                        <small style="color: var(--error-color);"><?= $data['phone_err']; ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" placeholder="Enter your address" rows="3"><?= isset($data['address']) ? $data['address'] : ''; ?></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Choose a password" required>
                            <span class="password-toggle" data-target="password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <?php if(isset($data['password_err']) && !empty($data['password_err'])): ?>
                            <small style="color: var(--error-color);"><?= $data['password_err']; ?></small>
                        <?php endif; ?>
                        <small style="color: var(--medium-text);">Password must be at least 6 characters long</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div style="position: relative;">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                            <span class="password-toggle" data-target="confirm_password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <?php if(isset($data['confirm_password_err']) && !empty($data['confirm_password_err'])): ?>
                            <small style="color: var(--error-color);"><?= $data['confirm_password_err']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: flex-start; cursor: pointer;">
                        <input type="checkbox" name="terms" required style="margin-right: 0.5rem; margin-top: 0.3rem;">
                        <span>I agree to the <a href="<?= APP_URL ?>/terms" target="_blank">Terms and Conditions</a> and <a href="<?= APP_URL ?>/privacy" target="_blank">Privacy Policy</a></span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            
            <div class="form-switch">
                <p>Already have an account? <a href="<?= APP_URL ?>/login">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>