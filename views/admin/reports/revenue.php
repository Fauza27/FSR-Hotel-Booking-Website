<?php
// views/admin/reports/revenue.php
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
    </div>

    <div class="container mt-4">
        <h2 class="mb-4">Laporan Pendapatan</h2>
        <form method="get" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Periode</label>
                <select name="period" class="form-select">
                    <option value="monthly" <?= ($_GET['period'] ?? 'monthly')==='monthly'?'selected':'' ?>>Bulanan</option>
                    <option value="yearly" <?= ($_GET['period'] ?? '')==='yearly'?'selected':'' ?>>Tahunan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Dari</label>
                <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($_GET['start'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label>Sampai</label>
                <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($_GET['end'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Tampilkan</button>
                <a href="/admin/reports/export/excel/revenue?<?= http_build_query($_GET) ?>" class="btn btn-success me-2">Export Excel</a>
                <a href="/admin/reports/export/pdf/revenue?<?= http_build_query($_GET) ?>" class="btn btn-danger">Export PDF</a>
            </div>
        </form>
        <div class="mb-4">
            <h5>Grafik Pendapatan</h5>
            <canvas id="revenueChart" height="80"></canvas>
        </div>
        <div class="mb-4">
            <h5>Breakdown Pendapatan per Kategori Kamar</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['breakdown'])): ?>
                            <?php $no=1; foreach ($data['breakdown'] as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td>Rp <?= number_format($row['revenue'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center">Tidak ada data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mb-4">
            <h5>Perbandingan Periode</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="card text-bg-success mb-3">
                        <div class="card-body">
                            <div class="fw-bold">Periode Ini</div>
                            <div class="fs-4">Rp <?= number_format($data['current_period'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-bg-secondary mb-3">
                        <div class="card-body">
                            <div class="fw-bold">Periode Sebelumnya</div>
                            <div class="fs-4">Rp <?= number_format($data['previous_period'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = {
    labels: <?= json_encode(array_map(fn($r) => $r['label'] ?? $r['month'] ?? $r['year'], $data['chart'] ?? [])) ?>,
    datasets: [{
        label: 'Pendapatan',
        data: <?= json_encode(array_map(fn($r) => $r['revenue'] ?? 0, $data['chart'] ?? [])) ?>,
        backgroundColor: 'rgba(40, 167, 69, 0.5)',
        borderColor: 'rgba(40, 167, 69, 1)',
        borderWidth: 2
    }]
};
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
