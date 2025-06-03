<?php
// views/admin/users/index.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2 class="mb-3">Daftar User</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form method="get" class="admin-actions" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <select name="role" class="form-control">
                <option value="">-- Semua Role --</option>
                <option value="admin" <?= (($_GET['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= (($_GET['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Total Booking</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php $no = 1; foreach ($users as $user): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($user->full_name) ?></td>
                            <td><?= htmlspecialchars($user->email) ?></td>
                            <td><?= htmlspecialchars($user->phone) ?></td>
                            <td>
                                <?php
                                // Hitung total booking user
                                if (isset($user->total_booking)) {
                                    echo $user->total_booking;
                                } else {
                                    // Jika tidak ada properti, hitung manual jika $user->user_id tersedia
                                    if (isset($user->user_id) && isset($this->bookingModel)) {
                                        $total = count($this->bookingModel->getBookingsByUserId($user->user_id));
                                        echo $total;
                                    } else {
                                        echo '-';
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($user->status)): ?>
                                    <?php if ($user->status === 'active'): ?>
                                        <span class="badge badge-confirmed">Aktif</span>
                                    <?php elseif ($user->status === 'blocked'): ?>
                                        <span class="badge badge-cancelled">Blocked</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-pending">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user->role === 'admin'): ?>
                                    <span class="badge bg-info">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= APP_URL ?>/admin/users/view/<?= $user->user_id ?>" class="btn btn-info btn-sm">Detail</a>
                                <?php if ($user->role !== 'admin'): ?>
                                    <a href="<?= APP_URL ?>/admin/users/make-admin/<?= htmlspecialchars($user->user_id) ?>" 
                                       class="btn btn-secondary btn-sm"
                                       onclick="return confirm('Yakin jadikan user ini sebagai admin?')">
                                        Jadikan Admin
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">Tidak ada user ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
