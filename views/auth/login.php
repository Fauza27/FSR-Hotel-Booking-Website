<?php
$pageTitle = 'Login';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-4 mb-4">
    <div class="auth-container">
        <div class="auth-form">
            <div class="auth-title">
                <h2>Login to Your Account</h2>
                <p>Enter your credentials to access your account</p>
            </div>
            
            <form action="<?= APP_URL ?>/login" method="POST">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username or email" value="<?= isset($data['username']) ? $data['username'] : ''; ?>" required>
                    <?php if(isset($data['username_err']) && !empty($data['username_err'])): ?>
                        <small style="color: var(--error-color);"><?= $data['username_err']; ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        <span class="password-toggle" data-target="password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <?php if(isset($data['password_err']) && !empty($data['password_err'])): ?>
                        <small style="color: var(--error-color);"><?= $data['password_err']; ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="remember" value="1" style="margin-right: 0.5rem;">
                            <span>Remember me</span>
                        </label>
                    </div>
                    <a href="<?= APP_URL ?>/forgot-password">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div class="form-switch">
                <p>Don't have an account? <a href="<?= APP_URL ?>/register">Register here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>