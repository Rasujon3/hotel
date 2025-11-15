-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2025 at 07:03 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `go_hotel`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `paid` decimal(10,2) DEFAULT 0.00,
  `due` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `total`, `paid`, `due`, `status`, `created_at`, `updated_at`, `hotel_id`, `payment_type`) VALUES
(9, 5, 6000.00, 6000.00, 0.00, 'pending', '2025-09-17 11:01:36', '2025-09-17 11:52:41', 1, NULL),
(10, 5, 6000.00, 600.00, 5400.00, 'confirmed', '2025-10-05 10:12:30', '2025-10-05 10:12:30', 1, 'Online');

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

CREATE TABLE `booking_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `floor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `booking_start_date` timestamp NULL DEFAULT NULL,
  `booking_end_date` timestamp NULL DEFAULT NULL,
  `check_in` timestamp NULL DEFAULT NULL,
  `check_out` timestamp NULL DEFAULT NULL,
  `day_count` varchar(50) DEFAULT '1',
  `rent` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'confirmed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`id`, `booking_id`, `user_id`, `hotel_id`, `building_id`, `floor_id`, `room_id`, `booking_start_date`, `booking_end_date`, `check_in`, `check_out`, `day_count`, `rent`, `status`, `created_at`, `updated_at`) VALUES
(9, 9, 5, 1, NULL, 3, 4, '2025-09-18 06:00:00', '2025-09-20 05:00:00', '2025-09-17 11:30:04', '2025-09-17 11:55:50', '2', 4000.00, 'checked_out', '2025-09-17 11:01:36', '2025-09-17 11:55:50'),
(10, 9, 5, 1, NULL, 3, 5, '2025-09-18 06:00:00', '2025-09-20 05:00:00', NULL, NULL, '2', 2000.00, 'confirmed', '2025-09-17 11:01:36', '2025-09-17 11:01:36'),
(11, 10, 5, 1, NULL, 3, 4, '2025-10-05 06:00:00', '2025-10-07 05:00:00', NULL, NULL, '2', 4000.00, 'confirmed', '2025-10-05 10:12:30', '2025-10-05 10:12:30'),
(12, 10, 5, 1, NULL, 3, 5, '2025-10-05 06:00:00', '2025-10-07 05:00:00', NULL, NULL, '2', 2000.00, 'confirmed', '2025-10-05 10:12:30', '2025-10-05 10:12:30');

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`id`, `user_id`, `hotel_id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(6, 6, 1, '1st Building', 'Active', 6, NULL, '2025-09-22 12:22:43', '2025-09-22 12:22:43'),
(7, 6, 1, '2nd Building', 'Active', 6, NULL, '2025-10-09 05:15:37', '2025-10-09 05:15:37');

-- --------------------------------------------------------

--
-- Table structure for table `building_imgs`
--

CREATE TABLE `building_imgs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building_imgs`
--

INSERT INTO `building_imgs` (`id`, `user_id`, `hotel_id`, `building_id`, `image_url`, `image_path`, `created_at`, `updated_at`) VALUES
(10, 6, 1, 6, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/building/99d3f6d3-0e12-4026-be23-31ed8c8676cb.png', 'building/99d3f6d3-0e12-4026-be23-31ed8c8676cb.png', '2025-09-22 12:22:44', '2025-09-22 12:22:44'),
(11, 6, 1, 7, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/building/7e29d885-a389-4596-b543-24c22de4d8ee.png', 'building/7e29d885-a389-4596-b543-24c22de4d8ee.png', '2025-10-09 05:15:37', '2025-10-09 05:15:37');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `hotel_id`, `expense_id`, `name`, `quantity`, `unit`, `amount`, `created_at`, `updated_at`) VALUES
(6, 8, 1, NULL, 'Shampoo', '100', 'PCS', 1000.00, '2025-09-11 05:54:58', '2025-09-11 05:54:58'),
(7, 8, 1, NULL, 'Hand Wash', '100', 'PCS', 1000.00, '2025-09-11 05:58:59', '2025-09-11 05:58:59');

-- --------------------------------------------------------

--
-- Table structure for table `expense_imgs`
--

CREATE TABLE `expense_imgs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_id` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_imgs`
--

