<?php
// AdminPaymentController
class AdminPaymentController {
    private $paymentModel;
    private $bookingModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Payment.php';
        require_once __DIR__ . '/../models/Booking.php';
        $this->paymentModel = new Payment();
        $this->bookingModel = new Booking();
        $this->checkAdminAuth();
    }

    // Tampilkan daftar pembayaran dengan filter metode/status
    public function index() {
        $method = $_GET['method'] ?? '';
        $status = $_GET['status'] ?? '';
        $payments = $this->paymentModel->getAllPaymentsFiltered($method, $status);
        require __DIR__ . '/../views/admin/payments/index.php';
    }

    public function view($id) {
        if (!$id) {
            $_SESSION['error'] = 'ID pembayaran tidak ditemukan';
            header('Location: ' . APP_URL . '/admin/payments');
            exit;
        }

        // Mengambil detail pembayaran berdasarkan ID
        if ($id === null) {
            // Pastikan ID ada di query string jika tidak diberikan di URL
            $id = $_GET['id'] ?? null;
        }

        $payment = $this->paymentModel->getPaymentById($id);
        require __DIR__ . '/../views/admin/payments/view.php';
    }

    // Tampilkan form update status pembayaran
    public function updatestatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Tampilkan form untuk update status
            if (!$id) {
                $_SESSION['error'] = 'ID pembayaran tidak ditemukan';
                header('Location: ' . APP_URL . '/admin/payments');
                exit;
            }
            
            $payment = $this->paymentModel->getPaymentById($id);
            if (!$payment) {
                $_SESSION['error'] = 'Pembayaran tidak ditemukan';
                header('Location: ' . APP_URL . '/admin/payments');
                exit;
            }
            
            require __DIR__ . '/../views/admin/payments/updatestatus.php';
            
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Proses update status
            $status = $_POST['status'] ?? '';
            
            if (!$id) {
                $_SESSION['error'] = 'ID pembayaran tidak ditemukan';
                header('Location: ' . APP_URL . '/admin/payments');
                exit;
            }
            
            if ($status && in_array($status, ['pending', 'completed', 'failed', 'refunded'])) {
                $result = $this->paymentModel->updatePaymentStatus($id, $status);
                
                if ($result) {
                    // Jika status yang baru adalah completed, maka status booking terkait juga diperbarui menjadi confirmed
                    if ($status === 'completed') {
                        $payment = $this->paymentModel->getPaymentById($id);
                        if ($payment && isset($payment->booking_id)) {
                            $this->bookingModel->updateBookingStatus($payment->booking_id, 'confirmed');
                        }
                    }
                    $_SESSION['success'] = 'Status pembayaran berhasil diperbarui';
                } else {
                    $_SESSION['error'] = 'Gagal memperbarui status pembayaran';
                }
            } else {
                $_SESSION['error'] = 'Status tidak valid';
            }
            
            header('Location: ' . APP_URL . '/admin/payments');
            exit;
        }
    }

    // Konfirmasi pembayaran manual
    public function confirmManual($id) {
        $this->paymentModel->updatePaymentStatus($id, 'completed');
        $payment = $this->paymentModel->getPaymentById($id);
        if ($payment && isset($payment->booking_id)) {
            $this->bookingModel->updateBookingStatus($payment->booking_id, 'confirmed');
        }
        $_SESSION['success'] = 'Pembayaran berhasil dikonfirmasi secara manual';
        header('Location: ' . APP_URL . '/admin/payments'); 
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