<?php
// Set up the view by including header
$pageTitle = 'Edit Profile';
$currentPage = 'profile';
$activeMenu = 'edit'; // Variabel selalu diset dengan nilai yang benar
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
        <h2 class="mb-3">Edit Profile</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"> <?= $error ?> </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"> <?= $success ?> </div>
        <?php endif; ?>
        <form action="<?= APP_URL; ?>/profile/edit" method="POST" enctype="multipart/form-data" class="auth-form" style="max-width:500px;">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" value="<?= htmlspecialchars($data['full_name'] ?? $user->full_name); ?>" required>
                <?php if (isset($data['errors']['full_name'])): ?>
                    <small class="text-danger"><?= $data['errors']['full_name']; ?></small>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($data['phone'] ?? $user->phone); ?>">
                <?php if (isset($data['errors']['phone'])): ?>
                    <small class="text-danger"><?= $data['errors']['phone']; ?></small>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="form-control" rows="2"><?= htmlspecialchars($data['address'] ?? $user->address); ?></textarea>
            </div>
            <div class="form-group">
                <label for="avatar">Avatar (optional)</label>
                <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                <small style="color: var(--medium-text);">Accepted: JPG, PNG. Max: 2MB</small>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
        </form>
    </section>
</div>
<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>