<?php

/*
Fungsi: Mengelola alur kerja yang berkaitan dengan otentikasi pengguna.

Tugas Spesifik:
    register():
        Menampilkan halaman registrasi (memanggil loadView() yang me-render views/auth/register.php).
        Memproses data dari form registrasi: mengambil input $_POST, melakukan validasi.
        Jika valid, memanggil metode register() di User model.
        Berdasarkan hasil dari Model, membuat session, menampilkan pesan sukses/gagal, dan mengarahkan pengguna.
    login():
        Menampilkan halaman login (memanggil loadLoginView() yang me-render views/auth/login.php).
        Memproses data dari form login: mengambil input $_POST, validasi dasar.
        Memanggil metode login() di User model.
        Jika berhasil, membuat session, membuat cookie "remember me" (jika dicentang) dengan bantuan Auth model, dan mengarahkan pengguna (ke dashboard admin atau halaman utama).
    logout(): 
        Menghapus session, menghapus cookie "remember me" (dengan bantuan Auth model), dan mengarahkan ke halaman login.
    forgotPassword(): 
        Menampilkan halaman lupa password dan (secara konseptual) akan memproses permintaan reset password (saat ini hanya menampilkan pesan sukses).

Ketergantungan: Membuat instance dari Auth model dan User model untuk berinteraksi dengan mereka.
*/

class AuthController {
    private $authModel;
    private $userModel;
    
    public function __construct() {
        $this->authModel = new Auth();
        $this->userModel = new User();
    }
    