INSERT INTO `expense_imgs` (`id`, `user_id`, `hotel_id`, `expense_id`, `image_url`, `image_path`, `created_at`, `updated_at`) VALUES
(4, 8, 2, '6', 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/expense/3c48b9d0-14d2-4fe5-8d06-9e0cae23ea79.png', 'expense/3c48b9d0-14d2-4fe5-8d06-9e0cae23ea79.png', '2025-09-11 05:54:59', '2025-09-11 05:54:59'),
(5, 8, 2, '7', 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/expense/c21e38f1-bf81-4198-9123-488637f168aa.png', 'expense/c21e38f1-bf81-4198-9123-488637f168aa.png', '2025-09-11 05:59:00', '2025-09-11 05:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `name`, `updated_by`, `created_by`, `hotel_id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Swimming Pool update', 6, 16, 1, 16, 'Active', '2025-09-14 06:16:48', '2025-09-14 06:18:39'),
(3, 'Swimming Pool update 2', 6, 6, 1, 6, 'Active', '2025-09-14 06:18:11', '2025-09-14 06:19:28');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`id`, `user_id`, `building_id`, `hotel_id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(3, 6, NULL, 1, '1st Floor', 'Active', NULL, NULL, '2025-09-09 11:07:33', '2025-09-09 11:07:33'),
(4, 6, NULL, 1, '2nd Floor', 'Active', NULL, NULL, '2025-09-09 11:08:05', '2025-09-09 11:08:05'),
(6, 8, NULL, 2, '2nd Floor', 'Active', NULL, NULL, '2025-09-09 11:14:34', '2025-09-09 11:14:34'),
(7, 6, 6, 1, '1st Floor', 'Active', 6, NULL, '2025-10-09 05:14:39', '2025-10-09 05:14:39'),
(8, 6, 7, 1, '1st Floor', 'Active', 6, NULL, '2025-10-09 05:16:10', '2025-10-09 05:16:10'),
(9, 6, 6, 1, '2nd Floor', 'Active', 6, NULL, '2025-10-09 05:17:39', '2025-10-09 05:17:39'),
(10, 6, 7, 1, '2nd Floor', 'Active', 6, NULL, '2025-10-09 05:17:46', '2025-10-09 05:17:46');

-- --------------------------------------------------------

--
-- Table structure for table `floor_imgs`
--

CREATE TABLE `floor_imgs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `floor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `floor_imgs`
--

INSERT INTO `floor_imgs` (`id`, `user_id`, `hotel_id`, `floor_id`, `image_url`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 7, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/floor/011771db-3b8a-4d96-8ba8-3b5e23dcffc9.png', 'floor/011771db-3b8a-4d96-8ba8-3b5e23dcffc9.png', '2025-10-09 05:14:51', '2025-10-09 05:14:51'),
(2, 6, 1, 8, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/floor/aa8db84f-836d-45fd-b3d0-f99788a69c80.png', 'floor/aa8db84f-836d-45fd-b3d0-f99788a69c80.png', '2025-10-09 05:16:11', '2025-10-09 05:16:11'),
(3, 6, 1, 9, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/floor/1e15b0a2-fb29-446d-b6d3-15c7ac469ce7.png', 'floor/1e15b0a2-fb29-446d-b6d3-15c7ac469ce7.png', '2025-10-09 05:17:40', '2025-10-09 05:17:40'),
(4, 6, 1, 10, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/floor/28dc3a6a-bf1d-4b04-90e1-37847767c4bd.png', 'floor/28dc3a6a-bf1d-4b04-90e1-37847767c4bd.png', '2025-10-09 05:17:47', '2025-10-09 05:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `hotel_name` varchar(255) DEFAULT NULL,
  `hotel_description` text DEFAULT NULL,
  `hotel_address` varchar(255) DEFAULT NULL,
  `lat` varchar(255) DEFAULT NULL,
  `long` varchar(255) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `package_id` varchar(255) DEFAULT NULL,
  `property_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Inactive','Active') DEFAULT 'Inactive',
  `booking_percentage` decimal(10,2) DEFAULT NULL,
  `system_commission` decimal(10,2) DEFAULT 0.00,
  `check_out_time` time DEFAULT NULL,
  `check_in_time` time DEFAULT NULL,
  `package_start_date` varchar(255) DEFAULT NULL,
  `package_end_date` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `popular_place_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `user_id`, `hotel_name`, `hotel_description`, `hotel_address`, `lat`, `long`, `balance`, `package_id`, `property_type_id`, `status`, `booking_percentage`, `system_commission`, `check_out_time`, `check_in_time`, `package_start_date`, `package_end_date`, `created_at`, `updated_at`, `popular_place_id`) VALUES
(1, 6, 'Hotel update', 'hotel_description update', 'hotel_address update', '23.8103', '90.4125', 2300.00, '2', 1, 'Active', 20.00, 10.00, '11:00:00', '12:00:00', '2025-09-09 07:42:05', '2025-09-16 07:42:05', '2025-09-07 23:56:13', '2025-10-11 05:42:26', 3),
(2, 8, 'Hotel update', 'hotel_description update', 'hotel_address update', '23.8103', '90.4125', 0.00, '2', 1, 'Active', 10.00, 0.00, NULL, NULL, '2025-09-09 07:48:57', '2025-09-16 07:48:57', '2025-09-09 00:06:16', '2025-09-13 06:28:09', NULL),
(3, 15, 'Hotel 2', 'hotel_description', 'hotel_address', '23.8103', '90.4125', 0.00, '2', 1, 'Active', NULL, 0.00, NULL, NULL, '2025-09-13 18:11:20', '2025-09-20 18:11:20', '2025-09-13 12:10:07', '2025-09-13 12:11:20', NULL),
(5, 18, 'Hotel 6', 'hotel_description', 'hotel_address', '23.8103', '90.4125', 0.00, '4', NULL, 'Active', NULL, 0.00, NULL, NULL, NULL, NULL, '2025-09-22 05:41:03', '2025-09-22 05:41:03', NULL),
(6, 19, 'Hotel 6', 'hotel_description', 'hotel_address', '23.8103', '90.4125', 0.00, '4', NULL, 'Active', NULL, 0.00, NULL, NULL, NULL, NULL, '2025-09-23 12:10:28', '2025-09-23 12:10:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hotel_imgs`
--

CREATE TABLE `hotel_imgs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotel_imgs`
--

INSERT INTO `hotel_imgs` (`id`, `user_id`, `hotel_id`, `image_url`, `image_path`, `created_at`, `updated_at`) VALUES
(19, 6, 1, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/hotel/8d632d4a-ad39-4b6c-b9d0-d750342f72f7.png', 'hotel/8d632d4a-ad39-4b6c-b9d0-d750342f72f7.png', '2025-10-11 05:36:57', '2025-10-11 05:36:57'),
(20, 6, 1, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/hotel/25ad4393-7900-4827-8829-35c2694273e0.png', 'hotel/25ad4393-7900-4827-8829-35c2694273e0.png', '2025-10-11 05:36:57', '2025-10-11 05:36:57'),
(21, 6, 1, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/hotel/2ee704fa-6dee-4e0e-b530-bfb66e30c7c7.png', 'hotel/2ee704fa-6dee-4e0e-b530-bfb66e30c7c7.png', '2025-10-11 05:36:57', '2025-10-11 05:36:57'),
(22, 6, 1, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/hotel/6a450cd8-d9b1-47db-a2b4-e0d2bc2498c1.png', 'hotel/6a450cd8-d9b1-47db-a2b4-e0d2bc2498c1.png', '2025-10-11 05:36:57', '2025-10-11 05:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_01_21_101747_create_country_table', 1),
(5, '2025_02_03_205021_create_personal_access_tokens_table', 1),
(6, '2025_09_07_075019_create_projects_table', 2),
(7, '2025_09_07_080437_create_user_types_table', 3),
(8, '2025_09_07_093430_add_column_to_users_table', 4),
(9, '2025_09_07_103916_change_token_column_to_nullable_on_users_table', 5),
(10, '2025_09_08_053134_create_hotels_table', 6),
(11, '2025_09_08_083925_create_packages_table', 7),
(12, '2025_09_09_072240_add_column_to_hotels_table', 8),
(13, '2025_09_09_150833_create_package_payments_table', 9),
(14, '2025_09_09_154304_create_floors_table', 10),
(15, '2025_09_09_154910_add_status_to_floors_table', 11),
(16, '2025_09_09_175452_create_rooms_table', 12),
(17, '2025_09_09_182557_add_columns_to_rooms_table', 13),
(18, '2025_09_09_183349_add_columns_to_rooms_table', 14),
(19, '2025_09_10_115047_add_columns_to_rooms_table', 15),
(20, '2025_09_10_160115_add_columns_to_users_table', 16),
(21, '2025_09_10_145203_create_floor_imgs_table', 17),
(22, '2025_09_11_104000_create_expenses_table', 18),
(23, '2025_09_11_104317_create_expense_imgs_table', 18),
(24, '2025_09_11_111708_add_columns_to_expenses_table', 19),
(25, '2025_09_11_112141_add_columns_to_expense_imgs_table', 20),
(26, '2025_09_11_124827_create_receptionists_table', 21),
(27, '2025_09_11_161453_add_columns_to_receptionists_table', 22),
(28, '2025_09_13_111556_add_columns_to_hotels_table', 23),
(29, '2025_09_13_120703_create_hotel_imgs_table', 24),
(30, '2025_09_13_134817_add_columns_to_hotels_table', 25),
(31, '2025_09_13_145835_add_columns_to_rooms_table', 26),
(32, '2025_09_13_151955_create_room_imgs_table', 27),
(33, '2025_09_13_154155_add_columns_to_rooms_table', 28),
(34, '2025_09_13_164227_add_columns_to_rooms_table', 29),
(35, '2025_09_13_175141_add_column_to_users_table', 30),
(36, '2025_09_14_103531_create_facilities_table', 31),
(37, '2025_09_14_104613_add_columns_to_facilities_table', 32),
(38, '2025_09_14_133302_create_ratings_table', 33),
(39, '2025_09_14_140357_create_booking_details_table', 34),
(40, '2025_09_14_153811_create_bookings_table', 35),
(41, '2025_09_14_154118_create_payments_table', 35),
(42, '2025_09_15_120725_remove_foreign_keys_from_bookings_table', 36),
(43, '2025_09_15_131126_add_column_to_bookings_table', 37),
(44, '2025_09_15_165842_add_column_to_hotels_table', 38),
(45, '2025_09_15_180942_create_withdrawal_methods_table', 39),
(46, '2025_09_16_102750_create_withdraws_table', 40),
(47, '2025_09_16_105442_add_columns_to_withdraws_table', 41),
(48, '2025_09_16_165310_add_lat_long_index_to_hotels_table', 42),
(49, '2025_09_17_161101_add_columns_to_booking_details_table', 43),
(50, '2025_09_18_103911_create_popular_places_table', 44),
(51, '2025_09_18_115659_add_column_to_hotels_table', 45),
(52, '2025_09_19_165347_add_columns_to_hotels_table', 46),
(53, '2025_09_20_151941_create_offers_table', 47),
(54, '2025_09_20_175906_add_columns_to_bookings_table', 48),
(55, '2025_09_22_121755_add_columns_to_users_table', 48),
(56, '2025_09_22_155358_create_buildings_table', 49),
(57, '2025_09_22_160911_create_building_imgs_table', 49),
(58, '2025_09_22_160926_add_column_to_floors_table', 50),
(59, '2025_09_22_193653_add_column_to_rooms_table', 50),
(60, '2025_10_05_174337_create_property_types_table', 51),
(61, '2025_10_06_120159_add_columns_to_hotels_table', 52),
(62, '2025_10_11_124847_add_columns_to_users_table', 53),
(63, '2025_10_11_150940_add_columns_to_users_table', 54),
(64, '2025_10_11_154441_create_settings_table', 55),
(65, '2025_10_14_112825_add_columns_to_ratings_table', 56),
(66, '2025_10_14_162121_add_columns_to_booking_details_table', 57),
(67, '2025_10_15_140620_add_columns_to_offers_table', 57);

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `floor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_no` varchar(255) DEFAULT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `booking_price` decimal(10,2) DEFAULT NULL,
  `rent` decimal(10,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `hotel_id`, `building_id`, `floor_id`, `room_id`, `room_no`, `start_date`, `end_date`, `booking_price`, `rent`, `discount_amount`, `created_at`, `updated_at`) VALUES
(14, 1, 6, 3, 4, '102', '2025-10-14 18:00:00', '2025-10-24 18:00:00', 400.00, 2000.00, 1500.00, '2025-10-15 08:12:17', '2025-10-15 08:12:17');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `duration` enum('weekly','monthly','yearly') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `duration`, `price`, `status`, `created_at`, `updated_at`) VALUES
(4, '3 Star Hotel', 'monthly', 2000.00, 'Active', '2025-09-22 05:20:22', '2025-09-22 05:20:22'),
(5, '4 Star Hotel', 'monthly', 4000.00, 'Active', '2025-09-22 05:20:36', '2025-09-22 05:22:58'),
(7, '5 Star Hotel', 'monthly', 5000.00, 'Active', '2025-09-28 09:47:12', '2025-09-29 06:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `package_payments`
--

CREATE TABLE `package_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'Pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `payment_type` enum('Online','Offline') NOT NULL,
  `payment_method` enum('bkash','rocket','nagad','credit_card','cash','bank_transfer','other') NOT NULL,
  `acc_no` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `pay_type` enum('booking','additional') NOT NULL DEFAULT 'booking',
  `transaction_id` varchar(255) DEFAULT NULL,
  `reference` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `payment_type`, `payment_method`, `acc_no`, `amount`, `pay_type`, `transaction_id`, `reference`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(10, 9, 'Online', 'bkash', '01764401655', 600.00, 'booking', 'TX123456789', 'Advance booking via bKash', '2025-09-17 11:01:36', '2025-09-17 11:01:36', 5, NULL),
(12, 9, 'Offline', 'cash', NULL, 5000.00, 'additional', NULL, NULL, '2025-09-17 11:42:39', '2025-09-17 11:42:39', 6, NULL),
(13, 9, 'Offline', 'cash', NULL, 5000.00, 'additional', NULL, NULL, '2025-09-17 11:46:07', '2025-09-17 11:46:07', 6, NULL),
(14, 9, 'Offline', 'cash', NULL, 5000.00, 'additional', NULL, NULL, '2025-09-17 11:51:31', '2025-09-17 11:51:31', 6, NULL),
(15, 9, 'Offline', 'cash', NULL, 400.00, 'additional', NULL, NULL, '2025-09-17 11:52:41', '2025-09-17 11:52:41', 6, NULL),
(16, 10, 'Online', 'bkash', '01764401655', 600.00, 'booking', 'TX123456789', 'Advance booking via bKash', '2025-10-05 10:12:30', '2025-10-05 10:12:30', 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'API Token', '457b69b77a2da3c647f63b5e0ca617da83442686019ced475e8c2cf6708fee6a', '[\"*\"]', NULL, NULL, '2025-09-07 04:11:07', '2025-09-07 04:11:07'),
(2, 'App\\Models\\User', 2, 'API Token', 'ed8f326e9ed5e0a32c4fbe3bc97b97cc0c5164e5dcb64207d1ab934f84db5832', '[\"*\"]', NULL, NULL, '2025-09-07 04:42:04', '2025-09-07 04:42:04'),
(3, 'App\\Models\\User', 3, 'API Token', '92e36efcee065d4884afa3c99b53aaedc692daf93a27ddbcf00930fcca53a2ae', '[\"*\"]', NULL, NULL, '2025-09-07 04:42:43', '2025-09-07 04:42:43'),
(4, 'App\\Models\\User', 4, 'API Token', '85bf3bd70ec77a574ab5cc69e139b4884163ce33c2f947b1a5ec699fcb4274be', '[\"*\"]', NULL, NULL, '2025-09-07 22:55:32', '2025-09-07 22:55:32'),
(6, 'App\\Models\\User', 6, 'API Token', '25b840a0836442c76544eb62ca44fcfc730a47aedc2adad6ffa072eb7aec1997', '[\"*\"]', NULL, NULL, '2025-09-07 23:56:13', '2025-09-07 23:56:13'),
(8, 'App\\Models\\User', 5, 'API Token', 'ffaaf3d60a9611d46d20cf3857a2abff96db99b4638662f8a4daa027df4719c3', '[\"*\"]', NULL, NULL, '2025-09-08 00:50:04', '2025-09-08 00:50:04'),
(9, 'App\\Models\\User', 5, 'API Token', '32b18164724ff3da661eba87d59c5d6ee3fde03768bc1f8f9b954aabab19bd67', '[\"*\"]', '2025-09-08 23:02:51', NULL, '2025-09-08 23:02:42', '2025-09-08 23:02:51'),
(13, 'App\\Models\\User', 8, 'API Token', 'f496e2af1edd2aae642b7aa0c9df0c5d679adaa68e14afb2defeab6fb44d0250', '[\"*\"]', NULL, NULL, '2025-09-09 00:06:16', '2025-09-09 00:06:16'),
(16, 'App\\Models\\User', 6, 'API Token', '83a4d9cf0e53c92bed34f9f902e54cfeb91d9deb838b8c52ba42ea40e669dc18', '[\"*\"]', '2025-09-09 11:11:20', NULL, '2025-09-09 10:00:14', '2025-09-09 11:11:20'),
(17, 'App\\Models\\User', 8, 'API Token', '843dd2a91d729ffd09a38328edf399c15c644aaabaef8c402490565e35ca5245', '[\"*\"]', '2025-09-10 05:54:04', NULL, '2025-09-09 11:11:59', '2025-09-10 05:54:04'),
(18, 'App\\Models\\User', 6, 'API Token', '356a06ddd858efdde91724b13f5b816d2ef4e3db2e360145070aad3f39cbf429', '[\"*\"]', '2025-09-10 06:00:57', NULL, '2025-09-10 05:55:10', '2025-09-10 06:00:57'),
(19, 'App\\Models\\User', 8, 'API Token', '5c6fd06bc202f93b21a111482a1ee1ea2f6592cf52807131d6a6f3750b042e44', '[\"*\"]', '2025-09-10 06:01:34', NULL, '2025-09-10 06:01:22', '2025-09-10 06:01:34'),
(20, 'App\\Models\\User', 6, 'API Token', '051d3bd5022798c070d726e16e5ed6ad1e7f2490e114ee252b666aef10a72d98', '[\"*\"]', '2025-09-10 06:23:37', NULL, '2025-09-10 06:01:45', '2025-09-10 06:23:37'),
(26, 'App\\Models\\User', 9, 'API Token', 'edd2a124c68fbcfd56f4c0370a38237654ae0921ebb83ae275bf15694d4e92f4', '[\"*\"]', NULL, NULL, '2025-09-10 10:16:57', '2025-09-10 10:16:57'),
(27, 'App\\Models\\User', 10, 'API Token', 'd83057c807c944c9603d206e64c795ec6497db8751aed1c7784b162fd2fa0a27', '[\"*\"]', NULL, NULL, '2025-09-10 10:22:08', '2025-09-10 10:22:08'),
(28, 'App\\Models\\User', 8, 'API Token', 'f2b4012dfb85b2bb3695fb1a7bc7cb17812bfda67b68bb52f21966421e4b7bb0', '[\"*\"]', '2025-09-11 05:59:05', NULL, '2025-09-11 05:00:33', '2025-09-11 05:59:05'),
(30, 'App\\Models\\User', 8, 'API Token', '15e6943b701c1612f11f954f339452a347c16c444fa4ea8b9dd2a034efd2fe64', '[\"*\"]', '2025-09-13 05:10:38', NULL, '2025-09-11 09:30:54', '2025-09-13 05:10:38'),
(31, 'App\\Models\\User', 6, 'API Token', '0a4ccdce25fcbb51b30687cf3c072e8784c15a62d8d36aeefde0ff90702d3e09', '[\"*\"]', '2025-09-13 07:07:40', NULL, '2025-09-13 05:12:25', '2025-09-13 07:07:40'),
(32, 'App\\Models\\User', 6, 'API Token', 'a5f79fdd63e35108eb389dfb76c4646741f7daf388cc7ee7c9c69d61558a8941', '[\"*\"]', '2025-09-13 07:59:43', NULL, '2025-09-13 07:59:33', '2025-09-13 07:59:43'),
(33, 'App\\Models\\User', 6, 'API Token', '180ecb9c43c9dc5094928687076ed3fdc1cfc16299123e405089a96c537951ed', '[\"*\"]', '2025-09-13 09:27:57', NULL, '2025-09-13 09:15:59', '2025-09-13 09:27:57'),
(34, 'App\\Models\\User', 6, 'API Token', '58ab877b0a9ff7983cfddeecb49a3bd70462082ed05e6bad1b85c68ccfe743cb', '[\"*\"]', '2025-09-13 11:58:04', NULL, '2025-09-13 10:15:50', '2025-09-13 11:58:04'),
(35, 'App\\Models\\User', 6, 'API Token', 'd43438ed9fac72e385a469229c5a40831f36e63a9cdd5dc75394d7ffddb2da4e', '[\"*\"]', NULL, NULL, '2025-09-13 11:32:05', '2025-09-13 11:32:05'),
(37, 'App\\Models\\User', 15, 'API Token', 'c902a31200f955007b693184f4229659f428ba3f3b01cf41549eac841abfdcfc', '[\"*\"]', NULL, NULL, '2025-09-13 12:10:07', '2025-09-13 12:10:07'),
(38, 'App\\Models\\User', 14, 'API Token', '69efb8c4d5b6fa743b2ca379f5d8771dc625ec4a4d13f393a3fa9c1f504bfc49', '[\"*\"]', '2025-09-14 05:40:30', NULL, '2025-09-14 05:21:39', '2025-09-14 05:40:30'),
(39, 'App\\Models\\User', 5, 'API Token', '94a90ee824568107df4865048b0044b503ecdb0962ab901339f7765c41965756', '[\"*\"]', NULL, NULL, '2025-09-14 05:39:05', '2025-09-14 05:39:05'),
(40, 'App\\Models\\User', 5, 'API Token', '4643f5bb6ee7f012c3b67d76c426e6522154cccadc6db621e71f7c59e3023343', '[\"*\"]', NULL, NULL, '2025-09-14 05:39:31', '2025-09-14 05:39:31'),
(41, 'App\\Models\\User', 6, 'API Token', '1287d905f5c4ece161da0f7b9c514efed620bbadae4c6e8c9f694be7fb77ef68', '[\"*\"]', NULL, NULL, '2025-09-14 05:40:24', '2025-09-14 05:40:24'),
(42, 'App\\Models\\User', 6, 'API Token', '126bd16cf6e4a945470971e027547f8c1e584a5bb12e4cde7764d3fec3933369', '[\"*\"]', '2025-09-14 05:41:49', NULL, '2025-09-14 05:41:40', '2025-09-14 05:41:49'),
(43, 'App\\Models\\User', 16, 'API Token', '221e89a92d4b7a28e858cb33bcc4738cab239ea2e9e9b728cf186b88baa5140f', '[\"*\"]', '2025-09-14 06:17:07', NULL, '2025-09-14 05:42:15', '2025-09-14 06:17:07'),
(44, 'App\\Models\\User', 8, 'API Token', 'e2778dbc57d6017bcc292ab1f566a5388cd3f5c70e5cd573a35b89f651687894', '[\"*\"]', '2025-09-14 06:17:22', NULL, '2025-09-14 06:17:17', '2025-09-14 06:17:22'),
(45, 'App\\Models\\User', 6, 'API Token', '633b3320868249d71e4ca239a9df72a9316ea49af5482c7d52f1d6d7bb084c24', '[\"*\"]', '2025-09-14 08:01:25', NULL, '2025-09-14 06:17:55', '2025-09-14 08:01:25'),
(46, 'App\\Models\\User', 5, 'API Token', '97370c90996cf37dd341c6b4dc247c3f5a5457f5f2ce2fcc884d4645691d9da4', '[\"*\"]', '2025-09-15 05:45:24', NULL, '2025-09-14 08:01:53', '2025-09-15 05:45:24'),
(47, 'App\\Models\\User', 5, 'API Token', '51356de3aa526126aadd8d3e879baae9b379626074c89ab0903c959be4c0b1c0', '[\"*\"]', '2025-09-15 05:51:51', NULL, '2025-09-15 05:45:41', '2025-09-15 05:51:51'),
(48, 'App\\Models\\User', 6, 'API Token', '1666d64dbd8f38b7fe5a886d423778f15dda81f8d7ee820bafeb5cc2ad501837', '[\"*\"]', '2025-09-16 05:18:09', NULL, '2025-09-15 07:06:59', '2025-09-16 05:18:09'),
(50, 'App\\Models\\User', 6, 'API Token', 'cba6a8349f3ca3d5570e72c30f4f21bdaad0129dc7f01eb282e6466c4f0645ca', '[\"*\"]', '2025-09-16 05:52:32', NULL, '2025-09-16 05:51:40', '2025-09-16 05:52:32'),
(52, 'App\\Models\\User', 6, 'API Token', 'ead4606f0d6b28367aa80666b9c3ef9358e6b0ee6ff89df7c152c9891cba0e62', '[\"*\"]', '2025-09-16 09:34:06', NULL, '2025-09-16 06:51:50', '2025-09-16 09:34:06'),
(53, 'App\\Models\\User', 16, 'API Token', '3ed031fca8c54fcb4cebbfc5f002ebc590d4b32268e96e2854236d186996a2d9', '[\"*\"]', '2025-09-17 05:05:29', NULL, '2025-09-17 05:03:59', '2025-09-17 05:05:29'),
(54, 'App\\Models\\User', 5, 'API Token', '39056a228f7054ac65fcca8c9889997f9ff4530b8e897e6ce1672f46bd4d838b', '[\"*\"]', '2025-09-17 09:27:09', NULL, '2025-09-17 05:07:46', '2025-09-17 09:27:09'),
(55, 'App\\Models\\User', 16, 'API Token', '64450f60b7b64cf0c52fb1c7e2b078a48e64fb01177e9c589d15c6dadc32864a', '[\"*\"]', '2025-09-17 09:31:59', NULL, '2025-09-17 09:27:27', '2025-09-17 09:31:59'),
(56, 'App\\Models\\User', 6, 'API Token', '23f6c9dac33d22108586d2599a7b85c67fc2767a120eadb3f326a6132de6da20', '[\"*\"]', '2025-09-17 10:50:03', NULL, '2025-09-17 09:32:15', '2025-09-17 10:50:03'),
(57, 'App\\Models\\User', 5, 'API Token', '55ca7d62216127bbf2a55a0bbab13a75404d8df888c20430eab17d5f88f185ba', '[\"*\"]', '2025-09-17 11:04:35', NULL, '2025-09-17 10:50:45', '2025-09-17 11:04:35'),
(58, 'App\\Models\\User', 6, 'API Token', '3663f7e12225b61d28a3a0f338631a60f2cc8939e06afe8f0a42dce0109123e6', '[\"*\"]', '2025-09-18 05:05:19', NULL, '2025-09-17 11:04:48', '2025-09-18 05:05:19'),
(60, 'App\\Models\\User', 6, 'API Token', '4d1876ae50185d79aa4983855364c567c057b411bcce38606c39cf2c13a625d0', '[\"*\"]', '2025-09-22 05:17:21', NULL, '2025-09-18 06:18:18', '2025-09-22 05:17:21'),
(62, 'App\\Models\\User', 6, 'API Token', '432d9808e50e88cb8544f914a5aa79ba6e422f4ba9ac2d935a55764ee7162108', '[\"*\"]', '2025-09-22 05:18:23', NULL, '2025-09-22 05:18:16', '2025-09-22 05:18:23'),
(64, 'App\\Models\\User', 17, 'API Token', 'db533fd20b4bdcd2b1557544c573eb9c5cb03d37c50df02d0436ab14d6c69fe1', '[\"*\"]', NULL, NULL, '2025-09-22 05:28:48', '2025-09-22 05:28:48'),
(65, 'App\\Models\\User', 18, 'API Token', 'b49d8a62887367189005987ecd9e724e9cbb9f1677b7284564a1bf197ee57f61', '[\"*\"]', NULL, NULL, '2025-09-22 05:41:03', '2025-09-22 05:41:03'),
(66, 'App\\Models\\User', 5, 'API Token', '1579dfb79c99e5e84645686ea5339960a632af233a88e10ab52bc3303232640e', '[\"*\"]', '2025-09-22 10:49:19', NULL, '2025-09-22 06:28:52', '2025-09-22 10:49:19'),
(67, 'App\\Models\\User', 6, 'API Token', '467fcd870abc6de02c21a5dbe6786769cf7d9ffd9ad07f3cf0251ccf52751eba', '[\"*\"]', '2025-09-23 04:54:00', NULL, '2025-09-22 10:49:45', '2025-09-23 04:54:00'),
(68, 'App\\Models\\User', 19, 'API Token', '06b9525445c191cd8589fed8df8384bd508626f2197f05fe41b3f0b20bc11299', '[\"*\"]', NULL, NULL, '2025-09-23 12:10:29', '2025-09-23 12:10:29'),
(70, 'App\\Models\\User', 16, 'API Token', '4271c6442020c5f7e9ae400fba46c541f1d759cdda257272371bfb779ceded50', '[\"*\"]', NULL, NULL, '2025-09-28 06:54:11', '2025-09-28 06:54:11'),
(71, 'App\\Models\\User', 16, 'API Token', 'ccb39f197a49f7649fb86165e23d25e25f6edb48bb30981e63c4859db04a7ee3', '[\"*\"]', NULL, NULL, '2025-09-28 06:54:19', '2025-09-28 06:54:19'),
(72, 'App\\Models\\User', 16, 'API Token', '28a8bc5d0ddaef581d6835f6ee99c21b44054878f2d8c5850b13cc4b05f009d4', '[\"*\"]', NULL, NULL, '2025-09-28 06:54:58', '2025-09-28 06:54:58'),
(75, 'App\\Models\\User', 16, 'API Token', '34d9b452d8357b75059810f39c9c62388f60133b668f22fd778d57c7a9cb3235', '[\"*\"]', NULL, NULL, '2025-09-28 08:03:05', '2025-09-28 08:03:05'),
(76, 'App\\Models\\User', 16, 'API Token', '480f3b240fa96ddc5e5b6248a326f153f8b0801858d06c0c5e37cfb8451dcb48', '[\"*\"]', NULL, NULL, '2025-09-28 08:22:31', '2025-09-28 08:22:31'),
(85, 'App\\Models\\User', 7, 'API Token', 'd58342bfb8e347229f3186f1c63d016d1f0ab22f30dee50426b73b8d2eff646e', '[\"*\"]', NULL, NULL, '2025-09-29 06:33:42', '2025-09-29 06:33:42'),
(86, 'App\\Models\\User', 7, 'API Token', '0d623cc66e0aa88cd96ab3677cebabecf2f43abb34a1d72c95648f01cc0b83b9', '[\"*\"]', '2025-09-29 09:31:48', NULL, '2025-09-29 08:37:34', '2025-09-29 09:31:48'),
(87, 'App\\Models\\User', 7, 'API Token', '9f8658506ca682f325ab1b581161276f150070a9bc30c3b23f75325f68198852', '[\"*\"]', '2025-10-05 08:12:20', NULL, '2025-10-05 04:30:23', '2025-10-05 08:12:20'),
(88, 'App\\Models\\User', 7, 'API Token', 'aa6b85faa47af49bb2634953eaad8cdacd463466ae6d497296b9cd022f63e258', '[\"*\"]', '2025-10-05 09:36:21', NULL, '2025-10-05 04:44:33', '2025-10-05 09:36:21'),
(89, 'App\\Models\\User', 6, 'API Token', '6549dc7b068d63cdb4f682c8362bed544992c425df5649be6216f4a42596e0b6', '[\"*\"]', '2025-10-05 09:38:15', NULL, '2025-10-05 09:36:42', '2025-10-05 09:38:15'),
(90, 'App\\Models\\User', 5, 'API Token', 'f2e0958df0f2d763ac73a561a0b1c43323beccb5867988076e03f0f5230a2339', '[\"*\"]', '2025-10-05 10:14:05', NULL, '2025-10-05 09:38:25', '2025-10-05 10:14:05'),
(91, 'App\\Models\\User', 6, 'API Token', 'b0931902e17ac28201273bdc628a5b97820cc43abd9c94b463706fbe0cd8530f', '[\"*\"]', '2025-10-09 05:17:46', NULL, '2025-10-05 10:14:16', '2025-10-09 05:17:46'),
(92, 'App\\Models\\User', 7, 'API Token', 'b1439abf8572528d1b942ceab9da965d402c947a45e7467091f1d3b9dcb90a9b', '[\"*\"]', '2025-10-05 12:17:23', NULL, '2025-10-05 11:14:49', '2025-10-05 12:17:23'),
(93, 'App\\Models\\User', 7, 'API Token', '2de3b20408da2096fdbc4f8da4754ccf9a6fba2cd512533b22dd9d06d454182e', '[\"*\"]', NULL, NULL, '2025-10-06 05:57:27', '2025-10-06 05:57:27'),
(94, 'App\\Models\\User', 6, 'API Token', 'f3001996c6bb56ece93392d7db9e3fef6cb14057659120bf0f32e959c7104f62', '[\"*\"]', '2025-10-11 05:47:48', NULL, '2025-10-11 05:36:32', '2025-10-11 05:47:48'),
(95, 'App\\Models\\User', 7, 'API Token', '73683c11eac0ed378c6bf02180ad1430fd2f6634ec6d9afb42f36d3fc5018f04', '[\"*\"]', '2025-10-11 06:01:15', NULL, '2025-10-11 06:00:36', '2025-10-11 06:01:15'),
(96, 'App\\Models\\User', 7, 'API Token', '140c3f3a5f4cb3488e3eb78cd17314f56d3f06fd0fb8dfa578cb2ae410402b56', '[\"*\"]', NULL, NULL, '2025-10-11 09:26:15', '2025-10-11 09:26:15'),
(97, 'App\\Models\\User', 5, 'API Token', 'b052a0f6cd6f493fa68235d395c3a2d54f21591b84761bdea299d58fd1ebd7a5', '[\"*\"]', '2025-10-14 06:19:13', NULL, '2025-10-13 12:48:30', '2025-10-14 06:19:13'),
(98, 'App\\Models\\User', 6, 'API Token', 'c8072109db122a516b5a2a33188d18c6ff70ab4bd967f035ad8c49cd3c1699a0', '[\"*\"]', '2025-10-14 09:28:14', NULL, '2025-10-14 06:20:29', '2025-10-14 09:28:14'),
(99, 'App\\Models\\User', 5, 'API Token', '67ca4a4a3cb9dbeacb865b2e0e3da34bab1dbafa267ffb004328734cfd7380ac', '[\"*\"]', '2025-10-14 09:35:59', NULL, '2025-10-14 09:28:26', '2025-10-14 09:35:59'),
(100, 'App\\Models\\User', 8, 'API Token', '351b8c2bdc1ece956d194a39dbc221ea8bed62d1790235dae1c4bed5dcc9ec73', '[\"*\"]', '2025-10-15 07:30:48', NULL, '2025-10-15 07:30:38', '2025-10-15 07:30:48'),
(101, 'App\\Models\\User', 6, 'API Token', 'bde6598c1c74cf7f8bdcefe75cf9a7f8f3f332cfd40751ffc59679e344f657a0', '[\"*\"]', '2025-10-15 08:53:17', NULL, '2025-10-15 07:31:02', '2025-10-15 08:53:17');

-- --------------------------------------------------------

--
-- Table structure for table `popular_places`
--

CREATE TABLE `popular_places` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `popular_places`
--

INSERT INTO `popular_places` (`id`, `name`, `image_url`, `image_path`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Cox’s Bazar', 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/popular_place/c2364e46-c80d-456a-b293-9142908fc161.png', 'popular_place/c2364e46-c80d-456a-b293-9142908fc161.png', 'Active', '2025-09-18 05:55:15', '2025-09-18 05:55:15'),
(4, 'Sreemangal', 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/popular_place/ec840812-1b80-4fad-af39-5fbe0733ce61.png', 'popular_place/ec840812-1b80-4fad-af39-5fbe0733ce61.png', 'Active', '2025-09-18 05:55:22', '2025-09-18 05:55:22'),
(7, 'Sajek Valley', 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/popular_place/c16f4705-db9c-45f6-8b2e-48bc6c3466d5.png', 'popular_place/c16f4705-db9c-45f6-8b2e-48bc6c3466d5.png', 'Active', '2025-10-11 06:01:10', '2025-10-11 06:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Hotel Management', '2025-09-07 02:01:58', '2025-09-07 02:01:58'),
(2, 'Agricultural Management', '2025-09-07 02:01:59', '2025-09-07 02:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `property_types`
--

CREATE TABLE `property_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_types`
--

INSERT INTO `property_types` (`id`, `name`, `image_url`, `image_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Hotel', 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/propertyType/ea76a86d-7049-4a1d-a3f2-4dff968a32ac.png', 'propertyType/ea76a86d-7049-4a1d-a3f2-4dff968a32ac.png', 'Active', '2025-10-06 05:59:48', '2025-10-06 05:59:48'),
(2, 'Ruhul Amin', 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/propertyType/d6865131-7cd0-46b0-8576-b875cfb2e61c.png', 'propertyType/d6865131-7cd0-46b0-8576-b875cfb2e61c.png', 'Active', '2025-10-06 06:33:53', '2025-10-06 06:33:53');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `hotel_id`, `rating`, `description`, `created_at`, `updated_at`) VALUES
(2, 5, 1, 4.5, NULL, '2025-09-14 08:14:51', '2025-09-14 08:14:51'),
(3, 5, 2, 3.5, 'Average', '2025-10-14 05:37:12', '2025-10-14 06:03:50');

-- --------------------------------------------------------

--
-- Table structure for table `receptionists`
--

CREATE TABLE `receptionists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `hotel_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `nid` varchar(255) DEFAULT NULL,
  `shift` enum('Morning','Evening','Night') DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `receptionists`
--

INSERT INTO `receptionists` (`id`, `user_id`, `hotel_id`, `created_by`, `updated_by`, `name`, `email`, `phone`, `nid`, `shift`, `image_path`, `image_url`, `created_at`, `updated_at`) VALUES
(4, 14, 2, 8, '8', 'John Doe update', 'johnupdate@example.com', '123456789', 'NID123456', 'Evening', 'profile/98c1fc7a-aac4-4132-ab01-8009d26ac388.jpg', 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/profile/98c1fc7a-aac4-4132-ab01-8009d26ac388.jpg', '2025-09-11 11:26:58', '2025-09-11 11:27:14'),
(5, 16, 1, 6, NULL, 'sujon', 'sujon@gmail.com', '01764401650', 'NID12345', 'Morning', '', '', '2025-09-14 05:41:53', '2025-09-14 05:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `floor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_no` varchar(255) DEFAULT NULL,
  `bed_type` enum('Single','Double','Triple') DEFAULT 'Single',
  `room_type` enum('AC','Non-AC') DEFAULT NULL,
  `view` varchar(255) DEFAULT NULL,
  `num_of_beds` enum('1','2','3') DEFAULT NULL,
  `current_status` enum('available','booked','maintenance','occupied') DEFAULT 'available',
  `end_booking_time` timestamp NULL DEFAULT NULL,
  `start_booking_time` timestamp NULL DEFAULT NULL,
  `booking_price` decimal(10,2) DEFAULT NULL,
  `rent` decimal(10,2) DEFAULT NULL,
  `system_commission` decimal(10,2) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `user_id`, `building_id`, `hotel_id`, `floor_id`, `room_no`, `bed_type`, `room_type`, `view`, `num_of_beds`, `current_status`, `end_booking_time`, `start_booking_time`, `booking_price`, `rent`, `system_commission`, `icon`, `status`, `created_by`, `updated_by`, `discount_amount`, `created_at`, `updated_at`) VALUES
(4, 6, 6, 1, 3, '102', 'Double', 'AC', 'Ocean View', '2', 'booked', '2025-10-07 05:00:00', '2025-10-05 06:00:00', 400.00, 2000.00, 10.00, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/hotel/7d58dfb1-ba73-4714-a07c-d9b9402618df.png', 'Active', NULL, NULL, 15.00, '2025-09-13 10:53:11', '2025-10-05 10:12:30'),
(5, 6, 6, 1, 3, '101', 'Single', 'AC', 'Ocean View', '2', 'booked', '2025-10-07 05:00:00', '2025-10-05 06:00:00', 200.00, 1000.00, 10.00, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/hotel/7d58dfb1-ba73-4714-a07c-d9b9402618df.png', 'Active', NULL, NULL, 15.00, '2025-09-13 10:53:11', '2025-10-05 10:12:30');

-- --------------------------------------------------------

--
-- Table structure for table `room_imgs`
--

CREATE TABLE `room_imgs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `floor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_imgs`
--

INSERT INTO `room_imgs` (`id`, `user_id`, `hotel_id`, `floor_id`, `room_id`, `image_url`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 3, 4, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/room/90bc8e2f-1b67-4c2a-b738-0bd6f0291117.png', 'room/90bc8e2f-1b67-4c2a-b738-0bd6f0291117.png', '2025-09-13 10:53:11', '2025-09-13 10:53:11'),
(2, 6, 1, 3, 4, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/room/f8a0b6b7-04f8-49dc-91b7-4dbebf29d696.png', 'room/f8a0b6b7-04f8-49dc-91b7-4dbebf29d696.png', '2025-09-13 10:53:11', '2025-09-13 10:53:11');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fpass_limit_per_day` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `fpass_limit_per_day`, `created_at`, `updated_at`) VALUES
(1, '3', '2025-10-11 09:47:52', '2025-10-11 09:47:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `user_type_id` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `lat` varchar(255) DEFAULT NULL,
  `long` varchar(255) DEFAULT NULL,
  `day` varchar(255) DEFAULT NULL,
  `month` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `fbase` varchar(255) DEFAULT NULL,
  `refer_code` varchar(255) DEFAULT NULL,
  `my_refer_code` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `otp_request_count` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `otp_last_request_date` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `address`, `user_type_id`, `role`, `ip_address`, `lat`, `long`, `day`, `month`, `year`, `fbase`, `refer_code`, `my_refer_code`, `email_verified_at`, `password`, `token`, `status`, `hotel_id`, `image_url`, `image_path`, `otp`, `otp_expires_at`, `otp_request_count`, `otp_last_request_date`, `remember_token`, `created_at`, `updated_at`) VALUES
(5, 'Ruhul Amin Sujon', 'sujon.egov@gmail.com', '01764401651', 'address', '2', 'user', '127.0.0.1', '', '', '08', 'Sep', '2025', 'firebase_token_123', 'REF2025', 'LKC792', NULL, '$2y$12$KRz8bk5ufAOZ3PhaCe1ABuhOrZY1GRyrRmP94njzV2sgMjyt7UsOS', NULL, 'Active', NULL, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/profile/8431b285-5795-4cd2-a8ab-0d1d7b7974a4.png', 'profile/8431b285-5795-4cd2-a8ab-0d1d7b7974a4.png', NULL, NULL, 0, NULL, NULL, '2025-09-07 22:57:36', '2025-09-22 07:48:01'),
(6, 'Ruhul Amin Sujon', 'sujon.egov2@gmail.com', '01764401652', NULL, '3', 'owner', '127.0.0.1', '23.8103', '90.4125', '08', 'Sep', '2025', 'firebase_token_123', 'LKC792', 'KOZ399', NULL, '$2y$12$y5L50lY/wUzjuFlvC0SetuuinVbaBFFFxZBYmQN4Mdayhnbstm7uW', NULL, 'Active', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2025-09-07 23:56:13', '2025-09-18 06:18:43'),
(7, 'Super Admin', 'superAdmin@gmail.com', '01712345678', NULL, '1', 'super_admin', '127.0.0.1', '0', '0', '08', 'Sep', '2025', NULL, NULL, NULL, NULL, '$2y$12$M9rzdizVT.bhfe7y7GBCoutKiv.SB/k1xPD8qodDhQI3EHYRZuSAy', 'P9XpYVCBKYfbMbgmGpDDCVzygBHhjnFmWa9d63PlkDNcSPCMv1MsQ3XfIjtN', 'Active', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2025-09-08 01:40:41', '2025-09-08 01:40:41'),
(8, 'Ruhul Amin', 'sujon.egov3@gmail.com', '01764401653', NULL, '3', 'owner', '127.0.0.1', '23.8103', '90.4125', '09', 'Sep', '2025', 'firebase_token_123', 'LKC792', 'OD1955', NULL, '$2y$12$J0F4KXrYJ12SONowGfZRP.w7037zluXDAggniD2z2HpXS9wBG2WiW', NULL, 'Active', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2025-09-09 00:06:16', '2025-09-09 01:48:57'),
(10, 'Ruhul Amin', 'sujon.egov4@gmail.com', '01764401654', NULL, '2', 'user', '127.0.0.1', '23.8103', '90.4125', '10', 'Sep', '2025', 'firebase_token_123', 'LKC792', 'LZY534', NULL, '$2y$12$WbO8VTJznD7PK683mdOI1OXu/MImdfIAqo9MM8D4BvaU33Fd2tAoe', NULL, 'Active', NULL, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/profile/cbf56f63-2dd8-4866-9478-05533ed7e4c4.jpg', 'profile/cbf56f63-2dd8-4866-9478-05533ed7e4c4.jpg', NULL, NULL, 0, NULL, NULL, '2025-09-10 10:22:08', '2025-09-10 10:22:08'),
(14, 'John Doe update', 'johnupdate@example.com', '1234567890', NULL, '4', 'receptionist', '127.0.0.1', '', '', '11', 'Sep', '2025', '', '', 'ZFJ977', NULL, '$2y$12$5Vki4Syeo.BuKLkFuX/D3eqsK4L9t6pS7tj/rqbTiNgFl1uO3dk56', NULL, 'Active', NULL, 'https://aisoft-hotel-project.s3.ap-south-1.amazonaws.com/profile/98c1fc7a-aac4-4132-ab01-8009d26ac388.jpg', 'profile/98c1fc7a-aac4-4132-ab01-8009d26ac388.jpg', NULL, NULL, 0, '2025-10-11 10:01:05', NULL, '2025-09-11 11:26:58', '2025-10-11 10:01:23'),
(15, 'Ruhul Amin', 'sujon.egov5@gmail.com', '01764401655', NULL, '3', 'owner', '127.0.0.1', '23.8103', '90.4125', '13', 'Sep', '2025', 'firebase_token_123', 'LKC792', 'HS8443', NULL, '$2y$12$NCDTCpfXlGW3CL2LPECm/ui3/Pr5BQA4HIKxXMgBKBQM.TcymwD6W', NULL, 'Active', 3, '', '', NULL, NULL, 0, NULL, NULL, '2025-09-13 12:10:07', '2025-09-13 12:11:20'),
(16, 'sujon', 'sujon@gmail.com', '01518376761', NULL, '4', 'receptionist', '127.0.0.1', '', '', '14', 'Sep', '2025', '', '', 'RBJ916', NULL, '$2y$12$JmfnM3RhIuOr4seWmSq.e.Q4HPyQpwxnhQSBMnVCykf1crKknPEhy', NULL, 'Active', 1, '', '', '$2y$12$7X/8MNNr1ifOhC25zzfNg.LBuP.yP.qq7.kOBY7olu28Ans/vk8ee', '2025-10-11 12:14:51', 3, '2025-10-11 12:04:51', NULL, '2025-09-14 05:41:53', '2025-10-11 12:04:51'),
(18, 'Ruhul Amin', 'sujon.egov6@gmail.com', '01764401656', NULL, '3', 'owner', '127.0.0.1', '23.8103', '90.4125', '22', 'Sep', '2025', 'firebase_token_123', 'LKC792', 'RD9293', NULL, '$2y$12$XwissJaDCRqpP8.7uayLGeBg65Cx8CRd6.fMotCOFPzQeQHQhzoYu', NULL, 'Active', NULL, '', '', NULL, NULL, 0, NULL, NULL, '2025-09-22 05:41:03', '2025-09-22 05:41:03'),
(19, 'Ruhul Amin', 'sujon.egov7@gmail.com', '01764401657', NULL, '3', 'owner', '127.0.0.1', '23.8103', '90.4125', '23', 'Sep', '2025', 'firebase_token_123', 'LKC792', 'PGS538', NULL, '$2y$12$IB1oxrAWKhVEmKlnT.Lc4OplorNsCTqsIEmeHGijGMpNqNfq9k5gG', NULL, 'Active', 6, '', '', NULL, NULL, 0, NULL, NULL, '2025-09-23 12:10:28', '2025-09-23 12:10:28');

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_showing` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`id`, `name`, `role`, `project_id`, `is_showing`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super_admin', NULL, 0, '2025-09-07 02:21:24', '2025-09-07 02:21:24'),
(2, 'User', 'user', NULL, 1, '2025-09-07 02:21:24', '2025-09-07 02:21:24'),
(3, 'Hotel Owner', 'owner', 1, 0, '2025-09-07 02:21:24', '2025-09-07 02:21:24'),
(4, 'Hotel Receptionist', 'receptionist', 1, 1, '2025-09-07 02:21:24', '2025-09-07 02:21:24');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_methods`
--

CREATE TABLE `withdrawal_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `acc_no` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `routing_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `withdrawal_methods`
--

INSERT INTO `withdrawal_methods` (`id`, `user_id`, `hotel_id`, `payment_method`, `acc_no`, `bank_name`, `branch_name`, `routing_number`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 'bkash', '123456789012', NULL, NULL, NULL, '2025-09-16 05:51:55', '2025-09-16 05:52:32');

-- --------------------------------------------------------

--
-- Table structure for table `withdraws`
--

CREATE TABLE `withdraws` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `withdrawal_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `payment_type` varchar(100) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `withdraw_at` timestamp NULL DEFAULT NULL,
  `trx_id` varchar(100) DEFAULT NULL,
  `reference` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `withdraws`
--

INSERT INTO `withdraws` (`id`, `user_id`, `hotel_id`, `withdrawal_method_id`, `title`, `payment_type`, `amount`, `withdraw_at`, `trx_id`, `reference`, `created_by`, `created_at`, `updated_at`) VALUES
(5, 7, 1, 1, 'Cash Out', 'Cash Out', 100.00, '2025-09-10 08:42:00', 'CXDT84514DFD84EDF', 'By Bkash_01630225015', 7, '2025-09-16 06:42:13', '2025-09-16 06:44:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_hotel_id_foreign` (`hotel_id`);

--
-- Indexes for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_details_booking_id_foreign` (`booking_id`),
  ADD KEY `booking_details_user_id_foreign` (`user_id`),
  ADD KEY `booking_details_hotel_id_foreign` (`hotel_id`),
  ADD KEY `booking_details_floor_id_foreign` (`floor_id`),
  ADD KEY `booking_details_room_id_foreign` (`room_id`),
  ADD KEY `booking_details_building_id_foreign` (`building_id`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buildings_user_id_foreign` (`user_id`),
  ADD KEY `buildings_hotel_id_foreign` (`hotel_id`),
  ADD KEY `buildings_created_by_foreign` (`created_by`),
  ADD KEY `buildings_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `building_imgs`
--
ALTER TABLE `building_imgs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `building_imgs_user_id_foreign` (`user_id`),
  ADD KEY `building_imgs_hotel_id_foreign` (`hotel_id`),
  ADD KEY `building_imgs_building_id_foreign` (`building_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_imgs`
--
ALTER TABLE `expense_imgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `facilities_user_id_foreign` (`user_id`),
  ADD KEY `facilities_hotel_id_foreign` (`hotel_id`),
  ADD KEY `facilities_created_by_foreign` (`created_by`),
  ADD KEY `facilities_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `floors_building_id_foreign` (`building_id`),
  ADD KEY `floors_updated_by_foreign` (`updated_by`),
  ADD KEY `floors_created_by_foreign` (`created_by`);

--
-- Indexes for table `floor_imgs`
--
ALTER TABLE `floor_imgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotels_user_id_foreign` (`user_id`),
  ADD KEY `idx_lat_long` (`id`,`lat`,`long`),
  ADD KEY `hotels_popular_place_id_foreign` (`popular_place_id`),
  ADD KEY `hotels_property_type_id_foreign` (`property_type_id`);

--
-- Indexes for table `hotel_imgs`
--
ALTER TABLE `hotel_imgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offers_hotel_id_foreign` (`hotel_id`),
  ADD KEY `offers_floor_id_foreign` (`floor_id`),
  ADD KEY `offers_building_id_foreign` (`building_id`),
  ADD KEY `offers_room_id_foreign` (`room_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_payments`
--
ALTER TABLE `package_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_payments_transaction_id_unique` (`transaction_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_booking_id_foreign` (`booking_id`),
  ADD KEY `payments_created_by_foreign` (`created_by`),
  ADD KEY `payments_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `popular_places`
--
ALTER TABLE `popular_places`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_types`
--
ALTER TABLE `property_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ratings_user_id_foreign` (`user_id`),
  ADD KEY `ratings_hotel_id_foreign` (`hotel_id`);

--
-- Indexes for table `receptionists`
--
ALTER TABLE `receptionists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receptionists_user_id_foreign` (`user_id`),
  ADD KEY `receptionists_hotel_id_foreign` (`hotel_id`),
  ADD KEY `receptionists_created_by_foreign` (`created_by`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rooms_building_id_foreign` (`building_id`),
  ADD KEY `rooms_updated_by_foreign` (`updated_by`),
  ADD KEY `rooms_created_by_foreign` (`created_by`);

--
-- Indexes for table `room_imgs`
--
ALTER TABLE `room_imgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawal_methods`
--
ALTER TABLE `withdrawal_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawal_methods_user_id_foreign` (`user_id`),
  ADD KEY `withdrawal_methods_hotel_id_foreign` (`hotel_id`);

--
-- Indexes for table `withdraws`
--
ALTER TABLE `withdraws`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdraws_user_id_foreign` (`user_id`),
  ADD KEY `withdraws_hotel_id_foreign` (`hotel_id`),
  ADD KEY `withdraws_withdrawal_method_id_foreign` (`withdrawal_method_id`),
  ADD KEY `withdraws_created_by_foreign` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `building_imgs`
--
ALTER TABLE `building_imgs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `expense_imgs`
--
ALTER TABLE `expense_imgs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `floor_imgs`
--
ALTER TABLE `floor_imgs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hotel_imgs`
--
ALTER TABLE `hotel_imgs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `package_payments`
--
ALTER TABLE `package_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `popular_places`
--
ALTER TABLE `popular_places`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `property_types`
--
ALTER TABLE `property_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `receptionists`
--
ALTER TABLE `receptionists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_imgs`
--
ALTER TABLE `room_imgs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `withdrawal_methods`
--
ALTER TABLE `withdrawal_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `withdraws`
--
ALTER TABLE `withdraws`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_details_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_details_floor_id_foreign` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_details_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_details_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `buildings`
--
ALTER TABLE `buildings`
  ADD CONSTRAINT `buildings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `buildings_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `buildings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `buildings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `building_imgs`
--
ALTER TABLE `building_imgs`
  ADD CONSTRAINT `building_imgs_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `building_imgs_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `building_imgs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `facilities`
--
ALTER TABLE `facilities`
  ADD CONSTRAINT `facilities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facilities_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facilities_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facilities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `floors`
--
ALTER TABLE `floors`
  ADD CONSTRAINT `floors_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `floors_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `floors_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_popular_place_id_foreign` FOREIGN KEY (`popular_place_id`) REFERENCES `popular_places` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotels_property_type_id_foreign` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotels_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offers_floor_id_foreign` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offers_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offers_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `receptionists`
--
ALTER TABLE `receptionists`
  ADD CONSTRAINT `receptionists_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receptionists_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receptionists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawal_methods`
--
ALTER TABLE `withdrawal_methods`
  ADD CONSTRAINT `withdrawal_methods_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `withdrawal_methods_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdraws`
--
ALTER TABLE `withdraws`
  ADD CONSTRAINT `withdraws_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `withdraws_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `withdraws_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `withdraws_withdrawal_method_id_foreign` FOREIGN KEY (`withdrawal_method_id`) REFERENCES `withdrawal_methods` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
