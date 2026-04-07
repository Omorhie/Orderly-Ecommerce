-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2026 at 04:52 PM
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
(6, 'petugas', 'petugas@gmail.com', '1234567654411', '$2y$10$N3OrO4JAZXY9mvKLz88Uhu56EiW27eBDetCZRh3qdfaGcXYe/m1UK', 'petugas', '2026-03-31 13:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `address` text DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_name`, `order_date`, `address`, `method`, `total_price`, `status`) VALUES
(16, 2, 'sepatuu', '2026-02-25 14:19:20', 'jakarta', 'COD', 100000, 'Pending'),
(17, 2, 'sepatuu', '2026-02-25 15:17:47', 'beji', 'COD', 200000, 'Pending'),
(19, 1, 'Nike Air vapormax Flyknit', '2026-02-26 09:33:17', 'beji', 'Transfer', 3000000, 'Pending'),
(20, 1, 'Nike Air vapormax Flyknit', '2026-02-26 10:19:55', 'jakarta', 'COD', 500000, 'Confirmed'),
(21, 1, 'Nike Air vapormax Flyknit', '2026-02-26 11:26:38', 'beji', 'COD', 500000, 'confirmed'),
(22, 1, 'Nike Air vapormax Flyknit', '2026-03-31 21:38:26', 'depok', 'COD', 500000, 'Confirmed'),
(23, 2, 'Chuck Taylor Throwback', '2026-04-06 09:43:31', 'depok', 'COD', 3597000, 'Confirmed'),
(24, 2, 'Nike V2K Run', '2026-04-06 09:46:53', 'beji', 'COD', 2099000, 'Confirmed'),
(25, 2, 'Nike Air Force 1 \'07, Converse Chuck Taylor Throwback', '2026-04-07 01:16:24', 'Depok', 'COD', 2748000, 'Pending'),
(26, 2, 'Nike Air Force 1 \'07, Converse Chuck Taylor Throwback', '2026-04-07 01:16:26', 'Depok', 'COD', 2748000, 'Pending'),
(27, 2, 'Nike Air Force 1 \'07, Converse Chuck Taylor Throwback', '2026-04-07 01:17:40', 'Depok', 'COD', 2748000, 'Pending'),
(28, 2, 'Nike Air Force 1 \'07, Converse Chuck Taylor Throwback', '2026-04-07 01:17:41', 'Depok', 'COD', 2748000, 'Pending'),
(29, 2, 'Nike Air Force 1 \'07, Converse Chuck Taylor Throwback', '2026-04-07 01:17:59', 'Depok', 'COD', 2748000, 'Confirmed'),
(30, 2, 'Converse Chuck Taylor Throwback, Nike Pegasus 42', '2026-04-07 01:18:47', 'Depok', 'COD', 3398000, 'Confirmed'),
(31, 2, 'Nike Pegasus 42', '2026-04-07 01:44:00', 'Depok', 'COD', 2199000, 'Confirmed'),
(32, 2, 'Nike Pegasus 42, Nike V2K Run', '2026-04-07 01:46:01', 'Depok', 'COD', 4298000, 'Confirmed'),
(33, 2, 'Air Jordan 1 Low', '2026-04-07 02:09:36', 'Depok', 'COD', 1429000, 'Confirmed'),
(34, 1, 'Converse Chuck Taylor Throwback, Nike Pegasus 42, Nike V2K Run', '2026-04-07 07:41:21', 'cagar alam', 'COD', 5497000, 'Confirmed'),
(35, 1, 'Converse Chuck Taylor Throwback', '2026-04-07 07:44:04', 'cagar alam', 'COD', 1199000, 'Confirmed'),
(36, 1, 'Converse Chuck Taylor Throwback', '2026-04-07 08:29:12', 'cagar alam', 'COD', 1199000, 'Confirmed'),
(37, 1, 'Air Jordan 1 Low', '2026-04-07 08:35:49', 'cagar alam', 'COD', 1429000, 'Confirmed');

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
(19, 'Converse Chuck Taylor Throwback', 'Converse', 'The Converse Chuck Taylor Throwback is a classic sneaker with a vintage style. It features a durable canvas upper, a rubber toe cap, and a comfortable sole for everyday wear.\r\nWith its retro design and timeless look, this shoe is easy to match with any outfit, making it perfect for casual activities and daily use.', 1199000, 91, '41', '1775440796_0888-CONA19787C00W09H-3.webp', '2026-04-06 01:59:56'),
(20, 'Star Player 76', 'Converse', 'converse', 1299000, 100, '43', '1775530383_0888-CONA21458CIDG09H-1.webp', '2026-04-07 02:53:03'),
(21, 'CONS Louie Lopez Pro 2 Suede', 'Converse', 'converse', 1049300, 100, '42', '1775530509_0888-CONA14324CIVO09H-1.webp', '2026-04-07 02:55:09'),
(23, 'Chuck Taylor All Star', 'Converse', 'converse\r\n', 899000, 100, '41', '1775532939_conm7650c-1.webp', '2026-04-07 03:28:15'),
(25, 'Nike Air Force 1 \'07', 'Nike', 'NIKE\r\n\r\n', 1999999, 100, '41', '1775533293_AIR_FORCE_1__07.jpg', '2026-04-07 03:41:33');

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
(24, 35, 1, 'Converse Chuck Taylor Throwback', 1, 1199000, '', '2026-04-07 00:44:04'),
(25, 36, 1, 'Converse Chuck Taylor Throwback', 1, 1199000, '', '2026-04-07 01:29:12'),
(26, 37, 1, 'Air Jordan 1 Low', 1, 1429000, '', '2026-04-07 01:35:49');

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
(3, 'vito', 'yazidauff@gmail.com', '431758193681', NULL, '$2y$10$m7ZSjvSJMMCqXUnzy1LVGu3kD.k8FbMXhFV5YarrxewzDpY1ez6n2', '2026-04-03 14:17:56');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `officer`
--
ALTER TABLE `officer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
