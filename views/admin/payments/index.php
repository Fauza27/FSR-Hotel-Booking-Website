<?php
// views/admin/payments/index.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2 class="mb-3">Daftar Pembayaran</h2>
        <form method="get" class="admin-actions" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
            <input type="text" name="method" class="form-control" placeholder="Metode pembayaran..." value="<?= htmlspecialchars($_GET['method'] ?? '') ?>">
            <select name="status" class="form-control">
                <option value="">-- Semua Status --</option>
                <option value="pending" <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="completed" <?= (($_GET['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Completed</option>
                <option value="failed" <?= (($_GET['status'] ?? '') === 'failed') ? 'selected' : '' ?>>Failed</option>
                <option value="refunded" <?= (($_GET['status'] ?? '') === 'refunded') ? 'selected' : '' ?>>Refunded</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Booking</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payments)): ?>
                    <?php $no=1; foreach ($payments as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p->transaction_id) ?></td>
                            <td>
                                <a href="/admin/bookings/view/<?= $p->booking_id ?>">#<?= $p->booking_id ?></a>
                            </td>
                            <td><?= htmlspecialchars($p->payment_method) ?></td>
                            <td>Rp <?= number_format($p->amount, 0, ',', '.') ?></td>
                            <td>
                                <?php
                                $status = $p->payment_status;
                                $badge = 'secondary';
                                if ($status === 'completed') $badge = 'confirmed';
                                elseif ($status === 'pending') $badge = 'pending';
                                elseif ($status === 'failed') $badge = 'cancelled';
                                elseif ($status === 'refunded') $badge = 'completed';
                                ?>
                                <span class="badge badge-<?= $badge ?>"><?= ucfirst($status) ?></span>
                            </td>
                            <td><?= date('d M Y H:i', strtotime($p->payment_date)) ?></td>
                            <td>
                                <a href="<?php echo APP_URL; ?>/admin/payments/view/<?= $p->payment_id ?>" class="btn btn-info btn-sm">Detail</a>
                                <a href="<?php echo APP_URL; ?>/admin/payments/updatestatus/<?= $p->payment_id ?>" class="btn btn-warning btn-sm">Update Status</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">Tidak ada pembayaran ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
