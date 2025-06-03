<?php
// AdminBookingController
class AdminBookingController {
    private $bookingModel;
    private $roomModel;
    public function __construct() {
        require_once __DIR__ . '/../models/Booking.php';
        require_once __DIR__ . '/../models/Room.php';
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
        $this->checkAdminAuth();
    }

    // Tampilkan daftar booking dengan filter status dan tanggal
    public function index() {
        // Mengambil parameter status dan tanggal dari query string
        $status = $_GET['status'] ?? '';
        $dateStart = $_GET['date_start'] ?? '';
        $dateEnd = $_GET['date_end'] ?? '';
        
        // Menambahkan filter berdasarkan tanggal
        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }
        if ($dateStart) {
            $filters['date_start'] = $dateStart;
        }
        if ($dateEnd) {
            $filters['date_end'] = $dateEnd;
        }
        
        // Mengambil data booking dengan filter
        $bookings = $this->bookingModel->getAllBookingsFiltered($filters);
        require __DIR__ . '/../views/admin/bookings/index.php';
    }

    // Tampilkan detail booking lengkap
    public function view($id = null) {
    if ($id === null) {
        // Pastikan ID ada di query string jika tidak diberikan di URL
        $id = $_GET['id'] ?? null;
    }
    if ($id) {
        $booking = $this->bookingModel->getBookingById($id);
        require __DIR__ . '/../views/admin/bookings/view.php';
    } else {
        // Redirect ke daftar booking jika ID tidak valid
        $_SESSION['error'] = 'ID booking tidak ditemukan';
        header('Location: /admin/bookings');
        exit;
    }
}

    // Update status booking (confirmed/cancelled)
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? '';
            if ($status && in_array($status, ['confirmed', 'cancelled', 'pending'])) {
                $this->bookingModel->updateBookingStatus($id, $status);
                // Jika cancelled, update status kamar ke available
                if ($status === 'cancelled') {
                    $booking = $this->bookingModel->getBookingDetailsById($id);
                    if ($booking && isset($booking['room_id'])) {
                        $this->roomModel->updateRoomStatus($booking['room_id'], 'available');
                    }
                }
                $_SESSION['success'] = 'Status booking berhasil diperbarui';
            } else {
                $_SESSION['error'] = 'Status tidak valid';
            }
        }
        header('Location: /admin/bookings/view?id=' . $id);
        exit;
    }

    // Batalkan booking dan update status kamar
    public function cancel($id) {
        $this->bookingModel->updateBookingStatus($id, 'cancelled');
        $booking = $this->bookingModel->getBookingDetailsById($id);
        if ($booking && isset($booking['room_id'])) {
            $this->roomModel->updateRoomStatus($booking['room_id'], 'available');
        }
        $_SESSION['success'] = 'Booking berhasil dibatalkan';
        header('Location: /admin/bookings');
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