    // Register page
    public function register() {
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL);
            exit;
        }
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
                'full_name' => trim($_POST['full_name']),
                'phone' => trim($_POST['phone']),
                'address' => trim($_POST['address']),
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'full_name_err' => '',
                'phone_err' => ''
            ];
            
            // Validate username
            if(empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            } else {
                // Check if username already exists
                if($this->userModel->findUserByUsername($data['username'])) {
                    $data['username_err'] = 'Username is already taken';
                }
            }
            
            // Validate email
            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                // Check if email already exists
                if($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already registered';
                }
            }
            
            // Validate password
            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif(strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }
            
            // Validate confirm password
            if(empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }
            
            // Validate full name
            if(empty($data['full_name'])) {
                $data['full_name_err'] = 'Please enter your full name';
            }
            
            // Validate phone
            if(empty($data['phone'])) {
                $data['phone_err'] = 'Please enter your phone number';
            }
            
            // Make sure errors are empty
            if(empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err']) && 
               empty($data['confirm_password_err']) && empty($data['full_name_err']) && empty($data['phone_err'])) {
                
                // Register user
                $user_id = $this->userModel->register($data);
                
                if($user_id) {
                    // User registered, create session
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_username'] = $data['username'];
                    $_SESSION['user_email'] = $data['email'];
                    $_SESSION['user_name'] = $data['full_name'];
                    
                    $_SESSION['flash_message'] = 'Registration successful! Welcome to ' . APP_NAME;
                    $_SESSION['flash_type'] = 'success';
                    
                    header('Location: ' . APP_URL);
                    exit;
                } else {
                    // Something went wrong
                    $_SESSION['flash_message'] = 'Something went wrong. Please try again.';
                    $_SESSION['flash_type'] = 'danger';
                    
                    $this->loadView($data);
                }
            } else {
                // Load view with errors
                $this->loadView($data);
            }
        } else {
            // Initialize data array
            $data = [
                'username' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'full_name' => '',
                'phone' => '',
                'address' => '',
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'full_name_err' => '',
                'phone_err' => ''
            ];
            
            // Load view
            $this->loadView($data);
        }
    }
    
    // Login page
    public function login() {
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL);
            exit;
        }
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'username' => trim($_POST['username']),
                'password' => $_POST['password'],
                'remember' => isset($_POST['remember']) ? true : false,
                'username_err' => '',
                'password_err' => ''
            ];
            
            // Validate username
            if(empty($data['username'])) {
                $data['username_err'] = 'Please enter username or email';
            }
            
            // Validate password
            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }
            
            // Check if all errors are empty
            if(empty($data['username_err']) && empty($data['password_err'])) {
                // Check if login is successful
                $user = $this->userModel->login($data['username'], $data['password']);
                  if($user) {
                    // User authenticated, create session
                    $_SESSION['user_id'] = $user->user_id;
                    $_SESSION['user_username'] = $user->username;
                    $_SESSION['user_email'] = $user->email;
                    $_SESSION['user_name'] = $user->full_name;
                    
                    // Set remember me cookie if requested
                    if($data['remember']) {
                        $token = $this->authModel->createRememberToken($user->user_id);
                        if($token) {
                            setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
                        }
                    }
                    
                    $_SESSION['flash_message'] = 'Login successful! Welcome back, ' . $user->full_name;
                    $_SESSION['flash_type'] = 'success';
                    
                    // Check user role and redirect accordingly
                    if($user->role === 'admin') {
                        // Set admin session
                        $_SESSION['admin_id'] = $user->user_id;
                        $_SESSION['admin_username'] = $user->username;
                        $_SESSION['admin_role'] = $user->role;
                        header('Location: ' . APP_URL . '/admin');
                    } else {
                        // Redirect to intended page if set, otherwise to home page
                        if(isset($_SESSION['intended_url'])) {
                            $intended_url = $_SESSION['intended_url'];
                            unset($_SESSION['intended_url']);
                            header('Location: ' . $intended_url);
                        } else {
                            header('Location: ' . APP_URL);
                        }
                    }
                    exit;
                } else {
                    // Login failed
                    $data['password_err'] = 'Invalid username/email or password';
                    
                    $this->loadLoginView($data);
                }
            } else {
                // Load view with errors
                $this->loadLoginView($data);
            }
        } else {
            // Initialize data array
            $data = [
                'username' => '',
                'password' => '',
                'remember' => false,
                'username_err' => '',
                'password_err' => ''
            ];
            
            // Load view
            $this->loadLoginView($data);
        }
    }
    
    // Logout
    public function logout() {
        // Unset all session variables
        unset($_SESSION['user_id']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        
        // Delete remember me cookie if exists
        if(isset($_COOKIE['remember_token'])) {
            $this->authModel->deleteRememberToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Flash message
        $_SESSION['flash_message'] = 'You have been logged out';
        $_SESSION['flash_type'] = 'success';
        
        // Redirect to login page
        header('Location: ' . APP_URL . '/login');
        exit;
    }
    
    // Load register view
    private function loadView($data) {
        $pageTitle = 'Register';
        
        require_once(VIEW_PATH . 'auth/register.php');
    }
    
    // Load login view
    private function loadLoginView($data) {
        $pageTitle = 'Login';
        
        require_once(VIEW_PATH . 'auth/login.php');
    }

    // Add this method to your AuthController.php class

// Forgot Password page
public function forgotPassword() {
    // Check if user is already logged in
    if(isset($_SESSION['user_id'])) {
        header('Location: ' . APP_URL);
        exit;
    }
    
    // Check if form is submitted
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $data = [
            'email' => trim($_POST['email']),
            'email_err' => ''
        ];
        
        // Validate email
        if(empty($data['email'])) {
            $data['email_err'] = 'Please enter email';
        } else {
            // Check if email exists
            if(!$this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'No account found with that email';
            }
        }
        
        // Make sure errors are empty
        if(empty($data['email_err'])) {
            // Email is valid and exists in database
            // Generate token and store in database (you need to implement this)
            // $token = bin2hex(random_bytes(32));
            // $this->authModel->storeResetToken($data['email'], $token);
            
            // In a real application, you would send an email with the reset link
            // But for this demo, we'll just show a success message
            
            $_SESSION['flash_message'] = 'Reset link has been sent to your email address';
            $_SESSION['flash_type'] = 'success';
            
            header('Location: ' . APP_URL . '/login');
            exit;
        } else {
            // Load view with errors
            $this->loadForgotPasswordView($data);
        }
    } else {
        // Init data
        $data = [
            'email' => '',
            'email_err' => ''
        ];
        
        // Load view
        $this->loadForgotPasswordView($data);
    }
}

// Load forgot password view
private function loadForgotPasswordView($data) {
    require_once(VIEW_PATH . 'auth/forgot_password.php');
}
}