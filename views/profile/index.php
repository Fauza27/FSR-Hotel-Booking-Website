<?php
// Set up the view by including header
$pageTitle = 'My Profile';
$currentPage = 'profile';
$activeMenu = 'profile'; // Variabel selalu diset dengan nilai tetap
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
        <h2 class="mb-3">Welcome, <?= $user->full_name; ?>!</h2>
        <div class="room-card">
            <div class="room-info">
                <p><strong>Email:</strong> <?= $user->email; ?></p>
                <p><strong>Phone:</strong> <?= $user->phone; ?></p>
                <p><strong>Address:</strong> <?= $user->address; ?></p>
            </div>
        </div>
    </section>
</div>
<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>