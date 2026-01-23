-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 23, 2026 at 06:58 PM
-- Server version: 8.0.35
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mainmakd_makook`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint UNSIGNED NOT NULL,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint UNSIGNED DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'delivery', 'Delivery user logged in', 'App\\Models\\Delivery', NULL, 2, 'App\\Models\\Delivery', 2, '{\"ip_address\": \"197.39.17.7\", \"user_agent\": \"Dart/3.9 (dart:io)\"}', NULL, '2026-01-19 09:09:17', '2026-01-19 09:09:17'),
(2, 'admin', 'Admin logged out', 'App\\Models\\Admin', NULL, 1, 'App\\Models\\Admin', 1, '[]', NULL, '2026-01-19 09:27:19', '2026-01-19 09:27:19'),
(3, 'admin', 'Admin logged in', 'App\\Models\\Admin', NULL, 1, 'App\\Models\\Admin', 1, '{\"ip_address\": \"197.39.17.7\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', NULL, '2026-01-19 09:27:51', '2026-01-19 09:27:51'),
(4, 'delivery_management', 'Delivery user updated', 'App\\Models\\Delivery', NULL, 5, 'App\\Models\\Admin', 1, '{\"action\": \"updated\", \"updated_by\": \"Super Admin\", \"delivery_user_name\": \"aya 1\", \"delivery_user_email\": \"arafaaya@gmail.com\"}', NULL, '2026-01-19 09:32:57', '2026-01-19 09:32:57'),
(5, 'user', 'User logged out', 'App\\Models\\User', NULL, 3, 'App\\Models\\User', 3, '[]', NULL, '2026-01-19 09:51:42', '2026-01-19 09:51:42'),
(6, 'user', 'User logged in', 'App\\Models\\User', NULL, 6, 'App\\Models\\User', 6, '{\"ip_address\": \"156.215.16.141\", \"user_agent\": \"PostmanRuntime/7.51.0\"}', NULL, '2026-01-19 10:01:42', '2026-01-19 10:01:42'),
(7, 'user', 'User logged in', 'App\\Models\\User', NULL, 1, 'App\\Models\\User', 1, '{\"ip_address\": \"156.215.16.141\", \"user_agent\": \"PostmanRuntime/7.51.0\"}', NULL, '2026-01-19 10:02:34', '2026-01-19 10:02:34'),
(8, 'user', 'User logged out', 'App\\Models\\User', NULL, 3, 'App\\Models\\User', 3, '[]', NULL, '2026-01-19 11:26:01', '2026-01-19 11:26:01'),
(9, 'user', 'User logged in', 'App\\Models\\User', NULL, 6, 'App\\Models\\User', 6, '{\"ip_address\": \"156.215.16.141\", \"user_agent\": \"Dart/3.9 (dart:io)\"}', NULL, '2026-01-19 11:29:27', '2026-01-19 11:29:27'),
(10, 'user', 'User logged out', 'App\\Models\\User', NULL, 6, 'App\\Models\\User', 6, '[]', NULL, '2026-01-19 11:41:25', '2026-01-19 11:41:25'),
(11, 'user', 'User logged in', 'App\\Models\\User', NULL, 6, 'App\\Models\\User', 6, '{\"ip_address\": \"156.215.16.141\", \"user_agent\": \"Dart/3.9 (dart:io)\"}', NULL, '2026-01-19 11:41:47', '2026-01-19 11:41:47'),
(12, 'module', 'Module updated', 'App\\Models\\Module', 'updated', 25, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"طعام\", \"name_en\": \"Food\", \"description_ar\": \"وصف الطعام\", \"description_en\": \"description of food\"}, \"attributes\": {\"name_ar\": \"مطبخ مكوك\", \"name_en\": \"makook kitchen\", \"description_ar\": \"مطبخ مكوك\", \"description_en\": \"makook kitchen\"}}', NULL, '2026-01-19 12:31:49', '2026-01-19 12:31:49'),
(13, 'module', 'Module updated', 'App\\Models\\Module', 'updated', 31, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"مستلزمات جيم\", \"name_en\": \"Gym supplies\", \"description_ar\": \"كل أنواع مستلزمات الجيم, بما فيها البروتين والسناكس الصحية\", \"description_en\": \"All kind of gym supplies including protein and healthy products/snacks\"}, \"attributes\": {\"name_ar\": \"سوبر ماركت مكوك\", \"name_en\": \"makook mart\", \"description_ar\": \"سوبر ماركت مكوك\", \"description_en\": \"makook mart\"}}', NULL, '2026-01-19 12:32:55', '2026-01-19 12:32:55'),
(14, 'module', 'Module updated', 'App\\Models\\Module', 'updated', 26, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"كوفي شوب\", \"name_en\": \"CoffeShop\", \"description_ar\": \"وصف كوفيه شوب\", \"description_en\": \"description of coffe shop\"}, \"attributes\": {\"name_ar\": \"مكوك سندوتش\", \"name_en\": \"makook sandwitch\", \"description_ar\": \"مكوك سندوتش\", \"description_en\": \"makook sandwitch\"}}', NULL, '2026-01-19 12:33:40', '2026-01-19 12:33:40'),
(15, 'module', 'Module updated', 'App\\Models\\Module', 'updated', 27, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"صيدلة\", \"name_en\": \"Pharmacy\", \"sort_order\": 4, \"description_ar\": \"وصف الصيدلة\", \"description_en\": \"description of pharmacy\"}, \"attributes\": {\"name_ar\": \"مكوك هيلثى\", \"name_en\": \"makook healthy\", \"sort_order\": 3, \"description_ar\": \"مكوك هيلثى\", \"description_en\": \"makook healthy\"}}', NULL, '2026-01-19 12:34:43', '2026-01-19 12:34:43'),
(16, 'admin', 'Admin logged out', 'App\\Models\\Admin', NULL, 1, 'App\\Models\\Admin', 1, '[]', NULL, '2026-01-20 06:42:28', '2026-01-20 06:42:28'),
(17, 'admin', 'Admin logged in', 'App\\Models\\Admin', NULL, 1, 'App\\Models\\Admin', 1, '{\"ip_address\": \"156.215.16.141\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', NULL, '2026-01-20 06:42:31', '2026-01-20 06:42:31'),
(18, 'user', 'User logged in', 'App\\Models\\User', NULL, 3, 'App\\Models\\User', 3, '{\"ip_address\": \"197.39.17.7\", \"user_agent\": \"Dart/3.9 (dart:io)\"}', NULL, '2026-01-20 06:48:04', '2026-01-20 06:48:04'),
(19, 'category', 'Category created', 'App\\Models\\Category', 'created', 59, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"برجر\", \"name_en\": \"Burger\", \"is_active\": true, \"sort_order\": 1, \"description_ar\": \"برجر\", \"description_en\": \"Burger\"}}', NULL, '2026-01-20 07:35:13', '2026-01-20 07:35:13'),
(20, 'category', 'Category created', 'App\\Models\\Category', 'created', 60, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"برجر\", \"name_en\": \"Burger\", \"is_active\": true, \"sort_order\": 1, \"description_ar\": \"برجر\", \"description_en\": \"Burger\"}}', NULL, '2026-01-20 07:35:20', '2026-01-20 07:35:20'),
(21, 'category', 'Category created', 'App\\Models\\Category', 'created', 61, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"برجر\", \"name_en\": \"Burger\", \"is_active\": true, \"sort_order\": 1, \"description_ar\": \"برجر\", \"description_en\": \"Burger\"}}', NULL, '2026-01-20 07:35:36', '2026-01-20 07:35:36'),
(22, 'product', 'Product created', 'App\\Models\\Product', 'created', 73, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"لفقثافقاقف\", \"name_en\": \"Xavier Guy\", \"is_active\": true, \"base_price\": \"50.00\", \"category_id\": 61, \"description_ar\": \"افقافقافقافق\", \"description_en\": \"Minim vitae velit co\", \"subcategory_id\": null}}', NULL, '2026-01-20 08:53:00', '2026-01-20 08:53:00'),
(23, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 444, 'App\\Models\\User', 6, '{\"attributes\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-21 06:49:54', '2026-01-21 06:49:54'),
(24, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 648, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 444, \"quantity\": 1, \"product_id\": 73}}', NULL, '2026-01-21 06:49:54', '2026-01-21 06:49:54'),
(25, 'default', 'Order created', 'App\\Models\\Order', 'created', 219, 'App\\Models\\User', 6, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-21 06:50:20', '2026-01-21 06:50:20'),
(26, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 250, 'App\\Models\\User', 6, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"50.00\", \"total_price\": \"50.00\"}}', NULL, '2026-01-21 06:50:20', '2026-01-21 06:50:20'),
(27, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 444, 'App\\Models\\User', 6, '{\"old\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-21 06:50:20', '2026-01-21 06:50:20'),
(28, 'default', 'Order created', 'App\\Models\\Order', NULL, 219, 'App\\Models\\User', 6, '{\"total\": 50, \"order_number\": \"ORD-20260121-00001\", \"payment_method\": \"cash\"}', NULL, '2026-01-21 06:50:20', '2026-01-21 06:50:20'),
(29, 'category', 'Category created', 'App\\Models\\Category', 'created', 62, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"مكوك سندوتش\", \"name_en\": \"makook sandwitch\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"مكوك سندوتش\", \"description_en\": \"makook sandwitch\"}}', NULL, '2026-01-21 11:15:44', '2026-01-21 11:15:44'),
(30, 'product', 'Product created', 'App\\Models\\Product', 'created', 74, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"مكوك سندوتش\", \"name_en\": \"makook sandwitch\", \"is_active\": true, \"base_price\": \"0.00\", \"category_id\": 62, \"description_ar\": \"مكوك سندوتش\", \"description_en\": \"makook sandwitch\", \"subcategory_id\": null}}', NULL, '2026-01-21 11:16:25', '2026-01-21 11:16:25'),
(31, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 445, 'App\\Models\\User', 6, '{\"attributes\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-21 11:49:12', '2026-01-21 11:49:12'),
(32, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 649, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 445, \"quantity\": 1, \"product_id\": 73}}', NULL, '2026-01-21 11:49:12', '2026-01-21 11:49:12'),
(33, 'admin', 'Admin logged in', 'App\\Models\\Admin', NULL, 1, 'App\\Models\\Admin', 1, '{\"ip_address\": \"156.196.177.251\", \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36\"}', NULL, '2026-01-22 05:26:09', '2026-01-22 05:26:09'),
(34, 'category', 'Category created', 'App\\Models\\Category', 'created', 63, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 06:33:38', '2026-01-22 06:33:38'),
(35, 'category', 'Category created', 'App\\Models\\Category', 'created', 64, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 06:33:45', '2026-01-22 06:33:45'),
(36, 'category', 'Category created', 'App\\Models\\Category', 'created', 65, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 06:33:58', '2026-01-22 06:33:58'),
(37, 'category', 'Category created', 'App\\Models\\Category', 'created', 66, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 06:35:00', '2026-01-22 06:35:00'),
(38, 'category', 'Category created', 'App\\Models\\Category', 'created', 67, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 06:36:30', '2026-01-22 06:36:30'),
(39, 'category', 'Category created', 'App\\Models\\Category', 'created', 68, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"برلبلب\", \"name_en\": \"Main Dishes\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"بلبلبلبلب\", \"description_en\": \"sdd\"}}', NULL, '2026-01-22 07:12:24', '2026-01-22 07:12:24'),
(40, 'category', 'Category deleted', 'App\\Models\\Category', 'deleted', 68, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"برلبلب\", \"name_en\": \"Main Dishes\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"بلبلبلبلب\", \"description_en\": \"sdd\"}}', NULL, '2026-01-22 07:46:20', '2026-01-22 07:46:20'),
(41, 'category', 'Category deleted', 'App\\Models\\Category', 'deleted', 67, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 07:46:35', '2026-01-22 07:46:35'),
(42, 'category', 'Category deleted', 'App\\Models\\Category', 'deleted', 66, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 07:46:42', '2026-01-22 07:46:42'),
(43, 'category', 'Category deleted', 'App\\Models\\Category', 'deleted', 65, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 07:46:53', '2026-01-22 07:46:53'),
(44, 'category', 'Category deleted', 'App\\Models\\Category', 'deleted', 64, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 07:46:56', '2026-01-22 07:46:56'),
(45, 'category', 'Category deleted', 'App\\Models\\Category', 'deleted', 63, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما يخص الالبان ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 07:46:58', '2026-01-22 07:46:58'),
(46, 'category', 'Category deleted', 'App\\Models\\Category', 'deleted', 61, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"برجر\", \"name_en\": \"Burger\", \"is_active\": true, \"sort_order\": 1, \"description_ar\": \"برجر\", \"description_en\": \"Burger\"}}', NULL, '2026-01-22 07:47:10', '2026-01-22 07:47:10'),
(47, 'category', 'Category deleted', 'App\\Models\\Category', 'deleted', 60, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"برجر\", \"name_en\": \"Burger\", \"is_active\": true, \"sort_order\": 1, \"description_ar\": \"برجر\", \"description_en\": \"Burger\"}}', NULL, '2026-01-22 07:47:12', '2026-01-22 07:47:12'),
(48, 'category', 'Category deleted', 'App\\Models\\Category', 'deleted', 59, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"برجر\", \"name_en\": \"Burger\", \"is_active\": true, \"sort_order\": 1, \"description_ar\": \"برجر\", \"description_en\": \"Burger\"}}', NULL, '2026-01-22 07:47:15', '2026-01-22 07:47:15'),
(49, 'category', 'Category created', 'App\\Models\\Category', 'created', 69, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"قسم الالبان\", \"name_en\": \"Dairy section\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"كل ما تحتاجه ستجده هنا\", \"description_en\": \"Everything related to dairy products can be found here.\"}}', NULL, '2026-01-22 07:49:47', '2026-01-22 07:49:47'),
(50, 'admin', 'Admin logged in', 'App\\Models\\Admin', NULL, 1, 'App\\Models\\Admin', 1, '{\"ip_address\": \"154.176.34.38\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36\"}', NULL, '2026-01-22 11:17:00', '2026-01-22 11:17:00'),
(51, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 650, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 445, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-22 14:24:36', '2026-01-22 14:24:36'),
(52, 'product', 'Product created', 'App\\Models\\Product', 'created', 75, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"جبنه بيضه\", \"name_en\": \"white cheese\", \"is_active\": true, \"base_price\": \"80.00\", \"category_id\": 69, \"description_ar\": \"جبنه بيضه ملح خفيف\", \"description_en\": \"white cheese with low salt\", \"subcategory_id\": null}}', NULL, '2026-01-23 06:11:56', '2026-01-23 06:11:56'),
(53, 'product', 'Product created', 'App\\Models\\Product', 'created', 76, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"جبنه رومي\", \"name_en\": \"Rumi cheese\", \"is_active\": true, \"base_price\": \"240.00\", \"category_id\": 69, \"description_ar\": \"جبنه رةمي قديمه بطارخ\", \"description_en\": \"Rumi cheese (Old Roman cheese)\", \"subcategory_id\": null}}', NULL, '2026-01-23 06:14:59', '2026-01-23 06:14:59'),
(54, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 649, 'App\\Models\\User', 6, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 06:20:08', '2026-01-23 06:20:08'),
(55, 'category', 'Category created', 'App\\Models\\Category', 'created', 70, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"وجبات فطار صحيه\", \"name_en\": \"Healthy breakfast meals\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"الوجبات تحتوي علي (بيض - فول - فلفل -خضار-جبنه )\", \"description_en\": \"meals contain (\\r\\nEggs - Beans - Peppers - Vegetables - Cheese)\"}}', NULL, '2026-01-23 06:22:42', '2026-01-23 06:22:42'),
(56, 'product', 'Product created', 'App\\Models\\Product', 'created', 77, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"طبق الجامبو\", \"name_en\": \"jambo dish\", \"is_active\": true, \"base_price\": \"130.00\", \"category_id\": 70, \"description_ar\": \"يحتوي علي (بيض - جزر - جبنه - خيار - بطاطا)\", \"description_en\": \"contain (eggs - carrots - cheese -Cucumber - potato)\", \"subcategory_id\": null}}', NULL, '2026-01-23 06:28:03', '2026-01-23 06:28:03'),
(57, 'user', 'User logged out', 'App\\Models\\User', NULL, 3, 'App\\Models\\User', 3, '[]', NULL, '2026-01-23 06:38:21', '2026-01-23 06:38:21'),
(58, 'product', 'Product deleted', 'App\\Models\\Product', 'deleted', 73, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"لفقثافقاقف\", \"name_en\": \"Xavier Guy\", \"is_active\": true, \"base_price\": \"50.00\", \"category_id\": 61, \"description_ar\": \"افقافقافقافق\", \"description_en\": \"Minim vitae velit co\", \"subcategory_id\": null}}', NULL, '2026-01-23 06:39:33', '2026-01-23 06:39:33'),
(59, 'product', 'Product deleted', 'App\\Models\\Product', 'deleted', 74, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"مكوك سندوتش\", \"name_en\": \"makook sandwitch\", \"is_active\": true, \"base_price\": \"0.00\", \"category_id\": 62, \"description_ar\": \"مكوك سندوتش\", \"description_en\": \"makook sandwitch\", \"subcategory_id\": null}}', NULL, '2026-01-23 06:39:37', '2026-01-23 06:39:37'),
(60, 'product', 'Product created', 'App\\Models\\Product', 'created', 78, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"مكوك سندوتش\", \"name_en\": \"makook sandwitch\", \"is_active\": true, \"base_price\": \"0.00\", \"category_id\": 62, \"description_ar\": \"مكوك سندوتش\", \"description_en\": \"makook sandwitch\", \"subcategory_id\": null}}', NULL, '2026-01-23 06:48:55', '2026-01-23 06:48:55'),
(61, 'product', 'Product deleted', 'App\\Models\\Product', 'deleted', 74, 'App\\Models\\Admin', 1, '{\"old\": {\"name_ar\": \"مكوك سندوتش\", \"name_en\": \"makook sandwitch\", \"is_active\": true, \"base_price\": \"0.00\", \"category_id\": 62, \"description_ar\": \"مكوك سندوتش\", \"description_en\": \"makook sandwitch\", \"subcategory_id\": null}}', NULL, '2026-01-23 06:49:57', '2026-01-23 06:49:57'),
(62, 'user', 'User logged in', 'App\\Models\\User', NULL, 3, 'App\\Models\\User', 3, '{\"ip_address\": \"197.39.17.7\", \"user_agent\": \"Dart/3.9 (dart:io)\"}', NULL, '2026-01-23 06:50:44', '2026-01-23 06:50:44'),
(63, 'category', 'Category created', 'App\\Models\\Category', 'created', 71, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"بيتزا\", \"name_en\": \"pizza\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": null, \"description_en\": null}}', NULL, '2026-01-23 06:53:46', '2026-01-23 06:53:46'),
(64, 'product', 'Product created', 'App\\Models\\Product', 'created', 79, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"بيتزا جبنه تقليديه\", \"name_en\": \"classic cheese pizza\", \"is_active\": true, \"base_price\": \"190.00\", \"category_id\": 71, \"description_ar\": \"جبنه زياده\", \"description_en\": \"extra cheese\", \"subcategory_id\": null}}', NULL, '2026-01-23 06:56:29', '2026-01-23 06:56:29'),
(65, 'user_address', 'User address updated', 'App\\Models\\UserAddress', 'updated', 163, 'App\\Models\\User', 3, '{\"old\": {\"is_default\": false}, \"attributes\": {\"is_default\": true}}', NULL, '2026-01-23 07:01:15', '2026-01-23 07:01:15'),
(66, 'user_address', 'User updated address', 'App\\Models\\UserAddress', NULL, 163, 'App\\Models\\User', 3, '{\"user_name\": \"aya\", \"ip_address\": \"197.39.17.7\", \"new_values\": {\"id\": 163, \"phone\": \"0109668688\", \"user_id\": 3, \"landmark\": null, \"latitude\": \"30.00060758\", \"longitude\": \"31.11462235\", \"created_at\": \"2026-01-13T09:07:12.000000Z\", \"deleted_at\": null, \"is_default\": true, \"updated_at\": \"2026-01-23T09:01:15.000000Z\", \"street_name\": \"الهرم\", \"address_name\": \"apartment\", \"address_type\": \"office\", \"floor_number\": \"22\", \"building_name\": \"فندق بيراميدز\", \"apartment_number\": \"22\"}, \"old_values\": {\"id\": 163, \"phone\": \"0109668688\", \"user_id\": 3, \"landmark\": null, \"latitude\": \"30.00060758\", \"longitude\": \"31.11462235\", \"created_at\": \"2026-01-13T09:07:12.000000Z\", \"deleted_at\": null, \"is_default\": false, \"updated_at\": \"2026-01-19T08:59:27.000000Z\", \"street_name\": \"الهرم\", \"address_name\": \"apartment\", \"address_type\": \"office\", \"floor_number\": \"22\", \"building_name\": \"فندق بيراميدز\", \"apartment_number\": \"22\"}, \"user_agent\": \"Dart/3.9 (dart:io)\", \"user_email\": \"essam@digitalvibesmarketing.com\"}', NULL, '2026-01-23 07:01:15', '2026-01-23 07:01:15'),
(67, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 446, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:02:00', '2026-01-23 07:02:00'),
(68, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 651, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 446, \"quantity\": 1, \"product_id\": 79}}', NULL, '2026-01-23 07:02:00', '2026-01-23 07:02:00'),
(69, 'default', 'Order created', 'App\\Models\\Order', 'created', 220, 'App\\Models\\User', 3, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 07:02:17', '2026-01-23 07:02:17'),
(70, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 251, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"190.00\", \"total_price\": \"190.00\"}}', NULL, '2026-01-23 07:02:17', '2026-01-23 07:02:17'),
(71, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 446, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:02:17', '2026-01-23 07:02:17'),
(72, 'default', 'Order created', 'App\\Models\\Order', NULL, 220, 'App\\Models\\User', 3, '{\"total\": 190, \"order_number\": \"ORD-20260123-00001\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 07:02:17', '2026-01-23 07:02:17'),
(73, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 447, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:03:42', '2026-01-23 07:03:42'),
(74, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 652, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 447, \"quantity\": 3, \"product_id\": 79}}', NULL, '2026-01-23 07:03:42', '2026-01-23 07:03:42'),
(75, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 652, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 3}, \"attributes\": {\"quantity\": 6}}', NULL, '2026-01-23 07:04:18', '2026-01-23 07:04:18'),
(76, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 652, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 447, \"quantity\": 6, \"product_id\": 79}}', NULL, '2026-01-23 07:04:28', '2026-01-23 07:04:28'),
(77, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 447, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:04:28', '2026-01-23 07:04:28'),
(78, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 448, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:04:35', '2026-01-23 07:04:35'),
(79, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 653, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 448, \"quantity\": 3, \"product_id\": 79}}', NULL, '2026-01-23 07:04:35', '2026-01-23 07:04:35'),
(80, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 653, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 3}, \"attributes\": {\"quantity\": 4}}', NULL, '2026-01-23 07:04:47', '2026-01-23 07:04:47'),
(81, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 653, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 4}, \"attributes\": {\"quantity\": 8}}', NULL, '2026-01-23 07:04:59', '2026-01-23 07:04:59'),
(82, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 653, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 8}, \"attributes\": {\"quantity\": 11}}', NULL, '2026-01-23 07:05:05', '2026-01-23 07:05:05'),
(83, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 653, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 11}, \"attributes\": {\"quantity\": 5}}', NULL, '2026-01-23 07:05:13', '2026-01-23 07:05:13'),
(84, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 653, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 5}, \"attributes\": {\"quantity\": 6}}', NULL, '2026-01-23 07:05:19', '2026-01-23 07:05:19'),
(85, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 653, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 6}, \"attributes\": {\"quantity\": 7}}', NULL, '2026-01-23 07:05:19', '2026-01-23 07:05:19'),
(86, 'product', 'Product created', 'App\\Models\\Product', 'created', 80, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"بيتزا استربس\", \"name_en\": \"strebs chicken pizza\", \"is_active\": true, \"base_price\": \"200.01\", \"category_id\": 71, \"description_ar\": \"بيتزا استربس تحتوي علي (استربس - جبنه - صوص - ماشروم - طماطم )\", \"description_en\": \"strebs chicken pizza contain (strebs -chees - sos -tomato - mashroum)\", \"subcategory_id\": null}}', NULL, '2026-01-23 07:11:27', '2026-01-23 07:11:27'),
(87, 'default', 'Order created', 'App\\Models\\Order', 'created', 221, 'App\\Models\\User', 3, '{\"attributes\": {\"notes\": \"Payment processed via Paymob\", \"order_status\": \"pending_payment\", \"payment_status\": \"pending\", \"payment_reference\": \"PAY-PLACEHOLDER-69733BD18A883\"}}', NULL, '2026-01-23 07:13:53', '2026-01-23 07:13:53'),
(88, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 252, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 7, \"unit_price\": \"190.00\", \"total_price\": \"1330.00\"}}', NULL, '2026-01-23 07:13:53', '2026-01-23 07:13:53'),
(89, 'default', 'Payment created', 'App\\Models\\Payment', 'created', 50, 'App\\Models\\User', 3, '{\"attributes\": {\"status\": \"completed\", \"success\": true, \"amount_cents\": 133000, \"transaction_id\": \"401942143\"}}', NULL, '2026-01-23 07:13:53', '2026-01-23 07:13:53'),
(90, 'default', 'Order updated', 'App\\Models\\Order', 'updated', 221, 'App\\Models\\User', 3, '{\"old\": {\"order_status\": \"pending_payment\", \"payment_status\": \"pending\", \"payment_reference\": \"PAY-PLACEHOLDER-69733BD18A883\"}, \"attributes\": {\"order_status\": \"confirmed\", \"payment_status\": \"paid\", \"payment_reference\": \"401942143\"}}', NULL, '2026-01-23 07:13:53', '2026-01-23 07:13:53'),
(91, 'default', 'Payment processed during checkout', 'App\\Models\\Payment', NULL, 50, 'App\\Models\\User', 3, '{\"success\": true, \"order_id\": 221, \"amount_cents\": 133000, \"order_number\": \"ORD-20260123-00002\", \"transaction_id\": \"401942143\"}', NULL, '2026-01-23 07:13:53', '2026-01-23 07:13:53'),
(92, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 448, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:13:53', '2026-01-23 07:13:53'),
(93, 'default', 'Order created', 'App\\Models\\Order', NULL, 221, 'App\\Models\\User', 3, '{\"total\": 1330, \"order_number\": \"ORD-20260123-00002\", \"payment_method\": \"online\"}', NULL, '2026-01-23 07:13:53', '2026-01-23 07:13:53'),
(94, 'product', 'Product updated', 'App\\Models\\Product', 'updated', 80, 'App\\Models\\Admin', 1, '{\"old\": {\"base_price\": \"200.01\"}, \"attributes\": {\"base_price\": \"200.00\"}}', NULL, '2026-01-23 07:18:03', '2026-01-23 07:18:03'),
(95, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 449, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:18:22', '2026-01-23 07:18:22'),
(96, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 654, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 449, \"quantity\": 3, \"product_id\": 80}}', NULL, '2026-01-23 07:18:22', '2026-01-23 07:18:22'),
(97, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 655, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 449, \"quantity\": 1, \"product_id\": 79}}', NULL, '2026-01-23 07:19:44', '2026-01-23 07:19:44'),
(98, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 654, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 3}, \"attributes\": {\"quantity\": 7}}', NULL, '2026-01-23 07:20:22', '2026-01-23 07:20:22'),
(99, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 655, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 449, \"quantity\": 1, \"product_id\": 79}}', NULL, '2026-01-23 07:20:25', '2026-01-23 07:20:25'),
(100, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 654, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 449, \"quantity\": 7, \"product_id\": 80}}', NULL, '2026-01-23 07:20:26', '2026-01-23 07:20:26'),
(101, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 449, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:20:26', '2026-01-23 07:20:26'),
(102, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 450, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:20:32', '2026-01-23 07:20:32'),
(103, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 656, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 450, \"quantity\": 3, \"product_id\": 80}}', NULL, '2026-01-23 07:20:32', '2026-01-23 07:20:32'),
(104, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 656, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 3}, \"attributes\": {\"quantity\": 4}}', NULL, '2026-01-23 07:20:36', '2026-01-23 07:20:36'),
(105, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 657, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 445, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:21:01', '2026-01-23 07:21:01'),
(106, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 656, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 4}, \"attributes\": {\"quantity\": 5}}', NULL, '2026-01-23 07:27:36', '2026-01-23 07:27:36'),
(107, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 656, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 5}, \"attributes\": {\"quantity\": 6}}', NULL, '2026-01-23 07:27:36', '2026-01-23 07:27:36'),
(108, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 656, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 6}, \"attributes\": {\"quantity\": 7}}', NULL, '2026-01-23 07:27:37', '2026-01-23 07:27:37'),
(109, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 656, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 7}, \"attributes\": {\"quantity\": 8}}', NULL, '2026-01-23 07:27:37', '2026-01-23 07:27:37'),
(110, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 656, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 8}, \"attributes\": {\"quantity\": 9}}', NULL, '2026-01-23 07:27:38', '2026-01-23 07:27:38'),
(111, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 656, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 9}, \"attributes\": {\"quantity\": 10}}', NULL, '2026-01-23 07:27:38', '2026-01-23 07:27:38'),
(112, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 656, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 10}, \"attributes\": {\"quantity\": 11}}', NULL, '2026-01-23 07:27:38', '2026-01-23 07:27:38'),
(113, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 656, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 11}, \"attributes\": {\"quantity\": 10}}', NULL, '2026-01-23 07:27:39', '2026-01-23 07:27:39'),
(114, 'default', 'Order created', 'App\\Models\\Order', 'created', 222, 'App\\Models\\User', 3, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 07:27:59', '2026-01-23 07:27:59'),
(115, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 253, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 10, \"unit_price\": \"245.00\", \"total_price\": \"2450.00\"}}', NULL, '2026-01-23 07:27:59', '2026-01-23 07:27:59'),
(116, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 450, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:27:59', '2026-01-23 07:27:59'),
(117, 'default', 'Order created', 'App\\Models\\Order', NULL, 222, 'App\\Models\\User', 3, '{\"total\": 2450, \"order_number\": \"ORD-20260123-00003\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 07:27:59', '2026-01-23 07:27:59'),
(118, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 451, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:28:51', '2026-01-23 07:28:51'),
(119, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 658, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 451, \"quantity\": 1, \"product_id\": 79}}', NULL, '2026-01-23 07:28:51', '2026-01-23 07:28:51'),
(120, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 659, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 451, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:28:58', '2026-01-23 07:28:58'),
(121, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 660, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 451, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:30:26', '2026-01-23 07:30:26'),
(122, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 658, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 451, \"quantity\": 1, \"product_id\": 79}}', NULL, '2026-01-23 07:30:34', '2026-01-23 07:30:34'),
(123, 'user', 'User logged in', 'App\\Models\\User', NULL, 1, 'App\\Models\\User', 1, '{\"ip_address\": \"156.215.210.128\", \"user_agent\": \"PostmanRuntime/7.51.0\"}', NULL, '2026-01-23 07:30:34', '2026-01-23 07:30:34'),
(124, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 659, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 451, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:30:36', '2026-01-23 07:30:36'),
(125, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 660, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 451, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:30:37', '2026-01-23 07:30:37'),
(126, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 451, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:30:37', '2026-01-23 07:30:37'),
(127, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 452, 'App\\Models\\User', 1, '{\"attributes\": {\"user_id\": 1, \"store_id\": null}}', NULL, '2026-01-23 07:30:50', '2026-01-23 07:30:50'),
(128, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 661, 'App\\Models\\User', 1, '{\"attributes\": {\"cart_id\": 452, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:30:50', '2026-01-23 07:30:50'),
(129, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 453, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:30:54', '2026-01-23 07:30:54'),
(130, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 662, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 453, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:30:54', '2026-01-23 07:30:54'),
(131, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 663, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 453, \"quantity\": 2, \"product_id\": 79}}', NULL, '2026-01-23 07:31:03', '2026-01-23 07:31:03'),
(132, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 662, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 07:31:25', '2026-01-23 07:31:25'),
(133, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 662, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 453, \"quantity\": 2, \"product_id\": 80}}', NULL, '2026-01-23 07:31:32', '2026-01-23 07:31:32'),
(134, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 664, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 453, \"quantity\": 3, \"product_id\": 80}}', NULL, '2026-01-23 07:31:54', '2026-01-23 07:31:54'),
(135, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 665, 'App\\Models\\User', 1, '{\"attributes\": {\"cart_id\": 452, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:31:57', '2026-01-23 07:31:57'),
(136, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 663, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 2}, \"attributes\": {\"quantity\": 1}}', NULL, '2026-01-23 07:32:00', '2026-01-23 07:32:00'),
(137, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 663, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 3}}', NULL, '2026-01-23 07:32:26', '2026-01-23 07:32:26'),
(138, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 663, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 3}, \"attributes\": {\"quantity\": 4}}', NULL, '2026-01-23 07:32:32', '2026-01-23 07:32:32'),
(139, 'default', 'Order created', 'App\\Models\\Order', 'created', 223, 'App\\Models\\User', 3, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 07:33:01', '2026-01-23 07:33:01'),
(140, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 254, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 4, \"unit_price\": \"190.00\", \"total_price\": \"760.00\"}}', NULL, '2026-01-23 07:33:01', '2026-01-23 07:33:01'),
(141, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 255, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 3, \"unit_price\": \"245.00\", \"total_price\": \"735.00\"}}', NULL, '2026-01-23 07:33:01', '2026-01-23 07:33:01'),
(142, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 453, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:33:01', '2026-01-23 07:33:01'),
(143, 'default', 'Order created', 'App\\Models\\Order', NULL, 223, 'App\\Models\\User', 3, '{\"total\": 1495, \"order_number\": \"ORD-20260123-00004\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 07:33:01', '2026-01-23 07:33:01'),
(144, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 454, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 07:36:33', '2026-01-23 07:36:33'),
(145, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 666, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 454, \"quantity\": 1, \"product_id\": 79}}', NULL, '2026-01-23 07:36:33', '2026-01-23 07:36:33'),
(146, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 666, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 07:36:51', '2026-01-23 07:36:51'),
(147, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 667, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 445, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:42:39', '2026-01-23 07:42:39'),
(148, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 657, 'App\\Models\\User', 6, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 07:42:48', '2026-01-23 07:42:48'),
(149, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 668, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 445, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:44:03', '2026-01-23 07:44:03'),
(150, 'category', 'Category created', 'App\\Models\\Category', 'created', 72, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"عصاير فريش\", \"name_en\": \"fresh juice\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": null, \"description_en\": null}}', NULL, '2026-01-23 07:45:23', '2026-01-23 07:45:23'),
(151, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 669, 'App\\Models\\User', 1, '{\"attributes\": {\"cart_id\": 452, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:45:41', '2026-01-23 07:45:41'),
(152, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 670, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 445, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:45:45', '2026-01-23 07:45:45'),
(153, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 671, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 445, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:45:56', '2026-01-23 07:45:56'),
(154, 'product', 'Product created', 'App\\Models\\Product', 'created', 81, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"عصير برتقال\", \"name_en\": \"orange juice\", \"is_active\": true, \"base_price\": \"55.00\", \"category_id\": 72, \"description_ar\": \"عصير برتقال بدون سكر\", \"description_en\": \"orange juice without sugar\", \"subcategory_id\": null}}', NULL, '2026-01-23 07:47:00', '2026-01-23 07:47:00'),
(155, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 672, 'App\\Models\\User', 1, '{\"attributes\": {\"cart_id\": 452, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:47:03', '2026-01-23 07:47:03'),
(156, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 673, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 445, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:47:23', '2026-01-23 07:47:23'),
(157, 'category', 'Category created', 'App\\Models\\Category', 'created', 73, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"بيبيسي\", \"name_en\": \"pepsi\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": null, \"description_en\": null}}', NULL, '2026-01-23 07:47:54', '2026-01-23 07:47:54'),
(158, 'product', 'Product created', 'App\\Models\\Product', 'created', 82, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"كولا\", \"name_en\": \"cola\", \"is_active\": true, \"base_price\": \"25.00\", \"category_id\": 73, \"description_ar\": \"كولا دايت\", \"description_en\": \"cola diet\", \"subcategory_id\": null}}', NULL, '2026-01-23 07:49:22', '2026-01-23 07:49:22'),
(159, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 455, 'App\\Models\\User', 6, '{\"attributes\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 07:51:45', '2026-01-23 07:51:45'),
(160, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 674, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 455, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:51:45', '2026-01-23 07:51:45'),
(161, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 675, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 455, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:51:49', '2026-01-23 07:51:49'),
(162, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 676, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 455, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:51:50', '2026-01-23 07:51:50'),
(163, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 677, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 455, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 07:52:23', '2026-01-23 07:52:23'),
(164, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 678, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 454, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:41:23', '2026-01-23 08:41:23'),
(165, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 666, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 454, \"quantity\": 2, \"product_id\": 79}}', NULL, '2026-01-23 08:41:30', '2026-01-23 08:41:30'),
(166, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 679, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 454, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:42:02', '2026-01-23 08:42:02'),
(167, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 680, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 454, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:42:17', '2026-01-23 08:42:17'),
(168, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 678, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 08:42:26', '2026-01-23 08:42:26'),
(169, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 680, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 08:42:33', '2026-01-23 08:42:33'),
(170, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 680, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 2}, \"attributes\": {\"quantity\": 1}}', NULL, '2026-01-23 08:42:35', '2026-01-23 08:42:35'),
(171, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 681, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 454, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:43:03', '2026-01-23 08:43:03'),
(172, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 681, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 08:45:07', '2026-01-23 08:45:07'),
(173, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 681, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 2}, \"attributes\": {\"quantity\": 3}}', NULL, '2026-01-23 08:45:08', '2026-01-23 08:45:08'),
(174, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 681, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 3}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 08:45:10', '2026-01-23 08:45:10'),
(175, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 681, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 2}, \"attributes\": {\"quantity\": 1}}', NULL, '2026-01-23 08:45:10', '2026-01-23 08:45:10'),
(176, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 681, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 454, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:45:11', '2026-01-23 08:45:11'),
(177, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 679, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 454, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:45:12', '2026-01-23 08:45:12'),
(178, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 678, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 2}, \"attributes\": {\"quantity\": 1}}', NULL, '2026-01-23 08:45:55', '2026-01-23 08:45:55'),
(179, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 678, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 08:46:00', '2026-01-23 08:46:00'),
(180, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 680, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 08:46:02', '2026-01-23 08:46:02'),
(181, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 680, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 2}, \"attributes\": {\"quantity\": 3}}', NULL, '2026-01-23 08:46:08', '2026-01-23 08:46:08'),
(182, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 682, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 454, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:46:20', '2026-01-23 08:46:20'),
(183, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 682, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 08:46:43', '2026-01-23 08:46:43');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(184, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 680, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 3}, \"attributes\": {\"quantity\": 1}}', NULL, '2026-01-23 08:46:47', '2026-01-23 08:46:47'),
(185, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 682, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 454, \"quantity\": 2, \"product_id\": 74}}', NULL, '2026-01-23 08:47:05', '2026-01-23 08:47:05'),
(186, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 680, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 454, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:47:07', '2026-01-23 08:47:07'),
(187, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 678, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 454, \"quantity\": 2, \"product_id\": 74}}', NULL, '2026-01-23 08:47:08', '2026-01-23 08:47:08'),
(188, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 454, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 08:47:08', '2026-01-23 08:47:08'),
(189, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 456, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 08:47:31', '2026-01-23 08:47:31'),
(190, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 683, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 456, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:47:31', '2026-01-23 08:47:31'),
(191, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 684, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 456, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:47:49', '2026-01-23 08:47:49'),
(192, 'default', 'Order created', 'App\\Models\\Order', 'created', 224, 'App\\Models\\User', 3, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 08:48:47', '2026-01-23 08:48:47'),
(193, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 256, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"30.00\", \"total_price\": \"30.00\"}}', NULL, '2026-01-23 08:48:47', '2026-01-23 08:48:47'),
(194, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 257, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"50.00\", \"total_price\": \"50.00\"}}', NULL, '2026-01-23 08:48:47', '2026-01-23 08:48:47'),
(195, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 456, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 08:48:47', '2026-01-23 08:48:47'),
(196, 'default', 'Order created', 'App\\Models\\Order', NULL, 224, 'App\\Models\\User', 3, '{\"total\": 80, \"order_number\": \"ORD-20260123-00005\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 08:48:47', '2026-01-23 08:48:47'),
(197, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 457, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 08:58:42', '2026-01-23 08:58:42'),
(198, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 685, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 457, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 08:58:42', '2026-01-23 08:58:42'),
(199, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 685, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 08:58:48', '2026-01-23 08:58:48'),
(200, 'admin', 'Admin logged in', 'App\\Models\\Admin', NULL, 1, 'App\\Models\\Admin', 1, '{\"ip_address\": \"41.232.7.120\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36\"}', NULL, '2026-01-23 09:09:08', '2026-01-23 09:09:08'),
(201, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 686, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 455, \"quantity\": 1, \"product_id\": 82}}', NULL, '2026-01-23 10:37:42', '2026-01-23 10:37:42'),
(202, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 687, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 455, \"quantity\": 1, \"product_id\": 77}}', NULL, '2026-01-23 10:38:04', '2026-01-23 10:38:04'),
(203, 'user', 'User logged out', 'App\\Models\\User', NULL, 6, 'App\\Models\\User', 6, '[]', NULL, '2026-01-23 10:39:43', '2026-01-23 10:39:43'),
(204, 'user', 'User logged in', 'App\\Models\\User', NULL, 3, 'App\\Models\\User', 3, '{\"ip_address\": \"197.39.17.7\", \"user_agent\": \"Dart/3.9 (dart:io)\"}', NULL, '2026-01-23 10:40:07', '2026-01-23 10:40:07'),
(205, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 688, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 457, \"quantity\": 1, \"product_id\": 77}}', NULL, '2026-01-23 10:40:15', '2026-01-23 10:40:15'),
(206, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 685, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 457, \"quantity\": 2, \"product_id\": 74}}', NULL, '2026-01-23 10:40:19', '2026-01-23 10:40:19'),
(207, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 688, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 10:40:21', '2026-01-23 10:40:21'),
(208, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 688, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 2}, \"attributes\": {\"quantity\": 3}}', NULL, '2026-01-23 10:40:28', '2026-01-23 10:40:28'),
(209, 'default', 'Order created', 'App\\Models\\Order', 'created', 225, 'App\\Models\\User', 3, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 10:40:44', '2026-01-23 10:40:44'),
(210, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 258, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 3, \"unit_price\": \"130.00\", \"total_price\": \"390.00\"}}', NULL, '2026-01-23 10:40:44', '2026-01-23 10:40:44'),
(211, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 457, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 10:40:44', '2026-01-23 10:40:44'),
(212, 'default', 'Order created', 'App\\Models\\Order', NULL, 225, 'App\\Models\\User', 3, '{\"total\": 390, \"order_number\": \"ORD-20260123-00006\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 10:40:44', '2026-01-23 10:40:44'),
(213, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 458, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 10:41:19', '2026-01-23 10:41:19'),
(214, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 689, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 458, \"quantity\": 1, \"product_id\": 82}}', NULL, '2026-01-23 10:41:19', '2026-01-23 10:41:19'),
(215, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 689, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 10:41:37', '2026-01-23 10:41:37'),
(216, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 689, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 2}, \"attributes\": {\"quantity\": 3}}', NULL, '2026-01-23 10:41:41', '2026-01-23 10:41:41'),
(217, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 689, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 3}, \"attributes\": {\"quantity\": 4}}', NULL, '2026-01-23 10:41:52', '2026-01-23 10:41:52'),
(218, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 689, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 458, \"quantity\": 4, \"product_id\": 82}}', NULL, '2026-01-23 10:50:29', '2026-01-23 10:50:29'),
(219, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 458, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 10:50:29', '2026-01-23 10:50:29'),
(220, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 459, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 10:50:50', '2026-01-23 10:50:50'),
(221, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 690, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 459, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 10:50:50', '2026-01-23 10:50:50'),
(222, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 691, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 459, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 10:59:43', '2026-01-23 10:59:43'),
(223, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 690, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 459, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 10:59:47', '2026-01-23 10:59:47'),
(224, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 692, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 459, \"quantity\": 1, \"product_id\": 77}}', NULL, '2026-01-23 11:00:11', '2026-01-23 11:00:11'),
(225, 'default', 'Order created', 'App\\Models\\Order', 'created', 226, 'App\\Models\\User', 3, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 11:01:19', '2026-01-23 11:01:19'),
(226, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 259, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"50.00\", \"total_price\": \"50.00\"}}', NULL, '2026-01-23 11:01:19', '2026-01-23 11:01:19'),
(227, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 260, 'App\\Models\\User', 3, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"170.00\", \"total_price\": \"170.00\"}}', NULL, '2026-01-23 11:01:19', '2026-01-23 11:01:19'),
(228, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 459, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 11:01:19', '2026-01-23 11:01:19'),
(229, 'default', 'Order created', 'App\\Models\\Order', NULL, 226, 'App\\Models\\User', 3, '{\"total\": 220, \"order_number\": \"ORD-20260123-00007\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 11:01:19', '2026-01-23 11:01:19'),
(230, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 460, 'App\\Models\\User', 3, '{\"attributes\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 11:01:33', '2026-01-23 11:01:33'),
(231, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 693, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 460, \"quantity\": 3, \"product_id\": 77}}', NULL, '2026-01-23 11:01:33', '2026-01-23 11:01:33'),
(232, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 694, 'App\\Models\\User', 3, '{\"attributes\": {\"cart_id\": 460, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 11:02:23', '2026-01-23 11:02:23'),
(233, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 693, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 460, \"quantity\": 3, \"product_id\": 77}}', NULL, '2026-01-23 11:02:47', '2026-01-23 11:02:47'),
(234, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 694, 'App\\Models\\User', 3, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 11:03:06', '2026-01-23 11:03:06'),
(235, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 694, 'App\\Models\\User', 3, '{\"old\": {\"cart_id\": 460, \"quantity\": 2, \"product_id\": 74}}', NULL, '2026-01-23 11:09:56', '2026-01-23 11:09:56'),
(236, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 460, 'App\\Models\\User', 3, '{\"old\": {\"user_id\": 3, \"store_id\": null}}', NULL, '2026-01-23 11:09:56', '2026-01-23 11:09:56'),
(237, 'user', 'User logged out', 'App\\Models\\User', NULL, 3, 'App\\Models\\User', 3, '[]', NULL, '2026-01-23 11:10:04', '2026-01-23 11:10:04'),
(238, 'user', 'User logged in', 'App\\Models\\User', NULL, 3, 'App\\Models\\User', 3, '{\"ip_address\": \"197.39.17.7\", \"user_agent\": \"Dart/3.9 (dart:io)\"}', NULL, '2026-01-23 11:10:22', '2026-01-23 11:10:22'),
(239, 'user', 'User logged out', 'App\\Models\\User', NULL, 3, 'App\\Models\\User', 3, '[]', NULL, '2026-01-23 11:11:33', '2026-01-23 11:11:33'),
(240, 'user', 'User created', 'App\\Models\\User', 'created', 69, NULL, NULL, '{\"attributes\": {\"name\": \"tester\", \"email\": \"arafaaya2289@gmail.com\"}}', NULL, '2026-01-23 11:12:09', '2026-01-23 11:12:09'),
(241, 'user', 'User registered', 'App\\Models\\User', NULL, 69, 'App\\Models\\User', 69, '{\"ip_address\": \"197.39.17.7\"}', NULL, '2026-01-23 11:12:09', '2026-01-23 11:12:09'),
(242, 'user', 'User created', 'App\\Models\\User', 'created', 70, NULL, NULL, '{\"attributes\": {\"name\": \"tester\", \"email\": \"arafaaya22289@gmail.com\"}}', NULL, '2026-01-23 11:14:04', '2026-01-23 11:14:04'),
(243, 'user', 'User registered', 'App\\Models\\User', NULL, 70, 'App\\Models\\User', 70, '{\"ip_address\": \"197.39.17.7\"}', NULL, '2026-01-23 11:14:04', '2026-01-23 11:14:04'),
(244, 'user_address', 'User address created', 'App\\Models\\UserAddress', 'created', 168, 'App\\Models\\User', 70, '{\"attributes\": {\"phone\": \"0106536136\", \"landmark\": null, \"latitude\": \"29.96660544\", \"longitude\": \"31.10396393\", \"is_default\": true, \"street_name\": \"vv\", \"address_name\": \"villa\", \"address_type\": \"villa\", \"floor_number\": \"5\", \"building_name\": \"fairouz\", \"apartment_number\": \"5\"}}', NULL, '2026-01-23 11:14:36', '2026-01-23 11:14:36'),
(245, 'user_address', 'User created new address', 'App\\Models\\UserAddress', NULL, 168, 'App\\Models\\User', 70, '{\"user_name\": \"tester\", \"ip_address\": \"197.39.17.7\", \"user_agent\": \"Dart/3.9 (dart:io)\", \"user_email\": \"arafaaya22289@gmail.com\", \"street_name\": \"vv\", \"address_type\": \"villa\", \"building_name\": \"fairouz\"}', NULL, '2026-01-23 11:14:36', '2026-01-23 11:14:36'),
(246, 'user_address', 'User address created', 'App\\Models\\UserAddress', 'created', 169, 'App\\Models\\User', 70, '{\"attributes\": {\"phone\": \"0106536136\", \"landmark\": null, \"latitude\": \"29.96660544\", \"longitude\": \"31.10396393\", \"is_default\": false, \"street_name\": \"vv\", \"address_name\": \"villa\", \"address_type\": \"villa\", \"floor_number\": \"5\", \"building_name\": \"fairouz\", \"apartment_number\": \"5\"}}', NULL, '2026-01-23 11:14:36', '2026-01-23 11:14:36'),
(247, 'user_address', 'User created new address', 'App\\Models\\UserAddress', NULL, 169, 'App\\Models\\User', 70, '{\"user_name\": \"tester\", \"ip_address\": \"197.39.17.7\", \"user_agent\": \"Dart/3.9 (dart:io)\", \"user_email\": \"arafaaya22289@gmail.com\", \"street_name\": \"vv\", \"address_type\": \"villa\", \"building_name\": \"fairouz\"}', NULL, '2026-01-23 11:14:36', '2026-01-23 11:14:36'),
(248, 'user_address', 'User address created', 'App\\Models\\UserAddress', 'created', 170, 'App\\Models\\User', 70, '{\"attributes\": {\"phone\": \"1125456917\", \"landmark\": null, \"latitude\": \"29.97455575\", \"longitude\": \"31.11643955\", \"is_default\": false, \"street_name\": \"gg\", \"address_name\": \"apartment\", \"address_type\": \"apartment\", \"floor_number\": \"99\", \"building_name\": \"karm\", \"apartment_number\": \"88\"}}', NULL, '2026-01-23 11:15:14', '2026-01-23 11:15:14'),
(249, 'user_address', 'User created new address', 'App\\Models\\UserAddress', NULL, 170, 'App\\Models\\User', 70, '{\"user_name\": \"tester\", \"ip_address\": \"197.39.17.7\", \"user_agent\": \"Dart/3.9 (dart:io)\", \"user_email\": \"arafaaya22289@gmail.com\", \"street_name\": \"gg\", \"address_type\": \"apartment\", \"building_name\": \"karm\"}', NULL, '2026-01-23 11:15:14', '2026-01-23 11:15:14'),
(250, 'user_address', 'User address created', 'App\\Models\\UserAddress', 'created', 171, 'App\\Models\\User', 70, '{\"attributes\": {\"phone\": \"1125456917\", \"landmark\": null, \"latitude\": \"29.97455575\", \"longitude\": \"31.11643955\", \"is_default\": false, \"street_name\": \"gg\", \"address_name\": \"apartment\", \"address_type\": \"apartment\", \"floor_number\": \"99\", \"building_name\": \"karm\", \"apartment_number\": \"88\"}}', NULL, '2026-01-23 11:15:14', '2026-01-23 11:15:14'),
(251, 'user_address', 'User created new address', 'App\\Models\\UserAddress', NULL, 171, 'App\\Models\\User', 70, '{\"user_name\": \"tester\", \"ip_address\": \"197.39.17.7\", \"user_agent\": \"Dart/3.9 (dart:io)\", \"user_email\": \"arafaaya22289@gmail.com\", \"street_name\": \"gg\", \"address_type\": \"apartment\", \"building_name\": \"karm\"}', NULL, '2026-01-23 11:15:14', '2026-01-23 11:15:14'),
(252, 'user', 'User logged in', 'App\\Models\\User', NULL, 6, 'App\\Models\\User', 6, '{\"ip_address\": \"156.215.210.128\", \"user_agent\": \"Dart/3.9 (dart:io)\"}', NULL, '2026-01-23 11:18:36', '2026-01-23 11:18:36'),
(253, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 695, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 455, \"quantity\": 1, \"product_id\": 82}}', NULL, '2026-01-23 11:18:38', '2026-01-23 11:18:38'),
(254, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 686, 'App\\Models\\User', 6, '{\"old\": {\"cart_id\": 455, \"quantity\": 1, \"product_id\": 82}}', NULL, '2026-01-23 11:18:49', '2026-01-23 11:18:49'),
(255, 'default', 'Order created', 'App\\Models\\Order', 'created', 227, 'App\\Models\\User', 6, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 11:18:54', '2026-01-23 11:18:54'),
(256, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 261, 'App\\Models\\User', 6, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"245.00\", \"total_price\": \"245.00\"}}', NULL, '2026-01-23 11:18:54', '2026-01-23 11:18:54'),
(257, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 262, 'App\\Models\\User', 6, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"170.00\", \"total_price\": \"170.00\"}}', NULL, '2026-01-23 11:18:54', '2026-01-23 11:18:54'),
(258, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 263, 'App\\Models\\User', 6, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"45.00\", \"total_price\": \"45.00\"}}', NULL, '2026-01-23 11:18:54', '2026-01-23 11:18:54'),
(259, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 455, 'App\\Models\\User', 6, '{\"old\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:18:54', '2026-01-23 11:18:54'),
(260, 'default', 'Order created', 'App\\Models\\Order', NULL, 227, 'App\\Models\\User', 6, '{\"total\": 460, \"order_number\": \"ORD-20260123-00008\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 11:18:54', '2026-01-23 11:18:54'),
(261, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 461, 'App\\Models\\User', 6, '{\"attributes\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:19:49', '2026-01-23 11:19:49'),
(262, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 696, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 461, \"quantity\": 1, \"product_id\": 82}}', NULL, '2026-01-23 11:19:49', '2026-01-23 11:19:49'),
(263, 'default', 'Order created', 'App\\Models\\Order', 'created', 228, 'App\\Models\\User', 6, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 11:20:00', '2026-01-23 11:20:00'),
(264, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 264, 'App\\Models\\User', 6, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"35.00\", \"total_price\": \"35.00\"}}', NULL, '2026-01-23 11:20:00', '2026-01-23 11:20:00'),
(265, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 461, 'App\\Models\\User', 6, '{\"old\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:20:00', '2026-01-23 11:20:00'),
(266, 'default', 'Order created', 'App\\Models\\Order', NULL, 228, 'App\\Models\\User', 6, '{\"total\": 35, \"order_number\": \"ORD-20260123-00009\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 11:20:00', '2026-01-23 11:20:00'),
(267, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 462, 'App\\Models\\User', 6, '{\"attributes\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:32:04', '2026-01-23 11:32:04'),
(268, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 697, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 462, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 11:32:04', '2026-01-23 11:32:04'),
(269, 'default', 'Order created', 'App\\Models\\Order', 'created', 229, 'App\\Models\\User', 6, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 11:33:32', '2026-01-23 11:33:32'),
(270, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 265, 'App\\Models\\User', 6, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"525.00\", \"total_price\": \"525.00\"}}', NULL, '2026-01-23 11:33:32', '2026-01-23 11:33:32'),
(271, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 462, 'App\\Models\\User', 6, '{\"old\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:33:32', '2026-01-23 11:33:32'),
(272, 'default', 'Order created', 'App\\Models\\Order', NULL, 229, 'App\\Models\\User', 6, '{\"total\": 525, \"order_number\": \"ORD-20260123-00010\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 11:33:32', '2026-01-23 11:33:32'),
(273, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 463, 'App\\Models\\User', 6, '{\"attributes\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:36:55', '2026-01-23 11:36:55'),
(274, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 698, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 463, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 11:36:55', '2026-01-23 11:36:55'),
(275, 'default', 'Order created', 'App\\Models\\Order', 'created', 230, 'App\\Models\\User', 6, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 11:37:08', '2026-01-23 11:37:08'),
(276, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 266, 'App\\Models\\User', 6, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"525.00\", \"total_price\": \"525.00\"}}', NULL, '2026-01-23 11:37:08', '2026-01-23 11:37:08'),
(277, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 463, 'App\\Models\\User', 6, '{\"old\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:37:08', '2026-01-23 11:37:08'),
(278, 'default', 'Order created', 'App\\Models\\Order', NULL, 230, 'App\\Models\\User', 6, '{\"total\": 525, \"order_number\": \"ORD-20260123-00011\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 11:37:08', '2026-01-23 11:37:08'),
(279, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 464, 'App\\Models\\User', 6, '{\"attributes\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:44:12', '2026-01-23 11:44:12'),
(280, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 699, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 464, \"quantity\": 1, \"product_id\": 80}}', NULL, '2026-01-23 11:44:12', '2026-01-23 11:44:12'),
(281, 'default', 'Order created', 'App\\Models\\Order', 'created', 231, 'App\\Models\\User', 6, '{\"attributes\": {\"notes\": \"Please call before delivery\", \"order_status\": \"pending\", \"payment_status\": \"pending\", \"payment_reference\": null}}', NULL, '2026-01-23 11:44:26', '2026-01-23 11:44:26'),
(282, 'default', 'Order item created', 'App\\Models\\OrderItem', 'created', 267, 'App\\Models\\User', 6, '{\"attributes\": {\"quantity\": 1, \"unit_price\": \"445.00\", \"total_price\": \"445.00\"}}', NULL, '2026-01-23 11:44:26', '2026-01-23 11:44:26'),
(283, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 464, 'App\\Models\\User', 6, '{\"old\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:44:26', '2026-01-23 11:44:26'),
(284, 'default', 'Order created', 'App\\Models\\Order', NULL, 231, 'App\\Models\\User', 6, '{\"total\": 445, \"order_number\": \"ORD-20260123-00012\", \"payment_method\": \"cash\"}', NULL, '2026-01-23 11:44:26', '2026-01-23 11:44:26'),
(285, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 465, 'App\\Models\\User', 6, '{\"attributes\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 11:47:20', '2026-01-23 11:47:20'),
(286, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 700, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 465, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 11:47:20', '2026-01-23 11:47:20'),
(287, 'default', 'Cart item deleted', 'App\\Models\\CartItem', 'deleted', 700, 'App\\Models\\User', 6, '{\"old\": {\"cart_id\": 465, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 12:18:43', '2026-01-23 12:18:43'),
(288, 'default', 'Cart deleted', 'App\\Models\\Cart', 'deleted', 465, 'App\\Models\\User', 6, '{\"old\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 12:18:43', '2026-01-23 12:18:43'),
(289, 'default', 'Cart created', 'App\\Models\\Cart', 'created', 466, 'App\\Models\\User', 6, '{\"attributes\": {\"user_id\": 6, \"store_id\": null}}', NULL, '2026-01-23 12:18:55', '2026-01-23 12:18:55'),
(290, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 701, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 466, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 12:18:55', '2026-01-23 12:18:55'),
(291, 'default', 'Cart item created', 'App\\Models\\CartItem', 'created', 702, 'App\\Models\\User', 6, '{\"attributes\": {\"cart_id\": 466, \"quantity\": 1, \"product_id\": 74}}', NULL, '2026-01-23 12:20:18', '2026-01-23 12:20:18'),
(292, 'default', 'Cart item updated', 'App\\Models\\CartItem', 'updated', 702, 'App\\Models\\User', 6, '{\"old\": {\"quantity\": 1}, \"attributes\": {\"quantity\": 2}}', NULL, '2026-01-23 12:29:22', '2026-01-23 12:29:22'),
(293, 'category', 'Category created', 'App\\Models\\Category', 'created', 74, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"منتجات البان\", \"name_en\": \"diary milk\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"منتجات البان\", \"description_en\": \"diary milk\"}}', NULL, '2026-01-23 12:47:26', '2026-01-23 12:47:26'),
(294, 'category', 'Category created', 'App\\Models\\Category', 'created', 75, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"رومى\", \"name_en\": \"romi\", \"is_active\": true, \"sort_order\": 0, \"description_ar\": \"رومى\", \"description_en\": \"romi\"}}', NULL, '2026-01-23 12:49:17', '2026-01-23 12:49:17'),
(295, 'product', 'Product created', 'App\\Models\\Product', 'created', 83, 'App\\Models\\Admin', 1, '{\"attributes\": {\"name_ar\": \"ربع رومى\", \"name_en\": \"1/4 old romi\", \"is_active\": true, \"base_price\": \"45.00\", \"category_id\": 74, \"description_ar\": \"ربع رومى\", \"description_en\": \"1/4 old romi\", \"subcategory_id\": 75}}', NULL, '2026-01-23 12:50:13', '2026-01-23 12:50:13'),
(296, 'user', 'User logged in', 'App\\Models\\User', NULL, 6, 'App\\Models\\User', 6, '{\"ip_address\": \"156.215.210.128\", \"user_agent\": \"Dart/3.9 (dart:io)\"}', NULL, '2026-01-23 12:57:12', '2026-01-23 12:57:12');

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `id` bigint UNSIGNED NOT NULL,
  `store_id` bigint UNSIGNED DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `addon_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'extras, sides, drinks, etc.',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`id`, `store_id`, `name_en`, `name_ar`, `description_en`, `description_ar`, `price`, `addon_category`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(30, NULL, 'lemon juice', 'عصير ليمون', NULL, NULL, 40.00, NULL, 1, '2026-01-23 06:27:48', '2026-01-23 06:27:48', NULL),
(31, NULL, 'extra cheese', 'جبنه زياده', NULL, NULL, 45.00, NULL, 1, '2026-01-23 07:11:22', '2026-01-23 07:11:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `status`, `remember_token`, `fcm_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Admin', 'superadmin@test.com', NULL, '$2y$12$XoswvDktuw1E2p5eFqTRN.tc0UC88BvoMyEsnO0Ixth2pIOv3TmD2', '+1234567890', 1, NULL, 'cWEhUE-eXwglfCkmJrTcSt:APA91bF1Ctjd_qFvsU8xXSDU-mVCUhyGp9079u0q8HVac6XjAgdfBfwvZ3xt6Tgkxi1W7YAQWojxH8K4utHchvnjJS7hWslFE4nX6ziU2YxqmgoihptOjII', '2025-10-07 03:18:29', '2026-01-23 11:13:54', NULL),
(2, 'Test Admin3', 'admin@test.com', NULL, '$2y$12$zJy4UtBd0Xmb/af97N7D6.EjBpeIZMJj0uL7haEFr2eCYaxhcV40W', '01030886464', 1, NULL, NULL, '2025-10-07 03:18:29', '2025-11-18 07:22:01', NULL),
(6, 'aya test', 'aya@test.com', NULL, '$2y$12$c/000bIvgPW/ITHTn.pWDen4wt/DarhiNL8icgQW0cb25zXhe8IHq', '+201065361311', 1, NULL, NULL, '2025-10-24 06:50:20', '2025-10-29 06:16:50', NULL),
(7, 'aya1', 'aya1@test.com', NULL, '$2y$12$PkdUYzv1QZSZW7KEZZ8zT.CeXC9w0ngPyWNWIrJ0FUoR1v4040ZMK', NULL, 1, NULL, NULL, '2025-10-29 06:41:00', '2025-11-24 13:19:32', NULL),
(8, 'AbdElrahman Gbr', 'admin3@test.com', NULL, '$2y$12$smIAwx08OvMQoOM04KHzgefRfCicdfa.uneMxL7fmBPlz9aBC5UD.', '201276464881', 1, NULL, NULL, '2025-11-18 07:29:12', '2025-12-25 12:06:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `store_id` bigint UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `working_hours` json DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-036gkIvXUlEMKgmX', 's:7:\"forever\";', 2083758299),
('laravel-cache-08BW3U3cml4z1EB9', 's:7:\"forever\";', 2084258548),
('laravel-cache-2o6ScgxAdhvcBf2b', 's:7:\"forever\";', 2083928828),
('laravel-cache-3pjlOzjwSPFMNz7U', 's:7:\"forever\";', 2083917416),
('laravel-cache-4GxCardU1dMb5kWM', 's:7:\"forever\";', 2083841234),
('laravel-cache-4HY6XWBuWes2sKPP', 's:7:\"forever\";', 2083143301),
('laravel-cache-5WZl4KR3Gv1cE3w4', 's:7:\"forever\";', 2084533804),
('laravel-cache-7ELkA9INoI7YyPK0', 's:7:\"forever\";', 2083758064),
('laravel-cache-7X6KjQSiA7tEiZbV', 's:7:\"forever\";', 2083308629),
('laravel-cache-81A4kUwtSY2eENAg', 's:7:\"forever\";', 2084189161),
('laravel-cache-89eeXU1DEWC4Zngu', 's:7:\"forever\";', 2083741306),
('laravel-cache-8aZtpp8bPkGwZ6pz', 's:7:\"forever\";', 2083583692),
('laravel-cache-b3KtBPiiMHuw4iWE', 's:7:\"forever\";', 2083834617),
('laravel-cache-BReK6TJ1D3W9slpu', 's:7:\"forever\";', 2083589656),
('laravel-cache-bydg0CLxGxbf408O', 's:7:\"forever\";', 2084531983),
('laravel-cache-c1nWs8yCS4sob7Bt', 's:7:\"forever\";', 2083757391),
('laravel-cache-ce18GV0ylVSGiRzO', 's:7:\"forever\";', 2083588545),
('laravel-cache-cLQMu8OEsqC2InsH', 's:7:\"forever\";', 2083196500),
('laravel-cache-cx9iQbZJQlem7x1J', 's:7:\"forever\";', 2083914037),
('laravel-cache-Ds34FGbQzxpbZWqC', 's:7:\"forever\";', 2083849564),
('laravel-cache-eafuAYYwCAJgCz52', 's:7:\"forever\";', 2084517501),
('laravel-cache-F1gYNvWbRmcHscnw', 's:7:\"forever\";', 2083145685),
('laravel-cache-fSvhAmdMStBre241', 's:7:\"forever\";', 2083825528),
('laravel-cache-G31Q8d3SeA0O7WDG', 's:7:\"forever\";', 2083824926),
('laravel-cache-gjkpSDH97tKjJlbC', 's:7:\"forever\";', 2083146003),
('laravel-cache-GsbqjM5sSqYZi6fF', 's:7:\"forever\";', 2083243254),
('laravel-cache-GX2iuS6sSgIPDNbl', 's:7:\"forever\";', 2083052664),
('laravel-cache-H5Rhq89aP8pGzOR7', 's:7:\"forever\";', 2083243085),
('laravel-cache-h834UTiE1hU1pckQ', 's:7:\"forever\";', 2083587885),
('laravel-cache-I4KLuHjbXIBvMoWr', 's:7:\"forever\";', 2084182039),
('laravel-cache-idK3EuiHBIkQgtHA', 's:7:\"forever\";', 2083927080),
('laravel-cache-JkZRK22xETBppDmv', 's:7:\"forever\";', 2083833998),
('laravel-cache-JuBqaEpicuaq6Qdp', 's:7:\"forever\";', 2084533893),
('laravel-cache-K3SIOi7s5Jud08kQ', 's:7:\"forever\";', 2083584357),
('laravel-cache-L0wrfkyYnelvBHv2', 's:7:\"forever\";', 2083928073),
('laravel-cache-l4OMJbLCnwXZmvFm', 's:7:\"forever\";', 2084190085),
('laravel-cache-Ly9rEcECUXaGogYY', 's:7:\"forever\";', 2083851699),
('laravel-cache-M7N8dFD4V2OWXctx', 's:7:\"forever\";', 2083316366),
('laravel-cache-MkVx10FAltJzO7Uv', 's:7:\"forever\";', 2083832721),
('laravel-cache-mYWpzzGElFQmuvh1', 's:7:\"forever\";', 2083664787),
('laravel-cache-NzUx9lOhCwkfJoa4', 's:7:\"forever\";', 2083832136),
('laravel-cache-o9LtRkvUh0gJSC0I', 's:7:\"forever\";', 2083913475),
('laravel-cache-oi7g6JneVzdIBHQ9', 's:7:\"forever\";', 2084172963),
('laravel-cache-ORRhVHG9DLYTlea0', 's:7:\"forever\";', 2083306509),
('laravel-cache-phHoqGWq0aIAUGQV', 's:7:\"forever\";', 2083738353),
('laravel-cache-PhLmH13E0UiVWxAk', 's:7:\"forever\";', 2083839724),
('laravel-cache-pndPxx70BQ0QuppR', 's:7:\"forever\";', 2083741166),
('laravel-cache-Pw1Id7A516iJy3aa', 's:7:\"forever\";', 2083049677),
('laravel-cache-pXLoCn0XVbWw1SCH', 's:7:\"forever\";', 2084172978),
('laravel-cache-qx1F7Bvhe5xDjou7', 's:7:\"forever\";', 2083589686),
('laravel-cache-rDq1UECE1huu2TzH', 's:7:\"forever\";', 2083828931),
('laravel-cache-rLL0vjLUzSQCmCWP', 's:7:\"forever\";', 2083850448),
('laravel-cache-RMw3npsd24hwLiUJ', 's:7:\"forever\";', 2084172523),
('laravel-cache-sdAV61i0hgVHPE9a', 's:7:\"forever\";', 2083145225),
('laravel-cache-sj21MdDCtXrAH3g3', 's:7:\"forever\";', 2083050308),
('laravel-cache-snpHFHrcGm4xLwBw', 's:7:\"forever\";', 2083928386),
('laravel-cache-SO2Mviwa9YC3bTJl', 's:7:\"forever\";', 2083850259),
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:40:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:16:\"admin-users.view\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:11;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:18:\"admin-users.create\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:18:\"admin-users.update\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:11;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:18:\"admin-users.delete\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:10;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:17:\"admin-users.roles\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:11;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:11:\"stores.view\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:11;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:13:\"stores.create\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:10;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:13:\"stores.update\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:11;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:13:\"stores.delete\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:14:\"stores.approve\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:10;i:4;i:11;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:15:\"categories.view\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:10;i:5;i:11;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:17:\"categories.create\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:17:\"categories.update\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:11;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:17:\"categories.delete\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:13:\"settings.view\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:10;i:3;i:11;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:15:\"settings.update\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:10;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:18:\"system.maintenance\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:13:\"products.view\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:4:{i:0;i:5;i:1;i:6;i:2;i:7;i:3;i:8;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:15:\"products.create\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:2:{i:0;i:5;i:1;i:6;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:15:\"products.update\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:2:{i:0;i:5;i:1;i:6;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:15:\"products.delete\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:1:{i:0;i:5;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:11:\"orders.view\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:4:{i:0;i:5;i:1;i:6;i:2;i:7;i:3;i:8;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:13:\"orders.update\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:3:{i:0;i:5;i:1;i:6;i:2;i:7;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:13:\"orders.cancel\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:1:{i:0;i:5;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:10:\"staff.view\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:2:{i:0;i:5;i:1;i:6;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:12:\"staff.create\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:1:{i:0;i:5;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:12:\"staff.update\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:1:{i:0;i:5;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:12:\"staff.delete\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:1:{i:0;i:5;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:20:\"vendor-settings.view\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:3:{i:0;i:5;i:1;i:6;i:2;i:8;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:22:\"vendor-settings.update\";s:1:\"c\";s:7:\"vendors\";s:1:\"r\";a:1:{i:0;i:5;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:19:\"delivery-users.view\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:21:\"delivery-users.create\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:21:\"delivery-users.update\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:21:\"delivery-users.delete\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:10:\"users.view\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:12:\"users.create\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:12:\"users.update\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:12:\"users.delete\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:18:\"notifications.view\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:20:\"notifications.create\";s:1:\"c\";s:6:\"admins\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:10:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:6:\"admins\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:6:\"admins\";}i:2;a:3:{s:1:\"a\";i:11;s:1:\"b\";s:2:\"13\";s:1:\"c\";s:6:\"admins\";}i:3;a:3:{s:1:\"a\";i:10;s:1:\"b\";s:5:\"mem 2\";s:1:\"c\";s:6:\"admins\";}i:4;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"manager\";s:1:\"c\";s:6:\"admins\";}i:5;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:9:\"moderator\";s:1:\"c\";s:6:\"admins\";}i:6;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"vendor_owner\";s:1:\"c\";s:7:\"vendors\";}i:7;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:14:\"vendor_manager\";s:1:\"c\";s:7:\"vendors\";}i:8;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"vendor_staff\";s:1:\"c\";s:7:\"vendors\";}i:9;a:3:{s:1:\"a\";i:8;s:1:\"b\";s:13:\"vendor_viewer\";s:1:\"c\";s:7:\"vendors\";}}}', 1769240980),
('laravel-cache-SytAkq2vmQbOT7S7', 's:7:\"forever\";', 2083572874),
('laravel-cache-T62xuug0yXcQpBNO', 's:7:\"forever\";', 2083050073),
('laravel-cache-Tf1qTuwVAlM79yDt', 's:7:\"forever\";', 2084183502),
('laravel-cache-tg8VIztDSCSjzWYG', 's:7:\"forever\";', 2083832517),
('laravel-cache-UIXIP9so9r3x3f5B', 's:7:\"forever\";', 2083243133),
('laravel-cache-URh7Bbe6H5v97krv', 's:7:\"forever\";', 2083396932),
('laravel-cache-UVYYfggLpkzyr8Vg', 's:7:\"forever\";', 2083586277),
('laravel-cache-VOq2fPf6iOsXrH9P', 's:7:\"forever\";', 2083756700),
('laravel-cache-VVCUk4jXBNwsHsiR', 's:7:\"forever\";', 2083657533),
('laravel-cache-VviHnC65ZHINE53x', 's:7:\"forever\";', 2083145222),
('laravel-cache-WBmeA9j244XIbM4Q', 's:7:\"forever\";', 2083757530),
('laravel-cache-we7YaOGycGPvFXBi', 's:7:\"forever\";', 2083741338),
('laravel-cache-wfQK3tamLHkvarV0', 's:7:\"forever\";', 2083929076),
('laravel-cache-xDvW4DWDcuP9NNw1', 's:7:\"forever\";', 2083928443),
('laravel-cache-XKOKaAGYdKJAqe7P', 's:7:\"forever\";', 2083832099),
('laravel-cache-XSoVmDOogbxrdDg4', 's:7:\"forever\";', 2083315764),
('laravel-cache-y4eWefQwIgAYQH4H', 's:7:\"forever\";', 2083586317),
('laravel-cache-YSQcqhtfcjIPiWEY', 's:7:\"forever\";', 2083586919),
('laravel-cache-Z4JbpDBwu7GmuDMm', 's:7:\"forever\";', 2083224902),
('laravel-cache-ZW4nbAcrHecwtVQ6', 's:7:\"forever\";', 2083584299);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `store_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `store_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(452, 1, NULL, '2026-01-23 07:30:50', '2026-01-23 07:30:50', NULL),
(466, 6, NULL, '2026-01-23 12:18:55', '2026-01-23 12:18:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `created_at`, `updated_at`, `deleted_at`) VALUES
(701, 466, 74, 1, '2026-01-23 12:18:55', '2026-01-23 12:18:55', NULL),
(702, 466, 74, 2, '2026-01-23 12:20:18', '2026-01-23 12:29:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart_item_addons`
--

CREATE TABLE `cart_item_addons` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_item_id` bigint UNSIGNED NOT NULL,
  `addon_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_item_options`
--

CREATE TABLE `cart_item_options` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_item_id` bigint UNSIGNED NOT NULL,
  `product_option_value_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_item_options`
--

INSERT INTO `cart_item_options` (`id`, `cart_item_id`, `product_option_value_id`, `created_at`, `deleted_at`) VALUES
(510, 701, 111, '2026-01-23 14:18:55', NULL),
(511, 701, 116, '2026-01-23 14:18:55', NULL),
(512, 701, 114, '2026-01-23 14:18:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `module_id` bigint UNSIGNED DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `store_id` bigint UNSIGNED DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `module_id`, `parent_id`, `store_id`, `name_en`, `name_ar`, `description_en`, `description_ar`, `image`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(59, 25, NULL, NULL, 'Burger', 'برجر', 'Burger', 'برجر', 'categories/0BXr1dRYsQ0vbInpHVZly5H8l3hV1Iv0AyU0OYLo.png', 1, 1, '2026-01-20 07:35:13', '2026-01-22 07:47:15', '2026-01-22 07:47:15'),
(60, 25, NULL, NULL, 'Burger', 'برجر', 'Burger', 'برجر', 'categories/u3TEFOh5SK7DsGxrkFzHEb71Zz4SKRWNEq7Vx0eQ.png', 1, 1, '2026-01-20 07:35:20', '2026-01-22 07:47:12', '2026-01-22 07:47:12'),
(61, 25, NULL, NULL, 'Burger', 'برجر', 'Burger', 'برجر', 'categories/wcBEbAjwgyuFKbE5ZWGpgI14OfayvL1iQaMk4VB1.png', 1, 1, '2026-01-20 07:35:36', '2026-01-22 07:47:10', '2026-01-22 07:47:10'),
(62, 26, NULL, NULL, 'makook sandwitch', 'مكوك سندوتش', 'makook sandwitch', 'مكوك سندوتش', 'categories/3dbepPN15Nqh0U7oKKUUVgcBDknaDfpzeJGtgOw7.png', 1, 0, '2026-01-21 11:15:44', '2026-01-21 11:15:44', NULL),
(69, 31, NULL, NULL, 'Dairy section', 'قسم الالبان', 'Everything related to dairy products can be found here.', 'كل ما تحتاجه ستجده هنا', 'categories/6pAu76FAi4VilVV7eQpUmj78AKu9ydOkzHzdeEnY.jpg', 1, 0, '2026-01-22 07:49:47', '2026-01-22 07:49:47', NULL),
(70, 27, NULL, NULL, 'Healthy breakfast meals', 'وجبات فطار صحيه', 'meals contain (\r\nEggs - Beans - Peppers - Vegetables - Cheese)', 'الوجبات تحتوي علي (بيض - فول - فلفل -خضار-جبنه )', 'categories/QGT6vkw9f3sGQFoaICY0b9d29MQPy4bEzVMyQDNk.jpg', 1, 0, '2026-01-23 06:22:42', '2026-01-23 06:22:42', NULL),
(71, 25, NULL, NULL, 'pizza', 'بيتزا', NULL, NULL, 'categories/xtI9rkymrHaA7ja1xulm50AbUSxuPZ1ALuypjfEI.jpg', 1, 0, '2026-01-23 06:53:46', '2026-01-23 06:53:46', NULL),
(74, 31, NULL, NULL, 'diary milk', 'منتجات البان', 'diary milk', 'منتجات البان', 'categories/O2HLboeuL2H7yCsxgDMRQu7ifc4hAvDzQ3agu8AV.png', 1, 0, '2026-01-23 12:47:26', '2026-01-23 12:47:26', NULL),
(75, 31, 74, NULL, 'romi', 'رومى', 'romi', 'رومى', 'categories/kNJIZJbVoU2FVRcpcuIfGcmoD44AGB1c1DMhEwIF.png', 1, 0, '2026-01-23 12:49:17', '2026-01-23 12:49:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `availability` tinyint(1) NOT NULL DEFAULT '1',
  `shift_start_time` time DEFAULT NULL,
  `shift_end_time` time DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `vehicle_type`, `vehicle_number`, `license_number`, `address`, `status`, `availability`, `shift_start_time`, `shift_end_time`, `remember_token`, `fcm_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'John Doe', 'john.delivery@example.com', NULL, '$2y$12$RooCkPf/YqZWZBoYyQpSeO6EHcumaMUEG4gzAo2Snitq7CIL17s1W', '+1234567890', 'motorcycle', 'ABC123', 'DL12345', '123 Main Street, City', 1, 1, NULL, NULL, NULL, 'eqITuGjpQv-3ME5bZN88Uy:APA91bFWzNHlCO6paNVZSm4WEfQHt4XTpp6HKyon0eQ5KuDlFiMoYy3Pexy7fMjaW9QdtfDd4aZY83_-6yk8mLwv-CPR4ZS8tRuWcAtcwNkeWaEZhCVox0g', '2025-11-24 08:02:12', '2026-01-14 11:29:40', NULL),
(2, 'John Updated', 'nucufug@mailinator.com', NULL, '$2y$12$1AG5txpjXKyn52zXZaVTAeYjK/MfTc97MfASjLzjW0amZ67r9Dv..', '+1 (945) 515-2967', 'car', '480', '651', 'Ipsum praesentium ad', 1, 1, NULL, NULL, NULL, 'dBWgwrUsRwqizxWQeYrdbl:APA91bFteqNzQyPWTwaS2GQpGlH0vWnKdNC9pfVvFiZfIm-BP30fOgcLnJrxubZWdy-IZ1F2FGGcPZg7WtfTGFUhs7L2SkAtKAHcOCtvGn_0dXUnwML_LPc', '2025-11-24 12:48:10', '2026-01-16 10:41:06', NULL),
(3, 'aya arafa', 'superadmin@test.com', NULL, '$2y$12$lagDSWm1RCfiuXgZeVPAKeQr4LqTqTdjW2pDrEHXhSDRLuvp0rkY2', '01065361362', 'motorcycle', NULL, NULL, 'giza, haram', 0, 1, NULL, NULL, NULL, NULL, '2026-01-12 07:09:59', '2026-01-12 08:06:50', NULL),
(4, 'test', 'arafaaya289@gmail.com', NULL, '$2y$12$2Cb40mvmuQlAAHt4ArwyGeeaitXKSZT7w5UoKtRtgIby1LDsZG4Zi', '01065361362', 'scooter', '480', '651', 'giza, haram', 1, 1, NULL, NULL, NULL, 'fsk5OMHjSRaoCb_h1MsPe9:APA91bEay4DzpWuLCVu-C-ZyrVhfACneBCbb-h2gdz0Rj-w3_cQs6s5wSF-4sOA4MnAG2vNkgGMEONkd4J8ReHUHbnry1CAizlzzJqyg7IJw7o1BDkQVhSc', '2026-01-12 07:47:04', '2026-01-12 08:06:51', NULL),
(5, 'aya 1', 'arafaaya@gmail.com', NULL, '$2y$12$kG.enDmWJmfCzA7IggeWFu7UOc8PKDOe6NTBIb4RLd/sF6FBGFWYi', '01065361362', 'scooter', '480', '651', 'giza, haram', 1, 1, NULL, NULL, NULL, NULL, '2026-01-12 07:52:41', '2026-01-12 07:52:41', NULL),
(6, 'Ahmed Gamal', 'agemydelivery@gmail.com', NULL, '$2y$12$oeaHo/gS.u/PMqNm/6M2B.q8EwvoQM0PV/fYRMg3YpiUeRmU8CNm2', '01234567899', 'motorcycle', '123ب ر ه', NULL, '10 Khaled Mekky street, El eshreen, faysal, Giza\r\n٤٠', 1, 1, NULL, NULL, NULL, 'eqITuGjpQv-3ME5bZN88Uy:APA91bFWzNHlCO6paNVZSm4WEfQHt4XTpp6HKyon0eQ5KuDlFiMoYy3Pexy7fMjaW9QdtfDd4aZY83_-6yk8mLwv-CPR4ZS8tRuWcAtcwNkeWaEZhCVox0g', '2026-01-12 14:23:16', '2026-01-13 06:56:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_invoices`
--

CREATE TABLE `delivery_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_id` bigint UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_invoices`
--

INSERT INTO `delivery_invoices` (`id`, `invoice_number`, `delivery_id`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DINV-20260106-00001', 1, 3630.00, 'paid', '2026-01-06 06:55:42', '2026-01-06 07:37:42');

-- --------------------------------------------------------

--
-- Table structure for table `elasticsearch_sync_queue`
--

CREATE TABLE `elasticsearch_sync_queue` (
  `id` bigint UNSIGNED NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` bigint UNSIGNED NOT NULL,
  `action` enum('create','update','delete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `elasticsearch_sync_queue`
--

INSERT INTO `elasticsearch_sync_queue` (`id`, `entity_type`, `entity_id`, `action`, `synced_at`, `created_at`) VALUES
(1, 'product', 1, 'create', NULL, '2025-10-09 13:34:08'),
(2, 'product', 1, 'update', NULL, '2025-10-09 13:34:09'),
(3, 'product', 1, 'update', NULL, '2025-10-09 14:15:00'),
(4, 'product', 2, 'create', NULL, '2025-10-09 14:23:47'),
(5, 'product', 2, 'update', NULL, '2025-10-09 14:23:48'),
(6, 'product', 2, 'update', NULL, '2025-10-09 14:24:05'),
(7, 'product', 2, 'update', NULL, '2025-10-09 14:24:08'),
(8, 'product', 1, 'update', NULL, '2025-10-09 14:25:51'),
(9, 'product', 1, 'delete', NULL, '2025-10-09 14:26:41'),
(10, 'product', 3, 'create', NULL, '2025-10-09 14:35:54'),
(11, 'product', 3, 'update', NULL, '2025-10-09 14:35:55'),
(12, 'product', 2, 'update', NULL, '2025-10-09 14:36:38'),
(13, 'product', 2, 'delete', NULL, '2025-10-09 14:39:21'),
(14, 'product', 3, 'update', NULL, '2025-10-09 14:54:55'),
(15, 'product', 3, 'update', NULL, '2025-10-09 14:57:07'),
(16, 'product', 3, 'update', NULL, '2025-10-09 15:01:44'),
(17, 'product', 3, 'update', NULL, '2025-10-11 09:26:40'),
(18, 'product', 3, 'update', NULL, '2025-10-11 09:30:51'),
(19, 'product', 3, 'update', NULL, '2025-10-11 09:31:01'),
(20, 'product', 3, 'update', NULL, '2025-10-11 09:38:48'),
(21, 'product', 3, 'update', NULL, '2025-10-11 10:20:25'),
(22, 'product', 3, 'update', NULL, '2025-10-11 10:23:13'),
(23, 'product', 3, 'update', NULL, '2025-10-11 10:24:09'),
(24, 'product', 3, 'update', NULL, '2025-10-11 10:25:07'),
(25, 'product', 3, 'update', NULL, '2025-10-11 10:30:41'),
(26, 'product', 3, 'update', NULL, '2025-10-11 10:36:28'),
(27, 'product', 3, 'update', NULL, '2025-10-13 12:08:29'),
(28, 'product', 3, 'update', NULL, '2025-10-14 08:51:30'),
(29, 'product', 3, 'update', NULL, '2025-10-14 08:51:45'),
(30, 'product', 3, 'update', NULL, '2025-10-14 08:52:02'),
(31, 'product', 3, 'update', NULL, '2025-10-14 08:52:21'),
(32, 'product', 3, 'update', NULL, '2025-10-14 08:56:04'),
(33, 'product', 3, 'update', NULL, '2025-10-14 08:58:09'),
(34, 'product', 3, 'update', NULL, '2025-10-14 14:53:19'),
(35, 'product', 3, 'update', NULL, '2025-10-14 14:57:40'),
(36, 'product', 3, 'update', NULL, '2025-10-14 15:00:22'),
(37, 'product', 3, 'update', NULL, '2025-10-14 15:03:43'),
(38, 'product', 3, 'update', NULL, '2025-10-14 15:05:11'),
(39, 'product', 3, 'update', NULL, '2025-10-14 15:05:27'),
(40, 'product', 3, 'update', NULL, '2025-10-14 15:05:33'),
(41, 'product', 3, 'update', NULL, '2025-10-14 15:11:01'),
(42, 'product', 3, 'update', NULL, '2025-10-14 15:12:44'),
(43, 'product', 3, 'update', NULL, '2025-10-14 15:13:05'),
(44, 'product', 3, 'update', NULL, '2025-10-14 15:14:20'),
(45, 'product', 3, 'update', NULL, '2025-10-14 15:18:54'),
(46, 'product', 3, 'update', NULL, '2025-10-14 15:21:13'),
(47, 'product', 3, 'update', NULL, '2025-10-14 15:21:18'),
(48, 'product', 3, 'update', NULL, '2025-10-14 15:21:27'),
(49, 'product', 3, 'update', NULL, '2025-10-14 15:25:06'),
(50, 'product', 3, 'update', NULL, '2025-10-14 15:25:21'),
(51, 'product', 3, 'update', NULL, '2025-10-14 15:25:30'),
(52, 'product', 3, 'update', NULL, '2025-10-14 15:26:34'),
(53, 'product', 3, 'update', NULL, '2025-10-15 06:42:34'),
(54, 'product', 3, 'update', NULL, '2025-10-15 06:42:58'),
(55, 'product', 3, 'update', NULL, '2025-10-15 06:43:18'),
(56, 'product', 3, 'update', NULL, '2025-10-15 06:43:30'),
(57, 'product', 3, 'update', NULL, '2025-10-15 06:55:30'),
(58, 'product', 3, 'update', NULL, '2025-10-15 06:55:42'),
(59, 'product', 3, 'update', NULL, '2025-10-15 06:59:27'),
(60, 'product', 3, 'update', NULL, '2025-10-15 07:02:13'),
(61, 'product', 3, 'update', NULL, '2025-10-15 07:08:27'),
(62, 'product', 3, 'update', NULL, '2025-10-15 07:08:57'),
(63, 'product', 3, 'update', NULL, '2025-10-15 07:12:23'),
(64, 'product', 3, 'update', NULL, '2025-10-15 07:16:26'),
(65, 'product', 4, 'create', NULL, '2025-10-15 13:48:55'),
(66, 'product', 4, 'update', NULL, '2025-10-15 14:04:11'),
(67, 'product', 5, 'create', NULL, '2025-10-15 14:23:11'),
(68, 'product', 5, 'update', NULL, '2025-10-15 14:23:14'),
(69, 'product', 5, 'update', NULL, '2025-10-15 14:25:26'),
(70, 'product', 6, 'create', NULL, '2025-10-15 15:05:36'),
(71, 'product', 6, 'update', NULL, '2025-10-15 15:06:23'),
(72, 'product', 4, 'update', NULL, '2025-10-15 15:07:11'),
(73, 'product', 6, 'update', NULL, '2025-10-16 08:03:26'),
(74, 'product', 6, 'update', NULL, '2025-10-16 09:31:19'),
(75, 'product', 6, 'update', NULL, '2025-10-16 09:35:04'),
(76, 'product', 6, 'update', NULL, '2025-10-16 09:40:39'),
(77, 'product', 6, 'update', NULL, '2025-10-16 09:54:34'),
(78, 'product', 6, 'update', NULL, '2025-10-16 10:09:57'),
(79, 'product', 7, 'create', NULL, '2025-10-16 10:47:19'),
(80, 'product', 7, 'update', NULL, '2025-10-16 10:47:37'),
(81, 'product', 7, 'update', NULL, '2025-10-16 10:48:14'),
(82, 'product', 7, 'update', NULL, '2025-10-16 10:48:14'),
(83, 'product', 7, 'update', NULL, '2025-10-16 10:48:14'),
(84, 'product', 7, 'update', NULL, '2025-10-16 10:52:29'),
(85, 'product', 7, 'update', NULL, '2025-10-16 10:52:29'),
(86, 'product', 7, 'update', NULL, '2025-10-16 10:54:18'),
(87, 'product', 7, 'update', NULL, '2025-10-16 11:00:13'),
(88, 'product', 7, 'update', NULL, '2025-10-16 11:05:34'),
(89, 'product', 7, 'update', NULL, '2025-10-16 11:06:37'),
(90, 'product', 7, 'update', NULL, '2025-10-16 11:16:37'),
(91, 'product', 7, 'update', NULL, '2025-10-16 11:16:44'),
(92, 'product', 7, 'update', NULL, '2025-10-16 11:19:27'),
(93, 'product', 7, 'update', NULL, '2025-10-16 11:29:18'),
(94, 'product', 5, 'update', NULL, '2025-10-16 11:38:58'),
(95, 'product', 7, 'update', NULL, '2025-10-16 11:43:14'),
(96, 'product', 7, 'update', NULL, '2025-10-16 11:49:53'),
(97, 'product', 8, 'create', NULL, '2025-10-16 12:48:21'),
(98, 'product', 9, 'create', NULL, '2025-10-16 12:49:47'),
(99, 'product', 10, 'create', NULL, '2025-10-16 13:20:01'),
(100, 'product', 9, 'update', NULL, '2025-10-16 13:20:10'),
(101, 'product', 9, 'update', NULL, '2025-10-16 13:20:18'),
(102, 'product', 9, 'update', NULL, '2025-10-16 13:21:17'),
(103, 'product', 10, 'update', NULL, '2025-10-16 13:21:28'),
(104, 'product', 7, 'update', NULL, '2025-10-16 13:22:46'),
(105, 'product', 11, 'create', NULL, '2025-10-16 13:23:45'),
(106, 'product', 11, 'update', NULL, '2025-10-16 13:24:09'),
(107, 'product', 12, 'create', NULL, '2025-10-16 13:28:38'),
(108, 'product', 13, 'create', NULL, '2025-10-16 13:32:41'),
(109, 'product', 14, 'create', NULL, '2025-10-16 13:38:21'),
(110, 'product', 14, 'update', NULL, '2025-10-16 13:38:37'),
(111, 'product', 14, 'update', NULL, '2025-10-16 13:39:57'),
(112, 'product', 15, 'create', NULL, '2025-10-16 13:42:30'),
(113, 'product', 14, 'update', NULL, '2025-10-16 14:01:31'),
(114, 'product', 15, 'update', NULL, '2025-10-16 14:25:56'),
(115, 'product', 15, 'update', NULL, '2025-10-16 14:30:45'),
(116, 'product', 14, 'update', NULL, '2025-10-16 14:32:16'),
(117, 'product', 15, 'update', NULL, '2025-10-16 14:54:11'),
(118, 'product', 15, 'update', NULL, '2025-10-16 14:57:39'),
(119, 'product', 4, 'update', NULL, '2025-10-16 15:06:09'),
(120, 'product', 4, 'update', NULL, '2025-10-16 15:08:39'),
(121, 'product', 4, 'update', NULL, '2025-10-16 15:20:36'),
(122, 'product', 4, 'update', NULL, '2025-10-16 15:28:25'),
(123, 'product', 5, 'update', NULL, '2025-10-16 15:30:59'),
(124, 'product', 4, 'update', NULL, '2025-10-16 15:33:41'),
(125, 'product', 5, 'update', NULL, '2025-10-16 15:34:56'),
(126, 'product', 4, 'update', NULL, '2025-10-16 15:36:51'),
(127, 'product', 15, 'update', NULL, '2025-10-16 15:46:48'),
(128, 'product', 15, 'update', NULL, '2025-10-16 15:50:16'),
(129, 'product', 14, 'update', NULL, '2025-10-16 15:50:28'),
(130, 'product', 14, 'update', NULL, '2025-10-16 15:57:12'),
(131, 'product', 15, 'update', NULL, '2025-10-16 16:05:15'),
(132, 'product', 14, 'update', NULL, '2025-10-16 16:09:28'),
(133, 'product', 7, 'update', NULL, '2025-10-17 07:07:02'),
(134, 'product', 7, 'update', NULL, '2025-10-17 07:07:18'),
(135, 'product', 16, 'create', NULL, '2025-10-17 07:21:28'),
(136, 'product', 17, 'create', NULL, '2025-10-17 07:22:17'),
(137, 'product', 18, 'create', NULL, '2025-10-17 08:29:30'),
(138, 'product', 18, 'update', NULL, '2025-10-17 08:29:49'),
(139, 'product', 19, 'create', NULL, '2025-10-17 09:12:50'),
(140, 'product', 19, 'update', NULL, '2025-10-17 09:15:44'),
(141, 'product', 19, 'update', NULL, '2025-10-17 09:15:49'),
(142, 'product', 19, 'update', NULL, '2025-10-17 09:16:16'),
(143, 'product', 19, 'update', NULL, '2025-10-17 09:24:35'),
(144, 'product', 19, 'update', NULL, '2025-10-17 09:30:50'),
(145, 'product', 19, 'update', NULL, '2025-10-17 09:31:07'),
(146, 'product', 19, 'update', NULL, '2025-10-17 09:37:12'),
(147, 'product', 19, 'update', NULL, '2025-10-17 10:12:50'),
(148, 'product', 19, 'update', NULL, '2025-10-17 10:12:57'),
(149, 'product', 18, 'update', NULL, '2025-10-17 10:13:07'),
(150, 'product', 19, 'update', NULL, '2025-10-17 10:13:30'),
(151, 'product', 19, 'update', NULL, '2025-10-17 10:13:37'),
(152, 'product', 18, 'update', NULL, '2025-10-17 10:13:45'),
(153, 'product', 19, 'update', NULL, '2025-10-17 10:15:15'),
(154, 'product', 19, 'update', NULL, '2025-10-17 10:15:33'),
(155, 'product', 19, 'update', NULL, '2025-10-17 10:16:57'),
(156, 'product', 19, 'update', NULL, '2025-10-17 10:25:46'),
(157, 'product', 20, 'create', NULL, '2025-10-17 11:55:10'),
(158, 'product', 20, 'update', NULL, '2025-10-17 11:55:21'),
(159, 'product', 21, 'create', NULL, '2025-10-17 12:13:28'),
(160, 'product', 22, 'create', NULL, '2025-10-17 12:28:06'),
(161, 'product', 22, 'update', NULL, '2025-10-17 12:28:34'),
(162, 'product', 22, 'update', NULL, '2025-10-17 12:30:05'),
(163, 'product', 19, 'update', NULL, '2025-10-18 08:05:57'),
(164, 'product', 19, 'update', NULL, '2025-10-20 07:25:36'),
(165, 'product', 19, 'update', NULL, '2025-10-20 07:32:48'),
(166, 'product', 19, 'update', NULL, '2025-10-20 08:21:28'),
(167, 'product', 19, 'update', NULL, '2025-10-20 08:32:09'),
(168, 'product', 22, 'update', NULL, '2025-10-20 10:38:25'),
(169, 'product', 22, 'update', NULL, '2025-10-20 10:38:36'),
(170, 'product', 23, 'create', NULL, '2025-10-20 12:03:29'),
(171, 'product', 24, 'create', NULL, '2025-10-20 14:54:44'),
(172, 'product', 25, 'create', NULL, '2025-10-21 07:45:38'),
(173, 'product', 26, 'create', NULL, '2025-10-21 09:26:00'),
(174, 'product', 26, 'update', NULL, '2025-10-21 09:26:29'),
(175, 'product', 26, 'update', NULL, '2025-10-21 09:27:12'),
(176, 'product', 26, 'update', NULL, '2025-10-21 09:31:32'),
(177, 'product', 26, 'update', NULL, '2025-10-21 09:38:54'),
(178, 'product', 26, 'update', NULL, '2025-10-21 09:44:05'),
(179, 'product', 26, 'update', NULL, '2025-10-21 09:44:39'),
(180, 'product', 26, 'update', NULL, '2025-10-21 09:47:34'),
(181, 'product', 26, 'update', NULL, '2025-10-21 09:53:54'),
(182, 'product', 26, 'update', NULL, '2025-10-21 09:56:36'),
(183, 'product', 26, 'update', NULL, '2025-10-21 10:02:14'),
(184, 'product', 26, 'update', NULL, '2025-10-21 10:02:25'),
(185, 'product', 26, 'update', NULL, '2025-10-21 10:04:06'),
(186, 'product', 26, 'update', NULL, '2025-10-21 10:05:02'),
(187, 'product', 26, 'update', NULL, '2025-10-21 10:05:25'),
(188, 'product', 26, 'update', NULL, '2025-10-21 10:07:27'),
(189, 'product', 27, 'create', NULL, '2025-10-21 10:07:28'),
(190, 'product', 27, 'update', NULL, '2025-10-21 10:07:28'),
(191, 'product', 28, 'create', NULL, '2025-10-21 11:36:51'),
(192, 'product', 28, 'update', NULL, '2025-10-21 11:37:02'),
(193, 'product', 26, 'update', NULL, '2025-10-21 11:37:09'),
(194, 'product', 27, 'update', NULL, '2025-10-21 11:37:12'),
(195, 'product', 27, 'update', NULL, '2025-10-21 11:37:54'),
(196, 'product', 27, 'update', NULL, '2025-10-21 11:40:02'),
(197, 'product', 27, 'update', NULL, '2025-10-21 11:41:12'),
(198, 'product', 27, 'update', NULL, '2025-10-21 11:46:31'),
(199, 'product', 27, 'update', NULL, '2025-10-21 11:54:00'),
(200, 'product', 27, 'update', NULL, '2025-10-21 11:54:09'),
(201, 'product', 27, 'update', NULL, '2025-10-21 11:55:07'),
(202, 'product', 27, 'update', NULL, '2025-10-21 11:56:46'),
(203, 'product', 27, 'update', NULL, '2025-10-21 12:00:35'),
(204, 'product', 27, 'update', NULL, '2025-10-21 12:30:18'),
(205, 'product', 27, 'update', NULL, '2025-10-21 12:37:58'),
(206, 'product', 27, 'update', NULL, '2025-10-21 12:38:01'),
(207, 'product', 27, 'update', NULL, '2025-10-21 13:07:56'),
(208, 'product', 27, 'update', NULL, '2025-10-21 13:08:00'),
(209, 'product', 29, 'create', NULL, '2025-10-21 13:10:47'),
(210, 'product', 29, 'update', NULL, '2025-10-21 13:23:08'),
(211, 'product', 25, 'update', NULL, '2025-10-21 13:23:16'),
(212, 'product', 25, 'update', NULL, '2025-10-21 13:36:50'),
(213, 'product', 29, 'update', NULL, '2025-10-21 13:46:16'),
(214, 'product', 29, 'update', NULL, '2025-10-21 13:46:21'),
(215, 'product', 29, 'update', NULL, '2025-10-21 13:52:29'),
(216, 'product', 29, 'update', NULL, '2025-10-21 13:58:21'),
(217, 'product', 25, 'update', NULL, '2025-10-21 14:52:04'),
(218, 'product', 26, 'update', NULL, '2025-10-21 16:00:28'),
(219, 'product', 27, 'update', NULL, '2025-10-21 16:00:28'),
(220, 'product', 27, 'update', NULL, '2025-10-21 16:00:39'),
(221, 'product', 26, 'update', NULL, '2025-10-21 16:00:39'),
(222, 'product', 26, 'update', NULL, '2025-10-21 16:01:55'),
(223, 'product', 27, 'update', NULL, '2025-10-21 16:01:55'),
(224, 'product', 30, 'create', NULL, '2025-10-21 16:33:45'),
(225, 'product', 30, 'update', NULL, '2025-10-22 07:39:17'),
(226, 'product', 30, 'update', NULL, '2025-10-22 07:41:23'),
(227, 'product', 26, 'update', NULL, '2025-10-22 07:44:04'),
(228, 'product', 29, 'update', NULL, '2025-10-22 07:44:53'),
(229, 'product', 27, 'update', NULL, '2025-10-22 07:45:09'),
(230, 'product', 30, 'update', NULL, '2025-10-22 07:45:39'),
(231, 'product', 30, 'update', NULL, '2025-10-22 07:50:06'),
(232, 'product', 30, 'update', NULL, '2025-10-22 07:50:31'),
(233, 'product', 30, 'update', NULL, '2025-10-22 07:50:40'),
(234, 'product', 30, 'update', NULL, '2025-10-22 07:50:44'),
(235, 'product', 31, 'create', NULL, '2025-10-22 07:54:20'),
(236, 'product', 32, 'create', NULL, '2025-10-22 08:55:47'),
(237, 'product', 32, 'update', NULL, '2025-10-22 08:56:41'),
(238, 'product', 32, 'delete', NULL, '2025-10-22 09:12:56'),
(239, 'product', 33, 'create', NULL, '2025-10-22 09:42:03'),
(240, 'product', 34, 'create', NULL, '2025-10-22 09:44:32'),
(241, 'product', 35, 'create', NULL, '2025-10-22 09:45:44'),
(242, 'product', 36, 'create', NULL, '2025-10-22 10:53:00'),
(243, 'product', 26, 'update', NULL, '2025-10-22 10:59:02'),
(244, 'product', 26, 'update', NULL, '2025-10-22 10:59:07'),
(245, 'product', 36, 'update', NULL, '2025-10-22 11:08:11'),
(246, 'product', 36, 'update', NULL, '2025-10-22 11:08:33'),
(247, 'product', 36, 'update', NULL, '2025-10-22 11:08:48'),
(248, 'product', 36, 'update', NULL, '2025-10-22 11:11:20'),
(249, 'product', 31, 'update', NULL, '2025-10-22 14:26:20'),
(250, 'product', 35, 'update', NULL, '2025-10-23 07:15:11'),
(251, 'product', 36, 'update', NULL, '2025-10-23 07:15:55'),
(252, 'product', 31, 'delete', NULL, '2025-10-23 07:26:14'),
(253, 'product', 24, 'update', NULL, '2025-10-23 07:29:14'),
(254, 'product', 25, 'update', NULL, '2025-10-23 07:29:17'),
(255, 'product', 30, 'delete', NULL, '2025-10-23 07:31:13'),
(256, 'product', 24, 'delete', NULL, '2025-10-23 07:31:41'),
(257, 'product', 25, 'delete', NULL, '2025-10-23 07:31:48'),
(258, 'product', 26, 'delete', NULL, '2025-10-23 07:35:58'),
(259, 'product', 27, 'delete', NULL, '2025-10-23 07:36:40'),
(260, 'product', 28, 'delete', NULL, '2025-10-23 07:37:04'),
(261, 'product', 19, 'delete', NULL, '2025-10-23 07:38:57'),
(262, 'product', 22, 'delete', NULL, '2025-10-23 07:40:55'),
(263, 'product', 21, 'delete', NULL, '2025-10-23 07:44:42'),
(264, 'product', 17, 'delete', NULL, '2025-10-23 07:48:01'),
(265, 'product', 16, 'delete', NULL, '2025-10-23 07:49:32'),
(266, 'product', 15, 'delete', NULL, '2025-10-23 07:51:28'),
(267, 'product', 11, 'delete', NULL, '2025-10-23 07:55:52'),
(268, 'product', 10, 'delete', NULL, '2025-10-23 07:58:21'),
(269, 'product', 9, 'update', NULL, '2025-10-23 07:58:29'),
(270, 'product', 9, 'update', NULL, '2025-10-23 07:58:35'),
(271, 'product', 9, 'update', NULL, '2025-10-23 08:00:34'),
(272, 'product', 9, 'update', NULL, '2025-10-23 08:00:56'),
(273, 'product', 9, 'update', NULL, '2025-10-23 08:02:45'),
(274, 'product', 9, 'update', NULL, '2025-10-23 08:03:49'),
(275, 'product', 9, 'update', NULL, '2025-10-23 08:08:18'),
(276, 'product', 9, 'update', NULL, '2025-10-23 08:18:39'),
(277, 'product', 9, 'update', NULL, '2025-10-23 08:19:13'),
(278, 'product', 9, 'update', NULL, '2025-10-23 08:19:17'),
(279, 'product', 37, 'create', NULL, '2025-10-23 08:19:19'),
(280, 'product', 37, 'update', NULL, '2025-10-23 08:19:20'),
(281, 'product', 37, 'update', NULL, '2025-10-23 08:22:10'),
(282, 'product', 33, 'update', NULL, '2025-10-23 08:22:46'),
(283, 'product', 14, 'update', NULL, '2025-10-23 08:23:04'),
(284, 'product', 5, 'update', NULL, '2025-10-23 08:23:14'),
(285, 'product', 18, 'update', NULL, '2025-10-23 08:24:20'),
(286, 'product', 33, 'update', NULL, '2025-10-23 08:27:01'),
(287, 'product', 33, 'update', NULL, '2025-10-23 08:28:34'),
(288, 'product', 33, 'update', NULL, '2025-10-23 08:33:31'),
(289, 'product', 33, 'update', NULL, '2025-10-23 08:33:36'),
(290, 'product', 33, 'update', NULL, '2025-10-23 08:36:10'),
(291, 'product', 33, 'update', NULL, '2025-10-23 08:36:41'),
(292, 'product', 13, 'update', NULL, '2025-10-23 08:38:17'),
(293, 'product', 20, 'update', NULL, '2025-10-23 08:38:28'),
(294, 'product', 38, 'create', NULL, '2025-10-23 08:42:32'),
(295, 'product', 38, 'update', NULL, '2025-10-23 08:42:33'),
(296, 'product', 20, 'update', NULL, '2025-10-23 08:48:01'),
(297, 'product', 20, 'update', NULL, '2025-10-23 08:48:08'),
(298, 'product', 38, 'update', NULL, '2025-10-23 08:50:27'),
(299, 'product', 20, 'update', NULL, '2025-10-23 08:50:49'),
(300, 'product', 20, 'update', NULL, '2025-10-23 08:53:18'),
(301, 'product', 39, 'create', NULL, '2025-10-23 14:03:54'),
(302, 'product', 33, 'update', NULL, '2025-10-24 07:38:07'),
(303, 'product', 33, 'update', NULL, '2025-10-24 07:38:26'),
(304, 'product', 33, 'update', NULL, '2025-10-24 07:40:03'),
(305, 'product', 34, 'update', NULL, '2025-10-24 07:40:42'),
(306, 'product', 34, 'update', NULL, '2025-10-24 07:40:56'),
(307, 'product', 34, 'update', NULL, '2025-10-24 07:47:56'),
(308, 'product', 33, 'update', NULL, '2025-10-24 07:48:09'),
(309, 'product', 33, 'update', NULL, '2025-10-24 07:56:49'),
(310, 'product', 35, 'update', NULL, '2025-10-24 07:57:16'),
(311, 'product', 35, 'update', NULL, '2025-10-24 07:57:44'),
(312, 'product', 35, 'update', NULL, '2025-10-24 08:00:45'),
(313, 'product', 35, 'update', NULL, '2025-10-24 08:01:12'),
(314, 'product', 35, 'update', NULL, '2025-10-24 08:01:20'),
(315, 'product', 35, 'update', NULL, '2025-10-24 08:01:56'),
(316, 'product', 35, 'update', NULL, '2025-10-24 08:02:59'),
(317, 'product', 35, 'update', NULL, '2025-10-24 08:08:11'),
(318, 'product', 33, 'delete', NULL, '2025-10-24 08:10:47'),
(319, 'product', 40, 'create', NULL, '2025-10-24 08:12:02'),
(320, 'product', 40, 'update', NULL, '2025-10-24 08:12:03'),
(321, 'product', 34, 'update', NULL, '2025-10-24 08:12:48'),
(322, 'product', 41, 'create', NULL, '2025-10-24 08:13:10'),
(323, 'product', 41, 'update', NULL, '2025-10-24 08:13:11'),
(324, 'product', 40, 'update', NULL, '2025-10-24 08:17:11'),
(325, 'product', 41, 'update', NULL, '2025-10-24 08:30:01'),
(326, 'product', 41, 'update', NULL, '2025-10-24 09:01:25'),
(327, 'product', 41, 'update', NULL, '2025-10-24 09:01:35'),
(328, 'product', 42, 'create', NULL, '2025-10-24 09:22:59'),
(329, 'product', 43, 'create', NULL, '2025-10-24 11:55:12'),
(330, 'product', 41, 'update', NULL, '2025-10-29 07:39:16'),
(331, 'product', 41, 'update', NULL, '2025-10-29 07:39:19'),
(332, 'product', 41, 'update', NULL, '2025-10-29 07:40:16'),
(333, 'product', 41, 'update', NULL, '2025-10-29 07:44:49'),
(334, 'product', 34, 'update', NULL, '2025-10-29 07:45:43'),
(335, 'product', 34, 'update', NULL, '2025-10-29 07:46:15'),
(336, 'product', 36, 'update', NULL, '2025-10-29 07:49:29'),
(337, 'product', 36, 'update', NULL, '2025-10-29 07:49:49'),
(338, 'product', 36, 'update', NULL, '2025-10-29 07:51:22'),
(339, 'product', 41, 'delete', NULL, '2025-10-29 07:54:32'),
(340, 'product', 35, 'update', NULL, '2025-10-29 07:56:50'),
(341, 'product', 35, 'update', NULL, '2025-10-29 07:56:58'),
(342, 'product', 43, 'update', NULL, '2025-11-04 08:51:19'),
(343, 'product', 44, 'create', NULL, '2025-11-04 09:22:43'),
(344, 'product', 42, 'update', NULL, '2025-11-04 12:41:49'),
(345, 'product', 44, 'update', NULL, '2025-11-05 12:32:48'),
(346, 'product', 36, 'update', NULL, '2025-11-05 12:43:50'),
(347, 'product', 36, 'update', NULL, '2025-11-05 12:44:22'),
(348, 'product', 8, 'update', NULL, '2025-11-05 12:44:32'),
(349, 'product', 34, 'update', NULL, '2025-11-05 12:45:24'),
(350, 'product', 34, 'update', NULL, '2025-11-05 12:45:51'),
(351, 'product', 44, 'update', NULL, '2025-11-05 12:47:09'),
(352, 'product', 40, 'update', NULL, '2025-11-05 12:47:23'),
(353, 'product', 40, 'update', NULL, '2025-11-05 12:47:30'),
(354, 'product', 8, 'update', NULL, '2025-11-05 13:27:59'),
(355, 'product', 8, 'update', NULL, '2025-11-05 13:28:03'),
(356, 'product', 8, 'update', NULL, '2025-11-05 14:35:27'),
(357, 'product', 8, 'update', NULL, '2025-11-05 14:38:16'),
(358, 'product', 36, 'update', NULL, '2025-11-05 16:08:58'),
(359, 'product', 42, 'update', NULL, '2025-11-05 16:35:17'),
(360, 'product', 8, 'update', NULL, '2025-11-06 08:07:14'),
(361, 'product', 7, 'update', NULL, '2025-11-06 08:07:23'),
(362, 'product', 7, 'update', NULL, '2025-11-06 08:07:33'),
(363, 'product', 8, 'update', NULL, '2025-11-06 08:07:39'),
(364, 'product', 4, 'update', NULL, '2025-11-06 08:07:55'),
(365, 'product', 29, 'update', NULL, '2025-11-06 08:08:03'),
(366, 'product', 8, 'update', NULL, '2025-11-06 08:08:55'),
(367, 'product', 7, 'update', NULL, '2025-11-06 08:09:07'),
(368, 'product', 6, 'update', NULL, '2025-11-06 08:09:16'),
(369, 'product', 5, 'update', NULL, '2025-11-06 08:09:21'),
(370, 'product', 4, 'update', NULL, '2025-11-06 08:09:24'),
(371, 'product', 36, 'update', NULL, '2025-11-06 08:09:43'),
(372, 'product', 42, 'update', NULL, '2025-11-06 08:09:49'),
(373, 'product', 8, 'update', NULL, '2025-11-06 08:25:53'),
(374, 'product', 6, 'update', NULL, '2025-11-06 08:26:09'),
(375, 'product', 5, 'update', NULL, '2025-11-06 08:26:39'),
(376, 'product', 40, 'update', NULL, '2025-11-06 08:26:52'),
(377, 'product', 40, 'update', NULL, '2025-11-06 08:27:48'),
(378, 'product', 4, 'update', NULL, '2025-11-06 08:27:49'),
(379, 'product', 5, 'update', NULL, '2025-11-06 08:27:53'),
(380, 'product', 6, 'update', NULL, '2025-11-06 08:27:57'),
(381, 'product', 7, 'update', NULL, '2025-11-06 08:28:01'),
(382, 'product', 8, 'update', NULL, '2025-11-06 08:28:06'),
(383, 'product', 42, 'update', NULL, '2025-11-06 08:29:09'),
(384, 'product', 42, 'update', NULL, '2025-11-06 08:29:18'),
(385, 'product', 42, 'update', NULL, '2025-11-06 08:34:18'),
(386, 'product', 42, 'update', NULL, '2025-11-06 08:34:29'),
(387, 'product', 42, 'update', NULL, '2025-11-06 08:35:41'),
(388, 'product', 42, 'update', NULL, '2025-11-06 08:36:33'),
(389, 'product', 36, 'update', NULL, '2025-11-06 08:37:56'),
(390, 'product', 36, 'update', NULL, '2025-11-06 08:38:04'),
(391, 'product', 36, 'update', NULL, '2025-11-06 08:38:45'),
(392, 'product', 42, 'update', NULL, '2025-11-06 08:39:46'),
(393, 'product', 36, 'update', NULL, '2025-11-06 08:40:38'),
(394, 'product', 6, 'update', NULL, '2025-11-06 08:40:45'),
(395, 'product', 36, 'update', NULL, '2025-11-06 08:40:49'),
(396, 'product', 8, 'update', NULL, '2025-11-06 08:41:26'),
(397, 'product', 7, 'update', NULL, '2025-11-06 08:41:30'),
(398, 'product', 6, 'update', NULL, '2025-11-06 08:41:34'),
(399, 'product', 5, 'update', NULL, '2025-11-06 08:41:41'),
(400, 'product', 4, 'update', NULL, '2025-11-06 08:41:45'),
(401, 'product', 8, 'update', NULL, '2025-11-06 08:52:10'),
(402, 'product', 7, 'update', NULL, '2025-11-06 08:52:16'),
(403, 'product', 6, 'update', NULL, '2025-11-06 08:52:19'),
(404, 'product', 5, 'update', NULL, '2025-11-06 08:52:22'),
(405, 'product', 36, 'update', NULL, '2025-11-06 08:53:44'),
(406, 'product', 5, 'update', NULL, '2025-11-06 08:54:06'),
(407, 'product', 44, 'update', NULL, '2025-11-06 08:54:45'),
(408, 'product', 5, 'update', NULL, '2025-11-06 09:09:20'),
(409, 'product', 5, 'update', NULL, '2025-11-06 09:10:21'),
(410, 'product', 5, 'update', NULL, '2025-11-06 09:11:09'),
(411, 'product', 5, 'update', NULL, '2025-11-06 09:26:57'),
(412, 'product', 5, 'update', NULL, '2025-11-06 09:27:58'),
(413, 'product', 5, 'update', NULL, '2025-11-06 09:30:06'),
(414, 'product', 5, 'update', NULL, '2025-11-06 09:36:11'),
(415, 'product', 5, 'update', NULL, '2025-11-06 09:36:37'),
(416, 'product', 5, 'update', NULL, '2025-11-06 09:37:20'),
(417, 'product', 6, 'update', NULL, '2025-11-06 09:37:58'),
(418, 'product', 5, 'update', NULL, '2025-11-06 09:38:00'),
(419, 'product', 29, 'update', NULL, '2025-11-06 09:38:05'),
(420, 'product', 29, 'update', NULL, '2025-11-06 09:38:12'),
(421, 'product', 7, 'update', NULL, '2025-11-06 09:38:15'),
(422, 'product', 45, 'create', NULL, '2025-11-06 09:39:43'),
(423, 'product', 35, 'update', NULL, '2025-11-06 09:40:12'),
(424, 'product', 36, 'update', NULL, '2025-11-06 09:40:31'),
(425, 'product', 45, 'update', NULL, '2025-11-06 09:40:49'),
(426, 'product', 46, 'create', NULL, '2025-11-06 09:41:16'),
(427, 'product', 46, 'update', NULL, '2025-11-06 09:41:16'),
(428, 'product', 46, 'update', NULL, '2025-11-06 09:41:28'),
(429, 'product', 35, 'update', NULL, '2025-11-06 09:41:40'),
(430, 'product', 29, 'update', NULL, '2025-11-06 09:41:44'),
(431, 'product', 4, 'update', NULL, '2025-11-06 09:41:48'),
(432, 'product', 4, 'update', NULL, '2025-11-06 09:42:22'),
(433, 'product', 29, 'update', NULL, '2025-11-06 09:42:29'),
(434, 'product', 5, 'update', NULL, '2025-11-06 09:42:32'),
(435, 'product', 8, 'update', NULL, '2025-11-06 09:42:35'),
(436, 'product', 46, 'delete', NULL, '2025-11-06 09:43:39'),
(437, 'product', 6, 'update', NULL, '2025-11-06 09:43:42'),
(438, 'product', 45, 'update', NULL, '2025-11-06 09:50:14'),
(439, 'product', 45, 'update', NULL, '2025-11-06 09:50:26'),
(440, 'product', 5, 'update', NULL, '2025-11-06 10:04:51'),
(441, 'product', 5, 'update', NULL, '2025-11-06 10:07:50'),
(442, 'product', 5, 'update', NULL, '2025-11-06 10:13:01'),
(443, 'product', 5, 'update', NULL, '2025-11-06 10:13:08'),
(444, 'product', 5, 'update', NULL, '2025-11-06 10:13:14'),
(445, 'product', 35, 'update', NULL, '2025-11-06 10:35:11'),
(446, 'product', 8, 'update', NULL, '2025-11-06 10:51:04'),
(447, 'product', 7, 'update', NULL, '2025-11-06 10:51:11'),
(448, 'product', 7, 'update', NULL, '2025-11-06 10:51:54'),
(449, 'product', 6, 'update', NULL, '2025-11-06 10:52:23'),
(450, 'product', 8, 'update', NULL, '2025-11-06 10:52:31'),
(451, 'product', 4, 'update', NULL, '2025-11-06 10:52:36'),
(452, 'product', 5, 'update', NULL, '2025-11-06 10:55:30'),
(453, 'product', 42, 'update', NULL, '2025-11-06 10:56:16'),
(454, 'product', 45, 'update', NULL, '2025-11-06 10:56:40'),
(455, 'product', 6, 'update', NULL, '2025-11-06 10:56:49'),
(456, 'product', 6, 'update', NULL, '2025-11-06 10:58:39'),
(457, 'product', 7, 'update', NULL, '2025-11-06 13:08:04'),
(458, 'product', 8, 'update', NULL, '2025-11-06 13:21:53'),
(459, 'product', 8, 'update', NULL, '2025-11-06 13:25:41'),
(460, 'product', 8, 'update', NULL, '2025-11-06 13:28:42'),
(461, 'product', 5, 'update', NULL, '2025-11-06 13:28:50'),
(462, 'product', 5, 'update', NULL, '2025-11-06 14:38:52'),
(463, 'product', 6, 'update', NULL, '2025-11-06 14:39:19'),
(464, 'product', 6, 'update', NULL, '2025-11-06 14:44:07'),
(465, 'product', 35, 'update', NULL, '2025-11-06 14:51:35'),
(466, 'product', 7, 'update', NULL, '2025-11-06 14:51:42'),
(467, 'product', 29, 'update', NULL, '2025-11-06 14:52:16'),
(468, 'product', 5, 'update', NULL, '2025-11-06 15:24:19'),
(469, 'product', 5, 'update', NULL, '2025-11-06 15:29:55'),
(470, 'product', 5, 'update', NULL, '2025-11-06 15:38:18'),
(471, 'product', 5, 'update', NULL, '2025-11-06 15:43:21'),
(472, 'product', 5, 'update', NULL, '2025-11-06 15:51:57'),
(473, 'product', 5, 'update', NULL, '2025-11-07 10:33:28'),
(474, 'product', 35, 'update', NULL, '2025-11-07 12:36:51'),
(475, 'product', 45, 'update', NULL, '2025-11-07 12:36:55'),
(476, 'product', 5, 'update', NULL, '2025-11-07 12:39:41'),
(477, 'product', 5, 'update', NULL, '2025-11-07 12:43:36'),
(478, 'product', 5, 'update', NULL, '2025-11-07 12:44:45'),
(479, 'product', 5, 'update', NULL, '2025-11-07 12:46:52'),
(480, 'product', 5, 'update', NULL, '2025-11-07 12:51:26'),
(481, 'product', 5, 'update', NULL, '2025-11-07 12:52:16'),
(482, 'product', 4, 'update', NULL, '2025-11-07 12:53:03'),
(483, 'product', 5, 'update', NULL, '2025-11-07 12:53:06'),
(484, 'product', 5, 'update', NULL, '2025-11-07 12:54:50'),
(485, 'product', 5, 'update', NULL, '2025-11-07 13:04:02'),
(486, 'product', 35, 'update', NULL, '2025-11-07 13:04:12'),
(487, 'product', 7, 'update', NULL, '2025-11-07 13:04:17'),
(488, 'product', 5, 'update', NULL, '2025-11-07 13:11:07'),
(489, 'product', 5, 'update', NULL, '2025-11-07 13:11:20'),
(490, 'product', 5, 'update', NULL, '2025-11-07 13:11:27'),
(491, 'product', 5, 'update', NULL, '2025-11-07 13:21:43'),
(492, 'product', 5, 'update', NULL, '2025-11-07 13:22:30'),
(493, 'product', 5, 'update', NULL, '2025-11-07 13:44:08'),
(494, 'product', 42, 'update', NULL, '2025-11-10 07:41:32'),
(495, 'product', 42, 'update', NULL, '2025-11-10 08:04:50'),
(496, 'product', 42, 'update', NULL, '2025-11-10 08:08:55'),
(497, 'product', 35, 'update', NULL, '2025-11-10 14:57:21'),
(498, 'product', 42, 'update', NULL, '2025-11-10 14:57:31'),
(499, 'product', 42, 'update', NULL, '2025-11-10 14:58:09'),
(500, 'product', 42, 'update', NULL, '2025-11-10 14:58:23'),
(501, 'product', 5, 'update', NULL, '2025-11-11 13:50:45'),
(502, 'product', 5, 'update', NULL, '2025-11-11 15:55:53'),
(503, 'product', 5, 'update', NULL, '2025-11-11 16:01:24'),
(504, 'product', 5, 'update', NULL, '2025-11-11 16:08:05'),
(505, 'product', 5, 'update', NULL, '2025-11-12 10:02:24'),
(506, 'product', 5, 'update', NULL, '2025-11-12 10:36:52'),
(507, 'product', 5, 'update', NULL, '2025-11-12 10:40:48'),
(508, 'product', 3, 'update', NULL, '2025-11-12 11:02:54'),
(509, 'product', 35, 'update', NULL, '2025-11-12 11:03:04'),
(510, 'product', 5, 'update', NULL, '2025-11-12 11:03:09'),
(511, 'product', 5, 'update', NULL, '2025-11-12 11:04:51'),
(512, 'product', 5, 'update', NULL, '2025-11-12 11:08:03'),
(513, 'product', 5, 'update', NULL, '2025-11-12 11:17:49'),
(514, 'product', 6, 'update', NULL, '2025-11-12 11:35:25'),
(515, 'product', 35, 'update', NULL, '2025-11-12 11:35:36'),
(516, 'product', 29, 'update', NULL, '2025-11-12 11:35:43'),
(517, 'product', 4, 'update', NULL, '2025-11-12 11:35:46'),
(518, 'product', 5, 'update', NULL, '2025-11-12 11:35:53'),
(519, 'product', 5, 'update', NULL, '2025-11-12 11:36:02'),
(520, 'product', 5, 'update', NULL, '2025-11-12 11:36:06'),
(521, 'product', 5, 'update', NULL, '2025-11-12 11:37:57'),
(522, 'product', 5, 'update', NULL, '2025-11-12 11:40:24'),
(523, 'product', 12, 'update', NULL, '2025-11-12 11:40:44'),
(524, 'product', 35, 'update', NULL, '2025-11-12 11:40:47'),
(525, 'product', 4, 'update', NULL, '2025-11-12 11:40:50'),
(526, 'product', 6, 'update', NULL, '2025-11-12 11:40:54'),
(527, 'product', 5, 'update', NULL, '2025-11-12 11:45:54'),
(528, 'product', 5, 'update', NULL, '2025-11-12 12:12:58'),
(529, 'product', 5, 'update', NULL, '2025-11-12 12:13:27'),
(530, 'product', 5, 'update', NULL, '2025-11-12 12:13:37'),
(531, 'product', 5, 'update', NULL, '2025-11-12 12:13:50'),
(532, 'product', 5, 'update', NULL, '2025-11-12 12:16:21'),
(533, 'product', 5, 'update', NULL, '2025-11-12 12:33:31'),
(534, 'product', 5, 'update', NULL, '2025-11-12 12:33:48'),
(535, 'product', 5, 'update', NULL, '2025-11-12 12:34:08'),
(536, 'product', 5, 'update', NULL, '2025-11-12 12:34:36'),
(537, 'product', 5, 'update', NULL, '2025-11-12 12:56:17'),
(538, 'product', 5, 'update', NULL, '2025-11-12 12:56:24'),
(539, 'product', 5, 'update', NULL, '2025-11-12 13:02:03'),
(540, 'product', 5, 'update', NULL, '2025-11-12 13:02:16'),
(541, 'product', 5, 'update', NULL, '2025-11-12 13:02:36'),
(542, 'product', 35, 'update', NULL, '2025-11-12 13:03:02'),
(543, 'product', 42, 'update', NULL, '2025-11-12 13:03:09'),
(544, 'product', 5, 'update', NULL, '2025-11-12 13:04:24'),
(545, 'product', 13, 'update', NULL, '2025-11-12 13:04:53'),
(546, 'product', 35, 'update', NULL, '2025-11-12 13:04:56'),
(547, 'product', 5, 'update', NULL, '2025-11-12 13:11:27'),
(548, 'product', 5, 'update', NULL, '2025-11-12 13:11:41'),
(549, 'product', 35, 'update', NULL, '2025-11-12 13:16:13'),
(550, 'product', 45, 'update', NULL, '2025-11-12 13:16:15'),
(551, 'product', 6, 'update', NULL, '2025-11-12 13:39:54'),
(552, 'product', 5, 'update', NULL, '2025-11-12 13:40:09'),
(553, 'product', 5, 'update', NULL, '2025-11-12 13:40:15'),
(554, 'product', 5, 'update', NULL, '2025-11-12 13:41:07'),
(555, 'product', 5, 'update', NULL, '2025-11-12 13:41:24'),
(556, 'product', 5, 'update', NULL, '2025-11-12 13:42:38'),
(557, 'product', 5, 'update', NULL, '2025-11-12 13:46:14'),
(558, 'product', 35, 'update', NULL, '2025-11-12 13:47:56'),
(559, 'product', 45, 'update', NULL, '2025-11-12 13:47:59'),
(560, 'product', 5, 'update', NULL, '2025-11-12 13:49:47'),
(561, 'product', 8, 'update', NULL, '2025-11-12 14:03:11'),
(562, 'product', 5, 'update', NULL, '2025-11-12 14:03:15'),
(563, 'product', 5, 'update', NULL, '2025-11-12 14:17:48'),
(564, 'product', 45, 'update', NULL, '2025-11-12 14:25:37'),
(565, 'product', 5, 'update', NULL, '2025-11-12 14:26:36'),
(566, 'product', 5, 'update', NULL, '2025-11-12 14:26:49'),
(567, 'product', 5, 'update', NULL, '2025-11-12 14:27:06'),
(568, 'product', 5, 'update', NULL, '2025-11-12 14:27:13'),
(569, 'product', 5, 'update', NULL, '2025-11-12 14:27:39'),
(570, 'product', 6, 'update', NULL, '2025-11-12 15:03:09'),
(571, 'product', 45, 'update', NULL, '2025-11-12 15:04:07'),
(572, 'product', 6, 'update', NULL, '2025-11-12 15:04:54'),
(573, 'product', 6, 'update', NULL, '2025-11-12 15:08:36'),
(574, 'product', 7, 'update', NULL, '2025-11-12 15:09:20'),
(575, 'product', 5, 'update', NULL, '2025-11-12 15:15:58'),
(576, 'product', 5, 'update', NULL, '2025-11-12 15:25:46'),
(577, 'product', 6, 'update', NULL, '2025-11-12 15:32:19'),
(578, 'product', 3, 'update', NULL, '2025-11-12 15:34:13'),
(579, 'product', 5, 'update', NULL, '2025-11-12 15:34:24'),
(580, 'product', 6, 'update', NULL, '2025-11-12 15:36:12'),
(581, 'product', 5, 'update', NULL, '2025-11-12 15:37:34'),
(582, 'product', 6, 'update', NULL, '2025-11-12 15:38:03'),
(583, 'product', 5, 'update', NULL, '2025-11-12 15:40:14'),
(584, 'product', 6, 'update', NULL, '2025-11-12 15:40:28'),
(585, 'product', 45, 'update', NULL, '2025-11-12 15:41:55'),
(586, 'product', 7, 'update', NULL, '2025-11-12 15:44:36'),
(587, 'product', 6, 'update', NULL, '2025-11-12 15:44:51'),
(588, 'product', 45, 'update', NULL, '2025-11-12 15:52:00'),
(589, 'product', 5, 'update', NULL, '2025-11-12 15:52:03'),
(590, 'product', 45, 'update', NULL, '2025-11-12 15:52:11'),
(591, 'product', 45, 'update', NULL, '2025-11-12 16:00:07'),
(592, 'product', 5, 'update', NULL, '2025-11-13 08:16:10'),
(593, 'product', 5, 'update', NULL, '2025-11-13 08:23:47'),
(594, 'product', 5, 'update', NULL, '2025-11-13 08:24:09'),
(595, 'product', 5, 'update', NULL, '2025-11-13 08:25:00'),
(596, 'product', 5, 'update', NULL, '2025-11-13 08:25:16'),
(597, 'product', 5, 'update', NULL, '2025-11-13 08:33:50'),
(598, 'product', 5, 'update', NULL, '2025-11-13 08:34:25'),
(599, 'product', 5, 'update', NULL, '2025-11-13 08:37:06'),
(600, 'product', 5, 'update', NULL, '2025-11-13 08:37:58'),
(601, 'product', 5, 'update', NULL, '2025-11-13 08:39:50'),
(602, 'product', 5, 'update', NULL, '2025-11-13 08:41:03'),
(603, 'product', 5, 'update', NULL, '2025-11-13 08:42:27'),
(604, 'product', 5, 'update', NULL, '2025-11-13 08:44:47'),
(605, 'product', 5, 'update', NULL, '2025-11-13 08:46:45'),
(606, 'product', 5, 'update', NULL, '2025-11-13 08:47:35'),
(607, 'product', 5, 'update', NULL, '2025-11-13 08:48:02'),
(608, 'product', 6, 'update', NULL, '2025-11-13 08:52:12'),
(609, 'product', 5, 'update', NULL, '2025-11-13 08:52:15'),
(610, 'product', 5, 'update', NULL, '2025-11-13 09:01:22'),
(611, 'product', 5, 'update', NULL, '2025-11-13 09:03:51'),
(612, 'product', 5, 'update', NULL, '2025-11-13 09:04:34'),
(613, 'product', 5, 'update', NULL, '2025-11-13 09:12:02'),
(614, 'product', 5, 'update', NULL, '2025-11-13 09:14:23'),
(615, 'product', 5, 'update', NULL, '2025-11-13 09:16:29'),
(616, 'product', 5, 'update', NULL, '2025-11-13 09:33:51'),
(617, 'product', 5, 'update', NULL, '2025-11-13 09:37:05'),
(618, 'product', 5, 'update', NULL, '2025-11-13 09:37:22'),
(619, 'product', 35, 'update', NULL, '2025-11-13 09:38:43'),
(620, 'product', 6, 'update', NULL, '2025-11-13 09:38:49'),
(621, 'product', 8, 'update', NULL, '2025-11-13 09:40:24'),
(622, 'product', 5, 'update', NULL, '2025-11-13 09:55:59'),
(623, 'product', 4, 'update', NULL, '2025-11-13 09:57:18'),
(624, 'product', 5, 'update', NULL, '2025-11-13 09:57:57'),
(625, 'product', 5, 'update', NULL, '2025-11-13 09:58:43'),
(626, 'product', 45, 'update', NULL, '2025-11-13 09:59:54'),
(627, 'product', 5, 'update', NULL, '2025-11-13 10:00:05'),
(628, 'product', 5, 'update', NULL, '2025-11-13 10:01:08'),
(629, 'product', 5, 'update', NULL, '2025-11-13 10:02:23'),
(630, 'product', 6, 'update', NULL, '2025-11-13 10:02:34'),
(631, 'product', 35, 'update', NULL, '2025-11-13 10:25:16'),
(632, 'product', 12, 'update', NULL, '2025-11-13 10:25:52'),
(633, 'product', 3, 'update', NULL, '2025-11-13 10:25:54'),
(634, 'product', 5, 'update', NULL, '2025-11-13 10:45:54'),
(635, 'product', 5, 'update', NULL, '2025-11-13 10:46:06'),
(636, 'product', 35, 'update', NULL, '2025-11-13 10:55:26'),
(637, 'product', 5, 'update', NULL, '2025-11-13 10:55:42'),
(638, 'product', 5, 'update', NULL, '2025-11-13 10:58:00'),
(639, 'product', 5, 'update', NULL, '2025-11-13 10:58:08'),
(640, 'product', 5, 'update', NULL, '2025-11-13 11:02:08'),
(641, 'product', 5, 'update', NULL, '2025-11-13 11:02:55'),
(642, 'product', 5, 'update', NULL, '2025-11-13 11:07:43'),
(643, 'product', 5, 'update', NULL, '2025-11-13 11:12:13'),
(644, 'product', 5, 'update', NULL, '2025-11-13 11:12:20'),
(645, 'product', 5, 'update', NULL, '2025-11-13 11:12:39'),
(646, 'product', 35, 'update', NULL, '2025-11-13 11:15:56'),
(647, 'product', 3, 'update', NULL, '2025-11-13 11:18:51'),
(648, 'product', 5, 'update', NULL, '2025-11-13 11:19:06'),
(649, 'product', 7, 'update', NULL, '2025-11-13 11:19:33'),
(650, 'product', 5, 'update', NULL, '2025-11-13 11:20:15'),
(651, 'product', 5, 'update', NULL, '2025-11-13 13:35:18'),
(652, 'product', 5, 'update', NULL, '2025-11-13 13:35:53'),
(653, 'product', 5, 'update', NULL, '2025-11-13 13:44:01'),
(654, 'product', 5, 'update', NULL, '2025-11-13 13:45:19'),
(655, 'product', 5, 'update', NULL, '2025-11-13 13:45:24'),
(656, 'product', 5, 'update', NULL, '2025-11-13 13:45:26'),
(657, 'product', 5, 'update', NULL, '2025-11-13 13:45:39'),
(658, 'product', 5, 'update', NULL, '2025-11-13 13:49:08'),
(659, 'product', 5, 'update', NULL, '2025-11-13 13:49:23'),
(660, 'product', 5, 'update', NULL, '2025-11-13 13:49:41'),
(661, 'product', 5, 'update', NULL, '2025-11-13 14:00:51'),
(662, 'product', 5, 'update', NULL, '2025-11-13 14:02:27'),
(663, 'product', 5, 'update', NULL, '2025-11-13 14:05:52'),
(664, 'product', 5, 'update', NULL, '2025-11-13 14:06:04'),
(665, 'product', 5, 'update', NULL, '2025-11-13 14:13:39'),
(666, 'product', 5, 'update', NULL, '2025-11-13 14:14:33'),
(667, 'product', 5, 'update', NULL, '2025-11-13 14:15:32'),
(668, 'product', 5, 'update', NULL, '2025-11-13 14:16:45'),
(669, 'product', 5, 'update', NULL, '2025-11-13 14:17:39'),
(670, 'product', 5, 'update', NULL, '2025-11-13 14:18:21'),
(671, 'product', 5, 'update', NULL, '2025-11-13 14:30:39'),
(672, 'product', 5, 'update', NULL, '2025-11-13 14:31:32'),
(673, 'product', 5, 'update', NULL, '2025-11-13 14:31:59'),
(674, 'product', 6, 'update', NULL, '2025-11-13 14:32:30'),
(675, 'product', 6, 'update', NULL, '2025-11-13 14:32:37'),
(676, 'product', 6, 'update', NULL, '2025-11-13 14:33:02'),
(677, 'product', 5, 'update', NULL, '2025-11-13 14:33:33'),
(678, 'product', 5, 'update', NULL, '2025-11-13 14:34:11'),
(679, 'product', 5, 'update', NULL, '2025-11-13 14:34:25'),
(680, 'product', 5, 'update', NULL, '2025-11-13 14:34:35'),
(681, 'product', 5, 'update', NULL, '2025-11-13 14:35:05'),
(682, 'product', 5, 'update', NULL, '2025-11-13 15:00:51'),
(683, 'product', 5, 'update', NULL, '2025-11-13 15:01:50'),
(684, 'product', 5, 'update', NULL, '2025-11-13 15:12:41'),
(685, 'product', 5, 'update', NULL, '2025-11-13 15:15:26'),
(686, 'product', 5, 'update', NULL, '2025-11-13 15:27:39'),
(687, 'product', 5, 'update', NULL, '2025-11-13 15:29:18'),
(688, 'product', 5, 'update', NULL, '2025-11-13 15:29:52'),
(689, 'product', 5, 'update', NULL, '2025-11-13 15:38:51'),
(690, 'product', 8, 'update', NULL, '2025-11-15 11:34:18'),
(691, 'product', 13, 'update', NULL, '2025-11-15 11:34:20'),
(692, 'product', 35, 'update', NULL, '2025-11-15 11:34:22'),
(693, 'product', 3, 'update', NULL, '2025-11-15 11:34:24'),
(694, 'product', 8, 'update', NULL, '2025-11-15 11:34:33'),
(695, 'product', 8, 'update', NULL, '2025-11-15 11:39:54'),
(696, 'product', 35, 'update', NULL, '2025-11-15 11:40:55'),
(697, 'product', 5, 'update', NULL, '2025-11-15 12:04:38'),
(698, 'product', 5, 'update', NULL, '2025-11-15 12:04:52'),
(699, 'product', 5, 'update', NULL, '2025-11-15 12:05:18'),
(700, 'product', 5, 'update', NULL, '2025-11-15 12:06:33'),
(701, 'product', 5, 'update', NULL, '2025-11-15 12:06:48'),
(702, 'product', 5, 'update', NULL, '2025-11-15 12:06:54'),
(703, 'product', 5, 'update', NULL, '2025-11-15 12:07:17'),
(704, 'product', 3, 'update', NULL, '2025-11-15 12:17:31'),
(705, 'product', 35, 'update', NULL, '2025-11-15 19:40:05'),
(706, 'product', 42, 'update', NULL, '2025-11-15 19:40:13'),
(707, 'product', 42, 'update', NULL, '2025-11-17 07:45:35'),
(708, 'product', 5, 'update', NULL, '2025-11-17 07:46:03'),
(709, 'product', 5, 'update', NULL, '2025-11-17 07:52:56'),
(710, 'product', 5, 'update', NULL, '2025-11-17 07:53:31'),
(711, 'product', 5, 'update', NULL, '2025-11-17 08:21:34'),
(712, 'product', 5, 'update', NULL, '2025-11-17 08:21:44'),
(713, 'product', 5, 'update', NULL, '2025-11-17 08:26:57'),
(714, 'product', 5, 'update', NULL, '2025-11-17 08:27:38'),
(715, 'product', 5, 'update', NULL, '2025-11-17 08:29:30'),
(716, 'product', 35, 'update', NULL, '2025-11-17 08:33:23'),
(717, 'product', 35, 'update', NULL, '2025-11-17 08:33:52'),
(718, 'product', 5, 'update', NULL, '2025-11-17 08:33:53'),
(719, 'product', 5, 'update', NULL, '2025-11-17 08:36:39'),
(720, 'product', 5, 'update', NULL, '2025-11-17 08:37:09'),
(721, 'product', 35, 'update', NULL, '2025-11-17 08:37:09'),
(722, 'product', 35, 'update', NULL, '2025-11-17 08:37:13'),
(723, 'product', 35, 'update', NULL, '2025-11-17 08:37:17'),
(724, 'product', 35, 'update', NULL, '2025-11-17 08:37:29'),
(725, 'product', 35, 'update', NULL, '2025-11-17 08:37:31'),
(726, 'product', 45, 'update', NULL, '2025-11-17 08:37:36'),
(727, 'product', 42, 'update', NULL, '2025-11-17 08:37:48'),
(728, 'product', 35, 'update', NULL, '2025-11-17 08:38:05'),
(729, 'product', 35, 'update', NULL, '2025-11-17 08:38:11'),
(730, 'product', 35, 'update', NULL, '2025-11-17 08:38:12'),
(731, 'product', 35, 'update', NULL, '2025-11-17 08:38:14'),
(732, 'product', 35, 'update', NULL, '2025-11-17 08:38:16'),
(733, 'product', 35, 'update', NULL, '2025-11-17 08:38:19'),
(734, 'product', 6, 'update', NULL, '2025-11-17 08:38:48'),
(735, 'product', 6, 'update', NULL, '2025-11-17 08:41:26'),
(736, 'product', 45, 'update', NULL, '2025-11-17 08:42:42'),
(737, 'product', 42, 'update', NULL, '2025-11-17 08:43:26'),
(738, 'product', 35, 'update', NULL, '2025-11-17 08:43:30'),
(739, 'product', 42, 'update', NULL, '2025-11-17 08:43:38'),
(740, 'product', 42, 'update', NULL, '2025-11-17 08:44:22'),
(741, 'product', 42, 'update', NULL, '2025-11-17 08:45:35'),
(742, 'product', 42, 'update', NULL, '2025-11-17 08:45:56'),
(743, 'product', 45, 'update', NULL, '2025-11-17 08:46:35'),
(744, 'product', 3, 'update', NULL, '2025-11-17 08:55:00'),
(745, 'product', 35, 'update', NULL, '2025-11-17 08:59:43'),
(746, 'product', 45, 'update', NULL, '2025-11-17 08:59:47'),
(747, 'product', 42, 'update', NULL, '2025-11-17 09:02:56'),
(748, 'product', 5, 'update', NULL, '2025-11-17 09:15:15'),
(749, 'product', 8, 'update', NULL, '2025-11-17 09:18:49'),
(750, 'product', 35, 'update', NULL, '2025-11-17 09:22:05'),
(751, 'product', 5, 'update', NULL, '2025-11-17 09:22:07'),
(752, 'product', 5, 'update', NULL, '2025-11-17 10:25:36'),
(753, 'product', 13, 'update', NULL, '2025-11-17 10:25:43'),
(754, 'product', 6, 'update', NULL, '2025-11-17 10:26:07'),
(755, 'product', 35, 'update', NULL, '2025-11-17 10:26:43'),
(756, 'product', 45, 'update', NULL, '2025-11-17 10:26:44'),
(757, 'product', 42, 'update', NULL, '2025-11-17 10:26:51'),
(758, 'product', 35, 'update', NULL, '2025-11-17 10:37:10'),
(759, 'product', 12, 'update', NULL, '2025-11-17 10:37:13'),
(760, 'product', 6, 'update', NULL, '2025-11-17 10:37:24'),
(761, 'product', 6, 'update', NULL, '2025-11-17 11:17:19'),
(762, 'product', 6, 'update', NULL, '2025-11-17 11:19:25'),
(763, 'product', 47, 'create', NULL, '2025-11-17 11:48:39'),
(764, 'product', 47, 'update', NULL, '2025-11-17 11:49:13'),
(765, 'product', 47, 'update', NULL, '2025-11-17 12:00:39'),
(766, 'product', 47, 'update', NULL, '2025-11-17 12:06:06'),
(767, 'product', 48, 'create', NULL, '2025-11-17 12:08:29'),
(768, 'product', 49, 'create', NULL, '2025-11-17 12:10:25'),
(769, 'product', 3, 'update', NULL, '2025-11-17 12:18:19'),
(770, 'product', 5, 'update', NULL, '2025-11-17 12:18:22'),
(771, 'product', 5, 'update', NULL, '2025-11-17 12:18:56'),
(772, 'product', 5, 'update', NULL, '2025-11-17 12:19:13'),
(773, 'product', 5, 'update', NULL, '2025-11-17 12:19:47'),
(774, 'product', 5, 'update', NULL, '2025-11-17 12:19:53'),
(775, 'product', 5, 'update', NULL, '2025-11-17 12:19:59'),
(776, 'product', 5, 'update', NULL, '2025-11-17 12:20:10'),
(777, 'product', 5, 'update', NULL, '2025-11-17 12:20:26'),
(778, 'product', 35, 'update', NULL, '2025-11-17 13:50:36'),
(779, 'product', 8, 'update', NULL, '2025-11-17 13:51:03'),
(780, 'product', 8, 'update', NULL, '2025-11-17 13:53:27'),
(781, 'product', 8, 'update', NULL, '2025-11-17 14:12:30'),
(782, 'product', 8, 'update', NULL, '2025-11-17 14:29:12'),
(783, 'product', 5, 'update', NULL, '2025-11-17 14:29:27'),
(784, 'product', 5, 'update', NULL, '2025-11-17 14:31:03'),
(785, 'product', 5, 'update', NULL, '2025-11-17 14:31:24'),
(786, 'product', 5, 'update', NULL, '2025-11-17 14:31:26'),
(787, 'product', 5, 'update', NULL, '2025-11-17 14:31:30'),
(788, 'product', 5, 'update', NULL, '2025-11-17 14:33:00'),
(789, 'product', 5, 'update', NULL, '2025-11-17 14:47:34'),
(790, 'product', 5, 'update', NULL, '2025-11-17 14:47:50'),
(791, 'product', 5, 'update', NULL, '2025-11-17 14:48:23'),
(792, 'product', 5, 'update', NULL, '2025-11-17 14:54:08'),
(793, 'product', 5, 'update', NULL, '2025-11-17 14:55:52'),
(794, 'product', 5, 'update', NULL, '2025-11-17 14:56:11'),
(795, 'product', 5, 'update', NULL, '2025-11-17 15:00:42'),
(796, 'product', 5, 'update', NULL, '2025-11-17 15:01:08'),
(797, 'product', 5, 'update', NULL, '2025-11-17 15:42:27'),
(798, 'product', 5, 'update', NULL, '2025-11-17 15:44:56'),
(799, 'product', 35, 'update', NULL, '2025-11-18 08:26:38'),
(800, 'product', 5, 'update', NULL, '2025-11-18 08:26:58'),
(801, 'product', 5, 'update', NULL, '2025-11-18 08:27:41'),
(802, 'product', 5, 'update', NULL, '2025-11-18 08:32:51'),
(803, 'product', 5, 'update', NULL, '2025-11-18 08:33:09'),
(804, 'product', 5, 'update', NULL, '2025-11-18 08:33:32'),
(805, 'product', 5, 'update', NULL, '2025-11-18 08:34:10'),
(806, 'product', 5, 'update', NULL, '2025-11-18 08:34:18'),
(807, 'product', 5, 'update', NULL, '2025-11-18 08:34:57'),
(808, 'product', 5, 'update', NULL, '2025-11-18 08:36:05'),
(809, 'product', 5, 'update', NULL, '2025-11-18 08:36:47'),
(810, 'product', 5, 'update', NULL, '2025-11-18 09:10:14'),
(811, 'product', 5, 'update', NULL, '2025-11-18 09:10:38'),
(812, 'product', 5, 'update', NULL, '2025-11-18 09:10:55'),
(813, 'product', 29, 'update', NULL, '2025-11-18 09:11:56'),
(814, 'product', 5, 'update', NULL, '2025-11-18 09:16:39'),
(815, 'product', 5, 'update', NULL, '2025-11-18 09:18:08'),
(816, 'product', 5, 'update', NULL, '2025-11-18 09:21:09'),
(817, 'product', 29, 'update', NULL, '2025-11-18 09:22:00'),
(818, 'product', 6, 'update', NULL, '2025-11-18 09:22:04'),
(819, 'product', 47, 'update', NULL, '2025-11-18 09:22:45'),
(820, 'product', 6, 'update', NULL, '2025-11-18 09:23:29'),
(821, 'product', 5, 'update', NULL, '2025-11-18 09:23:49'),
(822, 'product', 6, 'update', NULL, '2025-11-18 09:25:03'),
(823, 'product', 5, 'update', NULL, '2025-11-18 09:25:19'),
(824, 'product', 5, 'update', NULL, '2025-11-18 09:26:09'),
(825, 'product', 5, 'update', NULL, '2025-11-18 09:33:17'),
(826, 'product', 48, 'update', NULL, '2025-11-18 09:54:31'),
(827, 'product', 48, 'update', NULL, '2025-11-18 09:54:45'),
(828, 'product', 5, 'update', NULL, '2025-11-18 09:55:17'),
(829, 'product', 5, 'update', NULL, '2025-11-18 09:55:33'),
(830, 'product', 5, 'update', NULL, '2025-11-18 10:19:05'),
(831, 'product', 6, 'update', NULL, '2025-11-18 10:19:24'),
(832, 'product', 5, 'update', NULL, '2025-11-18 10:56:53'),
(833, 'product', 5, 'update', NULL, '2025-11-18 10:57:09'),
(834, 'product', 6, 'update', NULL, '2025-11-18 10:57:46'),
(835, 'product', 5, 'update', NULL, '2025-11-18 12:42:30'),
(836, 'product', 5, 'update', NULL, '2025-11-18 12:59:38'),
(837, 'product', 5, 'update', NULL, '2025-11-18 12:59:48'),
(838, 'product', 6, 'update', NULL, '2025-11-18 13:00:03'),
(839, 'product', 7, 'update', NULL, '2025-11-18 13:00:12'),
(840, 'product', 3, 'update', NULL, '2025-11-18 13:00:22'),
(841, 'product', 5, 'update', NULL, '2025-11-18 14:00:45'),
(842, 'product', 5, 'update', NULL, '2025-11-18 14:01:15'),
(843, 'product', 5, 'update', NULL, '2025-11-18 14:46:43'),
(844, 'product', 5, 'update', NULL, '2025-11-18 15:03:00'),
(845, 'product', 5, 'update', NULL, '2025-11-18 15:03:07'),
(846, 'product', 5, 'update', NULL, '2025-11-18 15:04:01'),
(847, 'product', 5, 'update', NULL, '2025-11-18 15:04:39'),
(848, 'product', 5, 'update', NULL, '2025-11-18 15:05:09'),
(849, 'product', 5, 'update', NULL, '2025-11-18 15:10:15'),
(850, 'product', 5, 'update', NULL, '2025-11-18 15:10:20'),
(851, 'product', 5, 'update', NULL, '2025-11-18 15:10:58'),
(852, 'product', 5, 'update', NULL, '2025-11-18 15:11:10'),
(853, 'product', 5, 'update', NULL, '2025-11-18 15:11:33'),
(854, 'product', 5, 'update', NULL, '2025-11-18 15:11:50'),
(855, 'product', 7, 'update', NULL, '2025-11-18 16:07:48');
INSERT INTO `elasticsearch_sync_queue` (`id`, `entity_type`, `entity_id`, `action`, `synced_at`, `created_at`) VALUES
(856, 'product', 5, 'update', NULL, '2025-11-18 16:08:18'),
(857, 'product', 5, 'update', NULL, '2025-11-18 16:08:43'),
(858, 'product', 6, 'update', NULL, '2025-11-19 08:09:07'),
(859, 'product', 5, 'update', NULL, '2025-11-19 08:09:15'),
(860, 'product', 6, 'update', NULL, '2025-11-19 08:09:27'),
(861, 'product', 5, 'update', NULL, '2025-11-19 08:09:57'),
(862, 'product', 5, 'update', NULL, '2025-11-19 08:10:13'),
(863, 'product', 6, 'update', NULL, '2025-11-19 08:10:23'),
(864, 'product', 5, 'update', NULL, '2025-11-19 08:15:36'),
(865, 'product', 5, 'update', NULL, '2025-11-19 08:44:45'),
(866, 'product', 5, 'update', NULL, '2025-11-19 08:45:42'),
(867, 'product', 6, 'update', NULL, '2025-11-19 08:45:46'),
(868, 'product', 6, 'update', NULL, '2025-11-19 08:49:42'),
(869, 'product', 6, 'update', NULL, '2025-11-19 08:49:58'),
(870, 'product', 6, 'update', NULL, '2025-11-19 08:56:13'),
(871, 'product', 35, 'update', NULL, '2025-11-19 08:58:28'),
(872, 'product', 29, 'update', NULL, '2025-11-19 09:55:48'),
(873, 'product', 45, 'update', NULL, '2025-11-19 09:55:51'),
(874, 'product', 5, 'update', NULL, '2025-11-19 09:55:57'),
(875, 'product', 5, 'update', NULL, '2025-11-19 09:56:23'),
(876, 'product', 5, 'update', NULL, '2025-11-19 09:56:41'),
(877, 'product', 5, 'update', NULL, '2025-11-19 10:54:33'),
(878, 'product', 5, 'update', NULL, '2025-11-19 10:54:54'),
(879, 'product', 5, 'update', NULL, '2025-11-19 10:55:42'),
(880, 'product', 5, 'update', NULL, '2025-11-19 10:55:56'),
(881, 'product', 5, 'update', NULL, '2025-11-20 07:55:50'),
(882, 'product', 5, 'update', NULL, '2025-11-21 08:44:56'),
(883, 'product', 5, 'update', NULL, '2025-11-21 08:59:22'),
(884, 'product', 5, 'update', NULL, '2025-11-21 09:05:15'),
(885, 'product', 5, 'update', NULL, '2025-11-21 09:06:10'),
(886, 'product', 5, 'update', NULL, '2025-11-21 09:07:07'),
(887, 'product', 5, 'update', NULL, '2025-11-21 09:07:44'),
(888, 'product', 5, 'update', NULL, '2025-11-21 09:08:02'),
(889, 'product', 5, 'update', NULL, '2025-11-21 09:08:24'),
(890, 'product', 5, 'update', NULL, '2025-11-21 09:09:28'),
(891, 'product', 5, 'update', NULL, '2025-11-21 09:09:45'),
(892, 'product', 5, 'update', NULL, '2025-11-21 09:11:15'),
(893, 'product', 5, 'update', NULL, '2025-11-21 09:11:33'),
(894, 'product', 5, 'update', NULL, '2025-11-21 09:12:00'),
(895, 'product', 5, 'update', NULL, '2025-11-21 09:17:24'),
(896, 'product', 12, 'update', NULL, '2025-11-29 12:32:27'),
(897, 'product', 5, 'update', NULL, '2025-12-02 13:39:02'),
(898, 'product', 50, 'create', NULL, '2025-12-02 14:18:00'),
(899, 'product', 51, 'create', NULL, '2025-12-05 08:34:13'),
(900, 'product', 51, 'update', NULL, '2025-12-05 08:49:30'),
(901, 'product', 5, 'update', NULL, '2025-12-05 08:49:46'),
(902, 'product', 52, 'create', NULL, '2025-12-05 08:51:56'),
(903, 'product', 52, 'update', NULL, '2025-12-05 08:52:06'),
(904, 'product', 51, 'update', NULL, '2025-12-05 08:52:17'),
(905, 'product', 52, 'update', NULL, '2025-12-05 08:52:19'),
(906, 'product', 52, 'update', NULL, '2025-12-05 08:52:43'),
(907, 'product', 52, 'update', NULL, '2025-12-05 09:00:11'),
(908, 'product', 51, 'update', NULL, '2025-12-05 09:00:16'),
(909, 'product', 35, 'update', NULL, '2025-12-05 09:01:56'),
(910, 'product', 3, 'update', NULL, '2025-12-05 09:01:59'),
(911, 'product', 3, 'update', NULL, '2025-12-05 09:02:02'),
(912, 'product', 7, 'update', NULL, '2025-12-05 09:02:13'),
(913, 'product', 7, 'update', NULL, '2025-12-05 09:02:28'),
(914, 'product', 52, 'update', NULL, '2025-12-05 09:14:37'),
(915, 'product', 3, 'update', NULL, '2025-12-05 09:17:24'),
(916, 'product', 7, 'update', NULL, '2025-12-05 09:17:33'),
(917, 'product', 3, 'update', NULL, '2025-12-05 09:17:54'),
(918, 'product', 8, 'update', NULL, '2025-12-05 10:58:24'),
(919, 'product', 4, 'update', NULL, '2025-12-05 10:58:38'),
(920, 'product', 5, 'update', NULL, '2025-12-05 10:59:16'),
(921, 'product', 53, 'create', NULL, '2025-12-05 11:29:36'),
(922, 'product', 53, 'update', NULL, '2025-12-05 11:30:17'),
(923, 'product', 5, 'update', NULL, '2025-12-05 11:30:28'),
(924, 'product', 53, 'update', NULL, '2025-12-05 11:31:40'),
(925, 'product', 35, 'update', NULL, '2025-12-05 13:27:53'),
(926, 'product', 35, 'update', NULL, '2025-12-05 13:27:56'),
(927, 'product', 45, 'update', NULL, '2025-12-05 13:27:59'),
(928, 'product', 52, 'update', NULL, '2025-12-05 13:28:27'),
(929, 'product', 52, 'update', NULL, '2025-12-05 13:28:33'),
(930, 'product', 35, 'update', NULL, '2025-12-05 13:31:00'),
(931, 'product', 35, 'update', NULL, '2025-12-05 13:31:05'),
(932, 'product', 35, 'update', NULL, '2025-12-05 13:31:30'),
(933, 'product', 35, 'update', NULL, '2025-12-05 13:31:36'),
(934, 'product', 45, 'update', NULL, '2025-12-05 13:31:48'),
(935, 'product', 51, 'update', NULL, '2025-12-05 13:32:09'),
(936, 'product', 52, 'update', NULL, '2025-12-05 13:32:18'),
(937, 'product', 52, 'update', NULL, '2025-12-05 13:32:29'),
(938, 'product', 35, 'update', NULL, '2025-12-05 13:33:56'),
(939, 'product', 8, 'update', NULL, '2025-12-05 13:40:17'),
(940, 'product', 7, 'update', NULL, '2025-12-05 13:40:19'),
(941, 'product', 35, 'update', NULL, '2025-12-05 13:40:27'),
(942, 'product', 35, 'update', NULL, '2025-12-05 13:41:38'),
(943, 'product', 35, 'update', NULL, '2025-12-05 13:46:53'),
(944, 'product', 35, 'update', NULL, '2025-12-05 13:47:48'),
(945, 'product', 35, 'update', NULL, '2025-12-05 13:48:32'),
(946, 'product', 35, 'update', NULL, '2025-12-05 13:49:31'),
(947, 'product', 35, 'update', NULL, '2025-12-05 13:50:27'),
(948, 'product', 51, 'update', NULL, '2025-12-05 13:53:23'),
(949, 'product', 51, 'update', NULL, '2025-12-05 13:53:30'),
(950, 'product', 52, 'update', NULL, '2025-12-05 14:03:20'),
(951, 'product', 35, 'update', NULL, '2025-12-05 14:09:16'),
(952, 'product', 45, 'update', NULL, '2025-12-05 14:09:22'),
(953, 'product', 35, 'update', NULL, '2025-12-05 14:09:25'),
(954, 'product', 45, 'update', NULL, '2025-12-05 14:09:33'),
(955, 'product', 35, 'update', NULL, '2025-12-05 14:09:55'),
(956, 'product', 35, 'update', NULL, '2025-12-05 14:12:46'),
(957, 'product', 45, 'update', NULL, '2025-12-05 14:13:17'),
(958, 'product', 35, 'update', NULL, '2025-12-05 14:13:22'),
(959, 'product', 51, 'update', NULL, '2025-12-05 14:13:37'),
(960, 'product', 52, 'update', NULL, '2025-12-05 14:13:41'),
(961, 'product', 35, 'update', NULL, '2025-12-05 15:04:05'),
(962, 'product', 35, 'update', NULL, '2025-12-05 15:38:44'),
(963, 'product', 35, 'update', NULL, '2025-12-05 15:46:13'),
(964, 'product', 51, 'update', NULL, '2025-12-05 16:25:06'),
(965, 'product', 35, 'update', NULL, '2025-12-05 16:47:13'),
(966, 'product', 51, 'update', NULL, '2025-12-05 16:59:19'),
(967, 'product', 51, 'update', NULL, '2025-12-05 16:59:25'),
(968, 'product', 35, 'update', NULL, '2025-12-06 07:41:00'),
(969, 'product', 45, 'update', NULL, '2025-12-06 07:41:04'),
(970, 'product', 5, 'update', NULL, '2025-12-06 08:08:30'),
(971, 'product', 53, 'update', NULL, '2025-12-06 08:09:02'),
(972, 'product', 35, 'update', NULL, '2025-12-06 08:10:10'),
(973, 'product', 8, 'update', NULL, '2025-12-06 08:17:41'),
(974, 'product', 8, 'update', NULL, '2025-12-06 08:17:47'),
(975, 'product', 51, 'update', NULL, '2025-12-06 08:20:02'),
(976, 'product', 52, 'update', NULL, '2025-12-06 08:20:21'),
(977, 'product', 5, 'update', NULL, '2025-12-06 08:21:04'),
(978, 'product', 5, 'update', NULL, '2025-12-06 08:21:14'),
(979, 'product', 5, 'update', NULL, '2025-12-06 08:21:48'),
(980, 'product', 51, 'update', NULL, '2025-12-06 08:59:31'),
(981, 'product', 51, 'update', NULL, '2025-12-06 09:02:21'),
(982, 'product', 51, 'update', NULL, '2025-12-06 09:02:26'),
(983, 'product', 51, 'update', NULL, '2025-12-06 09:03:21'),
(984, 'product', 52, 'update', NULL, '2025-12-06 09:12:12'),
(985, 'product', 51, 'update', NULL, '2025-12-06 09:12:37'),
(986, 'product', 52, 'update', NULL, '2025-12-06 09:12:45'),
(987, 'product', 51, 'update', NULL, '2025-12-06 09:12:57'),
(988, 'product', 51, 'update', NULL, '2025-12-06 09:13:07'),
(989, 'product', 51, 'update', NULL, '2025-12-06 09:13:55'),
(990, 'product', 51, 'update', NULL, '2025-12-06 09:14:26'),
(991, 'product', 8, 'update', NULL, '2025-12-06 09:45:42'),
(992, 'product', 6, 'update', NULL, '2025-12-06 09:45:47'),
(993, 'product', 35, 'update', NULL, '2025-12-06 10:02:39'),
(994, 'product', 51, 'update', NULL, '2025-12-06 10:03:37'),
(995, 'product', 51, 'update', NULL, '2025-12-06 10:03:40'),
(996, 'product', 45, 'update', NULL, '2025-12-06 10:08:23'),
(997, 'product', 45, 'update', NULL, '2025-12-06 10:08:26'),
(998, 'product', 35, 'update', NULL, '2025-12-06 10:08:31'),
(999, 'product', 4, 'update', NULL, '2025-12-06 10:12:53'),
(1000, 'product', 3, 'update', NULL, '2025-12-06 10:12:57'),
(1001, 'product', 51, 'update', NULL, '2025-12-06 10:15:03'),
(1002, 'product', 45, 'update', NULL, '2025-12-06 10:20:45'),
(1003, 'product', 51, 'update', NULL, '2025-12-06 10:22:23'),
(1004, 'product', 52, 'update', NULL, '2025-12-06 10:22:33'),
(1005, 'product', 52, 'update', NULL, '2025-12-06 10:22:47'),
(1006, 'product', 6, 'update', NULL, '2025-12-06 10:23:45'),
(1007, 'product', 8, 'update', NULL, '2025-12-06 10:24:00'),
(1008, 'product', 6, 'update', NULL, '2025-12-06 10:24:06'),
(1009, 'product', 5, 'update', NULL, '2025-12-06 10:24:12'),
(1010, 'product', 6, 'update', NULL, '2025-12-06 10:33:17'),
(1011, 'product', 6, 'update', NULL, '2025-12-06 10:33:30'),
(1012, 'product', 6, 'update', NULL, '2025-12-06 10:33:44'),
(1013, 'product', 6, 'update', NULL, '2025-12-06 10:34:14'),
(1014, 'product', 8, 'update', NULL, '2025-12-06 10:35:45'),
(1015, 'product', 8, 'update', NULL, '2025-12-06 10:35:53'),
(1016, 'product', 8, 'update', NULL, '2025-12-06 10:36:27'),
(1017, 'product', 6, 'update', NULL, '2025-12-06 10:37:15'),
(1018, 'product', 7, 'update', NULL, '2025-12-06 10:37:20'),
(1019, 'product', 8, 'update', NULL, '2025-12-06 10:38:43'),
(1020, 'product', 6, 'update', NULL, '2025-12-06 10:40:00'),
(1021, 'product', 8, 'update', NULL, '2025-12-06 10:40:24'),
(1022, 'product', 8, 'update', NULL, '2025-12-06 10:46:39'),
(1023, 'product', 8, 'update', NULL, '2025-12-06 10:46:43'),
(1024, 'product', 8, 'update', NULL, '2025-12-06 10:46:58'),
(1025, 'product', 8, 'update', NULL, '2025-12-06 10:47:00'),
(1026, 'product', 5, 'update', NULL, '2025-12-06 10:47:16'),
(1027, 'product', 5, 'update', NULL, '2025-12-06 10:47:21'),
(1028, 'product', 5, 'update', NULL, '2025-12-06 10:47:55'),
(1029, 'product', 5, 'update', NULL, '2025-12-06 10:48:11'),
(1030, 'product', 6, 'update', NULL, '2025-12-06 10:48:21'),
(1031, 'product', 4, 'update', NULL, '2025-12-06 10:49:57'),
(1032, 'product', 7, 'update', NULL, '2025-12-06 10:50:05'),
(1033, 'product', 7, 'update', NULL, '2025-12-06 10:50:26'),
(1034, 'product', 6, 'update', NULL, '2025-12-06 10:50:29'),
(1035, 'product', 6, 'update', NULL, '2025-12-06 10:50:50'),
(1036, 'product', 51, 'update', NULL, '2025-12-06 10:57:46'),
(1037, 'product', 51, 'update', NULL, '2025-12-06 10:57:55'),
(1038, 'product', 51, 'update', NULL, '2025-12-06 10:58:11'),
(1039, 'product', 51, 'update', NULL, '2025-12-06 10:58:31'),
(1040, 'product', 52, 'update', NULL, '2025-12-06 10:59:05'),
(1041, 'product', 52, 'update', NULL, '2025-12-06 10:59:25'),
(1042, 'product', 52, 'update', NULL, '2025-12-06 10:59:44'),
(1043, 'product', 8, 'update', NULL, '2025-12-06 11:00:15'),
(1044, 'product', 7, 'update', NULL, '2025-12-06 11:00:23'),
(1045, 'product', 6, 'update', NULL, '2025-12-06 11:00:45'),
(1046, 'product', 51, 'update', NULL, '2025-12-06 11:02:16'),
(1047, 'product', 52, 'update', NULL, '2025-12-06 11:02:23'),
(1048, 'product', 8, 'update', NULL, '2025-12-06 11:02:53'),
(1049, 'product', 5, 'update', NULL, '2025-12-06 11:03:03'),
(1050, 'product', 5, 'update', NULL, '2025-12-06 11:03:11'),
(1051, 'product', 5, 'update', NULL, '2025-12-06 11:03:35'),
(1052, 'product', 51, 'update', NULL, '2025-12-06 11:03:50'),
(1053, 'product', 52, 'update', NULL, '2025-12-06 11:03:56'),
(1054, 'product', 52, 'update', NULL, '2025-12-06 11:04:09'),
(1055, 'product', 35, 'update', NULL, '2025-12-06 11:04:19'),
(1056, 'product', 45, 'update', NULL, '2025-12-06 11:04:30'),
(1057, 'product', 45, 'update', NULL, '2025-12-06 11:04:49'),
(1058, 'product', 6, 'update', NULL, '2025-12-06 11:04:59'),
(1059, 'product', 51, 'update', NULL, '2025-12-06 11:22:07'),
(1060, 'product', 35, 'update', NULL, '2025-12-06 11:22:35'),
(1061, 'product', 52, 'update', NULL, '2025-12-06 11:22:42'),
(1062, 'product', 51, 'update', NULL, '2025-12-06 11:22:49'),
(1063, 'product', 35, 'update', NULL, '2025-12-06 11:23:29'),
(1064, 'product', 8, 'update', NULL, '2025-12-06 11:24:27'),
(1065, 'product', 6, 'update', NULL, '2025-12-06 11:24:37'),
(1066, 'product', 5, 'update', NULL, '2025-12-06 11:24:45'),
(1067, 'product', 5, 'update', NULL, '2025-12-06 11:24:50'),
(1068, 'product', 5, 'update', NULL, '2025-12-06 11:25:06'),
(1069, 'product', 6, 'update', NULL, '2025-12-06 11:25:43'),
(1070, 'product', 8, 'update', NULL, '2025-12-06 11:44:59'),
(1071, 'product', 35, 'update', NULL, '2025-12-06 11:57:12'),
(1072, 'product', 35, 'update', NULL, '2025-12-06 11:57:46'),
(1073, 'product', 35, 'update', NULL, '2025-12-06 11:58:20'),
(1074, 'product', 35, 'update', NULL, '2025-12-06 12:03:26'),
(1075, 'product', 35, 'update', NULL, '2025-12-06 12:10:55'),
(1076, 'product', 45, 'update', NULL, '2025-12-06 12:20:26'),
(1077, 'product', 35, 'update', NULL, '2025-12-06 12:21:32'),
(1078, 'product', 35, 'update', NULL, '2025-12-06 12:24:48'),
(1079, 'product', 35, 'update', NULL, '2025-12-06 12:35:19'),
(1080, 'product', 35, 'update', NULL, '2025-12-06 12:35:59'),
(1081, 'product', 35, 'update', NULL, '2025-12-06 12:36:02'),
(1082, 'product', 51, 'update', NULL, '2025-12-06 12:36:22'),
(1083, 'product', 3, 'update', NULL, '2025-12-07 16:40:55'),
(1084, 'product', 3, 'update', NULL, '2025-12-07 16:40:55'),
(1085, 'product', 3, 'update', NULL, '2025-12-07 16:40:57'),
(1086, 'product', 3, 'update', NULL, '2025-12-07 16:40:57'),
(1087, 'product', 3, 'update', NULL, '2025-12-07 16:41:00'),
(1088, 'product', 3, 'update', NULL, '2025-12-07 16:41:00'),
(1089, 'product', 3, 'update', NULL, '2025-12-07 16:41:00'),
(1090, 'product', 3, 'update', NULL, '2025-12-07 16:41:02'),
(1091, 'product', 3, 'update', NULL, '2025-12-07 16:41:02'),
(1092, 'product', 3, 'update', NULL, '2025-12-07 16:41:02'),
(1093, 'product', 3, 'update', NULL, '2025-12-07 16:41:02'),
(1094, 'product', 3, 'update', NULL, '2025-12-07 16:41:03'),
(1095, 'product', 13, 'update', NULL, '2025-12-07 16:41:17'),
(1096, 'product', 13, 'update', NULL, '2025-12-07 16:41:18'),
(1097, 'product', 13, 'update', NULL, '2025-12-07 16:41:18'),
(1098, 'product', 13, 'update', NULL, '2025-12-07 16:41:19'),
(1099, 'product', 13, 'update', NULL, '2025-12-07 16:41:19'),
(1100, 'product', 13, 'update', NULL, '2025-12-07 16:41:19'),
(1101, 'product', 13, 'update', NULL, '2025-12-07 16:41:19'),
(1102, 'product', 35, 'update', NULL, '2025-12-07 16:44:36'),
(1103, 'product', 35, 'update', NULL, '2025-12-07 16:44:37'),
(1104, 'product', 35, 'update', NULL, '2025-12-07 16:44:37'),
(1105, 'product', 35, 'update', NULL, '2025-12-07 16:44:38'),
(1106, 'product', 35, 'update', NULL, '2025-12-07 16:44:39'),
(1107, 'product', 35, 'update', NULL, '2025-12-07 16:44:39'),
(1108, 'product', 12, 'update', NULL, '2025-12-07 16:44:49'),
(1109, 'product', 12, 'update', NULL, '2025-12-07 16:44:49'),
(1110, 'product', 12, 'update', NULL, '2025-12-07 16:44:49'),
(1111, 'product', 12, 'update', NULL, '2025-12-07 16:44:49'),
(1112, 'product', 12, 'update', NULL, '2025-12-07 16:45:13'),
(1113, 'product', 12, 'update', NULL, '2025-12-07 16:45:13'),
(1114, 'product', 8, 'update', NULL, '2025-12-07 16:54:05'),
(1115, 'product', 4, 'update', NULL, '2025-12-08 07:47:09'),
(1116, 'product', 4, 'update', NULL, '2025-12-08 07:47:09'),
(1117, 'product', 4, 'update', NULL, '2025-12-08 07:47:09'),
(1118, 'product', 4, 'update', NULL, '2025-12-08 07:47:10'),
(1119, 'product', 4, 'update', NULL, '2025-12-08 07:47:10'),
(1120, 'product', 42, 'update', NULL, '2025-12-08 07:47:11'),
(1121, 'product', 42, 'update', NULL, '2025-12-08 07:47:11'),
(1122, 'product', 42, 'update', NULL, '2025-12-08 07:47:11'),
(1123, 'product', 13, 'update', NULL, '2025-12-08 07:47:12'),
(1124, 'product', 13, 'update', NULL, '2025-12-08 07:47:12'),
(1125, 'product', 13, 'update', NULL, '2025-12-08 07:47:13'),
(1126, 'product', 8, 'update', NULL, '2025-12-08 07:48:54'),
(1127, 'product', 8, 'update', NULL, '2025-12-08 07:48:54'),
(1128, 'product', 8, 'update', NULL, '2025-12-08 07:48:54'),
(1129, 'product', 29, 'update', NULL, '2025-12-08 07:48:55'),
(1130, 'product', 29, 'update', NULL, '2025-12-08 07:48:56'),
(1131, 'product', 29, 'update', NULL, '2025-12-08 07:48:56'),
(1132, 'product', 29, 'update', NULL, '2025-12-08 07:48:56'),
(1133, 'product', 35, 'update', NULL, '2025-12-08 07:48:57'),
(1134, 'product', 35, 'update', NULL, '2025-12-08 07:48:57'),
(1135, 'product', 35, 'update', NULL, '2025-12-08 07:48:59'),
(1136, 'product', 35, 'update', NULL, '2025-12-08 07:49:00'),
(1137, 'product', 51, 'update', NULL, '2025-12-08 07:51:58'),
(1138, 'product', 51, 'update', NULL, '2025-12-08 08:19:26'),
(1139, 'product', 35, 'update', NULL, '2025-12-08 08:19:57'),
(1140, 'product', 6, 'update', NULL, '2025-12-08 08:20:14'),
(1141, 'product', 35, 'update', NULL, '2025-12-08 08:20:34'),
(1142, 'product', 51, 'update', NULL, '2025-12-08 08:20:48'),
(1143, 'product', 35, 'update', NULL, '2025-12-08 08:21:03'),
(1144, 'product', 52, 'update', NULL, '2025-12-08 08:21:32'),
(1145, 'product', 52, 'update', NULL, '2025-12-08 08:22:47'),
(1146, 'product', 51, 'update', NULL, '2025-12-08 08:22:50'),
(1147, 'product', 52, 'update', NULL, '2025-12-08 08:22:54'),
(1148, 'product', 51, 'update', NULL, '2025-12-08 08:22:56'),
(1149, 'product', 51, 'update', NULL, '2025-12-08 08:23:03'),
(1150, 'product', 51, 'update', NULL, '2025-12-08 08:23:13'),
(1151, 'product', 51, 'update', NULL, '2025-12-08 08:23:26'),
(1152, 'product', 35, 'update', NULL, '2025-12-08 08:23:33'),
(1153, 'product', 35, 'update', NULL, '2025-12-08 08:23:41'),
(1154, 'product', 8, 'update', NULL, '2025-12-08 08:23:53'),
(1155, 'product', 7, 'update', NULL, '2025-12-08 08:24:05'),
(1156, 'product', 51, 'update', NULL, '2025-12-08 08:24:23'),
(1157, 'product', 52, 'update', NULL, '2025-12-08 08:24:29'),
(1158, 'product', 7, 'update', NULL, '2025-12-08 08:24:51'),
(1159, 'product', 51, 'update', NULL, '2025-12-08 08:25:13'),
(1160, 'product', 51, 'update', NULL, '2025-12-08 08:25:16'),
(1161, 'product', 8, 'update', NULL, '2025-12-08 08:25:53'),
(1162, 'product', 7, 'update', NULL, '2025-12-08 08:25:57'),
(1163, 'product', 51, 'update', NULL, '2025-12-08 08:27:08'),
(1164, 'product', 51, 'update', NULL, '2025-12-08 08:29:19'),
(1165, 'product', 8, 'update', NULL, '2025-12-08 08:29:39'),
(1166, 'product', 7, 'update', NULL, '2025-12-08 08:29:41'),
(1167, 'product', 52, 'update', NULL, '2025-12-08 08:39:16'),
(1168, 'product', 52, 'update', NULL, '2025-12-08 08:40:22'),
(1169, 'product', 7, 'update', NULL, '2025-12-08 08:40:43'),
(1170, 'product', 8, 'update', NULL, '2025-12-08 08:40:59'),
(1171, 'product', 6, 'update', NULL, '2025-12-08 08:41:11'),
(1172, 'product', 51, 'update', NULL, '2025-12-08 08:42:10'),
(1173, 'product', 52, 'update', NULL, '2025-12-08 08:42:24'),
(1174, 'product', 35, 'update', NULL, '2025-12-08 08:43:02'),
(1175, 'product', 45, 'update', NULL, '2025-12-08 08:43:26'),
(1176, 'product', 35, 'update', NULL, '2025-12-08 08:43:35'),
(1177, 'product', 29, 'update', NULL, '2025-12-08 08:44:14'),
(1178, 'product', 7, 'update', NULL, '2025-12-08 08:44:44'),
(1179, 'product', 45, 'update', NULL, '2025-12-08 08:46:38'),
(1180, 'product', 7, 'update', NULL, '2025-12-08 08:46:45'),
(1181, 'product', 45, 'update', NULL, '2025-12-08 08:47:08'),
(1182, 'product', 51, 'update', NULL, '2025-12-08 08:55:24'),
(1183, 'product', 35, 'update', NULL, '2025-12-08 08:56:21'),
(1184, 'product', 52, 'update', NULL, '2025-12-08 09:17:00'),
(1185, 'product', 54, 'create', NULL, '2025-12-08 09:55:09'),
(1186, 'product', 55, 'create', NULL, '2025-12-08 09:56:36'),
(1187, 'product', 56, 'create', NULL, '2025-12-08 09:57:46'),
(1188, 'product', 57, 'create', NULL, '2025-12-08 09:59:03'),
(1189, 'product', 57, 'delete', NULL, '2025-12-08 09:59:09'),
(1190, 'product', 56, 'delete', NULL, '2025-12-08 09:59:12'),
(1191, 'product', 55, 'delete', NULL, '2025-12-08 09:59:14'),
(1192, 'product', 54, 'update', NULL, '2025-12-08 10:03:25'),
(1193, 'product', 54, 'update', NULL, '2025-12-08 10:05:03'),
(1194, 'product', 52, 'update', NULL, '2025-12-08 10:06:00'),
(1195, 'product', 51, 'update', NULL, '2025-12-08 10:25:21'),
(1196, 'product', 35, 'update', NULL, '2025-12-08 10:25:36'),
(1197, 'product', 35, 'update', NULL, '2025-12-08 10:26:46'),
(1198, 'product', 58, 'create', NULL, '2025-12-08 10:32:27'),
(1199, 'product', 59, 'create', NULL, '2025-12-08 10:35:09'),
(1200, 'product', 51, 'update', NULL, '2025-12-08 10:45:25'),
(1201, 'product', 51, 'update', NULL, '2025-12-08 10:45:29'),
(1202, 'product', 51, 'update', NULL, '2025-12-08 10:47:14'),
(1203, 'product', 51, 'update', NULL, '2025-12-08 10:47:17'),
(1204, 'product', 58, 'update', NULL, '2025-12-08 10:55:33'),
(1205, 'product', 51, 'update', NULL, '2025-12-08 10:55:42'),
(1206, 'product', 52, 'update', NULL, '2025-12-08 10:55:50'),
(1207, 'product', 35, 'update', NULL, '2025-12-08 10:56:36'),
(1208, 'product', 45, 'update', NULL, '2025-12-08 10:56:47'),
(1209, 'product', 42, 'update', NULL, '2025-12-08 10:57:14'),
(1210, 'product', 8, 'update', NULL, '2025-12-08 10:57:58'),
(1211, 'product', 5, 'update', NULL, '2025-12-08 10:58:05'),
(1212, 'product', 51, 'update', NULL, '2025-12-08 10:58:33'),
(1213, 'product', 52, 'update', NULL, '2025-12-08 10:58:38'),
(1214, 'product', 58, 'update', NULL, '2025-12-08 10:58:50'),
(1215, 'product', 51, 'update', NULL, '2025-12-08 11:58:09'),
(1216, 'product', 60, 'create', NULL, '2025-12-08 12:06:07'),
(1217, 'product', 60, 'delete', NULL, '2025-12-08 12:06:13'),
(1218, 'product', 35, 'update', NULL, '2025-12-09 09:14:44'),
(1219, 'product', 35, 'update', NULL, '2025-12-09 09:30:04'),
(1220, 'product', 45, 'update', NULL, '2025-12-09 09:30:13'),
(1221, 'product', 51, 'update', NULL, '2025-12-09 09:30:19'),
(1222, 'product', 59, 'update', NULL, '2025-12-09 09:35:21'),
(1223, 'product', 51, 'update', NULL, '2025-12-09 09:43:44'),
(1224, 'product', 52, 'update', NULL, '2025-12-09 09:43:51'),
(1225, 'product', 52, 'update', NULL, '2025-12-09 10:29:04'),
(1226, 'product', 51, 'update', NULL, '2025-12-09 10:34:06'),
(1227, 'product', 52, 'update', NULL, '2025-12-09 10:34:41'),
(1228, 'product', 59, 'update', NULL, '2025-12-09 10:35:07'),
(1229, 'product', 3, 'update', NULL, '2025-12-09 12:41:20'),
(1230, 'product', 54, 'update', NULL, '2025-12-09 13:29:59'),
(1231, 'product', 51, 'update', NULL, '2025-12-09 13:54:22'),
(1232, 'product', 51, 'update', NULL, '2025-12-09 13:56:48'),
(1233, 'product', 52, 'update', NULL, '2025-12-09 13:56:53'),
(1234, 'product', 35, 'update', NULL, '2025-12-09 14:19:26'),
(1235, 'product', 51, 'update', NULL, '2025-12-09 14:25:19'),
(1236, 'product', 51, 'update', NULL, '2025-12-09 14:28:20'),
(1237, 'product', 52, 'update', NULL, '2025-12-09 14:28:26'),
(1238, 'product', 51, 'update', NULL, '2025-12-09 14:28:29'),
(1239, 'product', 51, 'update', NULL, '2025-12-09 14:30:02'),
(1240, 'product', 51, 'update', NULL, '2025-12-09 14:58:37'),
(1241, 'product', 51, 'update', NULL, '2025-12-09 14:58:42'),
(1242, 'product', 35, 'update', NULL, '2025-12-09 14:59:41'),
(1243, 'product', 35, 'update', NULL, '2025-12-09 15:04:16'),
(1244, 'product', 45, 'update', NULL, '2025-12-09 15:11:09'),
(1245, 'product', 35, 'update', NULL, '2025-12-09 15:11:13'),
(1246, 'product', 35, 'update', NULL, '2025-12-09 16:21:43'),
(1247, 'product', 51, 'update', NULL, '2025-12-10 08:08:13'),
(1248, 'product', 61, 'create', NULL, '2025-12-10 08:41:12'),
(1249, 'product', 61, 'update', NULL, '2025-12-10 08:43:13'),
(1250, 'product', 51, 'update', NULL, '2025-12-10 08:43:35'),
(1251, 'product', 7, 'update', NULL, '2025-12-10 08:43:52'),
(1252, 'product', 61, 'update', NULL, '2025-12-10 08:45:07'),
(1253, 'product', 51, 'update', NULL, '2025-12-10 08:49:25'),
(1254, 'product', 35, 'update', NULL, '2025-12-10 08:50:05'),
(1255, 'product', 45, 'update', NULL, '2025-12-10 08:50:19'),
(1256, 'product', 35, 'update', NULL, '2025-12-10 08:50:21'),
(1257, 'product', 35, 'update', NULL, '2025-12-10 09:20:40'),
(1258, 'product', 35, 'update', NULL, '2025-12-10 09:20:49'),
(1259, 'product', 35, 'update', NULL, '2025-12-10 09:25:46'),
(1260, 'product', 35, 'update', NULL, '2025-12-10 09:25:50'),
(1261, 'product', 45, 'update', NULL, '2025-12-10 09:25:55'),
(1262, 'product', 35, 'update', NULL, '2025-12-10 09:40:27'),
(1263, 'product', 35, 'update', NULL, '2025-12-10 09:40:31'),
(1264, 'product', 35, 'update', NULL, '2025-12-10 09:40:42'),
(1265, 'product', 35, 'update', NULL, '2025-12-10 09:48:47'),
(1266, 'product', 59, 'update', NULL, '2025-12-10 09:53:47'),
(1267, 'product', 35, 'update', NULL, '2025-12-10 09:55:51'),
(1268, 'product', 61, 'update', NULL, '2025-12-10 09:56:04'),
(1269, 'product', 51, 'update', NULL, '2025-12-10 09:56:35'),
(1270, 'product', 59, 'update', NULL, '2025-12-10 09:57:05'),
(1271, 'product', 59, 'update', NULL, '2025-12-10 09:57:34'),
(1272, 'product', 6, 'update', NULL, '2025-12-10 10:10:22'),
(1273, 'product', 51, 'update', NULL, '2025-12-10 10:10:44'),
(1274, 'product', 59, 'update', NULL, '2025-12-10 10:10:57'),
(1275, 'product', 52, 'update', NULL, '2025-12-10 10:11:15'),
(1276, 'product', 52, 'update', NULL, '2025-12-10 10:11:52'),
(1277, 'product', 52, 'update', NULL, '2025-12-10 10:12:28'),
(1278, 'product', 58, 'update', NULL, '2025-12-10 10:12:47'),
(1279, 'product', 61, 'update', NULL, '2025-12-10 10:12:53'),
(1280, 'product', 8, 'update', NULL, '2025-12-10 10:14:00'),
(1281, 'product', 5, 'update', NULL, '2025-12-10 10:14:07'),
(1282, 'product', 5, 'update', NULL, '2025-12-10 10:14:10'),
(1283, 'product', 61, 'update', NULL, '2025-12-10 10:58:23'),
(1284, 'product', 3, 'update', NULL, '2025-12-10 11:00:19'),
(1285, 'product', 3, 'update', NULL, '2025-12-10 11:00:25'),
(1286, 'product', 58, 'update', NULL, '2025-12-10 11:00:29'),
(1287, 'product', 3, 'update', NULL, '2025-12-10 11:00:32'),
(1288, 'product', 58, 'update', NULL, '2025-12-10 11:01:01'),
(1289, 'product', 58, 'update', NULL, '2025-12-10 11:01:14'),
(1290, 'product', 62, 'create', NULL, '2025-12-10 13:03:21'),
(1291, 'product', 62, 'update', NULL, '2025-12-10 13:50:05'),
(1292, 'product', 61, 'update', NULL, '2025-12-10 20:22:18'),
(1293, 'product', 54, 'update', NULL, '2025-12-11 15:14:17'),
(1294, 'product', 61, 'update', NULL, '2025-12-12 09:01:46'),
(1295, 'product', 8, 'update', NULL, '2025-12-12 10:30:47'),
(1296, 'product', 61, 'update', NULL, '2025-12-12 10:32:17'),
(1297, 'product', 45, 'update', NULL, '2025-12-12 10:33:42'),
(1298, 'product', 3, 'update', NULL, '2025-12-12 10:34:17'),
(1299, 'product', 35, 'update', NULL, '2025-12-12 10:35:12'),
(1300, 'product', 63, 'create', NULL, '2025-12-12 10:44:28'),
(1301, 'product', 63, 'delete', NULL, '2025-12-12 10:44:34'),
(1302, 'product', 51, 'update', NULL, '2025-12-12 11:04:35'),
(1303, 'product', 52, 'update', NULL, '2025-12-12 11:04:45'),
(1304, 'product', 58, 'update', NULL, '2025-12-12 11:05:02'),
(1305, 'product', 35, 'update', NULL, '2025-12-12 12:13:19'),
(1306, 'product', 35, 'update', NULL, '2025-12-12 12:13:26'),
(1307, 'product', 61, 'update', NULL, '2025-12-15 08:15:29'),
(1308, 'product', 51, 'update', NULL, '2025-12-15 08:15:46'),
(1309, 'product', 35, 'update', NULL, '2025-12-15 08:16:12'),
(1310, 'product', 35, 'update', NULL, '2025-12-15 08:19:54'),
(1311, 'product', 45, 'update', NULL, '2025-12-15 08:20:00'),
(1312, 'product', 42, 'update', NULL, '2025-12-15 08:20:10'),
(1313, 'product', 61, 'update', NULL, '2025-12-15 08:20:17'),
(1314, 'product', 59, 'update', NULL, '2025-12-15 08:20:24'),
(1315, 'product', 58, 'update', NULL, '2025-12-15 08:20:58'),
(1316, 'product', 61, 'update', NULL, '2025-12-15 08:24:08'),
(1317, 'product', 62, 'update', NULL, '2025-12-15 08:27:46'),
(1318, 'product', 13, 'update', NULL, '2025-12-15 08:28:26'),
(1319, 'product', 61, 'update', NULL, '2025-12-15 08:28:33'),
(1320, 'product', 61, 'update', NULL, '2025-12-15 08:31:10'),
(1321, 'product', 61, 'update', NULL, '2025-12-15 08:31:58'),
(1322, 'product', 61, 'update', NULL, '2025-12-15 08:33:41'),
(1323, 'product', 61, 'update', NULL, '2025-12-15 08:33:58'),
(1324, 'product', 51, 'update', NULL, '2025-12-15 08:37:14'),
(1325, 'product', 61, 'update', NULL, '2025-12-15 08:39:37'),
(1326, 'product', 61, 'update', NULL, '2025-12-15 08:53:48'),
(1327, 'product', 61, 'update', NULL, '2025-12-15 08:58:42'),
(1328, 'product', 51, 'update', NULL, '2025-12-15 09:00:46'),
(1329, 'product', 59, 'update', NULL, '2025-12-15 09:01:32'),
(1330, 'product', 51, 'update', NULL, '2025-12-15 09:11:17'),
(1331, 'product', 3, 'update', NULL, '2025-12-15 09:13:09'),
(1332, 'product', 3, 'update', NULL, '2025-12-15 09:14:19'),
(1333, 'product', 3, 'update', NULL, '2025-12-15 09:15:19'),
(1334, 'product', 3, 'update', NULL, '2025-12-15 09:15:56'),
(1335, 'product', 3, 'update', NULL, '2025-12-15 09:17:37'),
(1336, 'product', 3, 'update', NULL, '2025-12-15 09:19:34'),
(1337, 'product', 7, 'update', NULL, '2025-12-15 09:22:45'),
(1338, 'product', 3, 'update', NULL, '2025-12-15 09:23:56'),
(1339, 'product', 35, 'update', NULL, '2025-12-15 09:24:25'),
(1340, 'product', 3, 'update', NULL, '2025-12-15 09:24:55'),
(1341, 'product', 3, 'update', NULL, '2025-12-15 09:25:36'),
(1342, 'product', 61, 'update', NULL, '2025-12-15 09:25:50'),
(1343, 'product', 61, 'update', NULL, '2025-12-15 09:26:22'),
(1344, 'product', 3, 'update', NULL, '2025-12-15 09:27:33'),
(1345, 'product', 62, 'update', NULL, '2025-12-15 09:30:54'),
(1346, 'product', 62, 'update', NULL, '2025-12-15 09:31:56'),
(1347, 'product', 59, 'update', NULL, '2025-12-15 09:32:06'),
(1348, 'product', 53, 'update', NULL, '2025-12-15 09:32:11'),
(1349, 'product', 54, 'update', NULL, '2025-12-15 09:38:19'),
(1350, 'product', 62, 'update', NULL, '2025-12-15 09:38:21'),
(1351, 'product', 62, 'update', NULL, '2025-12-15 09:56:09'),
(1352, 'product', 54, 'update', NULL, '2025-12-15 09:58:50'),
(1353, 'product', 62, 'update', NULL, '2025-12-15 10:01:56'),
(1354, 'product', 62, 'update', NULL, '2025-12-15 10:17:37'),
(1355, 'product', 62, 'update', NULL, '2025-12-15 10:17:49'),
(1356, 'product', 62, 'update', NULL, '2025-12-15 10:17:58'),
(1357, 'product', 61, 'update', NULL, '2025-12-15 10:18:16'),
(1358, 'product', 61, 'update', NULL, '2025-12-15 10:18:21'),
(1359, 'product', 61, 'update', NULL, '2025-12-15 10:18:56'),
(1360, 'product', 61, 'update', NULL, '2025-12-15 10:19:48'),
(1361, 'product', 61, 'update', NULL, '2025-12-15 10:21:10'),
(1362, 'product', 51, 'update', NULL, '2025-12-15 10:21:46'),
(1363, 'product', 51, 'update', NULL, '2025-12-15 10:22:38'),
(1364, 'product', 59, 'update', NULL, '2025-12-15 10:27:22'),
(1365, 'product', 59, 'update', NULL, '2025-12-15 10:27:38'),
(1366, 'product', 59, 'update', NULL, '2025-12-15 10:28:18'),
(1367, 'product', 3, 'update', NULL, '2025-12-15 10:33:22'),
(1368, 'product', 62, 'update', NULL, '2025-12-15 10:33:57'),
(1369, 'product', 53, 'update', NULL, '2025-12-15 10:40:23'),
(1370, 'product', 62, 'update', NULL, '2025-12-15 10:42:57'),
(1371, 'product', 6, 'update', NULL, '2025-12-15 10:46:42'),
(1372, 'product', 6, 'update', NULL, '2025-12-15 10:46:48'),
(1373, 'product', 3, 'update', NULL, '2025-12-15 10:49:28'),
(1374, 'product', 51, 'update', NULL, '2025-12-15 10:50:35'),
(1375, 'product', 51, 'update', NULL, '2025-12-15 10:51:43'),
(1376, 'product', 53, 'update', NULL, '2025-12-15 10:52:11'),
(1377, 'product', 52, 'update', NULL, '2025-12-15 10:54:07'),
(1378, 'product', 51, 'update', NULL, '2025-12-15 10:54:38'),
(1379, 'product', 35, 'update', NULL, '2025-12-15 10:54:50'),
(1380, 'product', 8, 'update', NULL, '2025-12-15 10:55:12'),
(1381, 'product', 7, 'update', NULL, '2025-12-15 10:57:48'),
(1382, 'product', 52, 'update', NULL, '2025-12-15 11:33:02'),
(1383, 'product', 58, 'update', NULL, '2025-12-15 11:33:54'),
(1384, 'product', 58, 'update', NULL, '2025-12-15 11:34:41'),
(1385, 'product', 52, 'update', NULL, '2025-12-15 11:35:27'),
(1386, 'product', 52, 'update', NULL, '2025-12-15 12:22:44'),
(1387, 'product', 61, 'update', NULL, '2025-12-15 12:23:43'),
(1388, 'product', 51, 'update', NULL, '2025-12-15 12:27:47'),
(1389, 'product', 62, 'update', NULL, '2025-12-15 12:41:08'),
(1390, 'product', 51, 'update', NULL, '2025-12-15 12:57:08'),
(1391, 'product', 51, 'update', NULL, '2025-12-15 12:59:58'),
(1392, 'product', 51, 'update', NULL, '2025-12-15 13:07:20'),
(1393, 'product', 51, 'update', NULL, '2025-12-15 13:10:06'),
(1394, 'product', 51, 'update', NULL, '2025-12-15 13:10:40'),
(1395, 'product', 51, 'update', NULL, '2025-12-15 13:19:04'),
(1396, 'product', 51, 'update', NULL, '2025-12-15 13:38:05'),
(1397, 'product', 51, 'update', NULL, '2025-12-15 13:43:17'),
(1398, 'product', 51, 'update', NULL, '2025-12-15 13:44:08'),
(1399, 'product', 51, 'update', NULL, '2025-12-15 14:37:15'),
(1400, 'product', 51, 'update', NULL, '2025-12-15 14:56:59'),
(1401, 'product', 51, 'update', NULL, '2025-12-15 15:51:17'),
(1402, 'product', 62, 'update', NULL, '2025-12-16 07:57:58'),
(1403, 'product', 52, 'update', NULL, '2025-12-16 08:16:36'),
(1404, 'product', 52, 'update', NULL, '2025-12-16 08:20:46'),
(1405, 'product', 52, 'update', NULL, '2025-12-16 08:20:50'),
(1406, 'product', 58, 'update', NULL, '2025-12-16 08:48:30'),
(1407, 'product', 45, 'update', NULL, '2025-12-16 08:56:07'),
(1408, 'product', 13, 'update', NULL, '2025-12-16 09:00:04'),
(1409, 'product', 52, 'update', NULL, '2025-12-16 09:00:27'),
(1410, 'product', 58, 'update', NULL, '2025-12-16 09:06:44'),
(1411, 'product', 52, 'update', NULL, '2025-12-16 09:06:51'),
(1412, 'product', 52, 'update', NULL, '2025-12-16 09:08:21'),
(1413, 'product', 52, 'update', NULL, '2025-12-16 09:09:06'),
(1414, 'product', 58, 'update', NULL, '2025-12-16 09:09:20'),
(1415, 'product', 51, 'update', NULL, '2025-12-16 09:11:14'),
(1416, 'product', 8, 'update', NULL, '2025-12-16 09:11:56'),
(1417, 'product', 7, 'update', NULL, '2025-12-16 09:12:18'),
(1418, 'product', 8, 'update', NULL, '2025-12-16 09:12:22'),
(1419, 'product', 45, 'update', NULL, '2025-12-16 09:23:49'),
(1420, 'product', 35, 'update', NULL, '2025-12-16 09:23:58'),
(1421, 'product', 52, 'update', NULL, '2025-12-16 09:24:58'),
(1422, 'product', 51, 'update', NULL, '2025-12-16 09:36:43'),
(1423, 'product', 8, 'update', NULL, '2025-12-16 09:40:01'),
(1424, 'product', 51, 'update', NULL, '2025-12-16 09:49:43'),
(1425, 'product', 51, 'update', NULL, '2025-12-16 09:51:51'),
(1426, 'product', 51, 'update', NULL, '2025-12-16 10:03:37'),
(1427, 'product', 51, 'update', NULL, '2025-12-16 10:05:14'),
(1428, 'product', 51, 'update', NULL, '2025-12-16 10:05:17'),
(1429, 'product', 52, 'update', NULL, '2025-12-16 10:32:36'),
(1430, 'product', 52, 'update', NULL, '2025-12-16 10:35:33'),
(1431, 'product', 58, 'update', NULL, '2025-12-16 10:37:46'),
(1432, 'product', 58, 'update', NULL, '2025-12-16 10:38:20'),
(1433, 'product', 52, 'update', NULL, '2025-12-16 10:38:27'),
(1434, 'product', 52, 'update', NULL, '2025-12-16 10:39:24'),
(1435, 'product', 58, 'update', NULL, '2025-12-16 10:39:57'),
(1436, 'product', 58, 'update', NULL, '2025-12-16 10:40:19'),
(1437, 'product', 52, 'update', NULL, '2025-12-16 10:47:26'),
(1438, 'product', 8, 'update', NULL, '2025-12-16 10:48:25'),
(1439, 'product', 8, 'update', NULL, '2025-12-16 10:48:41'),
(1440, 'product', 5, 'update', NULL, '2025-12-16 10:48:54'),
(1441, 'product', 6, 'update', NULL, '2025-12-16 10:49:07'),
(1442, 'product', 6, 'update', NULL, '2025-12-16 10:49:18'),
(1443, 'product', 6, 'update', NULL, '2025-12-16 10:49:44'),
(1444, 'product', 6, 'update', NULL, '2025-12-16 10:50:08'),
(1445, 'product', 61, 'delete', NULL, '2025-12-16 10:57:06'),
(1446, 'product', 64, 'create', NULL, '2025-12-16 10:59:06'),
(1447, 'product', 64, 'update', NULL, '2025-12-16 10:59:32'),
(1448, 'product', 64, 'update', NULL, '2025-12-16 10:59:52'),
(1449, 'product', 64, 'update', NULL, '2025-12-16 11:00:04'),
(1450, 'product', 7, 'update', NULL, '2025-12-16 11:52:01'),
(1451, 'product', 7, 'update', NULL, '2025-12-16 11:52:25'),
(1452, 'product', 64, 'update', NULL, '2025-12-16 12:01:39'),
(1453, 'product', 64, 'update', NULL, '2025-12-16 12:48:01'),
(1454, 'product', 64, 'update', NULL, '2025-12-16 12:48:25'),
(1455, 'product', 64, 'update', NULL, '2025-12-16 12:51:45'),
(1456, 'product', 51, 'update', NULL, '2025-12-16 15:29:24'),
(1457, 'product', 7, 'update', NULL, '2025-12-16 15:29:50'),
(1458, 'product', 51, 'update', NULL, '2025-12-17 08:30:14'),
(1459, 'product', 62, 'update', NULL, '2025-12-17 08:33:53'),
(1460, 'product', 51, 'update', NULL, '2025-12-17 08:47:36'),
(1461, 'product', 51, 'update', NULL, '2025-12-17 08:48:42'),
(1462, 'product', 6, 'update', NULL, '2025-12-17 09:02:30'),
(1463, 'product', 52, 'update', NULL, '2025-12-17 09:03:39'),
(1464, 'product', 51, 'update', NULL, '2025-12-17 09:18:10'),
(1465, 'product', 51, 'update', NULL, '2025-12-17 09:26:31'),
(1466, 'product', 35, 'update', NULL, '2025-12-17 09:46:04'),
(1467, 'product', 8, 'update', NULL, '2025-12-17 09:46:52'),
(1468, 'product', 7, 'update', NULL, '2025-12-17 09:46:56'),
(1469, 'product', 7, 'update', NULL, '2025-12-17 09:59:11'),
(1470, 'product', 8, 'update', NULL, '2025-12-17 09:59:17'),
(1471, 'product', 51, 'update', NULL, '2025-12-17 09:59:43'),
(1472, 'product', 35, 'update', NULL, '2025-12-17 09:59:47'),
(1473, 'product', 6, 'update', NULL, '2025-12-17 10:12:59'),
(1474, 'product', 6, 'update', NULL, '2025-12-17 10:13:19'),
(1475, 'product', 6, 'update', NULL, '2025-12-17 10:13:30'),
(1476, 'product', 6, 'update', NULL, '2025-12-17 10:14:03'),
(1477, 'product', 6, 'update', NULL, '2025-12-17 10:33:45'),
(1478, 'product', 6, 'update', NULL, '2025-12-17 10:34:36'),
(1479, 'product', 6, 'update', NULL, '2025-12-17 10:35:16'),
(1480, 'product', 6, 'update', NULL, '2025-12-17 10:36:46'),
(1481, 'product', 6, 'update', NULL, '2025-12-17 10:37:20'),
(1482, 'product', 6, 'update', NULL, '2025-12-17 10:38:43'),
(1483, 'product', 6, 'update', NULL, '2025-12-17 10:41:41'),
(1484, 'product', 6, 'update', NULL, '2025-12-17 10:48:05'),
(1485, 'product', 6, 'update', NULL, '2025-12-17 10:49:57'),
(1486, 'product', 13, 'update', NULL, '2025-12-17 11:07:24'),
(1487, 'product', 7, 'update', NULL, '2025-12-17 11:07:32'),
(1488, 'product', 8, 'update', NULL, '2025-12-17 11:10:00'),
(1489, 'product', 8, 'update', NULL, '2025-12-17 11:10:00'),
(1490, 'product', 8, 'update', NULL, '2025-12-17 11:10:01'),
(1491, 'product', 8, 'update', NULL, '2025-12-17 11:10:03'),
(1492, 'product', 8, 'update', NULL, '2025-12-17 11:10:03'),
(1493, 'product', 8, 'update', NULL, '2025-12-17 11:10:04'),
(1494, 'product', 8, 'update', NULL, '2025-12-17 11:10:04'),
(1495, 'product', 29, 'update', NULL, '2025-12-17 11:10:04'),
(1496, 'product', 29, 'update', NULL, '2025-12-17 11:10:05'),
(1497, 'product', 29, 'update', NULL, '2025-12-17 11:10:05'),
(1498, 'product', 58, 'update', NULL, '2025-12-17 11:10:06'),
(1499, 'product', 58, 'update', NULL, '2025-12-17 11:10:06'),
(1500, 'product', 58, 'update', NULL, '2025-12-17 11:10:07'),
(1501, 'product', 51, 'update', NULL, '2025-12-17 11:10:22'),
(1502, 'product', 45, 'update', NULL, '2025-12-17 11:11:14'),
(1503, 'product', 6, 'update', NULL, '2025-12-17 11:11:35'),
(1504, 'product', 53, 'update', NULL, '2025-12-17 11:11:39'),
(1505, 'product', 51, 'update', NULL, '2025-12-17 11:43:01'),
(1506, 'product', 8, 'update', NULL, '2025-12-17 11:43:31'),
(1507, 'product', 6, 'update', NULL, '2025-12-17 11:48:17'),
(1508, 'product', 7, 'update', NULL, '2025-12-17 11:48:26'),
(1509, 'product', 8, 'update', NULL, '2025-12-17 11:48:28'),
(1510, 'product', 5, 'update', NULL, '2025-12-17 11:48:34'),
(1511, 'product', 4, 'update', NULL, '2025-12-17 11:53:36'),
(1512, 'product', 64, 'update', NULL, '2025-12-17 11:53:57'),
(1513, 'product', 52, 'update', NULL, '2025-12-17 11:54:03'),
(1514, 'product', 8, 'update', NULL, '2025-12-17 11:54:10'),
(1515, 'product', 6, 'update', NULL, '2025-12-17 11:54:15'),
(1516, 'product', 5, 'update', NULL, '2025-12-17 11:54:17'),
(1517, 'product', 4, 'update', NULL, '2025-12-17 11:54:20'),
(1518, 'product', 7, 'update', NULL, '2025-12-17 11:54:26'),
(1519, 'product', 42, 'update', NULL, '2025-12-17 12:13:17'),
(1520, 'product', 51, 'update', NULL, '2025-12-17 12:32:57'),
(1521, 'product', 35, 'update', NULL, '2025-12-17 12:32:59'),
(1522, 'product', 64, 'update', NULL, '2025-12-17 12:33:17'),
(1523, 'product', 6, 'update', NULL, '2025-12-17 12:33:29'),
(1524, 'product', 51, 'update', NULL, '2025-12-17 12:33:44'),
(1525, 'product', 29, 'update', NULL, '2025-12-17 12:34:18'),
(1526, 'product', 35, 'update', NULL, '2025-12-17 12:34:19'),
(1527, 'product', 12, 'update', NULL, '2025-12-17 12:34:20'),
(1528, 'product', 12, 'update', NULL, '2025-12-17 12:34:21'),
(1529, 'product', 7, 'update', NULL, '2025-12-17 12:34:52'),
(1530, 'product', 51, 'update', NULL, '2025-12-17 12:34:58'),
(1531, 'product', 6, 'update', NULL, '2025-12-17 12:44:47'),
(1532, 'product', 7, 'update', NULL, '2025-12-17 12:44:50'),
(1533, 'product', 5, 'update', NULL, '2025-12-17 12:44:53'),
(1534, 'product', 5, 'update', NULL, '2025-12-17 12:46:31'),
(1535, 'product', 5, 'update', NULL, '2025-12-17 12:52:07'),
(1536, 'product', 5, 'update', NULL, '2025-12-17 12:53:04'),
(1537, 'product', 5, 'update', NULL, '2025-12-17 13:06:26'),
(1538, 'product', 5, 'update', NULL, '2025-12-17 13:07:14'),
(1539, 'product', 5, 'update', NULL, '2025-12-17 13:09:13'),
(1540, 'product', 5, 'update', NULL, '2025-12-17 13:10:35'),
(1541, 'product', 6, 'update', NULL, '2025-12-17 13:10:45'),
(1542, 'product', 6, 'update', NULL, '2025-12-17 13:11:11'),
(1543, 'product', 6, 'update', NULL, '2025-12-17 13:11:19'),
(1544, 'product', 6, 'update', NULL, '2025-12-17 13:12:00'),
(1545, 'product', 5, 'update', NULL, '2025-12-17 13:12:03'),
(1546, 'product', 6, 'update', NULL, '2025-12-17 13:13:19'),
(1547, 'product', 5, 'update', NULL, '2025-12-17 13:13:22'),
(1548, 'product', 6, 'update', NULL, '2025-12-17 13:15:54'),
(1549, 'product', 6, 'update', NULL, '2025-12-17 13:19:57'),
(1550, 'product', 6, 'update', NULL, '2025-12-17 13:20:21'),
(1551, 'product', 6, 'update', NULL, '2025-12-17 13:35:09'),
(1552, 'product', 6, 'update', NULL, '2025-12-17 13:35:16'),
(1553, 'product', 6, 'update', NULL, '2025-12-17 13:53:57'),
(1554, 'product', 6, 'update', NULL, '2025-12-17 13:56:16'),
(1555, 'product', 6, 'update', NULL, '2025-12-17 13:57:06'),
(1556, 'product', 6, 'update', NULL, '2025-12-17 13:57:43'),
(1557, 'product', 6, 'update', NULL, '2025-12-17 13:58:46'),
(1558, 'product', 6, 'update', NULL, '2025-12-17 14:01:24'),
(1559, 'product', 6, 'update', NULL, '2025-12-17 14:02:17'),
(1560, 'product', 5, 'update', NULL, '2025-12-17 14:02:22'),
(1561, 'product', 6, 'update', NULL, '2025-12-17 14:02:31'),
(1562, 'product', 5, 'update', NULL, '2025-12-17 14:02:35'),
(1563, 'product', 6, 'update', NULL, '2025-12-17 14:03:17'),
(1564, 'product', 7, 'update', NULL, '2025-12-17 14:03:21'),
(1565, 'product', 8, 'update', NULL, '2025-12-17 14:03:25'),
(1566, 'product', 6, 'update', NULL, '2025-12-17 14:03:28'),
(1567, 'product', 5, 'update', NULL, '2025-12-17 14:03:33'),
(1568, 'product', 5, 'update', NULL, '2025-12-17 14:03:47'),
(1569, 'product', 5, 'update', NULL, '2025-12-17 14:04:46'),
(1570, 'product', 5, 'update', NULL, '2025-12-17 14:08:25'),
(1571, 'product', 5, 'update', NULL, '2025-12-17 14:08:35'),
(1572, 'product', 5, 'update', NULL, '2025-12-17 14:13:31'),
(1573, 'product', 4, 'update', NULL, '2025-12-17 14:13:38'),
(1574, 'product', 5, 'update', NULL, '2025-12-17 15:44:13'),
(1575, 'product', 5, 'update', NULL, '2025-12-17 15:55:14'),
(1576, 'product', 5, 'update', NULL, '2025-12-17 16:08:47'),
(1577, 'product', 5, 'update', NULL, '2025-12-17 16:09:25'),
(1578, 'product', 35, 'update', NULL, '2025-12-17 16:11:19'),
(1579, 'product', 5, 'update', NULL, '2025-12-17 16:20:34'),
(1580, 'product', 5, 'update', NULL, '2025-12-17 16:20:59'),
(1581, 'product', 64, 'update', NULL, '2025-12-18 08:26:07'),
(1582, 'product', 52, 'update', NULL, '2025-12-18 08:27:47'),
(1583, 'product', 53, 'update', NULL, '2025-12-18 08:39:34'),
(1584, 'product', 64, 'update', NULL, '2025-12-18 09:58:57'),
(1585, 'product', 64, 'update', NULL, '2025-12-18 10:39:01'),
(1586, 'product', 64, 'update', NULL, '2025-12-18 10:41:50'),
(1587, 'product', 6, 'update', NULL, '2025-12-18 10:49:49'),
(1588, 'product', 6, 'update', NULL, '2025-12-18 10:50:03'),
(1589, 'product', 6, 'update', NULL, '2025-12-18 10:50:55'),
(1590, 'product', 6, 'update', NULL, '2025-12-18 10:51:27'),
(1591, 'product', 6, 'update', NULL, '2025-12-18 11:24:34'),
(1592, 'product', 7, 'update', NULL, '2025-12-18 11:24:48'),
(1593, 'product', 7, 'update', NULL, '2025-12-18 11:26:18'),
(1594, 'product', 62, 'update', NULL, '2025-12-19 08:08:50'),
(1595, 'product', 29, 'update', NULL, '2025-12-19 08:25:14'),
(1596, 'product', 64, 'update', NULL, '2025-12-19 08:26:24'),
(1597, 'product', 51, 'update', NULL, '2025-12-19 08:26:58'),
(1598, 'product', 64, 'update', NULL, '2025-12-19 08:30:42'),
(1599, 'product', 59, 'update', NULL, '2025-12-19 08:31:56'),
(1600, 'product', 64, 'update', NULL, '2025-12-19 08:32:23'),
(1601, 'product', 52, 'update', NULL, '2025-12-19 08:32:35'),
(1602, 'product', 64, 'update', NULL, '2025-12-19 08:32:46'),
(1603, 'product', 64, 'update', NULL, '2025-12-19 08:35:03'),
(1604, 'product', 52, 'update', NULL, '2025-12-19 08:35:27'),
(1605, 'product', 58, 'update', NULL, '2025-12-19 08:35:30'),
(1606, 'product', 52, 'update', NULL, '2025-12-19 08:38:07'),
(1607, 'product', 52, 'update', NULL, '2025-12-19 08:38:28'),
(1608, 'product', 52, 'update', NULL, '2025-12-19 08:38:54'),
(1609, 'product', 8, 'update', NULL, '2025-12-19 08:59:33'),
(1610, 'product', 64, 'update', NULL, '2025-12-19 09:21:14'),
(1611, 'product', 52, 'update', NULL, '2025-12-19 09:21:41'),
(1612, 'product', 6, 'update', NULL, '2025-12-19 09:21:54'),
(1613, 'product', 58, 'update', NULL, '2025-12-19 11:02:12'),
(1614, 'product', 64, 'update', NULL, '2025-12-19 11:02:52'),
(1615, 'product', 52, 'update', NULL, '2025-12-19 11:03:14'),
(1616, 'product', 58, 'update', NULL, '2025-12-19 11:03:22'),
(1617, 'product', 64, 'update', NULL, '2025-12-19 11:04:40'),
(1618, 'product', 64, 'update', NULL, '2025-12-19 11:04:41'),
(1619, 'product', 6, 'update', NULL, '2025-12-19 11:04:57'),
(1620, 'product', 7, 'update', NULL, '2025-12-19 11:05:31'),
(1621, 'product', 8, 'update', NULL, '2025-12-19 11:05:36'),
(1622, 'product', 5, 'update', NULL, '2025-12-19 11:05:42'),
(1623, 'product', 5, 'update', NULL, '2025-12-19 11:05:53'),
(1624, 'product', 8, 'update', NULL, '2025-12-19 11:11:35'),
(1625, 'product', 62, 'update', NULL, '2025-12-20 10:00:03'),
(1626, 'product', 8, 'update', NULL, '2025-12-20 12:46:33'),
(1627, 'product', 7, 'update', NULL, '2025-12-20 12:46:37'),
(1628, 'product', 6, 'update', NULL, '2025-12-20 12:46:40'),
(1629, 'product', 6, 'update', NULL, '2025-12-20 12:46:50'),
(1630, 'product', 5, 'update', NULL, '2025-12-20 12:46:52'),
(1631, 'product', 5, 'update', NULL, '2025-12-20 12:47:07'),
(1632, 'product', 5, 'update', NULL, '2025-12-20 12:47:21'),
(1633, 'product', 5, 'update', NULL, '2025-12-20 12:47:52'),
(1634, 'product', 5, 'update', NULL, '2025-12-20 12:48:06'),
(1635, 'product', 6, 'update', NULL, '2025-12-20 13:14:43'),
(1636, 'product', 5, 'update', NULL, '2025-12-20 13:14:45'),
(1637, 'product', 5, 'update', NULL, '2025-12-20 13:18:54'),
(1638, 'product', 5, 'update', NULL, '2025-12-20 13:19:06'),
(1639, 'product', 5, 'update', NULL, '2025-12-20 13:19:14'),
(1640, 'product', 5, 'update', NULL, '2025-12-20 13:19:18'),
(1641, 'product', 5, 'update', NULL, '2025-12-20 13:19:30'),
(1642, 'product', 5, 'update', NULL, '2025-12-20 13:19:42'),
(1643, 'product', 5, 'update', NULL, '2025-12-20 13:27:15'),
(1644, 'product', 5, 'update', NULL, '2025-12-20 13:27:18'),
(1645, 'product', 5, 'update', NULL, '2025-12-20 13:31:06'),
(1646, 'product', 52, 'update', NULL, '2025-12-20 14:03:22'),
(1647, 'product', 5, 'update', NULL, '2025-12-20 14:44:12'),
(1648, 'product', 5, 'update', NULL, '2025-12-20 14:44:18'),
(1649, 'product', 51, 'update', NULL, '2025-12-20 14:56:54'),
(1650, 'product', 6, 'update', NULL, '2025-12-20 14:57:02'),
(1651, 'product', 5, 'update', NULL, '2025-12-20 14:57:09'),
(1652, 'product', 65, 'create', NULL, '2025-12-22 08:21:03'),
(1653, 'product', 7, 'update', NULL, '2025-12-22 08:23:18'),
(1654, 'product', 6, 'update', NULL, '2025-12-22 08:23:21'),
(1655, 'product', 7, 'update', NULL, '2025-12-22 08:23:24'),
(1656, 'product', 8, 'update', NULL, '2025-12-22 08:23:27'),
(1657, 'product', 6, 'update', NULL, '2025-12-22 08:23:28'),
(1658, 'product', 5, 'update', NULL, '2025-12-22 08:23:36'),
(1659, 'product', 6, 'update', NULL, '2025-12-22 08:23:51'),
(1660, 'product', 7, 'update', NULL, '2025-12-22 08:24:08'),
(1661, 'product', 6, 'update', NULL, '2025-12-22 08:24:13'),
(1662, 'product', 6, 'update', NULL, '2025-12-22 08:25:15'),
(1663, 'product', 6, 'update', NULL, '2025-12-22 08:25:34'),
(1664, 'product', 6, 'update', NULL, '2025-12-22 08:25:36'),
(1665, 'product', 6, 'update', NULL, '2025-12-22 08:25:47'),
(1666, 'product', 6, 'update', NULL, '2025-12-22 08:25:50'),
(1667, 'product', 6, 'update', NULL, '2025-12-22 08:25:53'),
(1668, 'product', 6, 'update', NULL, '2025-12-22 08:26:19'),
(1669, 'product', 6, 'update', NULL, '2025-12-22 08:26:23'),
(1670, 'product', 6, 'update', NULL, '2025-12-22 08:26:27'),
(1671, 'product', 6, 'update', NULL, '2025-12-22 08:26:47'),
(1672, 'product', 6, 'update', NULL, '2025-12-22 08:26:53'),
(1673, 'product', 6, 'update', NULL, '2025-12-22 08:27:04'),
(1674, 'product', 6, 'update', NULL, '2025-12-22 08:27:11'),
(1675, 'product', 6, 'update', NULL, '2025-12-22 08:28:06'),
(1676, 'product', 7, 'update', NULL, '2025-12-22 08:32:14'),
(1677, 'product', 6, 'update', NULL, '2025-12-22 08:32:16'),
(1678, 'product', 6, 'update', NULL, '2025-12-22 08:32:58'),
(1679, 'product', 6, 'update', NULL, '2025-12-22 08:33:07'),
(1680, 'product', 6, 'update', NULL, '2025-12-22 08:33:19'),
(1681, 'product', 6, 'update', NULL, '2025-12-22 08:33:35'),
(1682, 'product', 6, 'update', NULL, '2025-12-22 08:34:24'),
(1683, 'product', 6, 'update', NULL, '2025-12-22 08:35:08'),
(1684, 'product', 12, 'update', NULL, '2025-12-22 08:41:04'),
(1685, 'product', 7, 'update', NULL, '2025-12-22 08:42:37'),
(1686, 'product', 6, 'update', NULL, '2025-12-22 08:45:56'),
(1687, 'product', 6, 'update', NULL, '2025-12-22 08:46:00'),
(1688, 'product', 52, 'update', NULL, '2025-12-22 08:46:50'),
(1689, 'product', 58, 'update', NULL, '2025-12-22 08:46:55'),
(1690, 'product', 54, 'update', NULL, '2025-12-22 08:55:31'),
(1691, 'product', 66, 'create', NULL, '2025-12-22 09:00:32'),
(1692, 'product', 66, 'update', NULL, '2025-12-22 09:01:18'),
(1693, 'product', 66, 'update', NULL, '2025-12-22 09:03:12'),
(1694, 'product', 6, 'update', NULL, '2025-12-22 09:40:45');
INSERT INTO `elasticsearch_sync_queue` (`id`, `entity_type`, `entity_id`, `action`, `synced_at`, `created_at`) VALUES
(1695, 'product', 6, 'update', NULL, '2025-12-22 09:42:06'),
(1696, 'product', 7, 'update', NULL, '2025-12-22 09:42:18'),
(1697, 'product', 7, 'update', NULL, '2025-12-22 09:42:20'),
(1698, 'product', 6, 'update', NULL, '2025-12-22 09:42:22'),
(1699, 'product', 7, 'update', NULL, '2025-12-22 09:42:23'),
(1700, 'product', 6, 'update', NULL, '2025-12-22 09:42:26'),
(1701, 'product', 6, 'update', NULL, '2025-12-22 09:43:30'),
(1702, 'product', 5, 'update', NULL, '2025-12-22 12:26:27'),
(1703, 'product', 5, 'update', NULL, '2025-12-22 12:26:35'),
(1704, 'product', 5, 'update', NULL, '2025-12-22 12:29:36'),
(1705, 'product', 5, 'update', NULL, '2025-12-22 12:29:39'),
(1706, 'product', 5, 'update', NULL, '2025-12-22 12:29:42'),
(1707, 'product', 5, 'update', NULL, '2025-12-22 12:32:12'),
(1708, 'product', 5, 'update', NULL, '2025-12-22 12:32:14'),
(1709, 'product', 5, 'update', NULL, '2025-12-22 12:55:47'),
(1710, 'product', 5, 'update', NULL, '2025-12-22 12:55:49'),
(1711, 'product', 5, 'update', NULL, '2025-12-22 12:55:52'),
(1712, 'product', 5, 'update', NULL, '2025-12-22 12:55:55'),
(1713, 'product', 5, 'update', NULL, '2025-12-22 12:56:02'),
(1714, 'product', 5, 'update', NULL, '2025-12-22 13:05:12'),
(1715, 'product', 5, 'update', NULL, '2025-12-22 14:06:35'),
(1716, 'product', 5, 'update', NULL, '2025-12-22 14:06:42'),
(1717, 'product', 5, 'update', NULL, '2025-12-22 14:06:54'),
(1718, 'product', 5, 'update', NULL, '2025-12-22 14:10:39'),
(1719, 'product', 5, 'update', NULL, '2025-12-22 14:10:47'),
(1720, 'product', 5, 'update', NULL, '2025-12-22 14:10:50'),
(1721, 'product', 5, 'update', NULL, '2025-12-22 14:18:34'),
(1722, 'product', 5, 'update', NULL, '2025-12-22 14:40:59'),
(1723, 'product', 5, 'update', NULL, '2025-12-22 14:41:02'),
(1724, 'product', 5, 'update', NULL, '2025-12-22 14:43:36'),
(1725, 'product', 5, 'update', NULL, '2025-12-22 14:43:52'),
(1726, 'product', 5, 'update', NULL, '2025-12-22 14:43:55'),
(1727, 'product', 5, 'update', NULL, '2025-12-22 14:43:57'),
(1728, 'product', 5, 'update', NULL, '2025-12-22 14:45:31'),
(1729, 'product', 5, 'update', NULL, '2025-12-22 14:45:34'),
(1730, 'product', 5, 'update', NULL, '2025-12-22 14:46:06'),
(1731, 'product', 5, 'update', NULL, '2025-12-22 14:46:44'),
(1732, 'product', 5, 'update', NULL, '2025-12-22 14:46:46'),
(1733, 'product', 58, 'update', NULL, '2025-12-22 15:14:23'),
(1734, 'product', 13, 'update', NULL, '2025-12-22 15:14:42'),
(1735, 'product', 5, 'update', NULL, '2025-12-23 09:03:48'),
(1736, 'product', 5, 'update', NULL, '2025-12-23 09:03:49'),
(1737, 'product', 5, 'update', NULL, '2025-12-23 09:03:50'),
(1738, 'product', 5, 'update', NULL, '2025-12-23 09:03:51'),
(1739, 'product', 5, 'update', NULL, '2025-12-23 09:03:51'),
(1740, 'product', 5, 'update', NULL, '2025-12-23 09:03:52'),
(1741, 'product', 5, 'update', NULL, '2025-12-23 09:03:52'),
(1742, 'product', 5, 'update', NULL, '2025-12-23 09:03:53'),
(1743, 'product', 5, 'update', NULL, '2025-12-23 09:03:53'),
(1744, 'product', 5, 'update', NULL, '2025-12-23 09:03:54'),
(1745, 'product', 5, 'update', NULL, '2025-12-23 09:03:59'),
(1746, 'product', 5, 'update', NULL, '2025-12-23 09:03:59'),
(1747, 'product', 5, 'update', NULL, '2025-12-23 09:04:00'),
(1748, 'product', 5, 'update', NULL, '2025-12-23 09:04:00'),
(1749, 'product', 5, 'update', NULL, '2025-12-23 09:04:01'),
(1750, 'product', 5, 'update', NULL, '2025-12-23 09:04:02'),
(1751, 'product', 5, 'update', NULL, '2025-12-23 09:04:02'),
(1752, 'product', 5, 'update', NULL, '2025-12-23 09:04:03'),
(1753, 'product', 5, 'update', NULL, '2025-12-23 09:04:04'),
(1754, 'product', 6, 'update', NULL, '2025-12-24 08:20:40'),
(1755, 'product', 6, 'update', NULL, '2025-12-24 08:21:41'),
(1756, 'product', 65, 'update', NULL, '2025-12-24 14:23:01'),
(1757, 'product', 65, 'update', NULL, '2025-12-24 15:23:40'),
(1758, 'product', 35, 'update', NULL, '2025-12-25 08:54:18'),
(1759, 'product', 51, 'update', NULL, '2025-12-25 08:54:21'),
(1760, 'product', 64, 'update', NULL, '2025-12-25 08:56:38'),
(1761, 'product', 6, 'update', NULL, '2025-12-25 08:59:00'),
(1762, 'product', 6, 'update', NULL, '2025-12-25 08:59:11'),
(1763, 'product', 6, 'update', NULL, '2025-12-25 08:59:56'),
(1764, 'product', 6, 'update', NULL, '2025-12-25 09:00:12'),
(1765, 'product', 51, 'update', NULL, '2025-12-25 09:00:59'),
(1766, 'product', 64, 'update', NULL, '2025-12-25 09:01:14'),
(1767, 'product', 8, 'update', NULL, '2025-12-25 09:01:42'),
(1768, 'product', 66, 'update', NULL, '2025-12-25 11:17:59'),
(1769, 'product', 62, 'update', NULL, '2025-12-27 11:11:52'),
(1770, 'product', 64, 'update', NULL, '2025-12-27 13:42:06'),
(1771, 'product', 67, 'create', NULL, '2025-12-29 08:03:17'),
(1772, 'product', 67, 'delete', NULL, '2025-12-29 08:06:16'),
(1773, 'product', 66, 'delete', NULL, '2025-12-29 08:07:06'),
(1774, 'product', 68, 'create', NULL, '2025-12-29 08:08:47'),
(1775, 'product', 69, 'create', NULL, '2025-12-29 08:21:36'),
(1776, 'product', 69, 'update', NULL, '2025-12-29 08:22:21'),
(1777, 'product', 68, 'update', NULL, '2025-12-29 08:40:03'),
(1778, 'product', 68, 'update', NULL, '2025-12-29 08:40:11'),
(1779, 'product', 58, 'update', NULL, '2025-12-29 08:40:18'),
(1780, 'product', 68, 'update', NULL, '2025-12-29 09:44:24'),
(1781, 'product', 52, 'update', NULL, '2025-12-29 10:09:44'),
(1782, 'product', 68, 'update', NULL, '2025-12-29 11:09:06'),
(1783, 'product', 58, 'update', NULL, '2025-12-29 11:09:41'),
(1784, 'product', 69, 'update', NULL, '2025-12-29 11:27:33'),
(1785, 'product', 69, 'delete', NULL, '2025-12-29 11:27:51'),
(1786, 'product', 68, 'update', NULL, '2025-12-29 11:43:11'),
(1787, 'product', 58, 'update', NULL, '2025-12-29 11:43:16'),
(1788, 'product', 64, 'update', NULL, '2025-12-29 16:03:10'),
(1789, 'product', 54, 'update', NULL, '2025-12-29 16:04:59'),
(1790, 'product', 64, 'update', NULL, '2025-12-30 08:04:34'),
(1791, 'product', 64, 'update', NULL, '2025-12-30 08:14:45'),
(1792, 'product', 58, 'update', NULL, '2025-12-30 08:15:38'),
(1793, 'product', 58, 'update', NULL, '2025-12-30 08:16:14'),
(1794, 'product', 59, 'update', NULL, '2025-12-30 09:11:33'),
(1795, 'product', 7, 'update', NULL, '2025-12-30 09:11:40'),
(1796, 'product', 68, 'update', NULL, '2025-12-30 09:11:54'),
(1797, 'product', 58, 'update', NULL, '2025-12-30 09:12:00'),
(1798, 'product', 58, 'update', NULL, '2025-12-30 10:13:53'),
(1799, 'product', 68, 'update', NULL, '2025-12-30 10:43:06'),
(1800, 'product', 64, 'update', NULL, '2025-12-30 12:15:25'),
(1801, 'product', 65, 'update', NULL, '2025-12-30 12:18:06'),
(1802, 'product', 68, 'update', NULL, '2025-12-30 12:46:05'),
(1803, 'product', 59, 'update', NULL, '2025-12-30 15:33:51'),
(1804, 'product', 64, 'update', NULL, '2025-12-30 16:03:32'),
(1805, 'product', 68, 'update', NULL, '2025-12-31 08:08:37'),
(1806, 'product', 68, 'update', NULL, '2025-12-31 08:08:54'),
(1807, 'product', 68, 'update', NULL, '2025-12-31 08:09:08'),
(1808, 'product', 68, 'update', NULL, '2025-12-31 08:09:13'),
(1809, 'product', 68, 'update', NULL, '2025-12-31 08:10:08'),
(1810, 'product', 68, 'update', NULL, '2025-12-31 08:10:16'),
(1811, 'product', 68, 'update', NULL, '2025-12-31 08:59:55'),
(1812, 'product', 68, 'update', NULL, '2025-12-31 09:00:08'),
(1813, 'product', 68, 'update', NULL, '2025-12-31 09:00:20'),
(1814, 'product', 59, 'update', NULL, '2025-12-31 10:17:38'),
(1815, 'product', 64, 'update', NULL, '2025-12-31 10:17:44'),
(1816, 'product', 64, 'update', NULL, '2025-12-31 10:18:00'),
(1817, 'product', 65, 'update', NULL, '2025-12-31 12:18:19'),
(1818, 'product', 54, 'update', NULL, '2025-12-31 12:18:30'),
(1819, 'product', 64, 'update', NULL, '2025-12-31 12:22:36'),
(1820, 'product', 70, 'create', NULL, '2026-01-01 09:05:58'),
(1821, 'product', 64, 'update', NULL, '2026-01-01 10:58:21'),
(1822, 'product', 65, 'update', NULL, '2026-01-01 10:59:10'),
(1823, 'product', 65, 'update', NULL, '2026-01-01 11:01:36'),
(1824, 'product', 6, 'update', NULL, '2026-01-01 11:01:57'),
(1825, 'product', 5, 'update', NULL, '2026-01-01 11:02:09'),
(1826, 'product', 5, 'update', NULL, '2026-01-01 11:02:22'),
(1827, 'product', 65, 'update', NULL, '2026-01-01 11:03:43'),
(1828, 'product', 68, 'update', NULL, '2026-01-01 12:36:14'),
(1829, 'product', 64, 'update', NULL, '2026-01-01 12:36:21'),
(1830, 'product', 51, 'update', NULL, '2026-01-02 09:37:46'),
(1831, 'product', 51, 'update', NULL, '2026-01-02 09:38:05'),
(1832, 'product', 51, 'update', NULL, '2026-01-02 09:55:16'),
(1833, 'product', 64, 'update', NULL, '2026-01-02 09:55:26'),
(1834, 'product', 64, 'update', NULL, '2026-01-02 09:55:44'),
(1835, 'product', 51, 'update', NULL, '2026-01-03 06:31:35'),
(1836, 'product', 64, 'update', NULL, '2026-01-03 06:31:47'),
(1837, 'product', 51, 'update', NULL, '2026-01-03 06:32:14'),
(1838, 'product', 51, 'update', NULL, '2026-01-03 06:32:26'),
(1839, 'product', 51, 'update', NULL, '2026-01-03 06:50:09'),
(1840, 'product', 51, 'update', NULL, '2026-01-03 13:26:09'),
(1841, 'product', 35, 'update', NULL, '2026-01-03 13:26:50'),
(1842, 'product', 51, 'update', NULL, '2026-01-03 13:28:13'),
(1843, 'product', 51, 'update', NULL, '2026-01-05 14:14:00'),
(1844, 'product', 64, 'update', NULL, '2026-01-05 14:14:18'),
(1845, 'product', 35, 'update', NULL, '2026-01-05 14:14:30'),
(1846, 'product', 35, 'update', NULL, '2026-01-05 14:18:42'),
(1847, 'product', 35, 'update', NULL, '2026-01-05 14:19:00'),
(1848, 'product', 35, 'update', NULL, '2026-01-05 14:19:10'),
(1849, 'product', 35, 'update', NULL, '2026-01-05 14:19:27'),
(1850, 'product', 35, 'update', NULL, '2026-01-05 14:19:39'),
(1851, 'product', 35, 'update', NULL, '2026-01-05 14:19:45'),
(1852, 'product', 35, 'update', NULL, '2026-01-05 14:19:56'),
(1853, 'product', 35, 'update', NULL, '2026-01-05 14:20:01'),
(1854, 'product', 64, 'update', NULL, '2026-01-06 09:19:05'),
(1855, 'product', 64, 'update', NULL, '2026-01-06 09:19:12'),
(1856, 'product', 58, 'update', NULL, '2026-01-06 09:53:18'),
(1857, 'product', 52, 'update', NULL, '2026-01-06 09:53:27'),
(1858, 'product', 70, 'update', NULL, '2026-01-06 09:53:29'),
(1859, 'product', 70, 'update', NULL, '2026-01-06 09:53:46'),
(1860, 'product', 70, 'update', NULL, '2026-01-06 09:54:04'),
(1861, 'product', 70, 'update', NULL, '2026-01-06 09:54:14'),
(1862, 'product', 70, 'update', NULL, '2026-01-06 09:54:18'),
(1863, 'product', 70, 'update', NULL, '2026-01-06 09:54:29'),
(1864, 'product', 70, 'update', NULL, '2026-01-06 09:54:38'),
(1865, 'product', 70, 'update', NULL, '2026-01-06 09:54:46'),
(1866, 'product', 70, 'update', NULL, '2026-01-06 09:54:58'),
(1867, 'product', 70, 'update', NULL, '2026-01-06 09:55:07'),
(1868, 'product', 64, 'update', NULL, '2026-01-06 09:56:27'),
(1869, 'product', 64, 'update', NULL, '2026-01-06 09:56:34'),
(1870, 'product', 64, 'update', NULL, '2026-01-06 09:57:46'),
(1871, 'product', 64, 'update', NULL, '2026-01-06 09:58:38'),
(1872, 'product', 70, 'update', NULL, '2026-01-06 09:58:43'),
(1873, 'product', 64, 'update', NULL, '2026-01-06 09:58:52'),
(1874, 'product', 70, 'update', NULL, '2026-01-06 09:58:55'),
(1875, 'product', 52, 'update', NULL, '2026-01-06 09:59:20'),
(1876, 'product', 52, 'update', NULL, '2026-01-06 09:59:43'),
(1877, 'product', 65, 'update', NULL, '2026-01-06 10:02:00'),
(1878, 'product', 65, 'update', NULL, '2026-01-06 10:02:08'),
(1879, 'product', 65, 'update', NULL, '2026-01-06 10:02:13'),
(1880, 'product', 65, 'update', NULL, '2026-01-06 10:02:19'),
(1881, 'product', 35, 'update', NULL, '2026-01-06 11:19:19'),
(1882, 'product', 58, 'update', NULL, '2026-01-06 11:20:59'),
(1883, 'product', 65, 'update', NULL, '2026-01-06 16:51:57'),
(1884, 'product', 65, 'update', NULL, '2026-01-07 08:55:39'),
(1885, 'product', 64, 'update', NULL, '2026-01-07 08:56:02'),
(1886, 'product', 64, 'update', NULL, '2026-01-07 11:45:52'),
(1887, 'product', 64, 'update', NULL, '2026-01-07 12:15:36'),
(1888, 'product', 65, 'update', NULL, '2026-01-07 12:15:48'),
(1889, 'product', 65, 'update', NULL, '2026-01-07 12:18:16'),
(1890, 'product', 65, 'update', NULL, '2026-01-07 12:18:28'),
(1891, 'product', 65, 'update', NULL, '2026-01-07 12:18:36'),
(1892, 'product', 65, 'update', NULL, '2026-01-07 12:18:56'),
(1893, 'product', 65, 'update', NULL, '2026-01-07 12:19:14'),
(1894, 'product', 65, 'update', NULL, '2026-01-07 12:19:23'),
(1895, 'product', 65, 'update', NULL, '2026-01-07 12:19:37'),
(1896, 'product', 65, 'update', NULL, '2026-01-07 12:19:51'),
(1897, 'product', 65, 'update', NULL, '2026-01-07 12:19:56'),
(1898, 'product', 65, 'update', NULL, '2026-01-07 12:20:03'),
(1899, 'product', 7, 'update', NULL, '2026-01-07 12:20:22'),
(1900, 'product', 6, 'update', NULL, '2026-01-07 12:20:25'),
(1901, 'product', 6, 'update', NULL, '2026-01-07 12:20:35'),
(1902, 'product', 6, 'update', NULL, '2026-01-07 12:20:39'),
(1903, 'product', 6, 'update', NULL, '2026-01-07 12:20:46'),
(1904, 'product', 6, 'update', NULL, '2026-01-07 12:20:59'),
(1905, 'product', 64, 'update', NULL, '2026-01-07 12:29:53'),
(1906, 'product', 64, 'update', NULL, '2026-01-07 12:30:19'),
(1907, 'product', 64, 'update', NULL, '2026-01-07 12:30:38'),
(1908, 'product', 64, 'update', NULL, '2026-01-07 15:36:26'),
(1909, 'product', 64, 'update', NULL, '2026-01-07 15:52:35'),
(1910, 'product', 64, 'update', NULL, '2026-01-08 12:09:43'),
(1911, 'product', 13, 'update', NULL, '2026-01-09 08:13:58'),
(1912, 'product', 42, 'update', NULL, '2026-01-09 08:14:26'),
(1913, 'product', 59, 'update', NULL, '2026-01-09 08:27:50'),
(1914, 'product', 64, 'update', NULL, '2026-01-09 10:43:38'),
(1915, 'product', 64, 'update', NULL, '2026-01-09 11:00:25'),
(1916, 'product', 64, 'update', NULL, '2026-01-11 15:24:34'),
(1917, 'product', 51, 'update', NULL, '2026-01-11 15:28:29'),
(1918, 'product', 12, 'update', NULL, '2026-01-11 15:29:58'),
(1919, 'product', 51, 'update', NULL, '2026-01-12 08:28:31'),
(1920, 'product', 51, 'update', NULL, '2026-01-12 09:27:56'),
(1921, 'product', 51, 'update', NULL, '2026-01-12 09:37:50'),
(1922, 'product', 51, 'update', NULL, '2026-01-12 09:43:34'),
(1923, 'product', 64, 'update', NULL, '2026-01-12 09:51:10'),
(1924, 'product', 64, 'update', NULL, '2026-01-12 10:14:57'),
(1925, 'product', 65, 'update', NULL, '2026-01-12 10:41:54'),
(1926, 'product', 65, 'update', NULL, '2026-01-12 10:50:39'),
(1927, 'product', 51, 'update', NULL, '2026-01-12 10:57:57'),
(1928, 'product', 51, 'update', NULL, '2026-01-12 12:39:02'),
(1929, 'product', 64, 'update', NULL, '2026-01-12 12:43:25'),
(1930, 'product', 64, 'update', NULL, '2026-01-12 13:26:11'),
(1931, 'product', 70, 'update', NULL, '2026-01-12 15:32:12'),
(1932, 'product', 62, 'update', NULL, '2026-01-12 15:58:54'),
(1933, 'product', 54, 'update', NULL, '2026-01-12 16:34:12'),
(1934, 'product', 54, 'update', NULL, '2026-01-12 16:34:33'),
(1935, 'product', 59, 'update', NULL, '2026-01-13 08:28:45'),
(1936, 'product', 35, 'update', NULL, '2026-01-13 08:28:47'),
(1937, 'product', 7, 'update', NULL, '2026-01-13 08:28:53'),
(1938, 'product', 53, 'update', NULL, '2026-01-13 08:28:59'),
(1939, 'product', 53, 'update', NULL, '2026-01-13 08:29:02'),
(1940, 'product', 52, 'update', NULL, '2026-01-13 08:29:08'),
(1941, 'product', 64, 'update', NULL, '2026-01-13 08:39:59'),
(1942, 'product', 70, 'update', NULL, '2026-01-13 08:44:50'),
(1943, 'product', 65, 'update', NULL, '2026-01-13 09:21:30'),
(1944, 'product', 64, 'update', NULL, '2026-01-13 09:25:20'),
(1945, 'product', 70, 'update', NULL, '2026-01-13 09:25:26'),
(1946, 'product', 58, 'update', NULL, '2026-01-13 09:25:30'),
(1947, 'product', 58, 'update', NULL, '2026-01-13 09:26:58'),
(1948, 'product', 58, 'update', NULL, '2026-01-13 09:27:07'),
(1949, 'product', 58, 'update', NULL, '2026-01-13 09:27:14'),
(1950, 'product', 58, 'update', NULL, '2026-01-13 09:27:21'),
(1951, 'product', 58, 'update', NULL, '2026-01-13 09:27:25'),
(1952, 'product', 58, 'update', NULL, '2026-01-13 09:27:33'),
(1953, 'product', 64, 'update', NULL, '2026-01-13 09:27:50'),
(1954, 'product', 68, 'update', NULL, '2026-01-13 09:27:56'),
(1955, 'product', 68, 'update', NULL, '2026-01-13 09:29:33'),
(1956, 'product', 68, 'update', NULL, '2026-01-13 09:29:44'),
(1957, 'product', 68, 'update', NULL, '2026-01-13 09:29:51'),
(1958, 'product', 68, 'update', NULL, '2026-01-13 09:29:59'),
(1959, 'product', 68, 'update', NULL, '2026-01-13 09:30:16'),
(1960, 'product', 68, 'update', NULL, '2026-01-13 09:30:34'),
(1961, 'product', 68, 'update', NULL, '2026-01-13 09:30:54'),
(1962, 'product', 68, 'update', NULL, '2026-01-13 09:31:01'),
(1963, 'product', 68, 'update', NULL, '2026-01-13 09:31:06'),
(1964, 'product', 68, 'update', NULL, '2026-01-13 09:31:14'),
(1965, 'product', 64, 'update', NULL, '2026-01-13 09:32:14'),
(1966, 'product', 64, 'update', NULL, '2026-01-13 09:34:13'),
(1967, 'product', 64, 'update', NULL, '2026-01-13 09:35:09'),
(1968, 'product', 51, 'update', NULL, '2026-01-13 09:35:36'),
(1969, 'product', 59, 'update', NULL, '2026-01-13 09:35:47'),
(1970, 'product', 65, 'update', NULL, '2026-01-13 09:36:13'),
(1971, 'product', 51, 'update', NULL, '2026-01-13 09:36:26'),
(1972, 'product', 64, 'update', NULL, '2026-01-13 09:36:36'),
(1973, 'product', 51, 'update', NULL, '2026-01-13 09:36:42'),
(1974, 'product', 29, 'update', NULL, '2026-01-13 09:37:32'),
(1975, 'product', 29, 'update', NULL, '2026-01-13 09:38:00'),
(1976, 'product', 29, 'update', NULL, '2026-01-13 09:40:21'),
(1977, 'product', 29, 'update', NULL, '2026-01-13 09:40:50'),
(1978, 'product', 64, 'update', NULL, '2026-01-13 09:45:11'),
(1979, 'product', 68, 'update', NULL, '2026-01-13 09:45:24'),
(1980, 'product', 68, 'update', NULL, '2026-01-13 09:49:27'),
(1981, 'product', 68, 'update', NULL, '2026-01-13 09:49:47'),
(1982, 'product', 71, 'create', NULL, '2026-01-13 09:50:54'),
(1983, 'product', 71, 'update', NULL, '2026-01-13 09:51:10'),
(1984, 'product', 71, 'update', NULL, '2026-01-13 09:51:27'),
(1985, 'product', 71, 'update', NULL, '2026-01-13 09:51:38'),
(1986, 'product', 71, 'update', NULL, '2026-01-13 09:51:43'),
(1987, 'product', 71, 'update', NULL, '2026-01-13 09:51:51'),
(1988, 'product', 71, 'update', NULL, '2026-01-13 09:51:56'),
(1989, 'product', 71, 'update', NULL, '2026-01-13 09:52:04'),
(1990, 'product', 64, 'update', NULL, '2026-01-13 09:53:57'),
(1991, 'product', 64, 'update', NULL, '2026-01-13 09:54:06'),
(1992, 'product', 68, 'update', NULL, '2026-01-13 09:54:15'),
(1993, 'product', 51, 'update', NULL, '2026-01-13 09:57:54'),
(1994, 'product', 52, 'update', NULL, '2026-01-13 11:45:08'),
(1995, 'product', 29, 'update', NULL, '2026-01-13 11:45:36'),
(1996, 'product', 65, 'update', NULL, '2026-01-13 11:50:15'),
(1997, 'product', 5, 'update', NULL, '2026-01-13 14:30:42'),
(1998, 'product', 72, 'create', NULL, '2026-01-13 14:49:04'),
(1999, 'product', 72, 'update', NULL, '2026-01-13 14:49:08'),
(2000, 'product', 72, 'update', NULL, '2026-01-13 14:49:14'),
(2001, 'product', 72, 'update', NULL, '2026-01-13 14:50:48'),
(2002, 'product', 72, 'update', NULL, '2026-01-13 15:05:24'),
(2003, 'product', 51, 'update', NULL, '2026-01-14 09:02:36'),
(2004, 'product', 51, 'update', NULL, '2026-01-14 10:30:26'),
(2005, 'product', 65, 'update', NULL, '2026-01-14 10:32:12'),
(2006, 'product', 8, 'update', NULL, '2026-01-14 10:32:32'),
(2007, 'product', 65, 'update', NULL, '2026-01-14 10:32:41'),
(2008, 'product', 65, 'update', NULL, '2026-01-14 10:32:58'),
(2009, 'product', 65, 'update', NULL, '2026-01-14 10:33:10'),
(2010, 'product', 65, 'update', NULL, '2026-01-14 10:49:11'),
(2011, 'product', 65, 'update', NULL, '2026-01-14 10:49:47'),
(2012, 'product', 65, 'update', NULL, '2026-01-14 10:50:27'),
(2013, 'product', 65, 'update', NULL, '2026-01-14 10:50:38'),
(2014, 'product', 65, 'update', NULL, '2026-01-14 10:53:41'),
(2015, 'product', 65, 'update', NULL, '2026-01-14 10:54:24'),
(2016, 'product', 65, 'update', NULL, '2026-01-14 10:54:31'),
(2017, 'product', 71, 'update', NULL, '2026-01-15 08:18:58'),
(2018, 'product', 71, 'update', NULL, '2026-01-15 08:19:27'),
(2019, 'product', 71, 'update', NULL, '2026-01-15 08:19:34'),
(2020, 'product', 71, 'update', NULL, '2026-01-15 08:19:38'),
(2021, 'product', 71, 'update', NULL, '2026-01-15 08:19:44'),
(2022, 'product', 51, 'update', NULL, '2026-01-15 12:45:09'),
(2023, 'product', 35, 'update', NULL, '2026-01-15 12:45:13'),
(2024, 'product', 59, 'update', NULL, '2026-01-15 12:45:28'),
(2025, 'product', 59, 'update', NULL, '2026-01-15 12:45:55'),
(2026, 'product', 51, 'update', NULL, '2026-01-15 12:46:02'),
(2027, 'product', 59, 'update', NULL, '2026-01-15 12:46:17'),
(2028, 'product', 59, 'update', NULL, '2026-01-15 12:46:25'),
(2029, 'product', 65, 'update', NULL, '2026-01-15 12:50:13'),
(2030, 'product', 51, 'update', NULL, '2026-01-15 12:50:35'),
(2031, 'product', 51, 'update', NULL, '2026-01-15 12:50:53'),
(2032, 'product', 35, 'update', NULL, '2026-01-15 12:50:58'),
(2033, 'product', 35, 'update', NULL, '2026-01-15 12:51:17'),
(2034, 'product', 53, 'update', NULL, '2026-01-15 15:00:58'),
(2035, 'product', 54, 'update', NULL, '2026-01-15 15:16:35'),
(2036, 'product', 65, 'update', NULL, '2026-01-15 18:02:00'),
(2037, 'product', 64, 'update', NULL, '2026-01-16 08:54:21'),
(2038, 'product', 64, 'update', NULL, '2026-01-16 10:58:28'),
(2039, 'product', 3, 'update', NULL, '2026-01-16 10:58:30'),
(2040, 'product', 64, 'update', NULL, '2026-01-16 10:58:43'),
(2041, 'product', 7, 'update', NULL, '2026-01-17 08:52:23'),
(2042, 'product', 12, 'update', NULL, '2026-01-17 08:52:31'),
(2043, 'product', 53, 'update', NULL, '2026-01-19 08:59:21'),
(2044, 'product', 64, 'update', NULL, '2026-01-19 09:03:29'),
(2045, 'product', 51, 'update', NULL, '2026-01-19 09:03:48'),
(2046, 'product', 71, 'update', NULL, '2026-01-19 09:04:30'),
(2047, 'product', 73, 'create', NULL, '2026-01-20 10:53:00'),
(2048, 'product', 73, 'update', NULL, '2026-01-21 08:28:56'),
(2049, 'product', 73, 'update', NULL, '2026-01-21 08:30:57'),
(2050, 'product', 73, 'update', NULL, '2026-01-21 08:33:35'),
(2051, 'product', 73, 'update', NULL, '2026-01-21 08:33:50'),
(2052, 'product', 73, 'update', NULL, '2026-01-21 08:34:59'),
(2053, 'product', 73, 'update', NULL, '2026-01-21 08:35:10'),
(2054, 'product', 73, 'update', NULL, '2026-01-21 08:49:52'),
(2055, 'product', 73, 'update', NULL, '2026-01-21 08:54:38'),
(2056, 'product', 73, 'update', NULL, '2026-01-21 08:54:44'),
(2057, 'product', 74, 'create', NULL, '2026-01-21 13:16:25'),
(2058, 'product', 73, 'update', NULL, '2026-01-21 13:49:08'),
(2059, 'product', 74, 'update', NULL, '2026-01-21 14:46:27'),
(2060, 'product', 73, 'update', NULL, '2026-01-21 14:46:49'),
(2061, 'product', 74, 'update', NULL, '2026-01-21 14:46:53'),
(2062, 'product', 73, 'update', NULL, '2026-01-21 14:46:54'),
(2063, 'product', 74, 'update', NULL, '2026-01-21 14:46:55'),
(2064, 'product', 74, 'update', NULL, '2026-01-21 14:47:54'),
(2065, 'product', 74, 'update', NULL, '2026-01-21 14:52:26'),
(2066, 'product', 74, 'update', NULL, '2026-01-21 14:52:59'),
(2067, 'product', 74, 'update', NULL, '2026-01-21 15:42:35'),
(2068, 'product', 74, 'update', NULL, '2026-01-21 15:43:09'),
(2069, 'product', 74, 'update', NULL, '2026-01-21 15:44:00'),
(2070, 'product', 74, 'update', NULL, '2026-01-21 15:45:21'),
(2071, 'product', 74, 'update', NULL, '2026-01-21 15:45:37'),
(2072, 'product', 74, 'update', NULL, '2026-01-21 15:46:07'),
(2073, 'product', 74, 'update', NULL, '2026-01-21 15:46:10'),
(2074, 'product', 74, 'update', NULL, '2026-01-21 15:50:52'),
(2075, 'product', 74, 'update', NULL, '2026-01-21 15:51:08'),
(2076, 'product', 74, 'update', NULL, '2026-01-21 15:52:58'),
(2077, 'product', 74, 'update', NULL, '2026-01-21 15:53:10'),
(2078, 'product', 74, 'update', NULL, '2026-01-21 15:53:35'),
(2079, 'product', 74, 'update', NULL, '2026-01-21 15:54:07'),
(2080, 'product', 74, 'update', NULL, '2026-01-21 15:54:17'),
(2081, 'product', 74, 'update', NULL, '2026-01-21 15:54:30'),
(2082, 'product', 74, 'update', NULL, '2026-01-21 15:54:34'),
(2083, 'product', 74, 'update', NULL, '2026-01-21 15:54:37'),
(2084, 'product', 74, 'update', NULL, '2026-01-21 15:54:39'),
(2085, 'product', 74, 'update', NULL, '2026-01-21 15:55:08'),
(2086, 'product', 74, 'update', NULL, '2026-01-21 16:02:38'),
(2087, 'product', 74, 'update', NULL, '2026-01-21 16:02:51'),
(2088, 'product', 74, 'update', NULL, '2026-01-21 16:02:55'),
(2089, 'product', 74, 'update', NULL, '2026-01-21 16:02:58'),
(2090, 'product', 74, 'update', NULL, '2026-01-21 16:03:16'),
(2091, 'product', 74, 'update', NULL, '2026-01-21 16:04:56'),
(2092, 'product', 74, 'update', NULL, '2026-01-21 16:05:00'),
(2093, 'product', 74, 'update', NULL, '2026-01-21 16:05:58'),
(2094, 'product', 74, 'update', NULL, '2026-01-21 16:07:11'),
(2095, 'product', 74, 'update', NULL, '2026-01-21 16:18:45'),
(2096, 'product', 74, 'update', NULL, '2026-01-21 16:24:20'),
(2097, 'product', 74, 'update', NULL, '2026-01-21 16:26:43'),
(2098, 'product', 74, 'update', NULL, '2026-01-21 16:27:01'),
(2099, 'product', 74, 'update', NULL, '2026-01-21 16:27:08'),
(2100, 'product', 74, 'update', NULL, '2026-01-21 16:29:16'),
(2101, 'product', 74, 'update', NULL, '2026-01-21 16:29:24'),
(2102, 'product', 74, 'update', NULL, '2026-01-21 16:31:09'),
(2103, 'product', 74, 'update', NULL, '2026-01-21 16:34:36'),
(2104, 'product', 74, 'update', NULL, '2026-01-21 16:34:38'),
(2105, 'product', 74, 'update', NULL, '2026-01-21 16:34:43'),
(2106, 'product', 74, 'update', NULL, '2026-01-21 16:37:13'),
(2107, 'product', 74, 'update', NULL, '2026-01-21 16:37:30'),
(2108, 'product', 74, 'update', NULL, '2026-01-21 16:37:34'),
(2109, 'product', 74, 'update', NULL, '2026-01-21 16:38:59'),
(2110, 'product', 74, 'update', NULL, '2026-01-21 16:40:42'),
(2111, 'product', 74, 'update', NULL, '2026-01-21 16:47:13'),
(2112, 'product', 74, 'update', NULL, '2026-01-21 17:00:44'),
(2113, 'product', 74, 'update', NULL, '2026-01-21 21:26:18'),
(2114, 'product', 74, 'update', NULL, '2026-01-22 07:26:31'),
(2115, 'product', 74, 'update', NULL, '2026-01-22 07:27:22'),
(2116, 'product', 74, 'update', NULL, '2026-01-22 07:27:25'),
(2117, 'product', 74, 'update', NULL, '2026-01-22 07:28:00'),
(2118, 'product', 74, 'update', NULL, '2026-01-22 07:28:33'),
(2119, 'product', 74, 'update', NULL, '2026-01-22 07:29:37'),
(2120, 'product', 74, 'update', NULL, '2026-01-22 07:30:24'),
(2121, 'product', 74, 'update', NULL, '2026-01-22 07:31:29'),
(2122, 'product', 74, 'update', NULL, '2026-01-22 07:31:50'),
(2123, 'product', 74, 'update', NULL, '2026-01-22 07:32:48'),
(2124, 'product', 74, 'update', NULL, '2026-01-22 07:33:53'),
(2125, 'product', 74, 'update', NULL, '2026-01-22 07:34:20'),
(2126, 'product', 74, 'update', NULL, '2026-01-22 07:34:57'),
(2127, 'product', 74, 'update', NULL, '2026-01-22 07:35:37'),
(2128, 'product', 74, 'update', NULL, '2026-01-22 07:36:34'),
(2129, 'product', 74, 'update', NULL, '2026-01-22 07:36:51'),
(2130, 'product', 74, 'update', NULL, '2026-01-22 07:37:14'),
(2131, 'product', 74, 'update', NULL, '2026-01-22 07:38:11'),
(2132, 'product', 74, 'update', NULL, '2026-01-22 07:39:00'),
(2133, 'product', 74, 'update', NULL, '2026-01-22 07:39:21'),
(2134, 'product', 74, 'update', NULL, '2026-01-22 07:39:50'),
(2135, 'product', 74, 'update', NULL, '2026-01-22 07:41:35'),
(2136, 'product', 74, 'update', NULL, '2026-01-22 07:41:48'),
(2137, 'product', 74, 'update', NULL, '2026-01-22 07:42:14'),
(2138, 'product', 74, 'update', NULL, '2026-01-22 07:44:26'),
(2139, 'product', 74, 'update', NULL, '2026-01-22 07:44:59'),
(2140, 'product', 74, 'update', NULL, '2026-01-22 07:47:03'),
(2141, 'product', 74, 'update', NULL, '2026-01-22 07:48:50'),
(2142, 'product', 74, 'update', NULL, '2026-01-22 07:50:23'),
(2143, 'product', 74, 'update', NULL, '2026-01-22 07:51:34'),
(2144, 'product', 74, 'update', NULL, '2026-01-22 07:52:15'),
(2145, 'product', 74, 'update', NULL, '2026-01-22 07:53:12'),
(2146, 'product', 74, 'update', NULL, '2026-01-22 07:56:39'),
(2147, 'product', 74, 'update', NULL, '2026-01-22 07:57:30'),
(2148, 'product', 74, 'update', NULL, '2026-01-22 08:00:47'),
(2149, 'product', 74, 'update', NULL, '2026-01-22 08:08:22'),
(2150, 'product', 74, 'update', NULL, '2026-01-22 08:09:19'),
(2151, 'product', 74, 'update', NULL, '2026-01-22 08:11:09'),
(2152, 'product', 74, 'update', NULL, '2026-01-22 08:11:14'),
(2153, 'product', 74, 'update', NULL, '2026-01-22 08:14:04'),
(2154, 'product', 74, 'update', NULL, '2026-01-22 08:16:39'),
(2155, 'product', 74, 'update', NULL, '2026-01-22 08:19:39'),
(2156, 'product', 73, 'update', NULL, '2026-01-22 08:19:42'),
(2157, 'product', 74, 'update', NULL, '2026-01-22 08:20:02'),
(2158, 'product', 74, 'update', NULL, '2026-01-22 08:20:19'),
(2159, 'product', 74, 'update', NULL, '2026-01-22 08:20:27'),
(2160, 'product', 74, 'update', NULL, '2026-01-22 08:20:57'),
(2161, 'product', 74, 'update', NULL, '2026-01-22 08:21:10'),
(2162, 'product', 74, 'update', NULL, '2026-01-22 08:22:10'),
(2163, 'product', 74, 'update', NULL, '2026-01-22 08:22:22'),
(2164, 'product', 74, 'update', NULL, '2026-01-22 08:22:37'),
(2165, 'product', 74, 'update', NULL, '2026-01-22 08:30:19'),
(2166, 'product', 74, 'update', NULL, '2026-01-22 08:30:49'),
(2167, 'product', 74, 'update', NULL, '2026-01-22 08:34:29'),
(2168, 'product', 74, 'update', NULL, '2026-01-22 08:34:29'),
(2169, 'product', 74, 'update', NULL, '2026-01-22 08:39:42'),
(2170, 'product', 74, 'update', NULL, '2026-01-22 08:41:49'),
(2171, 'product', 74, 'update', NULL, '2026-01-22 08:47:58'),
(2172, 'product', 74, 'update', NULL, '2026-01-22 08:51:17'),
(2173, 'product', 74, 'update', NULL, '2026-01-22 09:48:28'),
(2174, 'product', 74, 'update', NULL, '2026-01-22 09:50:56'),
(2175, 'product', 74, 'update', NULL, '2026-01-22 09:51:17'),
(2176, 'product', 74, 'update', NULL, '2026-01-22 09:51:50'),
(2177, 'product', 74, 'update', NULL, '2026-01-22 09:51:51'),
(2178, 'product', 74, 'update', NULL, '2026-01-22 09:52:47'),
(2179, 'product', 74, 'update', NULL, '2026-01-22 09:53:28'),
(2180, 'product', 74, 'update', NULL, '2026-01-22 09:55:49'),
(2181, 'product', 74, 'update', NULL, '2026-01-22 09:56:12'),
(2182, 'product', 74, 'update', NULL, '2026-01-22 10:12:07'),
(2183, 'product', 74, 'update', NULL, '2026-01-22 10:13:43'),
(2184, 'product', 74, 'update', NULL, '2026-01-22 10:14:05'),
(2185, 'product', 74, 'update', NULL, '2026-01-22 10:14:10'),
(2186, 'product', 74, 'update', NULL, '2026-01-22 10:35:29'),
(2187, 'product', 74, 'update', NULL, '2026-01-22 10:45:39'),
(2188, 'product', 74, 'update', NULL, '2026-01-22 10:45:49'),
(2189, 'product', 74, 'update', NULL, '2026-01-22 10:45:51'),
(2190, 'product', 74, 'update', NULL, '2026-01-22 10:46:18'),
(2191, 'product', 74, 'update', NULL, '2026-01-22 10:47:53'),
(2192, 'product', 74, 'update', NULL, '2026-01-22 11:01:48'),
(2193, 'product', 74, 'update', NULL, '2026-01-22 11:02:45'),
(2194, 'product', 74, 'update', NULL, '2026-01-22 11:02:54'),
(2195, 'product', 74, 'update', NULL, '2026-01-22 11:02:55'),
(2196, 'product', 74, 'update', NULL, '2026-01-22 11:03:07'),
(2197, 'product', 74, 'update', NULL, '2026-01-22 11:03:16'),
(2198, 'product', 74, 'update', NULL, '2026-01-22 11:03:54'),
(2199, 'product', 74, 'update', NULL, '2026-01-22 11:05:22'),
(2200, 'product', 74, 'update', NULL, '2026-01-22 11:12:01'),
(2201, 'product', 74, 'update', NULL, '2026-01-22 11:12:40'),
(2202, 'product', 74, 'update', NULL, '2026-01-22 11:12:56'),
(2203, 'product', 74, 'update', NULL, '2026-01-22 11:13:06'),
(2204, 'product', 74, 'update', NULL, '2026-01-22 11:13:40'),
(2205, 'product', 74, 'update', NULL, '2026-01-22 11:13:58'),
(2206, 'product', 74, 'update', NULL, '2026-01-22 11:14:16'),
(2207, 'product', 74, 'update', NULL, '2026-01-22 11:14:27'),
(2208, 'product', 74, 'update', NULL, '2026-01-22 11:15:12'),
(2209, 'product', 74, 'update', NULL, '2026-01-22 11:17:09'),
(2210, 'product', 74, 'update', NULL, '2026-01-22 11:17:13'),
(2211, 'product', 74, 'update', NULL, '2026-01-22 11:33:23'),
(2212, 'product', 74, 'update', NULL, '2026-01-22 12:17:03'),
(2213, 'product', 74, 'update', NULL, '2026-01-22 12:25:31'),
(2214, 'product', 74, 'update', NULL, '2026-01-22 12:27:32'),
(2215, 'product', 73, 'update', NULL, '2026-01-22 12:49:36'),
(2216, 'product', 73, 'update', NULL, '2026-01-22 12:49:38'),
(2217, 'product', 73, 'update', NULL, '2026-01-22 12:49:39'),
(2218, 'product', 74, 'update', NULL, '2026-01-22 13:13:56'),
(2219, 'product', 74, 'update', NULL, '2026-01-22 14:21:25'),
(2220, 'product', 74, 'update', NULL, '2026-01-22 14:58:34'),
(2221, 'product', 74, 'update', NULL, '2026-01-22 15:00:10'),
(2222, 'product', 74, 'update', NULL, '2026-01-22 16:23:32'),
(2223, 'product', 74, 'update', NULL, '2026-01-22 16:24:31'),
(2224, 'product', 74, 'update', NULL, '2026-01-22 16:24:54'),
(2225, 'product', 73, 'update', NULL, '2026-01-22 16:24:58'),
(2226, 'product', 74, 'update', NULL, '2026-01-23 08:07:53'),
(2227, 'product', 74, 'update', NULL, '2026-01-23 08:07:55'),
(2228, 'product', 73, 'update', NULL, '2026-01-23 08:07:57'),
(2229, 'product', 74, 'update', NULL, '2026-01-23 08:09:33'),
(2230, 'product', 74, 'update', NULL, '2026-01-23 08:10:05'),
(2231, 'product', 74, 'update', NULL, '2026-01-23 08:10:24'),
(2232, 'product', 74, 'update', NULL, '2026-01-23 08:10:30'),
(2233, 'product', 74, 'update', NULL, '2026-01-23 08:10:38'),
(2234, 'product', 74, 'update', NULL, '2026-01-23 08:11:07'),
(2235, 'product', 74, 'update', NULL, '2026-01-23 08:11:19'),
(2236, 'product', 75, 'create', NULL, '2026-01-23 08:11:56'),
(2237, 'product', 74, 'update', NULL, '2026-01-23 08:12:19'),
(2238, 'product', 74, 'update', NULL, '2026-01-23 08:14:32'),
(2239, 'product', 76, 'create', NULL, '2026-01-23 08:14:59'),
(2240, 'product', 74, 'update', NULL, '2026-01-23 08:16:05'),
(2241, 'product', 74, 'update', NULL, '2026-01-23 08:16:25'),
(2242, 'product', 74, 'update', NULL, '2026-01-23 08:19:45'),
(2243, 'product', 73, 'update', NULL, '2026-01-23 08:20:02'),
(2244, 'product', 74, 'update', NULL, '2026-01-23 08:20:22'),
(2245, 'product', 74, 'update', NULL, '2026-01-23 08:25:11'),
(2246, 'product', 73, 'update', NULL, '2026-01-23 08:25:15'),
(2247, 'product', 77, 'create', NULL, '2026-01-23 08:28:03'),
(2248, 'product', 77, 'update', NULL, '2026-01-23 08:28:14'),
(2249, 'product', 74, 'update', NULL, '2026-01-23 08:38:50'),
(2250, 'product', 77, 'update', NULL, '2026-01-23 08:39:21'),
(2251, 'product', 73, 'delete', NULL, '2026-01-23 08:39:33'),
(2252, 'product', 74, 'delete', NULL, '2026-01-23 08:39:37'),
(2253, 'product', 78, 'create', NULL, '2026-01-23 08:48:55'),
(2254, 'product', 74, 'delete', NULL, '2026-01-23 08:49:57'),
(2255, 'product', 74, 'update', NULL, '2026-01-23 08:50:49'),
(2256, 'product', 74, 'update', NULL, '2026-01-23 08:50:50'),
(2257, 'product', 74, 'update', NULL, '2026-01-23 08:51:03'),
(2258, 'product', 74, 'update', NULL, '2026-01-23 08:51:07'),
(2259, 'product', 74, 'update', NULL, '2026-01-23 08:51:10'),
(2260, 'product', 74, 'update', NULL, '2026-01-23 08:51:27'),
(2261, 'product', 79, 'create', NULL, '2026-01-23 08:56:29'),
(2262, 'product', 74, 'update', NULL, '2026-01-23 08:56:59'),
(2263, 'product', 79, 'update', NULL, '2026-01-23 09:01:58'),
(2264, 'product', 79, 'update', NULL, '2026-01-23 09:02:27'),
(2265, 'product', 79, 'update', NULL, '2026-01-23 09:03:39'),
(2266, 'product', 79, 'update', NULL, '2026-01-23 09:04:15'),
(2267, 'product', 79, 'update', NULL, '2026-01-23 09:04:21'),
(2268, 'product', 79, 'update', NULL, '2026-01-23 09:04:23'),
(2269, 'product', 79, 'update', NULL, '2026-01-23 09:04:26'),
(2270, 'product', 79, 'update', NULL, '2026-01-23 09:04:31'),
(2271, 'product', 79, 'update', NULL, '2026-01-23 09:04:42'),
(2272, 'product', 79, 'update', NULL, '2026-01-23 09:04:54'),
(2273, 'product', 79, 'update', NULL, '2026-01-23 09:05:03'),
(2274, 'product', 79, 'update', NULL, '2026-01-23 09:05:10'),
(2275, 'product', 79, 'update', NULL, '2026-01-23 09:05:24'),
(2276, 'product', 79, 'update', NULL, '2026-01-23 09:05:33'),
(2277, 'product', 80, 'create', NULL, '2026-01-23 09:11:27'),
(2278, 'product', 80, 'update', NULL, '2026-01-23 09:17:34'),
(2279, 'product', 80, 'update', NULL, '2026-01-23 09:17:56'),
(2280, 'product', 80, 'update', NULL, '2026-01-23 09:18:03'),
(2281, 'product', 80, 'update', NULL, '2026-01-23 09:18:17'),
(2282, 'product', 79, 'update', NULL, '2026-01-23 09:19:40'),
(2283, 'product', 80, 'update', NULL, '2026-01-23 09:20:16'),
(2284, 'product', 80, 'update', NULL, '2026-01-23 09:20:30'),
(2285, 'product', 80, 'update', NULL, '2026-01-23 09:20:37'),
(2286, 'product', 80, 'update', NULL, '2026-01-23 09:20:52'),
(2287, 'product', 80, 'update', NULL, '2026-01-23 09:21:18'),
(2288, 'product', 80, 'update', NULL, '2026-01-23 09:23:08'),
(2289, 'product', 80, 'update', NULL, '2026-01-23 09:27:41'),
(2290, 'product', 79, 'update', NULL, '2026-01-23 09:28:48'),
(2291, 'product', 80, 'update', NULL, '2026-01-23 09:28:52'),
(2292, 'product', 80, 'update', NULL, '2026-01-23 09:30:18'),
(2293, 'product', 80, 'update', NULL, '2026-01-23 09:30:50'),
(2294, 'product', 80, 'update', NULL, '2026-01-23 09:30:57'),
(2295, 'product', 79, 'update', NULL, '2026-01-23 09:31:00'),
(2296, 'product', 80, 'update', NULL, '2026-01-23 09:31:51'),
(2297, 'product', 80, 'update', NULL, '2026-01-23 09:32:13'),
(2298, 'product', 79, 'update', NULL, '2026-01-23 09:32:23'),
(2299, 'product', 79, 'update', NULL, '2026-01-23 09:32:31'),
(2300, 'product', 80, 'update', NULL, '2026-01-23 09:32:41'),
(2301, 'product', 80, 'update', NULL, '2026-01-23 09:32:52'),
(2302, 'product', 79, 'update', NULL, '2026-01-23 09:36:31'),
(2303, 'product', 79, 'update', NULL, '2026-01-23 09:36:47'),
(2304, 'product', 80, 'update', NULL, '2026-01-23 09:42:36'),
(2305, 'product', 81, 'create', NULL, '2026-01-23 09:47:00'),
(2306, 'product', 82, 'create', NULL, '2026-01-23 09:49:22'),
(2307, 'product', 80, 'update', NULL, '2026-01-23 09:52:19'),
(2308, 'product', 74, 'update', NULL, '2026-01-23 10:31:32'),
(2309, 'product', 74, 'update', NULL, '2026-01-23 10:36:23'),
(2310, 'product', 74, 'update', NULL, '2026-01-23 10:38:49'),
(2311, 'product', 74, 'update', NULL, '2026-01-23 10:39:19'),
(2312, 'product', 79, 'update', NULL, '2026-01-23 10:41:27'),
(2313, 'product', 74, 'update', NULL, '2026-01-23 10:41:36'),
(2314, 'product', 74, 'update', NULL, '2026-01-23 10:41:36'),
(2315, 'product', 74, 'update', NULL, '2026-01-23 10:41:37'),
(2316, 'product', 74, 'update', NULL, '2026-01-23 10:41:37'),
(2317, 'product', 74, 'update', NULL, '2026-01-23 10:41:37'),
(2318, 'product', 74, 'update', NULL, '2026-01-23 10:41:38'),
(2319, 'product', 74, 'update', NULL, '2026-01-23 10:41:38'),
(2320, 'product', 74, 'update', NULL, '2026-01-23 10:41:39'),
(2321, 'product', 74, 'update', NULL, '2026-01-23 10:41:39'),
(2322, 'product', 74, 'update', NULL, '2026-01-23 10:41:39'),
(2323, 'product', 74, 'update', NULL, '2026-01-23 10:41:39'),
(2324, 'product', 74, 'update', NULL, '2026-01-23 10:42:13'),
(2325, 'product', 74, 'update', NULL, '2026-01-23 10:42:13'),
(2326, 'product', 74, 'update', NULL, '2026-01-23 10:42:13'),
(2327, 'product', 74, 'update', NULL, '2026-01-23 10:42:13'),
(2328, 'product', 74, 'update', NULL, '2026-01-23 10:42:14'),
(2329, 'product', 74, 'update', NULL, '2026-01-23 10:42:14'),
(2330, 'product', 74, 'update', NULL, '2026-01-23 10:42:14'),
(2331, 'product', 74, 'update', NULL, '2026-01-23 10:42:14'),
(2332, 'product', 74, 'update', NULL, '2026-01-23 10:46:12'),
(2333, 'product', 74, 'update', NULL, '2026-01-23 10:46:12'),
(2334, 'product', 74, 'update', NULL, '2026-01-23 10:47:15'),
(2335, 'product', 74, 'update', NULL, '2026-01-23 10:48:17'),
(2336, 'product', 74, 'update', NULL, '2026-01-23 10:50:00'),
(2337, 'product', 74, 'update', NULL, '2026-01-23 10:58:38'),
(2338, 'product', 74, 'update', NULL, '2026-01-23 11:08:12'),
(2339, 'product', 74, 'update', NULL, '2026-01-23 11:19:26'),
(2340, 'product', 74, 'update', NULL, '2026-01-23 11:19:48'),
(2341, 'product', 74, 'update', NULL, '2026-01-23 11:20:32'),
(2342, 'product', 74, 'update', NULL, '2026-01-23 11:20:37'),
(2343, 'product', 74, 'update', NULL, '2026-01-23 11:21:07'),
(2344, 'product', 74, 'update', NULL, '2026-01-23 11:21:09'),
(2345, 'product', 74, 'update', NULL, '2026-01-23 11:21:43'),
(2346, 'product', 74, 'update', NULL, '2026-01-23 11:22:02'),
(2347, 'product', 74, 'update', NULL, '2026-01-23 11:22:29'),
(2348, 'product', 74, 'update', NULL, '2026-01-23 11:23:09'),
(2349, 'product', 74, 'update', NULL, '2026-01-23 11:24:07'),
(2350, 'product', 74, 'update', NULL, '2026-01-23 11:24:26'),
(2351, 'product', 74, 'update', NULL, '2026-01-23 11:24:42'),
(2352, 'product', 74, 'update', NULL, '2026-01-23 11:24:43'),
(2353, 'product', 74, 'update', NULL, '2026-01-23 11:25:00'),
(2354, 'product', 74, 'update', NULL, '2026-01-23 11:30:22'),
(2355, 'product', 74, 'update', NULL, '2026-01-23 11:30:30'),
(2356, 'product', 74, 'update', NULL, '2026-01-23 11:30:37'),
(2357, 'product', 74, 'update', NULL, '2026-01-23 11:30:43'),
(2358, 'product', 74, 'update', NULL, '2026-01-23 11:30:54'),
(2359, 'product', 74, 'update', NULL, '2026-01-23 11:32:19'),
(2360, 'product', 74, 'update', NULL, '2026-01-23 11:32:25'),
(2361, 'product', 74, 'update', NULL, '2026-01-23 11:32:33'),
(2362, 'product', 74, 'update', NULL, '2026-01-23 11:32:56'),
(2363, 'product', 74, 'update', NULL, '2026-01-23 11:33:50'),
(2364, 'product', 74, 'update', NULL, '2026-01-23 11:33:54'),
(2365, 'product', 74, 'update', NULL, '2026-01-23 11:34:05'),
(2366, 'product', 74, 'update', NULL, '2026-01-23 11:34:10'),
(2367, 'product', 74, 'update', NULL, '2026-01-23 11:34:14'),
(2368, 'product', 74, 'update', NULL, '2026-01-23 11:34:26'),
(2369, 'product', 74, 'update', NULL, '2026-01-23 11:34:37'),
(2370, 'product', 74, 'update', NULL, '2026-01-23 11:35:20'),
(2371, 'product', 74, 'update', NULL, '2026-01-23 11:35:34'),
(2372, 'product', 74, 'update', NULL, '2026-01-23 11:35:43'),
(2373, 'product', 74, 'update', NULL, '2026-01-23 11:36:13'),
(2374, 'product', 74, 'update', NULL, '2026-01-23 11:36:20'),
(2375, 'product', 74, 'update', NULL, '2026-01-23 11:36:29'),
(2376, 'product', 74, 'update', NULL, '2026-01-23 11:36:46'),
(2377, 'product', 74, 'update', NULL, '2026-01-23 11:37:07'),
(2378, 'product', 74, 'update', NULL, '2026-01-23 11:38:05'),
(2379, 'product', 74, 'update', NULL, '2026-01-23 11:38:09'),
(2380, 'product', 74, 'update', NULL, '2026-01-23 11:38:28'),
(2381, 'product', 74, 'update', NULL, '2026-01-23 12:02:35'),
(2382, 'product', 80, 'update', NULL, '2026-01-23 12:09:31'),
(2383, 'product', 82, 'update', NULL, '2026-01-23 12:31:09'),
(2384, 'product', 82, 'update', NULL, '2026-01-23 12:36:23'),
(2385, 'product', 77, 'update', NULL, '2026-01-23 12:36:36'),
(2386, 'product', 82, 'update', NULL, '2026-01-23 12:36:54'),
(2387, 'product', 79, 'update', NULL, '2026-01-23 12:37:37'),
(2388, 'product', 82, 'update', NULL, '2026-01-23 12:37:39'),
(2389, 'product', 77, 'update', NULL, '2026-01-23 12:38:01'),
(2390, 'product', 77, 'update', NULL, '2026-01-23 12:40:13'),
(2391, 'product', 77, 'update', NULL, '2026-01-23 12:40:23'),
(2392, 'product', 77, 'update', NULL, '2026-01-23 12:40:34'),
(2393, 'product', 82, 'update', NULL, '2026-01-23 12:41:18'),
(2394, 'product', 82, 'update', NULL, '2026-01-23 12:41:36'),
(2395, 'product', 82, 'update', NULL, '2026-01-23 12:41:45'),
(2396, 'product', 81, 'update', NULL, '2026-01-23 12:46:14'),
(2397, 'product', 82, 'update', NULL, '2026-01-23 12:46:44'),
(2398, 'product', 77, 'update', NULL, '2026-01-23 12:49:08'),
(2399, 'product', 79, 'update', NULL, '2026-01-23 12:49:23'),
(2400, 'product', 80, 'update', NULL, '2026-01-23 12:49:25'),
(2401, 'product', 80, 'update', NULL, '2026-01-23 12:49:39'),
(2402, 'product', 80, 'update', NULL, '2026-01-23 12:50:16'),
(2403, 'product', 80, 'update', NULL, '2026-01-23 12:50:39'),
(2404, 'product', 80, 'update', NULL, '2026-01-23 12:50:57'),
(2405, 'product', 80, 'update', NULL, '2026-01-23 12:51:03'),
(2406, 'product', 80, 'update', NULL, '2026-01-23 12:51:11'),
(2407, 'product', 80, 'update', NULL, '2026-01-23 12:52:15'),
(2408, 'product', 80, 'update', NULL, '2026-01-23 12:52:28'),
(2409, 'product', 82, 'update', NULL, '2026-01-23 12:58:05'),
(2410, 'product', 74, 'update', NULL, '2026-01-23 12:59:37'),
(2411, 'product', 77, 'update', NULL, '2026-01-23 13:00:08'),
(2412, 'product', 82, 'update', NULL, '2026-01-23 13:00:08'),
(2413, 'product', 74, 'update', NULL, '2026-01-23 13:00:21'),
(2414, 'product', 74, 'update', NULL, '2026-01-23 13:00:43'),
(2415, 'product', 77, 'update', NULL, '2026-01-23 13:00:54'),
(2416, 'product', 77, 'update', NULL, '2026-01-23 13:01:31'),
(2417, 'product', 74, 'update', NULL, '2026-01-23 13:02:20'),
(2418, 'product', 74, 'update', NULL, '2026-01-23 13:02:26'),
(2419, 'product', 74, 'update', NULL, '2026-01-23 13:02:27'),
(2420, 'product', 74, 'update', NULL, '2026-01-23 13:02:28'),
(2421, 'product', 74, 'update', NULL, '2026-01-23 13:02:38'),
(2422, 'product', 74, 'update', NULL, '2026-01-23 13:02:38'),
(2423, 'product', 74, 'update', NULL, '2026-01-23 13:02:39'),
(2424, 'product', 74, 'update', NULL, '2026-01-23 13:02:39'),
(2425, 'product', 74, 'update', NULL, '2026-01-23 13:02:39'),
(2426, 'product', 74, 'update', NULL, '2026-01-23 13:02:40'),
(2427, 'product', 74, 'update', NULL, '2026-01-23 13:02:40'),
(2428, 'product', 74, 'update', NULL, '2026-01-23 13:02:44'),
(2429, 'product', 77, 'update', NULL, '2026-01-23 13:02:44'),
(2430, 'product', 77, 'update', NULL, '2026-01-23 13:02:45'),
(2431, 'product', 74, 'update', NULL, '2026-01-23 13:02:45'),
(2432, 'product', 77, 'update', NULL, '2026-01-23 13:02:47'),
(2433, 'product', 74, 'update', NULL, '2026-01-23 13:02:48'),
(2434, 'product', 74, 'update', NULL, '2026-01-23 13:02:49'),
(2435, 'product', 74, 'update', NULL, '2026-01-23 13:02:49'),
(2436, 'product', 74, 'update', NULL, '2026-01-23 13:02:49'),
(2437, 'product', 74, 'update', NULL, '2026-01-23 13:02:49'),
(2438, 'product', 74, 'update', NULL, '2026-01-23 13:02:50'),
(2439, 'product', 74, 'update', NULL, '2026-01-23 13:02:50'),
(2440, 'product', 74, 'update', NULL, '2026-01-23 13:02:50'),
(2441, 'product', 74, 'update', NULL, '2026-01-23 13:02:51'),
(2442, 'product', 74, 'update', NULL, '2026-01-23 13:03:10'),
(2443, 'product', 74, 'update', NULL, '2026-01-23 13:03:12'),
(2444, 'product', 74, 'update', NULL, '2026-01-23 13:03:13'),
(2445, 'product', 74, 'update', NULL, '2026-01-23 13:03:13'),
(2446, 'product', 74, 'update', NULL, '2026-01-23 13:03:13'),
(2447, 'product', 74, 'update', NULL, '2026-01-23 13:03:14'),
(2448, 'product', 74, 'update', NULL, '2026-01-23 13:03:14'),
(2449, 'product', 74, 'update', NULL, '2026-01-23 13:03:14'),
(2450, 'product', 74, 'update', NULL, '2026-01-23 13:03:14'),
(2451, 'product', 74, 'update', NULL, '2026-01-23 13:03:15'),
(2452, 'product', 74, 'update', NULL, '2026-01-23 13:03:16'),
(2453, 'product', 74, 'update', NULL, '2026-01-23 13:03:16'),
(2454, 'product', 74, 'update', NULL, '2026-01-23 13:03:16'),
(2455, 'product', 74, 'update', NULL, '2026-01-23 13:03:16'),
(2456, 'product', 74, 'update', NULL, '2026-01-23 13:03:17'),
(2457, 'product', 74, 'update', NULL, '2026-01-23 13:03:17'),
(2458, 'product', 74, 'update', NULL, '2026-01-23 13:03:17'),
(2459, 'product', 74, 'update', NULL, '2026-01-23 13:03:18'),
(2460, 'product', 74, 'update', NULL, '2026-01-23 13:03:18'),
(2461, 'product', 74, 'update', NULL, '2026-01-23 13:03:18'),
(2462, 'product', 74, 'update', NULL, '2026-01-23 13:03:20'),
(2463, 'product', 74, 'update', NULL, '2026-01-23 13:03:21'),
(2464, 'product', 74, 'update', NULL, '2026-01-23 13:03:21'),
(2465, 'product', 74, 'update', NULL, '2026-01-23 13:03:21'),
(2466, 'product', 74, 'update', NULL, '2026-01-23 13:03:21'),
(2467, 'product', 74, 'update', NULL, '2026-01-23 13:03:26'),
(2468, 'product', 80, 'update', NULL, '2026-01-23 13:09:18'),
(2469, 'product', 74, 'update', NULL, '2026-01-23 13:09:54'),
(2470, 'product', 74, 'update', NULL, '2026-01-23 13:09:55'),
(2471, 'product', 74, 'update', NULL, '2026-01-23 13:09:55'),
(2472, 'product', 74, 'update', NULL, '2026-01-23 13:09:56'),
(2473, 'product', 74, 'update', NULL, '2026-01-23 13:12:41'),
(2474, 'product', 82, 'update', NULL, '2026-01-23 13:15:56'),
(2475, 'product', 74, 'update', NULL, '2026-01-23 13:15:59'),
(2476, 'product', 74, 'update', NULL, '2026-01-23 13:16:15'),
(2477, 'product', 74, 'update', NULL, '2026-01-23 13:16:51'),
(2478, 'product', 74, 'update', NULL, '2026-01-23 13:16:57'),
(2479, 'product', 82, 'update', NULL, '2026-01-23 13:18:19'),
(2480, 'product', 82, 'update', NULL, '2026-01-23 13:18:33'),
(2481, 'product', 82, 'update', NULL, '2026-01-23 13:19:45'),
(2482, 'product', 82, 'update', NULL, '2026-01-23 13:21:52'),
(2483, 'product', 80, 'update', NULL, '2026-01-23 13:32:00'),
(2484, 'product', 80, 'update', NULL, '2026-01-23 13:36:52'),
(2485, 'product', 80, 'update', NULL, '2026-01-23 13:37:04'),
(2486, 'product', 80, 'update', NULL, '2026-01-23 13:44:07'),
(2487, 'product', 74, 'update', NULL, '2026-01-23 13:44:53'),
(2488, 'product', 74, 'update', NULL, '2026-01-23 13:47:09'),
(2489, 'product', 74, 'update', NULL, '2026-01-23 13:47:22'),
(2490, 'product', 74, 'update', NULL, '2026-01-23 13:47:22'),
(2491, 'product', 74, 'update', NULL, '2026-01-23 13:47:24'),
(2492, 'product', 74, 'update', NULL, '2026-01-23 13:47:24'),
(2493, 'product', 74, 'update', NULL, '2026-01-23 13:47:25'),
(2494, 'product', 74, 'update', NULL, '2026-01-23 14:18:49'),
(2495, 'product', 74, 'update', NULL, '2026-01-23 14:18:58'),
(2496, 'product', 74, 'update', NULL, '2026-01-23 14:20:03'),
(2497, 'product', 74, 'update', NULL, '2026-01-23 14:20:16'),
(2498, 'product', 74, 'update', NULL, '2026-01-23 14:20:22'),
(2499, 'product', 74, 'update', NULL, '2026-01-23 14:20:24'),
(2500, 'product', 74, 'update', NULL, '2026-01-23 14:20:32'),
(2501, 'product', 74, 'update', NULL, '2026-01-23 14:29:21'),
(2502, 'product', 74, 'update', NULL, '2026-01-23 14:29:26'),
(2503, 'product', 74, 'update', NULL, '2026-01-23 14:29:30'),
(2504, 'product', 82, 'update', NULL, '2026-01-23 14:38:45'),
(2505, 'product', 74, 'update', NULL, '2026-01-23 14:38:47'),
(2506, 'product', 74, 'update', NULL, '2026-01-23 14:49:08'),
(2507, 'product', 83, 'create', NULL, '2026-01-23 14:50:13'),
(2508, 'product', 79, 'update', NULL, '2026-01-23 14:50:47');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000001_create_stores_table', 1),
(4, '0001_01_01_000002_create_jobs_table', 1),
(5, '2024_01_01_000001_create_vendors_table', 1),
(6, '2024_01_01_000002_create_admins_table', 1),
(7, '2024_01_01_000003_create_deliveries_table', 1),
(8, '2024_09_29_000001_remove_role_column_from_admins_table', 1),
(9, '2024_10_06_000001_create_branches_table', 1),
(10, '2024_10_06_000002_add_foreign_keys_to_stores_table', 1),
(11, '2025_09_27_105010_create_main_categories_table', 1),
(12, '2025_09_28_101341_create_permission_tables', 1),
(13, '2025_10_01_000002_remove_store_name_from_vendors_table', 1),
(14, '2025_10_01_000003_create_main_category_store_table', 1),
(15, '2025_10_01_000005_add_store_id_to_vendors_table', 1),
(16, '2024_10_07_000001_create_categories_table', 2),
(17, '2024_10_08_000001_create_products_table', 3),
(18, '2024_10_08_000002_create_product_images_table', 3),
(19, '2024_10_08_000003_create_option_groups_table', 3),
(20, '2024_10_08_000004_create_option_values_table', 3),
(21, '2024_10_08_000005_create_product_options_table', 3),
(22, '2024_10_08_000006_create_product_option_values_table', 3),
(23, '2024_10_08_000007_create_addons_table', 3),
(24, '2024_10_08_000008_create_product_addons_table', 3),
(25, '2024_10_08_000009_create_elasticsearch_sync_queue_table', 3),
(26, '2025_10_15_121546_create_activity_log_table', 4),
(27, '2025_10_15_121547_add_event_column_to_activity_log_table', 4),
(28, '2025_10_15_121548_add_batch_uuid_column_to_activity_log_table', 4),
(29, '2024_10_17_063300_create_user_addresses_table', 5),
(30, '2024_10_17_112600_add_opening_closing_times_to_branches_table', 5),
(31, '2025_10_29_092120_create_carts_table', 6),
(32, '2025_10_29_092230_create_cart_items_table', 6),
(33, '2025_10_29_092232_create_cart_item_options_table', 6),
(34, '2025_10_29_092233_create_cart_item_addons_table', 6),
(35, '2025_10_30_081957_create_orders_table', 7),
(36, '2025_10_30_082035_create_order_items_table', 7),
(37, '2025_10_30_082059_create_order_item_options_table', 7),
(38, '2025_10_30_082124_create_order_item_addons_table', 7),
(39, '2025_11_11_110000_create_payments_table', 7),
(40, '2025_11_11_141234_add_simple_status_to_orders_table', 8),
(41, '2025_11_12_082603_update_existing_orders_simple_status', 9),
(42, '2025_12_01_110500_update_orders_simple_status_add_ready_to_pick', 10),
(43, '2025_12_01_163500_add_delivery_id_to_orders_table', 11),
(44, '2025_12_04_110113_add_branch_id_to_orders_table', 12),
(45, '2025_12_04_154227_add_order_pickup_image_to_orders_table', 13),
(46, '2025_12_05_000001_add_shift_times_to_deliveries_table', 14),
(47, '2025_12_12_115931_add_soft_deletes_to_all_tables', 14),
(48, '2025_12_12_124602_add_soft_deletes_to_products_table', 15),
(49, '2025_12_26_124737_add_is_cash_handed_over_to_orders_table', 16),
(50, '2025_12_29_101209_add_fcm_token_to_all_guards', 17),
(51, '2026_01_02_160800_create_push_notifications_table', 18),
(52, '2026_01_05_103000_create_vendor_invoices_table', 19),
(53, '2026_01_05_153000_add_invoice_number_to_vendor_invoices', 19),
(54, '2026_01_06_101500_create_delivery_invoices_table', 20),
(55, '2026_01_19_000000_make_store_id_nullable_in_products_table', 21),
(56, '2026_01_19_000001_rename_main_categories_to_modules', 21),
(57, '2026_01_19_000002_add_module_id_to_categories_table', 21),
(58, '2026_01_19_000003_add_parent_id_to_categories_table', 21),
(59, '2026_01_19_000004_add_subcategory_id_to_products_table', 21),
(60, '2026_01_19_000005_add_search_keywords_to_products_table', 22),
(61, '2026_01_19_000006_add_image_to_option_values_table', 23),
(62, '2026_01_19_000007_make_store_id_nullable_in_addons_table', 24),
(63, '2026_01_21_104103_make_store_id_nullable_in_carts_table', 25),
(64, '2026_01_21_104440_make_store_id_nullable_in_orders_table', 25),
(65, '2026_01_21_124955_add_image_and_makook_sandwitch_to_option_groups_table', 26),
(66, '2026_01_23_112629_remove_soft_deletes_from_product_option_values_table', 27);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\Admin', 1),
(2, 'App\\Models\\Admin', 2),
(11, 'App\\Models\\Admin', 6),
(4, 'App\\Models\\Admin', 7),
(2, 'App\\Models\\Admin', 8);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name_en`, `name_ar`, `description_en`, `description_ar`, `image`, `status`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(25, 'makook kitchen', 'مطبخ مكوك', 'makook kitchen', 'مطبخ مكوك', 'modules/72XbUxXbp7UmHnOTaIs1Xm1xHNjvv70X3hAbcYel.svg', 1, 0, '2025-10-15 09:15:16', '2026-01-19 12:31:49', NULL),
(26, 'makook sandwitch', 'مكوك سندوتش', 'makook sandwitch', 'مكوك سندوتش', 'modules/wZzJsS5M5wdIohzmk0rTt9k6YqgPigZhiA0mxiiM.svg', 1, 1, '2025-10-15 09:16:31', '2026-01-19 12:33:40', NULL),
(27, 'makook healthy', 'مكوك هيلثى', 'makook healthy', 'مكوك هيلثى', 'modules/E2YCeMlcIIKmM9px4rDyq6GYtKVKCiloiHSw6DxX.svg', 1, 3, '2025-10-15 09:17:43', '2026-01-19 12:34:43', NULL),
(31, 'makook mart', 'سوبر ماركت مكوك', 'makook mart', 'سوبر ماركت مكوك', 'modules/huI77uQVqGE9M0oV8puHM4qz7Nmgfi49N8LapDz0.svg', 1, 0, '2025-11-18 07:36:47', '2026-01-19 12:32:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `module_store`
--

CREATE TABLE `module_store` (
  `id` bigint UNSIGNED NOT NULL,
  `store_id` bigint UNSIGNED NOT NULL,
  `main_category_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `option_groups`
--

CREATE TABLE `option_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'size, weight, packaging, color, flavor, etc.',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `makook_sandwitch` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `option_groups`
--

INSERT INTO `option_groups` (`id`, `name_en`, `name_ar`, `type`, `is_active`, `image`, `makook_sandwitch`, `created_at`, `updated_at`, `deleted_at`) VALUES
(21, 'Bread', 'العيش', 'نوع العيش', 1, 'option_groups/y6OruTZDNpZEaKsPFTpn0UdZq52yNFzyjWaNOCES.webp', 1, '2026-01-21 14:30:55', '2026-01-21 14:30:55', NULL),
(22, 'protein', 'اللحوم', 'protein', 1, 'option_groups/nkq9FEYPY2fpghNJZOP9xp8teKznnRGvPfN9RGpu.png', 1, '2026-01-22 05:34:12', '2026-01-22 05:34:12', NULL),
(23, 'cheese', 'الجبن', 'cheese', 1, 'option_groups/pdMu154vW464oF9VhAPLWoUrDDqhGkIgMM96LVkD.png', 1, '2026-01-22 05:37:47', '2026-01-22 05:37:47', NULL),
(24, 'size of product', 'حجم المنتج', 'size', 1, NULL, 0, '2026-01-23 10:08:48', '2026-01-23 10:08:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `option_values`
--

CREATE TABLE `option_values` (
  `id` bigint UNSIGNED NOT NULL,
  `option_group_id` bigint UNSIGNED NOT NULL,
  `value_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `option_values`
--

INSERT INTO `option_values` (`id`, `option_group_id`, `value_en`, `value_ar`, `image`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(34, 21, 'italian bread', 'خبز ايطالى', 'option-values/r9bkPuojtO7GK78prbibeAxYdVVY32HBv9LnhGdh.png', 1, 1, '2026-01-22 05:30:15', '2026-01-22 05:30:15', NULL),
(35, 21, 'sesme bread', 'خبز بالسمسم', 'option-values/YDb45lJxP81m2f3p6Dr3l9RdZyFArLQRGTHauITj.png', 2, 1, '2026-01-22 05:32:09', '2026-01-22 05:53:58', NULL),
(36, 22, 'salami', 'سلامى', 'option-values/jQKSR4eqOuxNkzQs2JpVqWZpUZeOVRUKP7i6Q8OY.png', 1, 1, '2026-01-22 05:35:19', '2026-01-22 05:35:19', NULL),
(37, 22, 'smoke turkey', 'تركى مدخن', 'option-values/ISbDPKfThF1xTzBbBRnJWXUOuqf5kee8LaydcW4a.png', 2, 1, '2026-01-22 05:36:00', '2026-01-22 05:36:00', NULL),
(38, 23, 'romi', 'رومى', 'option-values/FSDYOqGXfhkp0nZGLDqRTmcTwoSO6IGsyXcuy7Kh.png', 1, 1, '2026-01-22 05:38:47', '2026-01-22 05:38:47', NULL),
(39, 23, 'cheeder', 'شيدر', 'option-values/LG8WYfFGWrGQd2CIs4DuL5WbDZaqWo9AArm8wxDC.png', 2, 1, '2026-01-22 05:39:38', '2026-01-22 05:39:38', NULL),
(40, 23, 'Roquefort', 'ريكفورت', NULL, 3, 1, '2026-01-22 05:49:22', '2026-01-22 05:49:22', NULL),
(41, 24, 'small', 'صغير', NULL, 1, 1, '2026-01-23 10:08:48', '2026-01-23 10:08:48', NULL),
(42, 24, 'large', 'كبير', NULL, 2, 1, '2026-01-23 10:08:48', '2026-01-23 10:08:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `store_id` bigint UNSIGNED DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `address_id` bigint UNSIGNED DEFAULT NULL,
  `address_snapshot` json NOT NULL,
  `payment_method` enum('cash','online') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_cash_handed_over` tinyint(1) NOT NULL DEFAULT '0',
  `payment_status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_status` enum('pending','pending_payment','confirmed','preparing','ready','out_for_delivery','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `simple_status` enum('in_progress','ready_to_pick','in_delivery','cancelled','delivered') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `order_pickup_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delivery_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_paid_to_vendor` tinyint(1) NOT NULL DEFAULT '0',
  `vendor_invoice_id` bigint UNSIGNED DEFAULT NULL,
  `delivery_invoice_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_snapshot` json NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item_addons`
--

CREATE TABLE `order_item_addons` (
  `id` bigint UNSIGNED NOT NULL,
  `order_item_id` bigint UNSIGNED NOT NULL,
  `addon_snapshot` json NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item_options`
--

CREATE TABLE `order_item_options` (
  `id` bigint UNSIGNED NOT NULL,
  `order_item_id` bigint UNSIGNED NOT NULL,
  `option_snapshot` json NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_cents` int NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EGP',
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_3d_secure` tinyint(1) NOT NULL DEFAULT '0',
  `card_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_pan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_response` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `txn_response_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `integration_id` int DEFAULT NULL,
  `hmac` text COLLATE utf8mb4_unicode_ci,
  `merchant_commission` decimal(10,2) NOT NULL DEFAULT '0.00',
  `accept_fees` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_created_at` timestamp NULL DEFAULT NULL,
  `raw_response` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin-users.view', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(2, 'admin-users.create', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(3, 'admin-users.update', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(4, 'admin-users.delete', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(5, 'admin-users.roles', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(6, 'stores.view', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(7, 'stores.create', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(8, 'stores.update', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(9, 'stores.delete', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(10, 'stores.approve', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(11, 'categories.view', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(12, 'categories.create', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(13, 'categories.update', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(14, 'categories.delete', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(15, 'settings.view', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(16, 'settings.update', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(17, 'system.maintenance', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(18, 'products.view', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(19, 'products.create', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(20, 'products.update', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(21, 'products.delete', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(22, 'orders.view', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(23, 'orders.update', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(24, 'orders.cancel', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(25, 'staff.view', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(26, 'staff.create', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(27, 'staff.update', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(28, 'staff.delete', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(29, 'vendor-settings.view', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(30, 'vendor-settings.update', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(31, 'delivery-users.view', 'admins', '2025-11-24 08:00:51', '2025-11-24 08:00:51'),
(32, 'delivery-users.create', 'admins', '2025-11-24 08:00:51', '2025-11-24 08:00:51'),
(33, 'delivery-users.update', 'admins', '2025-11-24 08:00:51', '2025-11-24 08:00:51'),
(34, 'delivery-users.delete', 'admins', '2025-11-24 08:00:51', '2025-11-24 08:00:51'),
(35, 'users.view', 'admins', '2026-01-02 06:30:40', '2026-01-02 06:30:40'),
(36, 'users.create', 'admins', '2026-01-02 06:30:40', '2026-01-02 06:30:40'),
(37, 'users.update', 'admins', '2026-01-02 06:30:40', '2026-01-02 06:30:40'),
(38, 'users.delete', 'admins', '2026-01-02 06:30:40', '2026-01-02 06:30:40'),
(39, 'notifications.view', 'admins', '2026-01-02 12:15:44', '2026-01-02 12:15:44'),
(40, 'notifications.create', 'admins', '2026-01-02 12:15:44', '2026-01-02 12:15:44');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `store_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `subcategory_id` bigint UNSIGNED DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `search_keywords` text COLLATE utf8mb4_unicode_ci COMMENT 'For Elasticsearch optimization',
  `base_price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `view_count` int NOT NULL DEFAULT '0' COMMENT 'For popularity ranking',
  `sales_count` int NOT NULL DEFAULT '0' COMMENT 'For popularity ranking',
  `metadata` json DEFAULT NULL COMMENT 'For flexible future data',
  `sort_order` int NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `store_id`, `category_id`, `subcategory_id`, `name_en`, `name_ar`, `description_en`, `description_ar`, `search_keywords`, `base_price`, `is_active`, `view_count`, `sales_count`, `metadata`, `sort_order`, `deleted_at`, `created_at`, `updated_at`) VALUES
(73, NULL, 61, NULL, 'Xavier Guy', 'لفقثافقاقف', 'Minim vitae velit co', 'افقافقافقافق', 'Impedit qui exercitg,gregerg,gregeg', 50.00, 1, 20, 0, NULL, 1, '2026-01-23 06:39:33', '2026-01-20 08:53:00', '2026-01-23 06:39:33'),
(74, NULL, 62, NULL, 'makook sandwitch', 'مكوك سندوتش', 'makook sandwitch', 'مكوك سندوتش', NULL, 0.00, 1, 336, 0, NULL, 2, NULL, '2026-01-21 11:16:25', '2026-01-23 12:49:08'),
(75, NULL, 69, NULL, 'white cheese', 'جبنه بيضه', 'white cheese with low salt', 'جبنه بيضه ملح خفيف', NULL, 80.00, 1, 0, 0, NULL, 3, NULL, '2026-01-23 06:11:56', '2026-01-23 06:11:56'),
(76, NULL, 69, NULL, 'Rumi cheese', 'جبنه رومي', 'Rumi cheese (Old Roman cheese)', 'جبنه رةمي قديمه بطارخ', NULL, 240.00, 1, 0, 0, NULL, 4, NULL, '2026-01-23 06:14:59', '2026-01-23 06:14:59'),
(77, NULL, 70, NULL, 'jambo dish', 'طبق الجامبو', 'contain (eggs - carrots - cheese -Cucumber - potato)', 'يحتوي علي (بيض - جزر - جبنه - خيار - بطاطا)', NULL, 130.00, 1, 14, 0, NULL, 5, NULL, '2026-01-23 06:28:03', '2026-01-23 11:02:47'),
(78, NULL, 62, NULL, 'makook sandwitch', 'مكوك سندوتش', 'makook sandwitch', 'مكوك سندوتش', NULL, 0.00, 1, 0, 0, NULL, 6, '2026-01-05 06:50:45', '2026-01-23 06:48:55', '2026-01-23 06:48:55'),
(79, NULL, 71, NULL, 'classic cheese pizza', 'بيتزا جبنه تقليديه', 'extra cheese', 'جبنه زياده', NULL, 190.00, 1, 25, 0, NULL, 6, NULL, '2026-01-23 06:56:29', '2026-01-23 12:50:47'),
(83, NULL, 74, 75, '1/4 old romi', 'ربع رومى', '1/4 old romi', 'ربع رومى', NULL, 45.00, 1, 0, 0, NULL, 7, NULL, '2026-01-23 12:50:13', '2026-01-23 12:50:13');

-- --------------------------------------------------------

--
-- Table structure for table `product_addons`
--

CREATE TABLE `product_addons` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `addon_id` bigint UNSIGNED NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_addons`
--

INSERT INTO `product_addons` (`id`, `product_id`, `addon_id`, `is_available`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(50, 77, 30, 1, 0, '2026-01-23 06:28:03', '2026-01-23 06:28:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_primary`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(77, 73, 'products/ic0mFr68MbowRuJmGB663RHSNt281liVUopRAl4g.jpg', 1, 0, '2026-01-20 08:53:00', '2026-01-20 08:53:00', NULL),
(78, 74, 'products/LLlZrhjVe9XKJYitHQ9WHSKPXtoKC9NG8siomwl8.jpg', 1, 0, '2026-01-21 11:16:25', '2026-01-21 11:16:25', NULL),
(79, 75, 'products/l74eZnZyvb20lPspmo414Pnu0fnug2QAfVHsLF28.webp', 1, 0, '2026-01-23 06:11:56', '2026-01-23 06:11:56', NULL),
(80, 76, 'products/ojxfbTvhPucQBqcmTgdlK7tlqsqMOIy7MHiq7axV.jpg', 1, 0, '2026-01-23 06:14:59', '2026-01-23 06:14:59', NULL),
(81, 77, 'products/pe5dK3rAGSR0bgvnzHYM8iaplyWjHkJSVogsVGXh.png', 1, 0, '2026-01-23 06:28:03', '2026-01-23 06:28:03', NULL),
(82, 78, 'products/6SzmtmGTHkGP32mKk4NpGPvler44XrxtyEYh4WQK.jpg', 1, 0, '2026-01-23 06:48:55', '2026-01-23 06:48:55', NULL),
(83, 79, 'products/UnA7AuAVFHdWy5K9djUDMPIpMns2ewEL79gExdf7.jpg', 1, 0, '2026-01-23 06:56:29', '2026-01-23 06:56:29', NULL),
(87, 83, 'products/nEumvRxC7axQYFbrU8QpQiBaIiiNIVHmYXkaNWIA.png', 1, 0, '2026-01-23 12:50:13', '2026-01-23 12:50:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_options`
--

CREATE TABLE `product_options` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `option_group_id` bigint UNSIGNED NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_options`
--

INSERT INTO `product_options` (`id`, `product_id`, `option_group_id`, `is_required`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(74, 74, 21, 1, 1, '2026-01-22 06:34:28', '2026-01-22 06:34:28', NULL),
(75, 74, 23, 1, 1, '2026-01-22 07:51:50', '2026-01-22 07:51:50', NULL),
(76, 74, 22, 1, 1, '2026-01-22 09:02:54', '2026-01-22 09:02:54', NULL),
(79, 79, 24, 1, 1, '2026-01-23 12:51:10', '2026-01-23 12:51:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_option_values`
--

CREATE TABLE `product_option_values` (
  `id` bigint UNSIGNED NOT NULL,
  `product_option_id` bigint UNSIGNED NOT NULL,
  `option_value_id` bigint UNSIGNED NOT NULL,
  `price_type` enum('fixed','additional','percentage') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'additional',
  `price_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_quantity` int NOT NULL DEFAULT '0',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_option_values`
--

INSERT INTO `product_option_values` (`id`, `product_option_id`, `option_value_id`, `price_type`, `price_value`, `stock_quantity`, `is_available`, `created_at`, `updated_at`) VALUES
(111, 74, 34, 'additional', 20.00, 0, 1, '2026-01-22 06:34:29', '2026-01-23 09:24:26'),
(113, 75, 38, 'additional', 20.00, 0, 1, '2026-01-22 07:51:50', '2026-01-22 07:51:50'),
(114, 75, 39, 'additional', 30.00, 0, 1, '2026-01-22 07:51:50', '2026-01-22 07:51:50'),
(116, 76, 36, 'additional', 50.00, 0, 1, '2026-01-22 09:02:54', '2026-01-23 11:16:14'),
(117, 76, 37, 'additional', 40.00, 0, 1, '2026-01-22 09:02:54', '2026-01-23 11:16:14'),
(131, 75, 40, 'additional', 0.00, 0, 1, '2026-01-23 11:16:57', '2026-01-23 11:16:57'),
(132, 79, 41, 'additional', 10.00, 0, 1, '2026-01-23 12:51:10', '2026-01-23 12:51:10'),
(133, 79, 42, 'additional', 10.00, 0, 1, '2026-01-23 12:51:10', '2026-01-23 12:51:10');

-- --------------------------------------------------------

--
-- Table structure for table `push_notifications`
--

CREATE TABLE `push_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all_users',
  `success_count` int NOT NULL DEFAULT '0',
  `failure_count` int NOT NULL DEFAULT '0',
  `sender_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `push_notifications`
--

INSERT INTO `push_notifications` (`id`, `title`, `body`, `image_url`, `target_type`, `success_count`, `failure_count`, `sender_id`, `created_at`, `updated_at`) VALUES
(26, 'i`m ahmed', 'test', NULL, 'all_users', 2, 2, 1, '2026-01-13 06:37:27', '2026-01-13 06:37:27');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(2, 'admin', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(3, 'manager', 'admins', '2025-10-07 03:18:29', '2025-11-04 04:48:24'),
(4, 'moderator', 'admins', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(5, 'vendor_owner', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(6, 'vendor_manager', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(7, 'vendor_staff', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(8, 'vendor_viewer', 'vendors', '2025-10-07 03:18:29', '2025-10-07 03:18:29'),
(10, 'mem 2', 'admins', '2025-10-24 07:25:09', '2025-10-24 07:25:09'),
(11, '13', 'admins', '2025-10-25 03:55:19', '2025-11-04 06:20:46');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(1, 2),
(6, 2),
(8, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(15, 2),
(6, 3),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(11, 3),
(12, 3),
(13, 3),
(14, 3),
(6, 4),
(11, 4),
(18, 5),
(19, 5),
(20, 5),
(21, 5),
(22, 5),
(23, 5),
(24, 5),
(25, 5),
(26, 5),
(27, 5),
(28, 5),
(29, 5),
(30, 5),
(18, 6),
(19, 6),
(20, 6),
(22, 6),
(23, 6),
(25, 6),
(29, 6),
(18, 7),
(22, 7),
(23, 7),
(18, 8),
(22, 8),
(29, 8),
(4, 10),
(7, 10),
(8, 10),
(10, 10),
(11, 10),
(15, 10),
(16, 10),
(1, 11),
(3, 11),
(5, 11),
(6, 11),
(8, 11),
(10, 11),
(11, 11),
(13, 11),
(15, 11);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('elCn70VxuAv3fe9R4DInBa7auZSpUlLYwdQZjBNM', NULL, '100.52.3.146', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/132.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaGlMRnJsaW5aUlA3Qmc0MDZnbmpTMG8yVVpRRzkyOGtrOTJqa1V4ZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vbWFpbm1hay5kZXZkaWdpdGFsdmliZXMuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1768861209),
('iocBsSlP0G4g1HLOHyRJLgxLrlaZcRTqwSgXUaUh', NULL, '34.138.123.126', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia2tzTkN0T01ja01md2w3TFZtcnQ2WnV2Y0RiVG9odjZhdjUyWlRrciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9tYWlubWFrLmRldmRpZ2l0YWx2aWJlcy5jb20iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1768840203),
('jp0c7Ka2x760lWS4tLvEGAI3JLOcaF08711EAXfU', NULL, '156.196.177.251', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlhDeWozQnU2MDhqSFBXeXhKakV6SjlQRDBuRjIwZk5VbnpEMnNFUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vbWFpbm1hay5kZXZkaWdpdGFsdmliZXMuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1769066503),
('OSg9HfjjifbYw9KvgV6YFcDhPmiDPKE2k2Hash5m', NULL, '162.142.125.36', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZEZtRGhuZ3dabk9UOGFQYjJsVFRHMDcyak5wWFBZdjEzZ3U3MFJFQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vbWFpbm1hay5kZXZkaWdpdGFsdmliZXMuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1769088255),
('PnicJ8P3Yr341HtMtRRushIw00sbSd2wDy5Tkk6W', NULL, '18.213.106.41', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibVpIRFZKWmYxTDBPaTd0ZFViMHlNcFZoSUhBR0taVE9jMHdpaHdPZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9tYWlubWFrLmRldmRpZ2l0YWx2aWJlcy5jb20iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1769173434),
('qX3zdt2FtGC63iLPcbAKvtduhUQDthHaH6przfTU', NULL, '13.229.135.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36 Assetnote/1.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZUZvRHdYRnNlY1ppRkJ2eWFHYjRTcVh0cXUzWGVFVlh5VTB3OHBRMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vbWFpbm1hay5kZXZkaWdpdGFsdmliZXMuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1768845283),
('ssrFbXJAiP7juH1CbeIwskFa03o2GmC24sLOlr5l', NULL, '3.93.174.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36 Assetnote/1.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWUJwOGg0bkxxRFljaXdhTjh6UjUxMExqbENzM0QzR1hIZkdja2llcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vbWFpbm1hay5kZXZkaWdpdGFsdmliZXMuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1768928607),
('UaarC0VYv7tY4JKzq4TL6uqlXpaUoxXLapqnbfkw', NULL, '204.76.203.25', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXRwZXFHVWswS0RJS0JZOG5PSHREbENSVENIZ25NM3lGZWUybFdPRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vbWFpbm1hay5kZXZkaWdpdGFsdmliZXMuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1768877760),
('Ups7XBx5UyzVA03t2MJLdbrTG2ArQmfQyFvENxJm', NULL, '195.178.110.132', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:133.0) Gecko/20100101 Firefox/133.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZkF0Z0oyY2NzYUNTc050Y3EyN21UbFI0WXd5Y21wVVlUcVROc0tyRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vbWFpbm1hay5kZXZkaWdpdGFsdmliZXMuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1768847958),
('Ym96SE1S3dYVdyKc9uZ3hu5wXnrJyiJL54nwhY5O', NULL, '167.94.138.199', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUVFGRzJac2ZyZEdzeUN5NUZjZFRnb2FtR2xKSzZ6VmF6a20wOU9rOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9tYWlubWFrLmRldmRpZ2l0YWx2aWJlcy5jb20iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1769085564);

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` bigint UNSIGNED NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_ar` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_note` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `fcm_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'karim', 'karim@gmail.com', NULL, '$2y$12$FiRAQfEJ9Kw0PudH08tUAuQXIXzTXWQsx1mWc1ADUuPVDFuFRzyru', NULL, NULL, '2025-10-28 08:49:45', '2025-10-28 08:49:45', NULL),
(2, 'ahmed gamal', 'agemy49@gmail.com', NULL, '$2y$12$5Y7WpStkwK2CRdE9LUPK1.l8RdfKS/Lsxs/i75omeQz9TsJ0f1hUq', NULL, NULL, '2025-10-28 09:04:24', '2025-10-28 09:04:24', NULL),
(3, 'aya', 'essam@digitalvibesmarketing.com', NULL, '$2y$12$E4IMSsZiLhBG0Ei81LqWm.HQeaVyHfSfnPI3VLvg2MB0FDR5GYGTa', NULL, 'd6d1rsQMRASKYE3Q7OtTjn:APA91bE2Ub8XbAMUQSXbkMYXxpPrLGsK8E1LKG1Q0iy_HShr2dMO4_cgErnYxG_l9hA5v_l7gtPQQYlOOyc8Yf6qze3ZiZmAwau8DrW8AcJcUYAG3YNN__M', '2025-10-31 05:48:17', '2026-01-20 06:48:05', NULL),
(4, 'karim', 'karim1@gmail.com', NULL, '$2y$12$X265t/ZJv3K5EfIRIgCzGecoHBfbmBlzMS/KyxWisS/WUqH43eEpy', NULL, NULL, '2025-11-12 04:03:14', '2025-11-12 04:03:14', NULL),
(5, 'test', 'arafaaya289@gmail.com', NULL, '$2y$12$16egvWsz/94Kz48l0lyG.eQDOPSJr4t07C6GDH0x5oP.RFZ3/oiIC', NULL, NULL, '2025-11-14 08:42:11', '2025-11-14 08:42:11', NULL),
(6, 'ahmed gamal', 'agemy@gmail.com', NULL, '$2y$12$Mmd6M8mWMxX20qFc/dlvHe8uMifgpEcPxoQ8hK7rR.xz7DqM0R22q', NULL, 'elY0Xip0Sq6DYt32fWLgRV:APA91bFAQybj3nHKLAOpxjzzMCJyjtrf8JudTA5SB2tWA-xdXyzV8xOPnLxBOfclqbM3laYOwTevqAtt_IEsoox2DzOME7J1xr-PprJNsF5q-wB7LCaf4uw', '2025-11-15 09:25:24', '2026-01-23 12:57:13', NULL),
(7, 'test', 'test@test.com', NULL, '$2y$12$aD8mKJrdOmGDDa1F5Vvg9.YIQuRG91pbwhXMhtagsbLQKClX5cp7m', NULL, NULL, '2025-11-15 09:54:51', '2025-11-15 09:54:51', NULL),
(8, 'karim', 'dev.karim12@gmail.com', NULL, '$2y$12$FiRAQfEJ9Kw0PudH08tUAuQXIXzTXWQsx1mWc1ADUuPVDFuFRzyru', NULL, NULL, '2025-11-17 06:31:19', '2025-11-17 06:31:19', NULL),
(9, 'ahmed', 'a@a.com', NULL, '$2y$12$I0.r8zTro3V6Nq5j7m6HLuSlB/3bzH2H/kp4VSj3/7r.jQtOZOZ6a', NULL, NULL, '2025-11-17 08:45:01', '2025-11-17 08:45:01', NULL),
(10, 'ahmedjimy', 'aa@gmail.com', NULL, '$2y$12$WndqcdxtZkFVCYjN5QqdyuUQ0EqtyKJAJMVDmlXizB6WxY0WIYZ12', NULL, NULL, '2025-11-17 08:46:39', '2025-11-17 08:46:39', NULL),
(11, 'test', 'rafaaya289@gmail.com', NULL, '$2y$12$RmDAp/nXZpqbrUbr7LTcUOGcdkU4dV7nzt7d11xUoFICs04o.T5x2', NULL, NULL, '2025-11-17 09:24:50', '2025-11-17 09:24:50', NULL),
(12, 'test', 'afaaya289@gmail.com', NULL, '$2y$12$EcF6ixMh9R2sq/V7L.yXJurYPGH.wvRrGkGE5TgpQx3xjaALrQWcK', NULL, NULL, '2025-11-17 09:27:31', '2025-11-17 09:27:31', NULL),
(13, 'Ahmedjimy', 'ahmed@gmail.com', NULL, '$2y$12$WVh8L21b9Frao8AMF3TIR.7jxms5edFql/IOVbxrASWFutefuZYCC', NULL, NULL, '2025-11-17 10:39:13', '2025-11-17 10:39:13', NULL),
(14, 'tt', 'aya@gmail.com', NULL, '$2y$12$zKCfXxyXdt43VrRpae8zcOJmtQ5Bs4hbPgneHJgWUinjnBaiflQb2', NULL, NULL, '2025-11-18 06:12:35', '2025-11-18 06:12:35', NULL),
(15, 'at', 'aya11@gmail.com', NULL, '$2y$12$wieZvDmD1JU62KmkY979PeFrxmHyJE1uV6zVfgKYgmEW3tZk/yViG', NULL, NULL, '2025-11-18 08:15:04', '2025-11-18 08:15:04', NULL),
(16, 'aya', 'aya12@gmail.com', NULL, '$2y$12$yDCp4xy.xPuEn2ZX20RHmu.a88oq.I4tDiN4tjXbZ/6d5Sw5bghsW', NULL, NULL, '2025-11-18 09:02:07', '2025-11-18 09:02:07', NULL),
(17, 'yy', 'aya1@gmail.com', NULL, '$2y$12$TxGWiytE8mpbQXcwNNaa0uxL6oe0mKOXr9S9qTrnffMYiRBpXdKo6', NULL, NULL, '2025-11-19 06:12:24', '2025-11-19 06:12:24', NULL),
(18, 'pp', 'ay@gmail.com', NULL, '$2y$12$1yjKMbvQYzUpuPQi.t/rU.yUOzwcolngFvr/hXqW503sSKkoW3Nze', NULL, NULL, '2025-11-19 06:32:06', '2025-11-19 06:32:06', NULL),
(19, 'uu', 'arafaaya@gmail.com', NULL, '$2y$12$o4GkNmhpt5JJY8d5DXgbremw8S8KfTra3RcqqXqxbADQBscQ6wDwu', NULL, NULL, '2025-11-19 06:35:49', '2025-11-19 06:35:49', NULL),
(20, 'yy', 'arafaa@gmail.com', NULL, '$2y$12$8lI63wvsWROlWjE3DEUH3OCD4l6iRWzwM/g/bzid7KY9vAUc62QzO', NULL, NULL, '2025-11-19 06:42:18', '2025-11-19 06:42:18', NULL),
(21, 'asasasasasasasas', 'ag@gmail.com', NULL, '$2y$12$5HpatvNZGP8DtIG1wYBqBe8QdupxU4tKYarc33cDjf8jw5.zm4o.6', NULL, NULL, '2025-11-19 07:48:35', '2025-11-19 07:48:35', NULL),
(22, 'asdfsadf', 'a1@gmail.com', NULL, '$2y$12$FXmuJSO0i10TcGo0rA9hPecta7Q8pVFsA/uBYRgW6/v6ZmA9VGBYm', NULL, NULL, '2025-11-19 07:52:54', '2025-11-19 07:52:54', NULL),
(23, 'www', 'w@w.com', NULL, '$2y$12$UOxxhN/dfwARdOpa.hRtC.FW2.YFFFBj5bNPEX6gvixWt57z/cZ0S', NULL, NULL, '2025-11-19 13:39:49', '2025-11-19 13:39:49', NULL),
(24, 'zxcvzxcvxczv', 'qq@gmail.com', NULL, '$2y$12$9Ej47V/wu4BrWn4590ybE.FSXLYLqfD8BWusE5CCKHiieB3i9IiDq', NULL, NULL, '2025-11-19 13:44:03', '2025-11-19 13:44:03', NULL),
(25, 'asadf', 'as@as.com', NULL, '$2y$12$seERk4.XfvXr2o0hWp08TeJPqW15ozhN.HIpXGJDaXYyo1gCrbVxy', NULL, NULL, '2025-11-19 13:45:35', '2025-11-19 13:45:35', NULL),
(26, 'zxzxc', 'a3@gmail.com', NULL, '$2y$12$bHXnoUyMvM.5bEJp/Gv3qO4npfEg9w1vYKVsk8cO5KsL51JjEniNa', NULL, NULL, '2025-11-19 13:58:20', '2025-11-19 13:58:20', NULL),
(27, 'asdasd', 'agemy1@gmail.com', NULL, '$2y$12$1opuK9veRyiFin.x5ZH1I.2x88RhHNU5IXlY6tkJI.1TFgEliyZoS', NULL, NULL, '2025-11-20 06:17:41', '2025-11-20 06:17:41', NULL),
(28, 'qwew', 'dd@gmail.com', NULL, '$2y$12$ByzmruwWmvYdIT0XyQs3ZOGjrXVIR6AAeE6S3K1dma3gOvOKhxPTS', NULL, NULL, '2025-11-20 06:40:53', '2025-11-20 06:40:53', NULL),
(29, 'asdasfgdf', 'age3@gmail.com', NULL, '$2y$12$iMvGTkfpswDQE8ngaiixQO/wxJIDSco7eTnfCfrkDdzEfYZvEfJ2y', NULL, NULL, '2025-11-20 06:54:07', '2025-11-20 06:54:07', NULL),
(30, 'asdasdfdgdg', 'ag4@gmail.com', NULL, '$2y$12$E4v8r5aoKINXmwJj.0nsT.XmB433HJToB9Vl5xRs2Sl2gVeLkhJMu', NULL, NULL, '2025-11-20 06:55:53', '2025-11-20 06:55:53', NULL),
(31, 'fasasdfasdfasdf', 'ag5@gmail.com', NULL, '$2y$12$b5rDewwRKqMOnTKsJwK/IOCNZG3IiTN2P/HthNi0niWkDntWsMRyu', NULL, NULL, '2025-11-20 07:27:52', '2025-11-20 07:27:52', NULL),
(32, 'safasdfasdf', 'asdfsdf@asdasf.com', NULL, '$2y$12$KwseeokCLjR/zBZu.8L5p.ml//O4FyHkoViK4RliPsM9gM3HeSLg.', NULL, NULL, '2025-11-20 07:46:04', '2025-11-20 07:46:04', NULL),
(33, 'dsfgdsfgdsfg', 'dfgsdfgsdfg@sdfdg.com', NULL, '$2y$12$w9s0lxJCvJobrMowjXS59uUgl2Wjii6GMH75nNcB4VesMag.Dp3im', NULL, NULL, '2025-11-20 07:50:01', '2025-11-20 07:50:01', NULL),
(34, 'asdfasdfsdfa', 'a44@gmail.com', NULL, '$2y$12$4qQNkyWJfJltjnWFEXYxwu5Q8Ys0thtB3tI1OaDR1521B7dHJ7Yqy', NULL, NULL, '2025-11-20 08:06:02', '2025-11-20 08:06:02', NULL),
(35, 'asdasdfvxc', 'a22@gmail.com', NULL, '$2y$12$b5b.6FgbTPCvjb.srsmqVukoy4FWV1K7drPFA6EoLmuqwRSmI0oRO', NULL, NULL, '2025-11-20 08:10:53', '2025-11-20 08:10:53', NULL),
(36, 'asdfasdfasf', 'ass@ass.com', NULL, '$2y$12$Cabbn2CvqC2z0IBqpGPEoONYUcSGnyRt0ZebvdOgnCllp7fpQRZPC', NULL, NULL, '2025-11-20 08:13:10', '2025-11-20 08:13:10', NULL),
(37, 'asdfasdfasdfg', 'ee@ee.com', NULL, '$2y$12$Ee9s5ZMLbPgLRT9ZJB0/XeCt2vsW4UVwrlU3F5GvRqezerOwmXjTa', NULL, NULL, '2025-11-20 08:14:49', '2025-11-20 08:14:49', NULL),
(38, 'asdfsadfsadfsfg', 'ff@gmail.com', NULL, '$2y$12$VBdPP7162WC2VE7zR9cMRutzS6CaJNJwY3YmG5btSGvGdxHEcN27y', NULL, NULL, '2025-11-20 08:21:57', '2025-11-20 08:21:57', NULL),
(39, 'asdfasdfasdfdfg', 'dd2@gmail.com', NULL, '$2y$12$f0IPhwPTRhMsQIE4EsfsauiJqGl36VrXFPFigf1R.OIASQZblADiC', NULL, NULL, '2025-11-20 08:47:21', '2025-11-20 08:47:21', NULL),
(40, 'asdfasgfhfg', 'vv@gmail.com', NULL, '$2y$12$Lu9r9/8Eo9GB/2dTFahWE.yoS75Y9dgMRb0WBvfKUpASvSxl3Qfbq', NULL, NULL, '2025-11-20 08:52:13', '2025-11-20 08:52:13', NULL),
(41, 'sadafsdgfhg', 'vv3@gmail.com', NULL, '$2y$12$u3EkdBPw586qpVXYLuMoY.8469fBCZIzmhR0JTaUJ6JoZMRCuyRKW', NULL, NULL, '2025-11-20 08:57:46', '2025-11-20 08:57:46', NULL),
(42, 'asdfasdfasdfa', 'aa2@gmail.com', NULL, '$2y$12$vRADuyrfaWa8XotBbA/MKuw415Jfo7AvHOVX3Ev1ou7wyGhObSHcq', NULL, NULL, '2025-11-20 10:45:00', '2025-11-20 10:45:00', NULL),
(43, 'karim', 'karim12@gmail.com', NULL, '$2y$12$0nYhdYfN23i8b5Q9NQrBE.prCWqEx.08V3hCjoxejeDb/WrP3KKuO', NULL, NULL, '2025-11-20 11:17:06', '2025-11-20 11:17:06', NULL),
(44, 'fasfasf', 'afsafsa@gai.com', NULL, '$2y$12$rGTypcoNzDHCrGp5wMXD6ucxR2NLBUwEMy8.UiAEs44iEA/PXb0hO', NULL, NULL, '2025-11-20 13:45:53', '2025-11-20 13:45:53', NULL),
(45, 'sfaasf', 'abdolgabr@gmail.com', NULL, '$2y$12$ud3iOBUhVETUbIJVapZ/7eNyaTlFLky6lRa2oktOBeHMu1wBS2gUO', NULL, NULL, '2025-11-20 13:50:11', '2025-11-20 13:50:11', NULL),
(46, 'asdfasdfasdf', 'aaa@gmail.com', NULL, '$2y$12$yuq.SknFiuDas8oj.zk9suld1aAs2a1Y4SqOzJHaTILX1N2IGA7o2', NULL, NULL, '2025-11-20 13:57:39', '2025-11-20 13:57:39', NULL),
(47, 'asdasfsdfasdf', 'aaaa1@gmail.com', NULL, '$2y$12$knwNHeeJnsiY0Sb1E4F6JO0AwK0Cr0tkQuwEJ4Uab.FgYkqsLBYYq', NULL, NULL, '2025-11-20 14:05:49', '2025-11-20 14:05:49', NULL),
(48, 'dsfgdfgcvb', 'sssss@gmail.com', NULL, '$2y$12$Dd3T6NeVQwCqwOV3D.caIO0JQZ8EOKlVbNCjD/scRiqTSCYOrW18i', NULL, NULL, '2025-11-20 14:07:24', '2025-11-20 14:07:24', NULL),
(49, 'test', 'ayaaa@gmail.com', NULL, '$2y$12$ju4UySlVC80mw92DbuROPeHf60IEhX9x73N.vjXiA5K3EJqdLudzq', NULL, NULL, '2025-12-09 07:10:47', '2025-12-09 07:10:47', NULL),
(50, 'ttest', 'aya.3rafa@gmail.com', NULL, '$2y$12$3simc/deUPS2YbV/.3TufuTtj6e0WR5zGIH3ogMoXk7doTzo/37a2', NULL, NULL, '2025-12-10 08:06:30', '2025-12-10 08:06:30', NULL),
(51, 'fhgcc', 'aya.3rafa11@gmail.com', NULL, '$2y$12$.zktASxRPrb200csSJs8Suj68woI07j5MkKp410ugr8Wtwo0fwkJG', NULL, NULL, '2025-12-10 08:49:39', '2025-12-10 08:49:39', NULL),
(52, 'اتتت', 'aya.3raf@gmail.com', NULL, '$2y$12$6enbC9U899Smlpuf4lpUG.xKQNYhTUcFunXhVvEen3zTfP6snBsNa', NULL, NULL, '2025-12-15 06:52:10', '2025-12-15 06:52:10', NULL),
(53, 'aya', 'essa@digitalvibesmarketing.com', NULL, '$2y$12$R2FTeZl/Rp1easqKxy2JWO6FTKuQcxBB/jorORIy37Fma417fimlu', NULL, NULL, '2025-12-19 08:52:56', '2025-12-19 08:52:56', NULL),
(54, 'cvc', 'ess@digitalvibesmarketing.com', NULL, '$2y$12$lomVb/4YQGedG9TqOqIhIeEQ4AFD2ETfE9gopbKbQ84rBH0xMYTFK', NULL, NULL, '2025-12-19 08:56:19', '2026-01-02 07:06:09', '2026-01-02 07:06:09'),
(55, 'ahmed gamal', 'agemy2@gmail.com', NULL, '$2y$12$VfIiTECrFEtZ2nWkKu4W3.NrsiiRZPdpDPRPCxl4QfkuyQLSy2bQK', NULL, NULL, '2025-12-20 12:19:41', '2025-12-20 12:19:41', NULL),
(56, 'no if gvvg', 'arafaaya89@gmail.com', NULL, '$2y$12$fMBCDnmYrCXqdZM0cFVKi.P2StXmJ00F6GT0urCJtH0qw58DZtLmq', NULL, NULL, '2025-12-22 06:54:39', '2025-12-22 06:54:39', NULL),
(57, 'tessstzz', 'tamem.qbdallah11@gmail.com', NULL, '$2y$12$H.JcBMRt96iBHMRiGlVh2O0NyrpfhMePqrrAUkVGnOz7v0Rno8QFW', NULL, NULL, '2026-01-01 08:55:31', '2026-01-02 07:05:57', '2026-01-02 07:05:57'),
(58, 'Regular User', 'dev.karim@gmail.com', NULL, '$2y$12$hm6jZELaFQOY9cServxXCuVFDmcmuti9OD9GHeGyio04eFUDAsd4e', NULL, 'caRduRUTlVcteU6AtOEJNw:APA91bF7c2mAeFP7_JmXg1qL_CHb8Vihw9qxMIIubVPFIeOuDKNecJ_VfilfAH3iWh4wIJGHHYM9g-dUn_un4S7wlOsmtFJx200q2hxD6VCFT86EBRl2Etg', '2026-01-02 07:08:38', '2026-01-02 07:08:38', NULL),
(59, 'Alaa Ali Farwi', 'alaafarwi@gmail.com', NULL, '$2y$12$7u5IprLNtfqBfuHZPovstufhCImd2MPts.lIKozX1I7YinX6ar3WO', NULL, NULL, '2026-01-06 11:05:22', '2026-01-06 11:05:22', NULL),
(60, 'ghh jhvv', 'aya.3ra11@gmail.com', NULL, '$2y$12$HyAZz6TDZ7jJFGu0tWnvKuyRWE8XRGrK1dZBmOBbpSRy2e76XAYGO', NULL, NULL, '2026-01-07 09:27:25', '2026-01-07 09:27:25', NULL),
(61, 'ahmed', 'agemy4@gmail.com', NULL, '$2y$12$TSfDSZOUpmmLdgDukr2y2ezorKEtJZSHD7DDnOGzd0pCfFiNzHL3.', NULL, NULL, '2026-01-07 09:33:50', '2026-01-07 09:34:45', '2026-01-07 09:34:45'),
(62, 'ahmed', 'a@a8.com', NULL, '$2y$12$Qte8XJapd5rYsyprd9/QJuhXETsAQpIoZeAu8EURuq1xs7c9sPhp6', NULL, NULL, '2026-01-07 09:39:49', '2026-01-07 09:40:03', '2026-01-07 09:40:03'),
(63, 'ahmed', 'agemy9@gmail.com', NULL, '$2y$12$l/Y4rtQCHDzyXsTJj75r6e5Nrr6XjO2EUEvIF2qQB9NQ/ExJ8Xewe', NULL, 'dldHrVv1Sea0k-B8OqK7WB:APA91bF0nsxXQX0Atu-P5zPUAYHRSsEOJwvAgzF8HATQZnhhIZvNEYVCOHUstqINRJ3HFHp_6qeOeoFn1JYhfy7noo8peQnJhfmCFeHNs3CAW4PM0E8E5UE', '2026-01-07 12:52:12', '2026-01-12 08:14:52', NULL),
(64, 'Ahmed Shaltout', 'accshaltout@gmail.com', NULL, '$2y$12$JF1k9fKoHtDeV.BVv0bLk.rmjSnEKuPpio77tyd54j2VEOow/fRyG', NULL, NULL, '2026-01-07 13:23:13', '2026-01-07 13:23:13', NULL),
(65, 'mohamed', 'nasrmohamed842@gmail.com', NULL, '$2y$12$n1Ik/gja2aFEKM4EA7oaJeEwsgw2AwF329Rw5.2kY.pKdsFHqeVoe', NULL, NULL, '2026-01-07 13:46:09', '2026-01-07 13:46:09', NULL),
(66, 'aaaaaaas', 'adolp.p24g38b.7gdg@icloud.com', NULL, '$2y$12$vdFcxr8QnZCcF7FJGhwl7.484jW2WUSeaES4MVK3GWEmE4eOWdXLO', NULL, NULL, '2026-01-09 06:12:26', '2026-01-09 06:15:09', '2026-01-09 06:15:09'),
(67, 'hhh', 'e@digitalvibesmarketing.com', NULL, '$2y$12$IUrtDxw33aarsCbw3SZmleWOpsL8W6jDwCRkRM50wB.G4DHD5efRC', NULL, NULL, '2026-01-13 09:49:32', '2026-01-13 09:49:32', NULL),
(68, 'SamiSa', 'yamen-h@outlook.com', NULL, '$2y$12$.05kUpqmuzHkmgPNJVuWHu4i5Ok3kikKXmgHWCevpofPDvFYUF8se', NULL, NULL, '2026-01-18 19:40:22', '2026-01-18 19:40:22', NULL),
(69, 'tester', 'arafaaya2289@gmail.com', NULL, '$2y$12$qX3bF32VsRkBpldNCWJm..wkiTOkgXrIMrpyAZojNNF8494ypccZi', NULL, NULL, '2026-01-23 11:12:09', '2026-01-23 11:12:09', NULL),
(70, 'tester', 'arafaaya22289@gmail.com', NULL, '$2y$12$4wxBIQtW3Y08dcGjG7P7VeLC68wpSWR8blhmMx7y74oZNzLGIZDOq', NULL, NULL, '2026-01-23 11:14:04', '2026-01-23 11:14:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `address_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_type` enum('villa','apartment','office') COLLATE utf8mb4_unicode_ci NOT NULL,
  `building_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apartment_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `floor_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `landmark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `address_name`, `address_type`, `building_name`, `apartment_number`, `floor_number`, `street_name`, `landmark`, `phone`, `latitude`, `longitude`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES
(7, 1, 'My Home', 'apartment', 'Tower A', '405', '4', 'Tahrir Street', 'Near Cairo Tower', '+201234567890', 30.04441960, 31.23571160, 0, '2025-11-03 03:54:35', '2025-11-20 10:48:14', NULL),
(8, 1, 'tanta', 'apartment', '11', '40', '4', 'test for tanta', 'mosque', '0123456789', 30.78650818, 31.00037500, 0, '2025-11-03 04:27:44', '2025-11-20 10:48:14', NULL),
(11, 1, 'sdfsadfsdf', 'apartment', '20', '40', '20', 'xdffdf', 'dfgdsgdsfg', '0123456789', 30.78858657, 31.00076728, 0, '2025-11-03 06:22:13', '2025-11-20 10:48:14', NULL),
(12, 1, 'wrwrwrdsfsaf', 'villa', '10', '30', '20', 'asfsdfasdfsadfs', 'afsafsfsaf', '0123456789', 30.78757532, 31.00044038, 0, '2025-11-03 06:27:39', '2025-11-20 10:48:14', NULL),
(15, 3, 'hadayek el Haram', 'office', '89', '89', '99', 'gbb', 'fgg', '01895888', 29.95156975, 31.10981952, 0, '2025-11-03 08:06:01', '2026-01-23 07:01:15', NULL),
(16, 1, 'lllllll', 'villa', '10', '20', '30', 'gfhjfghjfg', 'fghjfgjgfhjgfhj', '0123456789', 30.78650818, 31.00037500, 0, '2025-11-03 09:31:43', '2025-11-20 10:48:14', NULL),
(18, 4, 'My Home', 'apartment', 'Tower A', '405', '4', 'Tahrir Street', 'Near Cairo Tower', '+201234567890', 30.04441960, 31.23571160, 1, '2025-11-12 04:23:10', '2025-11-12 04:23:10', NULL),
(19, 3, 'home', 'villa', '12', '26', '5', 'dreem Park', 'dreem', '1065361362', 29.96539772, 31.06654845, 0, '2025-11-14 08:38:37', '2026-01-23 07:01:15', NULL),
(20, 5, 'elhadayek', 'villa', '200', '26', '4', 'aswak eltarek', 'خفرع', '01065361362', 29.96668764, 31.10395018, 0, '2025-11-14 08:43:26', '2025-11-15 09:22:48', NULL),
(21, 3, 'ee', 'apartment', '121', '8', '4', 'cvg', 'cgh', '010658526888', 30.76631003, 31.04147986, 0, '2025-11-15 09:18:20', '2026-01-23 07:01:15', NULL),
(22, 5, 'tanta', 'office', '12', '08', '85', 'ccvvv', 'cghhv', '0109856856', 30.78667206, 31.00111328, 0, '2025-11-15 09:20:05', '2025-11-15 09:22:48', NULL),
(23, 5, 'الخان', 'villa', '86', '86', '88', 'ةاةى', 'رةةت', '0188986698', 30.04912724, 31.26152694, 1, '2025-11-15 09:22:48', '2025-11-15 09:22:48', NULL),
(25, 7, 'dfsfsdf', 'villa', '12', '14', '13', 'ttttttt', 'tttteett', '01120687417', 30.78650818, 31.00037500, 1, '2025-11-15 09:55:49', '2025-11-15 09:55:49', NULL),
(26, 8, 'work', 'office', '20', '2', '4', 'nhda', 'zhran', '01207048631', 29.95997441, 31.26012716, 1, '2025-11-17 06:35:47', '2025-11-17 06:35:47', NULL),
(27, 3, 'ةوووة', 'villa', '856', '89', '88', 'ىاا', 'ىاا', '01568568555', 29.96668241, 31.10395052, 0, '2025-11-17 07:27:43', '2026-01-23 07:01:15', NULL),
(28, 10, 'dsfgdsfgdsfgsdfg', 'villa', '12', '14', '13', 'dfgdsfgsdfg', 'dgasdsdasdfdgfdg', '01234567899', 37.42199819, -122.08399985, 1, '2025-11-17 08:47:19', '2025-11-17 08:47:19', NULL),
(29, 11, 'cvv', 'villa', '85', '86', '885', 'cgcf', 'cgv', '0158668858', 29.96668241, 31.10394951, 1, '2025-11-17 09:25:49', '2025-11-17 09:25:49', NULL),
(30, 14, 'vv', 'office', '123', '55', '8', 'vvg', 'fgg', '01065523555', 30.01466039, 31.13305550, 1, '2025-11-18 06:13:08', '2025-11-18 06:13:08', NULL),
(31, 6, 'gfdhdfhfg', 'villa', '12', '14', '13', 'rweertewrt', 'sdgdfgsdfg', '01234567899', 37.42199819, -122.08399985, 0, '2025-11-18 07:34:22', '2025-12-20 12:03:18', NULL),
(32, 15, 'vhh', 'villa', '22', '9', '55', 'vv', 'gg', '01555555888', 29.96677681, 31.10389821, 1, '2025-11-18 08:15:29', '2025-11-18 08:15:29', NULL),
(33, 16, 'dggv', 'villa', '55', '99', '66', 'cgv', 'vbv', '0158858868', 29.96667921, 31.10388748, 1, '2025-11-18 09:02:39', '2025-11-18 09:02:39', NULL),
(34, 6, 'dfgdfgsdfasdf', 'villa', '14', '12', '13', 'qwerqweteertyrety', 'fdghfghdsfasfas', '01234567899', 37.42199819, -122.08399985, 0, '2025-11-18 10:36:48', '2025-12-20 12:03:18', NULL),
(35, 17, 'bnn', 'villa', '99', '5', '8', 'gg', 'ggg', '0157898', 29.96668648, 31.10394515, 1, '2025-11-19 06:12:44', '2025-11-19 06:12:44', NULL),
(36, 17, 'ghh', 'villa', '866', '66', '55', 'bb', 'vbb', '015588', 30.06650342, 31.11417376, 0, '2025-11-19 06:15:24', '2025-11-19 06:15:24', NULL),
(37, 18, 'vvg', 'villa', '633', '66', '55', 'vgg', 'ggg', '88555', 29.99424451, 31.11837309, 1, '2025-11-19 06:32:32', '2025-11-19 06:32:32', NULL),
(38, 19, 'bb', 'villa', '99', '99', '88', 'cvv', 'vvv', '08458', 29.99552099, 31.11797545, 1, '2025-11-19 06:36:13', '2025-11-19 06:36:13', NULL),
(39, 20, 'v', 'villa', '88', '88', '88', 'v', 'cv', '558588', 29.98793825, 31.14781804, 1, '2025-11-19 06:42:41', '2025-11-19 06:42:41', NULL),
(40, 21, 'sdfgdsfgdsfgsdfg', 'villa', '99', '99', '99', 'dfgdfgasfsd', 'sdfdghfhfgj', '01234567899', 37.42199819, -122.08399985, 1, '2025-11-19 07:49:08', '2025-11-19 07:49:08', NULL),
(41, 22, 'fghfhdfghfdgh', 'villa', '99', '99', '99', 'yuydrtstre', 'qwerwetewtrty', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 07:53:32', '2025-11-19 07:59:49', NULL),
(42, 22, 'sadfdfgdsfg', 'villa', '99', '99', '99', 'ghjgfdhfdgsdfgd', 'asdasasrewrtrty', '123456789', 37.42199819, -122.08399985, 1, '2025-11-19 07:59:49', '2025-11-19 07:59:49', NULL),
(43, 6, 'asdfsdfsadf', 'villa', '12', '12', '12', 'sadfasdfdfsgghfdh', 'fghsertweqwe', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 08:05:01', '2025-12-20 12:03:18', NULL),
(44, 6, 'fhfhfddsfdsfg', 'villa', '13', '13', '13', 'asfsdfasdf', 'wqerwqerwqer', '12345678', 37.42199819, -122.08399985, 0, '2025-11-19 08:07:52', '2025-12-20 12:03:18', NULL),
(45, 6, 'asderwqeqr', 'villa', '14', '14', '14', 'dfghdfgh', 'sdfgsdfgdsfg', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 08:13:10', '2025-12-20 12:03:18', NULL),
(46, 6, 'asdfsadfasf', 'villa', '13', '13', '13', 'safsdafasdf', 'dsfgdsfgdsfg', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 08:14:37', '2025-12-20 12:03:18', NULL),
(47, 6, 'asdfasdfasdf', 'villa', '15', '15', '15', 'dfgwerrqwewqer', 'qqtrtuytyutyuiy', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 08:19:11', '2025-12-20 12:03:18', NULL),
(48, 6, 'sfsdfsdgasf', 'villa', '15', '15', '15', 'erterterqwerrqwertr', 'ruytuttiuiyittry', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 08:20:42', '2025-12-20 12:03:18', NULL),
(49, 6, 'asdfasdfasdfgfgh', 'villa', '15', '15', '15', 'werqwerqertryretyrty', 'qwerwretyrtyerty', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 08:25:16', '2025-12-20 12:03:18', NULL),
(50, 6, 'dfgdhdfhdfghfsdg', 'villa', '15', '15', '15', 'wsrwerqwrqwer', 'errtyerwqerqwerertert', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 08:29:15', '2025-12-20 12:03:18', NULL),
(54, 6, 'asdfasdfasdf', 'villa', '44', '44', '44', 'afsdfawrew', 'asdfearwewq', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 08:48:01', '2025-12-20 12:03:18', NULL),
(55, 6, 'sdfasdfasdfasdf', 'villa', '55', '55', '55', 'sadfaserwear', 'asdfsadqreerwe', '123456798', 37.42199819, -122.08399985, 0, '2025-11-19 08:51:43', '2025-12-20 12:03:18', NULL),
(56, 6, 'sdfsdfgsdfgdfsg', 'villa', '13', '13', '13', 'fghfsgsdfgdfg', 'dsfgsdafdfghfggfjh', '1234566213', 37.42199819, -122.08399985, 0, '2025-11-19 10:21:48', '2025-12-20 12:03:18', NULL),
(57, 6, 'sdfsadfasdfsadf', 'villa', '11', '11', '11', 'sdafasdfsadfsadf', 'sdfdgdfhsdfasdf', '12312412534', 37.42199819, -122.08399985, 0, '2025-11-19 10:24:56', '2025-12-20 12:03:18', NULL),
(58, 6, 'asdfasdfasdffgfh', 'villa', '11', '11', '11', 'werqweqwewrweter', 'qwewerytruytyuuiuio', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 10:31:22', '2025-12-20 12:03:18', NULL),
(59, 6, 'asfsdfasdgdsgdgh', 'villa', '11', '11', '11', 'rqwerqweretyrty', 'hjjkghjgfhdfg', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 10:33:21', '2025-12-20 12:03:18', NULL),
(60, 6, 'asdfsadfsadfsadfsadf', 'villa', '11', '11', '11', 'asdfsadfasdfsadfasdf', 'dfgsdfgsdfghfjhgjfgjdfh', '123456798', 37.42199819, -122.08399985, 0, '2025-11-19 11:33:40', '2025-12-20 12:03:18', NULL),
(61, 6, 'sadfasdfsadfasdfsadf', 'villa', '11', '11', '11', 'asfsadfasdfsadfsadf', 'asdfsdafdfgdfhfgh', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 11:58:36', '2025-12-20 12:03:18', NULL),
(62, 6, 'dfsgdfgdfsg', 'villa', '11', '11', '11', 'sdfgdsfgdfgdfsg', 'dfgsdfgsdfgsdfgdfsg', '132456789', 37.42199819, -122.08399985, 0, '2025-11-19 12:22:04', '2025-12-20 12:03:18', NULL),
(63, 6, 'asdasdasdasdasdasd', 'villa', '11', '11', '11', 'adasdasdsfasdfgdfgdfgf', 'asdfasdfsdfgdhf', '13241313321312', 37.42199819, -122.08399985, 0, '2025-11-19 12:27:15', '2025-12-20 12:03:18', NULL),
(64, 6, 'sadfasdfasdfasdf', 'villa', '11', '11', '11', 'asdfasdfsdfgdsfg', 'asdfdsfgfdhdf', '123123123', 37.42199819, -122.08399985, 0, '2025-11-19 12:29:06', '2025-12-20 12:03:18', NULL),
(65, 6, 'sdasdasdasdasdasdasdfasdf', 'villa', '11', '11', '11', 'bnmbnmbvm', 'fghdfghgfjhgjkghjkhgk', '1321313213321', 37.42199819, -122.08399985, 0, '2025-11-19 12:31:39', '2025-12-20 12:03:18', NULL),
(66, 6, 'qweqweqwewretert', 'villa', '11', '11', '11', 'rweretyreuyfghdfsgsdf', 'fdghfgjghjfggsdfxcvcxbcvn', '1321321321231231213', 37.42199819, -122.08399985, 0, '2025-11-19 12:32:46', '2025-12-20 12:03:18', NULL),
(67, 6, 'asdfasdfasdfsd', 'villa', '11', '11', '11', 'hsdfsadgdsfgfdghdfh', 'sdfgsdfhjfgjhjsfg', '1313123132131321', 37.42199819, -122.08399985, 0, '2025-11-19 12:38:00', '2025-12-20 12:03:18', NULL),
(68, 6, 'asdfasdfasdfasdfsdafs', 'villa', '22', '22', '333', 'asdfsadfsadfsadfasdfasdfsadfasdf', 'sadfsadfasdfsadfsadfsadfsadfsadf', '131313231231332321', 37.42199819, -122.08399985, 0, '2025-11-19 12:44:45', '2025-12-20 12:03:18', NULL),
(69, 6, 'asdfsdfgdfg', 'villa', '11', '11', '11', 'fdgsdfsadfdgdfgsdf', 'sdfsdgdhfhsdfasfsfgfgdh', '123456789', 37.42199819, -122.08399985, 0, '2025-11-19 12:58:00', '2025-12-20 12:03:18', NULL),
(70, 6, 'asfasdfdasffsadfasdfsdfasdfsdf', 'villa', '11', '11', '11', 'weqeqwewqerwertrtrwetrtyery', 'sadfsadfxzcvxcvcvbzx', '123123123123132', 37.42199819, -122.08399985, 0, '2025-11-19 13:38:43', '2025-12-20 12:03:18', NULL),
(71, 23, 'sdfsdfsdfsdfsdfsdfdfg', 'villa', '11', '11', '11', 'wqeqwewerasdfsdfxv', 'cxvbxcsdfwaer', '123123123123123', 37.42199819, -122.08399985, 1, '2025-11-19 13:40:29', '2025-11-19 13:40:29', NULL),
(72, 24, 'asdfasdfvxzcvxzcv', 'villa', '11', '11', '11', 'ewrwerqwetwerteyertuytu', 'qwerwqerewrtreytruuti', '123123123123123', 37.42199819, -122.08399985, 1, '2025-11-19 13:44:40', '2025-11-19 13:44:40', NULL),
(73, 25, 'sadfsadfdsfgfghfghsd', 'villa', '22', '22', '22', 'cvzxvzxcvzxzzxcvzxcv', 'zxcvxzcvsdafdsgcvbxc', '123123123', 37.42199819, -122.08399985, 1, '2025-11-19 13:46:09', '2025-11-19 13:46:09', NULL),
(74, 26, 'rwerweqwerqwe', 'villa', '22', '22', '22', 'qwewrweqtert', 'werewrtet', '132123132132', 37.42199819, -122.08399985, 1, '2025-11-19 13:58:44', '2025-11-19 13:58:44', NULL),
(75, 6, 'asdasdasd', 'villa', '11', '11', '11', 'bnfgdhfghfgh', 'dfghffgh', '123456789', 37.42199819, -122.08399985, 0, '2025-11-20 06:12:50', '2025-12-20 12:03:18', NULL),
(76, 27, 'asdfasdfsdafdg', 'villa', '22', '33', '33', 'sadfsadfsdfsddfg', 'dfgfsdasdasfdsfg', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 06:18:41', '2025-11-20 06:18:41', NULL),
(77, 28, 'qwwqwerqwer', 'villa', '22', '22', '22', 'tyufhfgfhghjfgdgsdf', 'rtytruytiyuighjfgh', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 06:41:27', '2025-11-20 06:41:27', NULL),
(78, 29, 'sdfsdgdfghgj', 'villa', '22', '22', '22', 'swrersedfdf', 'erwerfdsdf', '1234567899', 37.42659414, -122.07987964, 1, '2025-11-20 06:54:45', '2025-11-20 06:54:45', NULL),
(79, 30, 'ghfghfghdf', 'villa', '22', '22', '22', 'sdfwersdfsdf', 'wersdfsdfdrwete', '1234567899', 37.42449097, -122.07929123, 1, '2025-11-20 06:56:30', '2025-11-20 06:56:30', NULL),
(80, 31, 'asdffdghgfghsadf', 'villa', '11', '11', '11', 'asdfsadgdfgfghdfgasdfsdf', 'ghfasfasdfgdfghfgjgdfsgdf', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 07:28:25', '2025-11-20 07:28:25', NULL),
(81, 32, 'fdhfdghfghsdfgsdf', 'villa', '22', '22', '22', 'sdafasdsfgwerqrqwer', 'sdfsafweqrrtewrtwqe', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 07:46:45', '2025-11-20 07:46:45', NULL),
(82, 33, 'asdfsadfsadf', 'villa', '22', '22', '22', 'qweqwerwertedsfsda', 'sadfsadfwerqrwert', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 07:50:30', '2025-11-20 07:50:30', NULL),
(83, 34, 'asdasdfsdfwerwqer', 'villa', '33', '33', '33', 'qweqweweert', 'zxcxcvxczsadfsdf', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 08:07:01', '2025-11-20 08:07:01', NULL),
(84, 35, 'fdsgdfgsdfgdsfgsdfg', 'villa', '22', '22', '22', 'qweqweqweqwe', 'qweqweqweqwe', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 08:11:20', '2025-11-20 08:11:20', NULL),
(85, 36, 'dfasdfasdfasdf', 'villa', '22', '22', '22', 'eqweqweqwerwerweert', 'qweqweweretert', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 08:13:42', '2025-11-20 08:13:42', NULL),
(86, 37, 'asdasdasdasd', 'villa', '22', '22', '22', 'qweqwwerwrter', 'asdsczxvdasd', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 08:16:13', '2025-11-20 08:16:13', NULL),
(87, 38, 'sdfsdfgdsfgsdfgfghh', 'villa', '33', '33', '33', 'asdfasdfdfvxcvzxcvxcv', 'werfdsfgdfgcxvxcvxv', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 08:22:26', '2025-11-20 08:22:26', NULL),
(88, 39, 'hdfghfdsdfgdfg', 'villa', '22', '22', '22', 'rwrwetqewersfdfg', 'rwrwqerdfgdfgrty', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 08:47:55', '2025-11-20 08:47:55', NULL),
(89, 40, 'asdsffdfgfdhfghhj', 'villa', '22', '22', '22', 'zxczxvxdfwf', 'sdfsdffdbcvb', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 08:53:11', '2025-11-20 08:53:11', NULL),
(90, 41, 'adfsdfsdfgsdfg', 'villa', '22', '22', '22', 'sdfasdfzxcv', 'dsfgdfgxccbxcvb', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 08:58:31', '2025-11-20 08:58:31', NULL),
(91, 42, 'dsfgdsfgdsfgdsfgdfg', 'villa', '22', '22', '22', 'dsfgsdfgasfasfasgdg', 'dsgasfsfasfdgdfhj', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 10:45:43', '2025-11-20 10:45:43', NULL),
(92, 1, 'My Home', 'apartment', 'Tower A', '405', '4', 'Tahrir Street', 'Near Cairo Tower', '+201234567890', 30.04441960, 31.23571160, 1, '2025-11-20 10:48:14', '2025-11-20 10:48:14', NULL),
(93, 6, 'asdassdfasdf', 'villa', '22', '22', '22', 'eqwewqerasff', 'qwerwerfsadffgasdf', '1234567899', 37.42199819, -122.08399985, 0, '2025-11-20 11:15:51', '2025-12-20 12:03:18', NULL),
(94, 6, 'ttt', 'villa', '22', '22', '22', 'tttt', 'trtrtrtrt', '1234567899', 37.42199819, -122.08399985, 0, '2025-11-20 11:18:51', '2025-12-20 12:03:18', NULL),
(95, 6, 'werqwer', 'villa', '22', '22', '22', 'weqrwqerwqer', 'weqrwqerwqerwqer', '1234567899', 37.42199819, -122.08399985, 0, '2025-11-20 11:20:56', '2025-12-20 12:03:18', NULL),
(96, 6, 'werwewerwewer', 'villa', '22', '22', '22', 'qweqweqweqwe', 'werwerqweqweqwewer', '1234567899', 37.42199819, -122.08399985, 0, '2025-11-20 11:50:30', '2025-12-20 12:03:18', NULL),
(97, 6, 'fsdfaasdf', 'villa', '22', '22', '22', 'sdfadfasdfasdf', 'asdfasdfasdfasdfasdf', '1234567899', 37.42199819, -122.08399985, 0, '2025-11-20 12:05:39', '2025-12-20 12:03:18', NULL),
(98, 6, 'asdasdasd', 'villa', '22', '22', '22', 'qweqweqweqwe', 'qweasdafsdfasd', '1234567899', 37.42199819, -122.08399985, 0, '2025-11-20 12:38:59', '2025-12-20 12:03:18', NULL),
(99, 6, 'qwerqwerqwer', 'villa', '22', '22', '22', 'werwqer', 'qweqweqwe', '01234567899', 37.42199819, -122.08399985, 0, '2025-11-20 12:45:34', '2025-12-20 12:03:18', NULL),
(100, 6, 'asdfasdfsdaf', 'villa', '22', '22', '22', 'werqwerqwerqwe', 'asdfasdfasdf', '01234567899', 37.42199819, -122.08399985, 0, '2025-11-20 12:50:13', '2025-12-20 12:03:18', NULL),
(101, 6, ';km;km', 'apartment', '13', '31', '31', 'l[k[', '1313', '0131313431', 37.42199819, -122.08399985, 0, '2025-11-20 12:58:02', '2026-01-11 13:19:30', NULL),
(102, 6, 'zxczczxcv', 'villa', '22', '22', '22', 'wqerqwerwqer', 'wqersdfasdfasdf', '01234567899', 37.42199819, -122.08399985, 0, '2025-11-20 13:05:08', '2025-12-20 12:03:18', NULL),
(103, 6, 'sdfgsdfgsdfgdf', 'villa', '22', '22', '22', 'eqwqerqwerqwer', 'fassdfasdfweqrqwer', '01234567899', 37.42199819, -122.08399985, 0, '2025-11-20 13:08:27', '2025-12-20 12:03:18', NULL),
(104, 6, 'asdfsdafasdfasdf', 'villa', '22', '22', '22', 'eqweqweadsasf', 'eqwerqwersadf', '01234567899', 37.42199819, -122.08399985, 0, '2025-11-20 13:11:57', '2025-12-20 12:03:18', NULL),
(105, 6, 'qwerwqerqwerqwer', 'villa', '22', '22', '22', 'qwerwqeasdfw', 'erwerqwrasasdfsaf', '01234567899', 37.42199819, -122.08399985, 0, '2025-11-20 13:13:38', '2025-12-20 12:03:18', NULL),
(106, 6, 'asdfsdafasdf', 'villa', '22', '22', '22', 'aweqwerasdfsa', 'qwerwqerasdfasdf', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 13:14:51', '2025-12-20 12:03:17', NULL),
(107, 6, 'fsafasf', 'villa', '213', '2', '2', 'weq', 'dqwd', '21321412412', 37.42199819, -122.08399985, 0, '2025-11-20 13:33:41', '2025-12-20 12:03:18', NULL),
(108, 6, 'fasfasfas', 'villa', '12', '2', '2', 'saf', 'asfsfa', '124124124124', 37.42199819, -122.08399985, 0, '2025-11-20 13:40:55', '2025-12-20 12:03:18', NULL),
(109, 47, 'dsfgdsfgdsfgsdfg', 'villa', '22', '22', '22', 'sdafasdfwer', 'sadfsadfwerw', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 14:06:21', '2025-11-20 14:06:21', NULL),
(110, 48, 'sdfgsafasdffasdf', 'villa', '22', '22', '22', 'fdssadfzxvxcvb', 'werrtfdsgda', '1234567899', 37.42199819, -122.08399985, 1, '2025-11-20 14:07:49', '2025-11-20 14:07:49', NULL),
(111, 3, 'maddi', 'villa', '888', '99', '88', 'fthgj', 'vhbcg', '0158896988', 29.95914859, 31.25837568, 0, '2025-12-05 06:42:13', '2026-01-23 07:01:15', NULL),
(112, 6, 'office', 'office', 'sadfsadfsdaf', '20', NULL, 'sadfasdfsdaf', NULL, '0123456789', 37.27956753, -121.98130745, 0, '2025-12-08 13:16:42', '2025-12-20 12:03:18', NULL),
(113, 3, 'apartment', 'villa', 'ددددددالنادي', '1111', '15555', 'الللللنادي حدايق الاهرام', NULL, '112545691733333', 29.97044576, 31.09402735, 0, '2025-12-09 06:58:14', '2026-01-23 07:01:15', NULL),
(114, 49, 'apartment', 'apartment', 'البوابه العظيمه', '1', '1', 'طريق الواحات', NULL, '1125456917', 29.96689067, 31.11150496, 0, '2025-12-09 07:11:29', '2025-12-09 07:54:14', NULL),
(115, 49, 'office', 'office', 'صالة بيراميدز', '1', '1', 'gate 2', NULL, '010653613', 29.96887009, 31.12998169, 0, '2025-12-09 07:18:08', '2025-12-09 07:54:14', NULL),
(116, 49, 'apartment', 'apartment', 'فندق بيراميدز', '1', '2', 'الرمايه', NULL, '0106536136', 30.00053325, 31.11592859, 0, '2025-12-09 07:19:22', '2025-12-09 07:54:14', NULL),
(117, 49, 'apartment', 'apartment', 'لتتت', '2', '5', 'ةوو', NULL, '9999', 29.96672453, 31.10391833, 1, '2025-12-09 07:51:33', '2025-12-09 07:54:14', NULL),
(118, 50, 'apartment', 'apartment', 'vbb', '5', '9', 'gggg', NULL, '015868858', 29.96669490, 31.10382110, 1, '2025-12-10 08:06:55', '2025-12-10 08:06:55', NULL),
(119, 51, 'apartment', 'apartment', 'dgg', '1', '5', 'vhvg', NULL, '0106536136', 29.96362910, 31.10616468, 1, '2025-12-10 08:50:31', '2025-12-10 08:50:31', NULL),
(120, 6, 'villa', 'villa', 'sadfsadfsadfeeeeeeeeeeeeeee', '123', '123', 'addfsadfdsfgdfsgsda', NULL, '012345689', 29.74904135, 31.31589632, 0, '2025-12-10 09:41:38', '2025-12-20 12:03:18', NULL),
(121, 3, 'villa', 'office', 'ةةةت', '888', '889', 'ىاالىاال', NULL, '8568888885', 29.99180998, 31.12846892, 0, '2025-12-15 06:46:59', '2026-01-23 07:01:15', NULL),
(122, 52, 'apartment', 'apartment', 'chv', '55', '96', 'cghg', NULL, '569866', 29.96669955, 31.10411916, 1, '2025-12-15 06:52:45', '2025-12-15 06:52:45', NULL),
(123, 53, 'villa', 'villa', 'الفيروز', '2', '3', 'شارع الحرمين', NULL, '01065361362', 30.05083716, 31.12259924, 1, '2025-12-19 08:54:05', '2025-12-19 08:54:05', NULL),
(124, 53, 'villa', 'villa', 'الفيروز', '2', '3', 'شارع الحرمين', NULL, '01065361362', 30.05083716, 31.12259924, 0, '2025-12-19 08:54:05', '2025-12-19 08:54:05', NULL),
(125, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.96668270, 31.10385060, 1, '2025-12-19 08:56:49', '2025-12-19 08:56:49', NULL),
(126, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.96668270, 31.10385060, 0, '2025-12-19 08:57:06', '2025-12-19 08:57:06', NULL),
(127, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.96668270, 31.10385060, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(128, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.96668270, 31.10385060, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(129, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.96668270, 31.10385060, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(130, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.96668270, 31.10385060, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(131, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(132, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(133, 54, 'apartment', 'apartment', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(134, 54, 'office', 'office', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(135, 54, 'office', 'office', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(136, 54, 'apartment', 'apartment', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(137, 54, 'apartment', 'apartment', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(138, 54, 'apartment', 'apartment', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(139, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.96668270, 31.10385060, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(140, 54, 'villa', 'villa', 'fhg', '86', '96', 'gyy', NULL, '01065361362', 29.96668270, 31.10385060, 0, '2025-12-19 08:57:07', '2025-12-19 08:57:07', NULL),
(141, 54, 'apartment', 'apartment', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:08', '2025-12-19 08:57:08', NULL),
(142, 54, 'apartment', 'apartment', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:08', '2025-12-19 08:57:08', NULL),
(143, 54, 'apartment', 'apartment', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:08', '2025-12-19 08:57:08', NULL),
(144, 54, 'apartment', 'apartment', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:11', '2025-12-19 08:57:11', NULL),
(145, 54, 'apartment', 'apartment', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:11', '2025-12-19 08:57:11', NULL),
(146, 54, 'apartment', 'apartment', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:12', '2025-12-19 08:57:12', NULL),
(147, 54, 'villa', 'villa', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:12', '2025-12-19 08:57:12', NULL),
(148, 54, 'villa', 'villa', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:14', '2025-12-19 08:57:14', NULL),
(149, 54, 'villa', 'villa', 'fhgvhg', '86', '96', 'gyy', NULL, '01065361362', 29.97847070, 31.10709641, 0, '2025-12-19 08:57:14', '2025-12-19 08:57:14', NULL),
(150, 55, 'apartment', 'apartment', 'safasdfsadf', '1212', '3131', 'asdfrettwewafd', NULL, '01234567899', 37.42199819, -122.08399985, 0, '2025-12-20 12:20:18', '2025-12-22 13:14:41', NULL),
(151, 56, 'apartment', 'apartment', 'vvbv', '96', '966', 'vhvcc', NULL, '01065361362', 29.96467651, 31.10490169, 1, '2025-12-22 06:54:59', '2025-12-22 06:54:59', NULL),
(152, 55, 'apartment', 'apartment', 'testest', '12', '4', 'testtesttesttest', NULL, '01234567899', 37.42199819, -122.08399985, 1, '2025-12-22 12:59:15', '2025-12-22 13:14:41', NULL),
(153, 2, 'apartment', 'apartment', 'sadfsadf', '12', '122', 'asdsafdfsdgdfg', NULL, '01234567899', 37.42199819, -122.08399985, 1, '2025-12-27 09:13:02', '2025-12-27 09:13:02', NULL),
(154, 57, 'villa', 'villa', 'الفيروز', '26', '4', '200ه البوابه الاولى', NULL, '1125456917', 30.03007896, 31.11314680, 0, '2026-01-01 08:56:52', '2026-01-01 08:58:07', NULL),
(155, 57, 'apartment', 'apartment', 'الازهار', '22', '5', 'شارع الجيش أمام البنزينه', NULL, '01065361362', 29.97025320, 31.11599464, 1, '2026-01-01 08:57:58', '2026-01-01 08:58:07', NULL),
(156, 60, 'apartment', 'apartment', 'vn v', '56', '66', 'fgg hvc', NULL, '01065361362', 29.95741385, 31.10625755, 1, '2026-01-07 09:27:41', '2026-01-07 09:27:41', NULL),
(157, 60, 'apartment', 'apartment', 'vn v', '56', '66', 'fgg hvc', NULL, '01065361362', 29.95741385, 31.10625755, 0, '2026-01-07 09:27:42', '2026-01-07 09:27:42', NULL),
(158, 61, 'apartment', 'apartment', 'test1', '12', '133', 'testest', NULL, '0123456789', 37.42199819, -122.08399985, 1, '2026-01-07 09:34:24', '2026-01-07 09:34:24', NULL),
(159, 63, 'apartment', 'apartment', 'testtest', '12', '12', 'testets', NULL, '0123456789', 37.42199819, -122.08399985, 1, '2026-01-07 12:52:52', '2026-01-07 12:52:52', NULL),
(160, 66, 'apartment', 'apartment', 'aaaa', '3', NULL, 'main', NULL, '666449999', 29.95950007, 31.25890005, 1, '2026-01-09 06:12:49', '2026-01-09 06:12:49', NULL),
(161, 66, 'apartment', 'apartment', 'aaaa', '3', NULL, 'main', NULL, '666449999', 29.95950007, 31.25890005, 0, '2026-01-09 06:12:50', '2026-01-09 06:12:50', NULL),
(162, 66, 'apartment', 'apartment', 'aaaa', '3', NULL, 'main', NULL, '666449999', 29.95950007, 31.25890005, 0, '2026-01-09 06:12:51', '2026-01-09 06:12:51', NULL),
(163, 3, 'apartment', 'office', 'فندق بيراميدز', '22', '22', 'الهرم', NULL, '0109668688', 30.00060758, 31.11462235, 1, '2026-01-13 07:07:12', '2026-01-23 07:01:15', NULL),
(164, 3, 'villa', 'apartment', 'ةتوة', '55', NULL, 'ةنةة', NULL, '999850', 30.33023207, 31.02213643, 0, '2026-01-13 07:13:17', '2026-01-23 07:01:15', NULL),
(165, 3, 'villa', 'office', 'ةتوة', '55', NULL, 'ةنةة', NULL, '999850', 30.58480078, 30.96181355, 0, '2026-01-13 07:13:18', '2026-01-23 07:01:15', NULL),
(166, 3, 'office', 'office', 'ghv', '89', '9.', 'g vs', NULL, '88595', 29.98132143, 31.11935075, 0, '2026-01-13 07:22:52', '2026-01-23 07:01:15', NULL),
(167, 67, 'office', 'office', 'منشأه', '69', '99', 'انةةا', NULL, '96965886', 30.01639703, 31.14349935, 1, '2026-01-13 09:49:57', '2026-01-13 09:49:57', NULL),
(168, 70, 'villa', 'villa', 'fairouz', '5', '5', 'vv', NULL, '0106536136', 29.96660544, 31.10396393, 1, '2026-01-23 11:14:36', '2026-01-23 11:14:36', NULL),
(169, 70, 'villa', 'villa', 'fairouz', '5', '5', 'vv', NULL, '0106536136', 29.96660544, 31.10396393, 0, '2026-01-23 11:14:36', '2026-01-23 11:14:36', NULL),
(170, 70, 'apartment', 'apartment', 'karm', '88', '99', 'gg', NULL, '1125456917', 29.97455575, 31.11643955, 0, '2026-01-23 11:15:14', '2026-01-23 11:15:14', NULL),
(171, 70, 'apartment', 'apartment', 'karm', '88', '99', 'gg', NULL, '1125456917', 29.97455575, 31.11643955, 0, '2026-01-23 11:15:14', '2026-01-23 11:15:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint UNSIGNED NOT NULL,
  `store_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_invoices`
--

CREATE TABLE `vendor_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` bigint UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addons_store_id_index` (`store_id`),
  ADD KEY `addons_addon_category_index` (`addon_category`),
  ADD KEY `addons_is_active_index` (`is_active`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branches_store_id_index` (`store_id`),
  ADD KEY `branches_is_active_index` (`is_active`),
  ADD KEY `branches_is_main_index` (`is_main`),
  ADD KEY `branches_latitude_longitude_index` (`latitude`,`longitude`);

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
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `carts_user_id_unique` (`user_id`),
  ADD KEY `carts_store_id_index` (`store_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_items_cart_id_index` (`cart_id`),
  ADD KEY `cart_items_product_id_index` (`product_id`);

--
-- Indexes for table `cart_item_addons`
--
ALTER TABLE `cart_item_addons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_item_addon_unique` (`cart_item_id`,`addon_id`),
  ADD KEY `cart_item_addons_cart_item_id_index` (`cart_item_id`),
  ADD KEY `cart_item_addons_addon_id_index` (`addon_id`);

--
-- Indexes for table `cart_item_options`
--
ALTER TABLE `cart_item_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_item_option_unique` (`cart_item_id`,`product_option_value_id`),
  ADD KEY `cart_item_options_cart_item_id_index` (`cart_item_id`),
  ADD KEY `cart_item_options_product_option_value_id_index` (`product_option_value_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_store_id_index` (`store_id`),
  ADD KEY `categories_is_active_index` (`is_active`),
  ADD KEY `categories_sort_order_index` (`sort_order`),
  ADD KEY `categories_module_id_foreign` (`module_id`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `deliveries_email_unique` (`email`);

--
-- Indexes for table `delivery_invoices`
--
ALTER TABLE `delivery_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `delivery_invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `delivery_invoices_delivery_id_foreign` (`delivery_id`);

--
-- Indexes for table `elasticsearch_sync_queue`
--
ALTER TABLE `elasticsearch_sync_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `elasticsearch_sync_queue_synced_at_index` (`synced_at`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module_store`
--
ALTER TABLE `module_store`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `main_category_store_store_id_main_category_id_unique` (`store_id`,`main_category_id`),
  ADD KEY `main_category_store_store_id_index` (`store_id`),
  ADD KEY `main_category_store_main_category_id_index` (`main_category_id`);

--
-- Indexes for table `option_groups`
--
ALTER TABLE `option_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `option_groups_type_index` (`type`),
  ADD KEY `option_groups_is_active_index` (`is_active`);

--
-- Indexes for table `option_values`
--
ALTER TABLE `option_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `option_values_option_group_id_index` (`option_group_id`),
  ADD KEY `option_values_is_active_index` (`is_active`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_address_id_foreign` (`address_id`),
  ADD KEY `orders_user_id_index` (`user_id`),
  ADD KEY `orders_store_id_index` (`store_id`),
  ADD KEY `orders_order_status_index` (`order_status`),
  ADD KEY `orders_payment_status_index` (`payment_status`),
  ADD KEY `orders_payment_reference_index` (`payment_reference`),
  ADD KEY `orders_created_at_index` (`created_at`),
  ADD KEY `orders_delivery_id_foreign` (`delivery_id`),
  ADD KEY `orders_branch_id_index` (`branch_id`),
  ADD KEY `orders_vendor_invoice_id_foreign` (`vendor_invoice_id`),
  ADD KEY `orders_delivery_invoice_id_foreign` (`delivery_invoice_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_index` (`order_id`),
  ADD KEY `order_items_product_id_index` (`product_id`);

--
-- Indexes for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_addons_order_item_id_index` (`order_item_id`);

--
-- Indexes for table `order_item_options`
--
ALTER TABLE `order_item_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_options_order_item_id_index` (`order_item_id`);

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
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `payments_order_id_index` (`order_id`),
  ADD KEY `payments_transaction_id_index` (`transaction_id`),
  ADD KEY `payments_status_index` (`status`),
  ADD KEY `payments_created_at_index` (`created_at`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_store_id_index` (`store_id`),
  ADD KEY `products_category_id_index` (`category_id`),
  ADD KEY `products_is_active_index` (`is_active`),
  ADD KEY `products_view_count_index` (`view_count`),
  ADD KEY `products_sales_count_index` (`sales_count`),
  ADD KEY `products_deleted_at_index` (`deleted_at`),
  ADD KEY `products_subcategory_id_foreign` (`subcategory_id`);
ALTER TABLE `products` ADD FULLTEXT KEY `products_search_keywords_fulltext` (`search_keywords`);

--
-- Indexes for table `product_addons`
--
ALTER TABLE `product_addons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_addon` (`product_id`,`addon_id`),
  ADD KEY `product_addons_product_id_index` (`product_id`),
  ADD KEY `product_addons_addon_id_index` (`addon_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_index` (`product_id`),
  ADD KEY `product_images_is_primary_index` (`is_primary`);

--
-- Indexes for table `product_options`
--
ALTER TABLE `product_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_option` (`product_id`,`option_group_id`),
  ADD KEY `product_options_product_id_index` (`product_id`),
  ADD KEY `product_options_option_group_id_index` (`option_group_id`);

--
-- Indexes for table `product_option_values`
--
ALTER TABLE `product_option_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_option_value` (`product_option_id`,`option_value_id`),
  ADD KEY `product_option_values_product_option_id_index` (`product_option_id`),
  ADD KEY `product_option_values_option_value_id_index` (`option_value_id`),
  ADD KEY `product_option_values_is_available_index` (`is_available`);

--
-- Indexes for table `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `push_notifications_sender_id_foreign` (`sender_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stores_status_index` (`status`),
  ADD KEY `stores_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_addresses_user_id_index` (`user_id`),
  ADD KEY `user_addresses_is_default_index` (`is_default`),
  ADD KEY `user_addresses_address_type_index` (`address_type`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_email_unique` (`email`),
  ADD KEY `vendors_store_id_index` (`store_id`);

--
-- Indexes for table `vendor_invoices`
--
ALTER TABLE `vendor_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `vendor_invoices_store_id_foreign` (`store_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;

--
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=467;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=703;

--
-- AUTO_INCREMENT for table `cart_item_addons`
--
ALTER TABLE `cart_item_addons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `cart_item_options`
--
ALTER TABLE `cart_item_options`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=513;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `delivery_invoices`
--
ALTER TABLE `delivery_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `elasticsearch_sync_queue`
--
ALTER TABLE `elasticsearch_sync_queue`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2509;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `module_store`
--
ALTER TABLE `module_store`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `option_groups`
--
ALTER TABLE `option_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `option_values`
--
ALTER TABLE `option_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268;

--
-- AUTO_INCREMENT for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `order_item_options`
--
ALTER TABLE `order_item_options`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `product_addons`
--
ALTER TABLE `product_addons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `product_options`
--
ALTER TABLE `product_options`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `product_option_values`
--
ALTER TABLE `product_option_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `push_notifications`
--
ALTER TABLE `push_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `vendor_invoices`
--
ALTER TABLE `vendor_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addons`
--
ALTER TABLE `addons`
  ADD CONSTRAINT `addons_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_item_addons`
--
ALTER TABLE `cart_item_addons`
  ADD CONSTRAINT `cart_item_addons_addon_id_foreign` FOREIGN KEY (`addon_id`) REFERENCES `addons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_item_addons_cart_item_id_foreign` FOREIGN KEY (`cart_item_id`) REFERENCES `cart_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_item_options`
--
ALTER TABLE `cart_item_options`
  ADD CONSTRAINT `cart_item_options_cart_item_id_foreign` FOREIGN KEY (`cart_item_id`) REFERENCES `cart_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_item_options_product_option_value_id_foreign` FOREIGN KEY (`product_option_value_id`) REFERENCES `product_option_values` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `categories_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_invoices`
--
ALTER TABLE `delivery_invoices`
  ADD CONSTRAINT `delivery_invoices_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `module_store`
--
ALTER TABLE `module_store`
  ADD CONSTRAINT `main_category_store_main_category_id_foreign` FOREIGN KEY (`main_category_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `main_category_store_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `option_values`
--
ALTER TABLE `option_values`
  ADD CONSTRAINT `option_values_option_group_id_foreign` FOREIGN KEY (`option_group_id`) REFERENCES `option_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `user_addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_delivery_invoice_id_foreign` FOREIGN KEY (`delivery_invoice_id`) REFERENCES `delivery_invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `orders_vendor_invoice_id_foreign` FOREIGN KEY (`vendor_invoice_id`) REFERENCES `vendor_invoices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  ADD CONSTRAINT `order_item_addons_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_item_options`
--
ALTER TABLE `order_item_options`
  ADD CONSTRAINT `order_item_options_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `products_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_addons`
--
ALTER TABLE `product_addons`
  ADD CONSTRAINT `product_addons_addon_id_foreign` FOREIGN KEY (`addon_id`) REFERENCES `addons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_addons_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_options`
--
ALTER TABLE `product_options`
  ADD CONSTRAINT `product_options_option_group_id_foreign` FOREIGN KEY (`option_group_id`) REFERENCES `option_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_options_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_option_values`
--
ALTER TABLE `product_option_values`
  ADD CONSTRAINT `product_option_values_option_value_id_foreign` FOREIGN KEY (`option_value_id`) REFERENCES `option_values` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_option_values_product_option_id_foreign` FOREIGN KEY (`product_option_id`) REFERENCES `product_options` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD CONSTRAINT `push_notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `stores_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_invoices`
--
ALTER TABLE `vendor_invoices`
  ADD CONSTRAINT `vendor_invoices_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
