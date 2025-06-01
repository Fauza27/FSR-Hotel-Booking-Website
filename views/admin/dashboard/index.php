<?php include_once(VIEW_PATH . 'admin/layouts/header.php'); ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    
    <div class="admin-content">
        <div class="dashboard-header">
            <h1>Dashboard Admin</h1>
            <p>Selamat datang, <?php echo $_SESSION['admin_username']; ?>!</p>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalRooms; ?></h3>
                    <p>Total Kamar</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalBookings; ?></h3>
                    <p>Total Booking</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total User</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pendingBookings; ?></h3>
                    <p>Booking Pending</p>
                </div>
            </div>
        </div>
        
        <!-- Charts and Tables -->
        <div class="dashboard-content">
            <div class="dashboard-row">
                <!-- Room Availability Chart -->
                <div class="dashboard-card">
                    <h3>Status Ketersediaan Kamar</h3>
                    <div class="chart-container">
                        <canvas id="roomAvailabilityChart"></canvas>
                    </div>
                </div>
                
                <!-- Monthly Revenue Chart -->
                <div class="dashboard-card">
                    <h3>Pendapatan Bulanan</h3>
                    <div class="chart-container">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Recent Bookings Table -->
            <div class="dashboard-card">
                <h3>Booking Terbaru</h3>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tamu</th>
                                <th>Kamar</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentBookings)): ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td>#<?php echo $booking->booking_id; ?></td>
                                        <td><?php echo htmlspecialchars($booking->full_name); ?></td>
                                        <td><?php echo htmlspecialchars($booking->room_number); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($booking->check_in_date)); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($booking->check_out_date)); ?></td>
                                        <td>Rp <?php echo number_format($booking->total_price, 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $booking->status; ?>">
                                                <?php echo ucfirst($booking->status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/admin/booking/details/<?php echo $booking->booking_id; ?>" 
                                               class="btn btn-sm btn-info">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada booking terbaru</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="/admin/bookings" class="btn btn-primary">Lihat Semua Booking</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Room Availability Chart
const roomCtx = document.getElementById('roomAvailabilityChart').getContext('2d');
const roomChart = new Chart(roomCtx, {
    type: 'doughnut',
    data: {
        labels: ['Tersedia', 'Terisi', 'Maintenance'],
        datasets: [{
            data: [
                <?php echo $roomAvailability['available']; ?>,
                <?php echo $roomAvailability['occupied']; ?>,
                <?php echo $roomAvailability['maintenance']; ?>
            ],
            backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Monthly Revenue Chart
const revenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($monthlyRevenue, 'month')); ?>,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: <?php echo json_encode(array_column($monthlyRevenue, 'revenue')); ?>,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>

<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>