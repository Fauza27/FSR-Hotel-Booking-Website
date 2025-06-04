<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/responsive.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="<?= APP_URL ?>" class="logo">FSR<span>Hotel</span></a>
                
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
                
                <ul class="nav-links">
                    <li><a href="<?= APP_URL ?>" class="<?= (isset($currentPage) && $currentPage == 'home') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?= APP_URL ?>/rooms" class="<?= (isset($currentPage) && $currentPage == 'rooms') ? 'active' : ''; ?>">Rooms</a></li>
                    <li><a href="<?= APP_URL ?>/about" class="<?= (isset($currentPage) && $currentPage == 'about') ? 'active' : ''; ?>">About</a></li>
                    <li><a href="<?= APP_URL ?>/contact" class="<?= (isset($currentPage) && $currentPage == 'contact') ? 'active' : ''; ?>">Contact</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?= APP_URL ?>/profile" class="<?= (isset($currentPage) && $currentPage == 'profile') ? 'active' : ''; ?>">My Account</a></li>
                        <li><a href="<?= APP_URL ?>/logout" class="btn btn-secondary">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= APP_URL ?>/login" class="btn btn-secondary">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- Flash messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="container mt-2">
            <div class="alert alert-<?= $_SESSION['flash_type'] ?>">
                <?= $_SESSION['flash_message']; ?>
            </div>
        </div>
        <?php 
        // Clear the flash message after displaying
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main>
