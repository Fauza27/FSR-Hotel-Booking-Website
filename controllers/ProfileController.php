<?php

/*
Fungsi: Mengelola alur kerja yang berkaitan dengan profil pengguna.

Tugas Spesifik:
    index():
        Memastikan pengguna login.
        Memanggil User model untuk mendapatkan detail pengguna.
        Memanggil Booking model (diasumsikan ada) dan Review model (diasumsikan ada) untuk mendapatkan jumlah booking dan review.
        Memuat view profile/index.php dan mengirimkan data pengguna, jumlah booking, dan review ke view tersebut.
    edit():
        Menampilkan halaman edit profil dan memproses perubahannya.
        Memvalidasi input.
        Memanggil User model untuk memperbarui data profil dan/atau password.
    bookings(): 
        Menampilkan riwayat booking pengguna.
    changePassword(): 
        Menampilkan halaman ganti password dan memproses perubahannya.

Ketergantungan: Membuat instance dari User model, Booking model, dan Review model.
*/

class ProfileController {
    private $userModel;
    private $bookingModel;
    private $reviewModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->bookingModel = new Booking();
        $this->reviewModel = new Review();
    }
    
    // Profile index page
    public function index() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = APP_URL . '/profile';
            $_SESSION['flash_message'] = 'Please login to view your profile';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Get user details
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // If user not found, redirect to logout
        if(!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/logout');
            exit;
        }
        
        // Get user's bookings count
        $bookings = $this->bookingModel->getUserBookings($_SESSION['user_id']);
        $bookingsCount = count($bookings);
        
        // Get user's reviews count
        $reviewsCount = $this->reviewModel->getUserReviewsCount($_SESSION['user_id']);
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'My Profile';
        $currentPage = 'profile';
        $activeMenu = 'profile';
        
        // Load view
        require_once(VIEW_PATH . 'profile/index.php');
    }
    
    // Edit profile page
    public function edit() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = APP_URL . '/profile/edit';
            $_SESSION['flash_message'] = 'Please login to edit your profile';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Get user details
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // If user not found, redirect to logout
        if(!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/logout');
            exit;
        }
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'user_id' => $_SESSION['user_id'],
                'full_name' => trim($_POST['full_name']),
                'phone' => trim($_POST['phone']),
                'address' => trim($_POST['address']),
                'current_password' => $_POST['current_password'] ?? '',
                'new_password' => $_POST['new_password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
                'update_password' => !empty($_POST['current_password']),
                'errors' => []
            ];
            
            // Validate full name
            if(empty($data['full_name'])) {
                $data['errors']['full_name'] = 'Please enter your full name';
            }
            
            // Validate phone
            if(empty($data['phone'])) {
                $data['errors']['phone'] = 'Please enter your phone number';
            }
            
            // Validate password if updating
            if($data['update_password']) {
                // Verify current password
                if(empty($data['current_password'])) {
                    $data['errors']['current_password'] = 'Please enter your current password';
                } elseif(!password_verify($data['current_password'], $user->password)) {
                    $data['errors']['current_password'] = 'Current password is incorrect';
                }
                
                // Validate new password
                if(empty($data['new_password'])) {
                    $data['errors']['new_password'] = 'Please enter a new password';
                } elseif(strlen($data['new_password']) < 6) {
                    $data['errors']['new_password'] = 'Password must be at least 6 characters';
                }
                
                // Validate confirm password
                if($data['new_password'] != $data['confirm_password']) {
                    $data['errors']['confirm_password'] = 'Passwords do not match';
                }
            }
            
            // If no errors, update user details
            if(empty($data['errors'])) {
                // Update user
                $result = $this->userModel->updateUser($data);
                
                // If password change is requested, update password separately
                if($data['update_password'] && $result) {
                    $passwordResult = $this->userModel->updatePassword($data['user_id'], $data['new_password']);
                    if(!$passwordResult) {
                        $_SESSION['flash_message'] = 'Profile updated but password change failed';
                        $_SESSION['flash_type'] = 'warning';
                        header('Location: ' . APP_URL . '/profile/edit');
                        exit;
                    }
                }
                
                if($result) {
                    $_SESSION['flash_message'] = 'Profile updated successfully';
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . APP_URL . '/profile');
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Failed to update profile';
                    $_SESSION['flash_type'] = 'danger';
                }
            }
            
            // If we get here, there were errors
            // We'll fall through to the view with the data
        } else {
            // Initialize data array with user details
            $data = [
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'address' => $user->address,
                'current_password' => '',
                'new_password' => '',
                'confirm_password' => '',
                'update_password' => false,
                'errors' => []
            ];
        }
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'Edit Profile';
        $currentPage = 'profile';
        $activeMenu = 'edit';
        
        // Load view directly, not through an include in another view
        require_once(VIEW_PATH . 'profile/edit.php');
    }
    
    // User bookings page
    public function bookings() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = APP_URL . '/profile/bookings';
            $_SESSION['flash_message'] = 'Please login to view your bookings';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Get user details for the sidebar
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // If user not found, redirect to logout
        if(!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/logout');
            exit;
        }
        
        // Get user bookings
        $bookings = $this->bookingModel->getUserBookings($_SESSION['user_id']);
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'My Bookings';
        $currentPage = 'profile';
        $activeMenu = 'booking_history';
        
        // Load view directly, not through an include in another view
        require_once(VIEW_PATH . 'profile/booking_history.php');
    }
    
    // Change password page
    public function changePassword() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = APP_URL . '/profile/change-password';
            $_SESSION['flash_message'] = 'Please login to change your password';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Get user details
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // If user not found, redirect to logout
        if(!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/logout');
            exit;
        }
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'user_id' => $_SESSION['user_id'],
                'current_password' => $_POST['current_password'],
                'new_password' => $_POST['new_password'],
                'confirm_password' => $_POST['confirm_password'],
                'errors' => []
            ];
            
            // Validate current password
            if(empty($data['current_password'])) {
                $data['errors']['current_password'] = 'Please enter your current password';
            } elseif(!password_verify($data['current_password'], $user->password)) {
                $data['errors']['current_password'] = 'Current password is incorrect';
            }
            
            // Validate new password
            if(empty($data['new_password'])) {
                $data['errors']['new_password'] = 'Please enter a new password';
            } elseif(strlen($data['new_password']) < 6) {
                $data['errors']['new_password'] = 'Password must be at least 6 characters';
            }
            
            // Validate confirm password
            if($data['new_password'] != $data['confirm_password']) {
                $data['errors']['confirm_password'] = 'Passwords do not match';
            }
            
            // If no errors, update password
            if(empty($data['errors'])) {
                if($this->userModel->updatePassword($data['user_id'], $data['new_password'])) {
                    $_SESSION['flash_message'] = 'Password updated successfully';
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . APP_URL . '/profile');
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Failed to update password';
                    $_SESSION['flash_type'] = 'danger';
                }
            }
            
            // If we get here, there were errors
            // We'll fall through to the view with the data
        } else {
            // Initialize data array
            $data = [
                'user_id' => $user->user_id,
                'current_password' => '',
                'new_password' => '',
                'confirm_password' => '',
                'errors' => []
            ];
        }
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'Change Password';
        $currentPage = 'profile';
        $activeMenu = 'change_password';
        
        // Load view
        require_once(VIEW_PATH . 'profile/change_password.php');
    }
}