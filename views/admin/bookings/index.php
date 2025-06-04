<?php include_once(VIEW_PATH . 'admin/layouts/header.php'); ?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2>Daftar Booking</h2>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="GET" action="<?= base_url('admin/bookings'); ?>">
            <div class="row">
                <div class="col-md-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?= (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_start">Tanggal Mulai Check-in</label> <!-- Perjelas label -->
                    <input type="date" name="date_start" id="date_start" class="form-control" value="<?= $_GET['date_start'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_end">Tanggal Selesai Check-out</label> <!-- Perjelas label -->
                    <input type="date" name="date_end" id="date_end" class="form-control" value="<?= $_GET['date_end'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary" style="margin-top: 30px;">Filter</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Kamar</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><?= $b->booking_id; ?></td>
                            <td><?= htmlspecialchars($b->user_name ?? ('User ID: ' . $b->user_id)); ?></td> <!-- Handle jika user_name null -->
                            <td><?= htmlspecialchars($b->room_number); ?></td>
                            <td><?= date('d M Y', strtotime($b->check_in_date)); ?></td>
                            <td><?= date('d M Y', strtotime($b->check_out_date)); ?></td>
                            <td>Rp <?= number_format($b->total_price, 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?= htmlspecialchars($b->status); ?>"><?= ucfirst(htmlspecialchars($b->status)); ?></span></td>
                            <td>
                                <a href="<?= base_url('admin/bookings/view/' . $b->booking_id); ?>" class="btn btn-info btn-sm">Detail</a>
                                <?php if ($b->status === 'pending'): ?>
                                    <a href="<?= base_url('admin/bookings/update-status/' . $b->booking_id . '?status=confirmed'); ?>" class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi booking ini?')">Konfirmasi</a>
                                    <a href="<?= base_url('admin/bookings/update-status/' . $b->booking_id . '?status=cancelled'); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan booking ini?')">Batalkan</a>
                                <?php elseif ($b->status === 'confirmed'): ?>
                                    <a href="<?= base_url('admin/bookings/update-status/' . $b->booking_id . '?status=completed'); ?>" class="btn btn-success btn-sm" onclick="return confirm('Tandai sebagai selesai?')">Selesai</a> 
                                    <a href="<?= base_url('admin/bookings/update-status/' . $b->booking_id . '?status=cancelled'); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan booking ini?')">Batalkan</a>
                                <?php elseif ($b->status === 'cancelled'): ?>
                                    <span class="text-muted">- Dibatalkan -</span>
                                <?php elseif ($b->status === 'completed'): ?>
                                    <span class="text-success">✓ Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">Tidak ada data booking yang sesuai dengan filter.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
