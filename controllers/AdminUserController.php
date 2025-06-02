<?php
// AdminUserController

class AdminUserController {
    private $userModel;
    private $bookingModel;
    public function __construct() {
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Booking.php';
        $this->userModel = new User();
        $this->bookingModel = new Booking();
        $this->checkAdminAuth();
    }

    // Tampilkan daftar semua user, dengan pencarian nama/email
    public function index() {
        $search = trim($_GET['search'] ?? '');
        if ($search !== '') {
            $users = $this->userModel->searchUsers($search);
        } else {
            $users = $this->userModel->getAllUsers();
        }
        require __DIR__ . '/../views/admin/users/index.php';
    }

    // Tampilkan detail user dan history booking
    public function view($id) {
        $user = $this->userModel->getUserById($id);
        $bookings = $this->bookingModel->getBookingsByUserId($id);
        require __DIR__ . '/../views/admin/users/view.php';
    }

    // Blokir akses user
    public function block($id) {
        if ($this->userModel->updateUserStatus($id, 'blocked')) {
            $_SESSION['success'] = 'User berhasil diblokir';
        } else {
            $_SESSION['error'] = 'Gagal memblokir user';
        }
        header('Location: /admin/users/view?id=' . $id);
        exit;
    }

    // Buka blokir user
    public function unblock($id) {
        if ($this->userModel->updateUserStatus($id, 'active')) {
            $_SESSION['success'] = 'User berhasil diaktifkan';
        } else {
            $_SESSION['error'] = 'Gagal mengaktifkan user';
        }
        header('Location: /admin/users/view?id=' . $id);
        exit;
    }

    // Jadikan user sebagai admin
    public function makeAdmin($id = null) {
        error_log("makeAdmin method called in AdminUserController");
        error_log("ID parameter: " . print_r($id, true));
        error_log("GET parameters: " . print_r($_GET, true));
        
        $this->checkAdminAuth();
        
        // Get ID from query string if not in path
        if (!$id && isset($_GET['id'])) {
            $id = $_GET['id'];
            error_log("Got ID from query string: $id");
        }

        if (!$id || !is_numeric($id)) {
            error_log("Invalid user ID: $id");
            $_SESSION['error'] = 'Invalid user ID';
            header('Location: ' . APP_URL . '/admin/users');
            exit;
        }

        error_log("Checking current role for user $id");
        // Check if user exists and is not already admin
        $currentRole = $this->userModel->getUserRole($id);
        error_log("Current role: " . print_r($currentRole, true));
        
        if (!$currentRole) {
            error_log("User not found: $id");
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: ' . APP_URL . '/admin/users');
            exit;
        }

        if ($currentRole === 'admin') {
            error_log("User is already admin: $id");
            $_SESSION['error'] = 'User sudah menjadi admin';
            header('Location: ' . APP_URL . '/admin/users');
            exit;
        }

        error_log("Attempting to make user $id an admin");
        if ($this->userModel->makeAdmin($id)) {
            error_log("Successfully made user $id an admin");
            $_SESSION['success'] = 'User berhasil dijadikan admin';
        } else {
            error_log("Failed to make user $id an admin");
            $_SESSION['error'] = 'Gagal mengubah role user';
        }

        header('Location: ' . APP_URL . '/admin/users');
        exit;
    }

    // Helper: autentikasi admin
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: <?= APP_URL ?>/auth/login');
            exit;
        }
    }
}
