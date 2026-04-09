-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2026 at 05:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_ukk`
--

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

CREATE TABLE `backups` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `qty`, `created_at`) VALUES
(11, 4, 11, 1, '2026-02-24 14:12:20');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sender` enum('user','admin') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `user_id`, `message`, `sender`, `created_at`) VALUES
(16, 2, 'hi', 'user', '2026-04-08 04:36:36'),
(17, 2, 'test', 'user', '2026-04-08 04:36:38'),
(18, 2, 'min', 'user', '2026-04-08 06:19:40'),
(19, 2, '1', 'user', '2026-04-08 06:52:59'),
(20, 2, 'hi', 'user', '2026-04-08 07:39:21'),
(21, 2, 'oi', 'admin', '2026-04-08 07:46:12'),
(22, 2, 'hi', 'user', '2026-04-09 02:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'NULL for global admin notification, ID for specific user',
  `type` varchar(50) NOT NULL COMMENT 'new_order, refund, delivery, chat',
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `is_read`, `created_at`) VALUES
(1, NULL, 'chat', 'Pesan baru dari User (ID: 2): raihan', 1, '2026-04-08 07:39:21'),
(2, NULL, 'order', 'Pesanan baru telah masuk! Order ID: #00049', 1, '2026-04-08 07:39:54'),
(3, 2, 'chat', 'Pesan baru dari Admin Orderly.', 1, '2026-04-08 07:46:12'),
(4, 2, 'delivery', 'Pesanan #00049 telah dikonfirmasi dan sedang dikemas.', 1, '2026-04-08 07:46:44'),
(5, 2, 'delivery', 'Status pengiriman Order #00049 diperbarui menjadi: Dalam Perjalanan', 1, '2026-04-08 07:47:17'),
(6, 2, 'delivery', 'Status pengiriman Order #00049 diperbarui menjadi: Selesai', 1, '2026-04-08 07:47:30'),
(7, NULL, 'chat', 'Pesan baru dari User (ID: 2): raihan', 1, '2026-04-09 02:14:24'),
(8, NULL, 'order', 'Pesanan baru telah masuk! Order ID: #00050', 1, '2026-04-09 02:23:45'),
(9, 2, 'delivery', 'Pesanan #00050 telah dikonfirmasi dan sedang dikemas.', 0, '2026-04-09 02:24:09'),
(10, NULL, 'order', 'Pesanan baru telah masuk! Order ID: #00051', 1, '2026-04-09 03:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `officer`
--

CREATE TABLE `officer` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officer`
--

INSERT INTO `officer` (`id`, `username`, `email`, `no_hp`, `password`, `role`, `created_at`) VALUES
(4, 'admin', 'admin@gmail.com', '012345678', '$2y$10$FSqiLRINkgVsYoe7cwFhweGHHgNW6u6N5unofbVAx6mkMlh//cxgG', 'admin', '2026-02-12 14:34:27'),
(5, 'fauzan', 'fauzan@gmail.com', '08913819313', '$2y$10$.RDsZdpZqcFCj4sPELBEau2vqXR/5VzPuQazo.9vV5iNq9s51jt5m', 'admin', '2026-02-19 09:28:09'),
(6, 'petugas', 'petugas@gmail.com', '1234567654411', '$2y$10$N3OrO4JAZXY9mvKLz88Uhu56EiW27eBDetCZRh3qdfaGcXYe/m1UK', 'petugas', '2026-03-31 13:09:41'),
(8, 'vito', 'vito@gmail.com', '0891381931322', '$2y$10$8gbnVsJV/54LGwX4VvoFqeDEFXlJPyMIiYw7rbOTeqyZatB7BkSti', 'admin', '2026-04-08 06:00:37');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `address` text DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `delivery_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_name`, `phone`, `order_date`, `address`, `method`, `total_price`, `status`, `delivery_status`) VALUES
(39, 2, 'Star Player 76', NULL, '2026-04-08 07:23:53', 'bandung', 'COD', 1299000, 'Refunded', NULL),
(40, 2, 'CONS Louie Lopez Pro 2 Suede', NULL, '2026-04-08 07:28:48', 'bandung', 'COD', 1049300, 'Rejected', NULL),
(41, 2, 'Nike Air Force 1 \'07', NULL, '2026-04-08 07:47:49', 'Depok', 'COD', 1999999, 'Refunded', NULL),
(42, 2, 'Converse Chuck Taylor Throwback', NULL, '2026-04-08 08:10:06', 'Depok', 'COD', 1199000, 'Refunded', NULL),
(43, 2, 'Chuck Taylor All Star', NULL, '2026-04-08 10:25:08', 'Depok', 'COD', 899000, 'Confirmed', NULL),
(44, 2, 'Chuck Taylor All Star', NULL, '2026-04-08 11:30:23', 'Depok', 'COD', 899000, 'Rejected', NULL),
(45, 2, 'Nike Air Force 1 \'07', NULL, '2026-04-08 11:39:22', 'Depok', 'COD', 3999998, 'Rejected', NULL),
(46, 2, 'Nike Air Force 1 \'07', NULL, '2026-04-08 13:08:02', 'Depok (Shipping: SiCepat - Express [Rp 35.000])', 'Transfer', 2034999, 'Confirmed', NULL),
(47, 2, 'Nike Air Force 1 \'07', NULL, '2026-04-08 13:17:09', 'Depok (Shipping: JNE - Regular [Rp 15.000])', 'Transfer', 2014999, 'Refunded', NULL),
(48, 2, 'CONS Louie Lopez Pro 2 Suede', NULL, '2026-04-08 13:32:49', 'Depok (Shipping: JNE - Regular [Rp 15.000])', 'COD', 1064300, 'Confirmed', 'Dalam Perjalanan'),
(49, 2, 'Nike Air Force 1 \'07', NULL, '2026-04-08 14:39:54', 'Depok (Shipping: JNE - Express [Rp 35.000])', 'COD', 4034998, 'Confirmed', 'Selesai'),
(50, 2, 'Nike Air Force 1 \'07, Chuck Taylor All Star', NULL, '2026-04-09 09:23:45', 'Depok (Shipping: SiCepat - Express [Rp 35.000])', 'COD', 4933996, 'Confirmed', 'Packaging'),
(51, 2, 'Nike Air Force 1 \'07', '081234567890', '2026-04-09 10:10:22', 'Depok (Shipping: JNE - Regular [Rp 15.000])', 'COD', 2014998, 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `brand`, `description`, `price`, `stock`, `size`, `image`, `created_at`) VALUES
(14, 'Nike Air vapormax Flyknit', 'Nike', 'The Nike Air VaporMax Flyknit is a lightweight, high-performance running shoe featuring revolutionary, full-length, direct-to-upper cushioning without a traditional midsole. Key features include a breathable, stretchy Flyknit upper for a sock-like fit and a durable, flexible, and responsive VaporMax Air unit designed for a \"running on air\" sensation.', 5000000, 96, '40', '1772072995_ac42d423_9d29_4d9c_bc58_c486316156ed.webp', '2026-02-26 02:29:01'),
(16, 'Air Jordan 1 Low', 'Nike', 'Always in, always fresh. The Air Jordan 1 Low sets you up with a piece of Jordan history and heritage that\'s comfortable all day. Choose your colours, then step out in the iconic profile that\'s built with a high-end mix of materials and encapsulated Air in the heel.', 1429000, 18, '39', '1775439524_WMNS_AIR_JORDAN_1_LOW__1_.jpg', '2026-04-06 01:38:44'),
(17, 'Nike V2K Run', 'Nike', 'Fast-forward. Rewind. Doesn\'t matter—this shoe takes retro into the future. The V2K remasters everything you love about the Vomero in a look pulled straight from an early \'00s running catalogue. It\'s layered up in a mixture of flashy metallics, referential plastic details and a soft midsole with a perfectly aged aesthetic. And the chunky heel makes sure wherever you go, it\'s in comfort.', 2099000, 47, '40', '1775439727_W_NIKE_V2K_RUN.jpg', '2026-04-06 01:42:07'),
(18, 'Nike Pegasus 42', 'Nike', 'Responsive full-length cushioning sculpted to energise an icon.\r\nResponsive cushioning in the Pegasus 42 provides an energised ride for everyday road runs. Experience power in every stride thanks to the propulsive feel of a curved, full-length Air Zoom unit and a ReactX foam midsole. An updated fit gives you more space in the forefoot and toe box.', 2199000, 96, '45', '1775440662_W_NIKE_AIR_ZOOM_PEGASUS_42.jpg', '2026-04-06 01:57:42'),
(19, 'Converse Chuck Taylor Throwback', 'Converse', 'The Converse Chuck Taylor Throwback is a classic sneaker with a vintage style. It features a durable canvas upper, a rubber toe cap, and a comfortable sole for everyday wear.\r\nWith its retro design and timeless look, this shoe is easy to match with any outfit, making it perfect for casual activities and daily use.', 1199000, 90, '41', '1775440796_0888-CONA19787C00W09H-3.webp', '2026-04-06 01:59:56'),
(20, 'Star Player 76', 'Converse', 'converse', 1299000, 99, '43', '1775530383_0888-CONA21458CIDG09H-1.webp', '2026-04-07 02:53:03'),
(21, 'CONS Louie Lopez Pro 2 Suede', 'Converse', 'converse', 1049300, 98, '42', '1775530509_0888-CONA14324CIVO09H-1.webp', '2026-04-07 02:55:09'),
(23, 'Chuck Taylor All Star', 'Converse', 'converse\r\n', 899000, 96, '41', '1775532939_conm7650c-1.webp', '2026-04-07 03:28:15'),
(25, 'Nike Air Force 1 \'07', 'Nike', 'NIKE\r\n\r\n', 1999998, 89, '41', '1775533293_AIR_FORCE_1__07.jpg', '2026-04-07 03:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`id`, `order_id`, `reason`, `status`, `created_at`) VALUES
(1, 42, 'mau ganti\r\n', 'approved', '2026-04-08 01:20:05'),
(2, 41, 'test', 'approved', '2026-04-08 01:37:12'),
(3, 39, 'mau ganti', 'approved', '2026-04-08 02:34:53'),
(4, 47, 'mau ganti', 'approved', '2026-04-08 06:18:47');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `proof_payment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `user_id`, `product_name`, `qty`, `price`, `proof_payment`, `created_at`) VALUES
(38, 48, 2, 'CONS Louie Lopez Pro 2 Suede', 1, 1049300, '', '2026-04-08 06:32:49'),
(39, 49, 2, 'Nike Air Force 1 \'07', 2, 3999998, '', '2026-04-08 07:39:54'),
(40, 50, 2, 'Nike Air Force 1 \'07', 2, 3999996, '', '2026-04-09 02:23:45'),
(41, 50, 2, 'Chuck Taylor All Star', 1, 899000, '', '2026-04-09 02:23:45'),
(42, 51, 2, 'Nike Air Force 1 \'07', 1, 1999998, '', '2026-04-09 03:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `address`, `password`, `created_at`) VALUES
(1, 'fauzan', 'fauzan@gmail.com', '895 3831 14323', 'cagar alam', '$2y$10$QM1XlscUOdye3Vf3ii2h3eWpA9rLzzNqmsPm17VRjMjdDgsyK90cC', '2026-02-22 14:14:46'),
(2, 'raihan', 'raihan@gmail.com', '895 3831 14322', 'Depok', '$2y$10$y9ISwxEbfNNcx8gwKeH0Ye3O704Qms0/B8tR5NLkpiYmuryAJBjzq', '2026-02-24 12:38:28'),
(3, 'vito', 'yazidauff@gmail.com', '431758193681', NULL, '$2y$10$m7ZSjvSJMMCqXUnzy1LVGu3kD.k8FbMXhFV5YarrxewzDpY1ez6n2', '2026-04-03 14:17:56'),
(6, 'raihan_test', 'raihan_test@test.com', '08123456780', NULL, '$2y$10$mTs38xmbf2RpTbSXerFI/On5ShMytsqP7Z6TTT7TFdyBmVlB0AeBq', '2026-04-09 02:40:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `officer`
--
ALTER TABLE `officer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `officer`
--
ALTER TABLE `officer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
