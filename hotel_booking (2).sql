-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 27 Bulan Mei 2025 pada 07.50
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_booking`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `email`, `full_name`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hotel.com', 'System Administrator', 'super_admin', '2025-05-20 00:50:03', '2025-05-24 02:50:46'),
(2, 'admin1', 'Admin1234', 'admin@example.com', 'admin 1', 'super_admin', '2025-05-24 02:45:53', '2025-05-24 02:45:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `adults` int(11) NOT NULL DEFAULT 1,
  `children` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `identity_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `total_price`, `adults`, `children`, `status`, `identity_file`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2025-05-21', '2025-05-22', 100.00, 1, 0, 'confirmed', 'uploads/identity/ID_3_20250520095645.png', '2025-05-20 02:56:45', '2025-05-20 02:58:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `facilities`
--

CREATE TABLE `facilities` (
  `facility_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `facilities`
--

INSERT INTO `facilities` (`facility_id`, `name`, `icon`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Wi-Fi', 'wifi', 'Free high-speed Wi-Fi', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(2, 'AC', 'air-conditioner', 'Air Conditioning', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(3, 'TV', 'tv', 'LED TV with cable channels', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(4, 'Mini Bar', 'bar', 'Mini bar with refreshments', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(5, 'Coffee Maker', 'coffee', 'Coffee and tea maker', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(6, 'Safety Box', 'safe', 'In-room safety deposit box', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(7, 'Swimming Pool', 'swim', 'Access to swimming pool', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(8, 'Gym', 'dumbbell', 'Access to fitness center', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(9, 'Spa', 'spa', 'Access to spa services', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(10, 'Breakfast', 'food', 'Complimentary breakfast', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(11, 'Hair Dryer', 'dryer', 'Pengering rambut tersedia di kamar', '2025-05-21 03:45:21', '2025-05-21 03:45:21'),
(12, 'Refrigerator', 'fridge', 'Kulkas mini di kamar', '2025-05-21 03:45:21', '2025-05-21 03:45:21'),
(13, 'Bathtub', 'bathtub', 'Bathtub di kamar mandi', '2025-05-21 03:48:42', '2025-05-21 03:48:42'),
(14, 'Balcony', 'balcony', 'Balkon pribadi dengan pemandangan luar', '2025-05-21 03:48:42', '2025-05-21 03:48:42'),
(15, 'Kitchenette', 'kitchenette', 'Dilengkapi dengan dapur mini', '2025-05-21 03:48:42', '2025-05-21 03:48:42'),
(16, 'Jacuzzi', 'jacuzzi', 'Jacuzzi pribadi di kamar', '2025-05-21 03:48:42', '2025-05-21 03:48:42'),
(17, 'Sauna', 'sauna', 'Sauna pribadi untuk relaksasi', '2025-05-21 03:48:42', '2025-05-21 03:48:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','bank_transfer','cash') NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `amount`, `payment_method`, `payment_status`, `transaction_id`, `payment_date`, `created_at`, `updated_at`) VALUES
(1, 1, 100.00, 'cash', 'completed', 'TXN17477098915283', '2025-05-20 02:58:11', '2025-05-20 02:58:11', '2025-05-20 02:58:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `size_sqm` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('available','occupied','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `category_id`, `price_per_night`, `capacity`, `size_sqm`, `image_url`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, '101', 1, 100.00, 2, 25.00, 'https://example.com/room1.jpg', 'Standard room with basic amenities', 'occupied', '2025-05-19 16:50:03', '2025-05-20 02:58:11'),
(2, '102', 2, 150.00, 2, 30.00, 'https://example.com/room2.jpg', 'Deluxe room with additional features and a great view', 'available', '2025-05-19 16:50:03', '2025-05-19 16:50:03'),
(5, '105', 8, 250.00, 2, 35.00, 'https://example.com/room5.jpg', 'Presidential Suite with luxury facilities', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(6, '106', 9, 600.00, 3, 60.00, 'https://example.com/room6.jpg', 'Royal Suite with panoramic views', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(7, '107', 10, 150.00, 2, 30.00, 'https://example.com/room7.jpg', 'Superior room with a more spacious feel', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(8, '108', 11, 300.00, 4, 50.00, 'https://example.com/room8.jpg', 'Luxury room with additional amenities', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(9, '109', 12, 200.00, 2, 40.00, 'https://example.com/room9.jpg', 'Ocean view room with a balcony', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(10, '110', 8, 280.00, 2, 45.00, 'https://example.com/room10.jpg', 'Presidential Suite with a Jacuzzi', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(11, '111', 9, 350.00, 3, 55.00, 'https://example.com/room11.jpg', 'Royal Suite with a sauna and Jacuzzi', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(12, '112', 10, 220.00, 2, 38.00, 'https://example.com/room12.jpg', 'Superior Room with a beautiful view', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(13, '113', 11, 320.00, 4, 60.00, 'https://example.com/room13.jpg', 'Luxury suite with a balcony', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(14, '114', 12, 250.00, 2, 50.00, 'https://example.com/room14.jpg', 'Ocean View Room with large windows', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(15, '115', 8, 450.00, 2, 75.00, 'https://example.com/room15.jpg', 'Presidential suite with a full kitchen', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(16, '116', 9, 500.00, 3, 80.00, 'https://example.com/room16.jpg', 'Royal suite with personal sauna', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(17, '117', 10, 280.00, 2, 45.00, 'https://example.com/room17.jpg', 'Superior Room with premium features', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(18, '118', 11, 350.00, 4, 60.00, 'https://example.com/room18.jpg', 'Luxury room with amazing city views', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(19, '119', 12, 220.00, 2, 35.00, 'https://example.com/room19.jpg', 'Ocean view room with a king-sized bed', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(20, '120', 8, 550.00, 3, 70.00, 'https://example.com/room20.jpg', 'Presidential Suite with full amenities', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(21, '121', 9, 600.00, 4, 80.00, 'https://example.com/room21.jpg', 'Royal Suite with a private balcony and hot tub', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(22, '122', 10, 400.00, 2, 60.00, 'https://example.com/room22.jpg', 'Superior room with a garden view', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(23, '123', 11, 420.00, 4, 65.00, 'https://example.com/room23.jpg', 'Luxury suite with a private pool', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(24, '124', 12, 250.00, 2, 40.00, 'https://example.com/room24.jpg', 'Ocean view room with a spacious layout', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(25, '125', 8, 600.00, 3, 90.00, 'https://example.com/room25.jpg', 'Presidential suite with an exclusive lounge', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(26, '126', 9, 700.00, 4, 100.00, 'https://example.com/room26.jpg', 'Royal suite with a hot tub and massage area', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(27, '127', 10, 320.00, 2, 50.00, 'https://example.com/room27.jpg', 'Superior Room with premium amenities', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(28, '128', 11, 380.00, 3, 55.00, 'https://example.com/room28.jpg', 'Luxury suite with ocean view', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(29, '129', 12, 270.00, 2, 45.00, 'https://example.com/room29.jpg', 'Ocean view room with premium services', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55'),
(30, '130', 8, 650.00, 4, 80.00, 'https://example.com/room30.jpg', 'Presidential suite with a private garden', 'available', '2025-05-21 03:53:55', '2025-05-21 03:53:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `room_categories`
--

CREATE TABLE `room_categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `room_categories`
--

INSERT INTO `room_categories` (`category_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Standard', 'Kamar standard dengan fasilitas dasar', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(2, 'Deluxe', 'Kamar deluxe dengan pemandangan dan fasilitas tambahan', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(3, 'Suite', 'Kamar suite mewah dengan ruang tamu terpisah', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(4, 'Family', 'Kamar luas cocok untuk keluarga', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(5, 'Executive', 'Kamar executive dengan akses ke lounge khusus', '2025-05-20 00:50:03', '2025-05-20 00:50:03'),
(6, 'Junior Suite', 'Kamar junior suite dengan ruang tamu kecil dan pemandangan kota', '2025-05-21 03:45:21', '2025-05-21 03:45:21'),
(7, 'Penthouse', 'Kamar penthouse mewah dengan teras pribadi dan pemandangan luas', '2025-05-21 03:45:21', '2025-05-21 03:45:21'),
(8, 'Presidential Suite', 'Kamar suite presiden dengan ruang tamu mewah dan layanan eksklusif', '2025-05-21 03:48:42', '2025-05-21 03:48:42'),
(9, 'Royal Suite', 'Kamar suite kerajaan dengan fasilitas premium dan pemandangan menakjubkan', '2025-05-21 03:48:42', '2025-05-21 03:48:42'),
(10, 'Superior', 'Kamar superior dengan fasilitas standar lebih baik', '2025-05-21 03:48:42', '2025-05-21 03:48:42'),
(11, 'Luxury', 'Kamar mewah dengan berbagai fasilitas tambahan', '2025-05-21 03:48:42', '2025-05-21 03:48:42'),
(12, 'Ocean View', 'Kamar dengan pemandangan laut yang menakjubkan', '2025-05-21 03:48:42', '2025-05-21 03:48:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `room_facilities`
--

CREATE TABLE `room_facilities` (
  `room_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `room_facilities`
--

INSERT INTO `room_facilities` (`room_id`, `facility_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 3),
(5, 13),
(5, 14),
(6, 14),
(6, 15),
(7, 16),
(7, 17),
(8, 13),
(8, 14),
(9, 13),
(9, 15),
(10, 14),
(10, 16),
(11, 13),
(11, 14),
(12, 13),
(12, 14),
(13, 14),
(13, 15),
(14, 13),
(14, 14),
(15, 13),
(15, 14),
(16, 13),
(16, 14),
(17, 13),
(17, 14),
(18, 13),
(18, 14),
(19, 13),
(19, 14);

-- --------------------------------------------------------

--
-- Struktur dari tabel `room_images`
--

CREATE TABLE `room_images` (
  `image_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `room_images`
--

INSERT INTO `room_images` (`image_id`, `room_id`, `image_url`, `is_primary`, `created_at`) VALUES
(27, 5, 'https://example.com/room5.jpg', 1, '2025-05-21 03:54:35'),
(28, 6, 'https://example.com/room6.jpg', 1, '2025-05-21 03:54:35'),
(29, 7, 'https://example.com/room7.jpg', 1, '2025-05-21 03:54:35'),
(30, 8, 'https://example.com/room8.jpg', 1, '2025-05-21 03:54:35'),
(31, 9, 'https://example.com/room9.jpg', 1, '2025-05-21 03:54:35'),
(32, 10, 'https://example.com/room10.jpg', 1, '2025-05-21 03:54:35'),
(33, 11, 'https://example.com/room11.jpg', 1, '2025-05-21 03:54:35'),
(34, 12, 'https://example.com/room12.jpg', 1, '2025-05-21 03:54:35'),
(35, 13, 'https://example.com/room13.jpg', 1, '2025-05-21 03:54:35'),
(36, 14, 'https://example.com/room14.jpg', 1, '2025-05-21 03:54:35'),
(37, 15, 'https://example.com/room15.jpg', 1, '2025-05-21 03:54:35'),
(38, 16, 'https://example.com/room16.jpg', 1, '2025-05-21 03:54:35'),
(39, 17, 'https://example.com/room17.jpg', 1, '2025-05-21 03:54:35'),
(40, 18, 'https://example.com/room18.jpg', 1, '2025-05-21 03:54:35'),
(41, 19, 'https://example.com/room19.jpg', 1, '2025-05-21 03:54:35'),
(42, 20, 'https://example.com/room20.jpg', 1, '2025-05-21 03:54:35'),
(43, 21, 'https://example.com/room21.jpg', 1, '2025-05-21 03:54:35'),
(44, 22, 'https://example.com/room22.jpg', 1, '2025-05-21 03:54:35'),
(45, 23, 'https://example.com/room23.jpg', 1, '2025-05-21 03:54:35'),
(46, 24, 'https://example.com/room24.jpg', 1, '2025-05-21 03:54:35'),
(47, 25, 'https://example.com/room25.jpg', 1, '2025-05-21 03:54:35'),
(48, 26, 'https://example.com/room26.jpg', 1, '2025-05-21 03:54:35'),
(49, 27, 'https://example.com/room27.jpg', 1, '2025-05-21 03:54:35'),
(50, 28, 'https://example.com/room28.jpg', 1, '2025-05-21 03:54:35'),
(51, 29, 'https://example.com/room29.jpg', 1, '2025-05-21 03:54:35'),
(52, 30, 'https://example.com/room30.jpg', 1, '2025-05-21 03:54:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `phone`, `address`, `created_at`, `updated_at`, `role`) VALUES
(1, 'pace', '$2y$10$V0Y5MEbZfpR4n5zNoxJQOimMYhLSZtL5BXk9pvkrjJrd0Ll6wT8xa', 'pace@example.com', 'pace keren', '1234567890', '123 Main Street, City, Country', '2025-05-19 16:50:03', '2025-05-19 16:50:03', 'user'),
(2, 'andriana', '$2y$10$g2iS9eHj6lggwHVKZlCljre.xelz4HzqAZ4P56qEmFuPrTQwv2SaW', 'andriana@example.com', 'andriana', '0987654321', '456 Elm Street, City, Country', '2025-05-19 16:50:03', '2025-05-19 16:50:03', 'user'),
(3, 'andre', '$2y$10$AFOHL3qhZjDdEYdV6A8QRO3C0nsUjZe6qEuOecUGxJ38/8u0AJU42', 'andre@example.com', 'andre kece', '0808080808', 'jalan andre perumahan kece', '2025-05-20 02:52:18', '2025-05-20 02:52:18', 'user'),
(4, 'admin7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin7@hotel.com', 'System Administrator', '08123456789', 'Admin Hotel', '2025-05-26 02:30:29', '2025-05-26 02:30:29', 'user'),
(5, 'admin27', '$2y$10$gh7lliJt0ON9I1NRHolxVeUH6L/Zcwqre/DoecMEJU6CS5aeNJ2aG', 'admin27@example.com', 'admin nomor 27', '0827272727', 'jalan dua puluh tujuh nomor27', '2025-05-27 05:30:56', '2025-05-27 05:30:56', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indeks untuk tabel `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`facility_id`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `room_categories`
--
ALTER TABLE `room_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeks untuk tabel `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD PRIMARY KEY (`room_id`,`facility_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indeks untuk tabel `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `facilities`
--
ALTER TABLE `facilities`
  MODIFY `facility_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `room_categories`
--
ALTER TABLE `room_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `room_images`
--
ALTER TABLE `room_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `room_categories` (`category_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD CONSTRAINT `room_facilities_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_facilities_ibfk_2` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
