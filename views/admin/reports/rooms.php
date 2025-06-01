<?php
// views/admin/reports/rooms.php
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
    </div>

    <div class="container mt-4">
        <h2 class="mb-4">Laporan Kamar</h2>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-info mb-3">
                    <div class="card-body">
                        <div class="fs-4 fw-bold"><?= $data['occupancy_rate'] ?? '0%' ?></div>
                        <div>Tingkat Okupansi Rata-rata</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success mb-3">
                    <div class="card-body">
                        <div class="fs-4 fw-bold"><?= $data['most_popular_room']['room_number'] ?? '-' ?></div>
                        <div>Kamar Paling Populer</div>
                        <div class="text-muted">(<?= $data['most_popular_room']['total_bookings'] ?? 0 ?> booking)</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-primary mb-3">
                    <div class="card-body">
                        <div class="fs-4 fw-bold"><?= $data['best_performance']['room_number'] ?? '-' ?></div>
                        <div>Performa Terbaik</div>
                        <div class="text-muted">Pendapatan: Rp <?= number_format($data['best_performance']['revenue'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <h5>Analisis Okupansi Kamar</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No Kamar</th>
                            <th>Kategori</th>
                            <th>Okupansi (%)</th>
                            <th>Total Booking</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['rooms'])): ?>
                            <?php $no=1; foreach ($data['rooms'] as $room): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($room['room_number']) ?></td>
                                    <td><?= htmlspecialchars($room['category_name']) ?></td>
                                    <td><?= $room['occupancy_rate'] ?>%</td>
                                    <td><?= $room['total_bookings'] ?></td>
                                    <td>Rp <?= number_format($room['revenue'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
