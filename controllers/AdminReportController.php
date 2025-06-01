<?php
// AdminReportController

class AdminReportController {
    private $bookingModel;
    private $roomModel;
    private $paymentModel;

    public function __construct() {
        $this->checkAdminAuth();
        require_once __DIR__ . '/../models/Booking.php';
        require_once __DIR__ . '/../models/Room.php';
        require_once __DIR__ . '/../models/Payment.php';
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
        $this->paymentModel = new Payment();
    }

    // Dashboard laporan utama
    public function index() {
        $summary = [
            'totalBookings' => $this->bookingModel->getTotalBookings(),
            'totalRevenue' => $this->paymentModel->getTotalRevenue(),
            'roomOccupancy' => $this->roomModel->getRoomOccupancyStats(),
        ];
        require __DIR__ . '/../views/admin/reports/index.php';
    }

    // Laporan booking (harian/bulanan/tahunan)
    public function bookings() {
        $period = $_GET['period'] ?? 'daily';
        $data = $this->bookingModel->getBookingReport($period);
        require __DIR__ . '/../views/admin/reports/bookings.php';
    }

    // Laporan pendapatan dengan grafik
    public function revenue() {
        $period = $_GET['period'] ?? 'monthly';
        $data = $this->paymentModel->getRevenueReport($period);
        require __DIR__ . '/../views/admin/reports/revenue.php';
    }

    // Laporan okupansi kamar
    public function rooms() {
        $data = $this->roomModel->getRoomOccupancyReport();
        require __DIR__ . '/../views/admin/reports/rooms.php';
    }

    // Export laporan ke PDF/Excel
    public function export($type = 'pdf', $report = 'bookings') {
        // Contoh sederhana, implementasi export sesuaikan kebutuhan
        if ($report === 'bookings') {
            $data = $this->bookingModel->getBookingReport('all');
        } elseif ($report === 'revenue') {
            $data = $this->paymentModel->getRevenueReport('all');
        } elseif ($report === 'rooms') {
            $data = $this->roomModel->getRoomOccupancyReport();
        } else {
            $data = [];
        }
        if ($type === 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="report_' . $report . '.xls"');
            // Output data sebagai tabel HTML
            echo '<table><tr><td>Export Excel Placeholder</td></tr></table>';
        } else {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="report_' . $report . '.pdf"');
            // Output data sebagai PDF (placeholder)
            echo 'Export PDF Placeholder';
        }
        exit;
    }

    // Helper: autentikasi admin
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }
}
