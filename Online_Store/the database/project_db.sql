-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 09 يناير 2026 الساعة 18:24
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_db`
--

-- --------------------------------------------------------

--
-- بنية الجدول `captcha_sessions`
--

CREATE TABLE `captcha_sessions` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `correct_answer` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(1, 6, 10, 1, '2025-09-28 23:04:18'),
(2, 6, 4, 1, '2025-09-28 23:04:59'),
(3, 9, 11, 1, '2025-09-29 06:20:22'),
(4, 9, 4, 1, '2025-09-29 06:20:38');

-- --------------------------------------------------------

--
-- بنية الجدول `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) DEFAULT 0.00,
  `category` enum('men','women','kids','accessories') NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `discount`, `category`, `image`) VALUES
(1, 'Jacket', 'Warm winter jacket', 42.99, 30.00, 'men', 'images/Home/1.jpg'),
(2, 'Black Denim Jeans', 'Comfortable fit', 62.99, 20.00, 'men', 'images/Home/2.jpg'),
(3, 'Summer Dress', 'Light and trendy', 66.99, 40.00, 'women', 'images/Home/3.jpg'),
(4, 'Casual Blazer', 'Perfect for office', 94.99, 15.00, 'men', 'images/Home/4.jpg'),
(5, 'Classic White T-Shirt', 'Everyday wear', 37.99, 20.00, 'men', 'images/men/1.png'),
(6, 'Black Denim Jeans', 'Stylish jeans', 49.99, 0.00, 'men', 'images/men/2.png'),
(7, 'Leather Jacket', 'Premium quality', 142.99, 30.00, 'men', 'images/men/3.png'),
(8, 'Casual Sneakers', 'Comfort shoes', 59.99, 0.00, 'men', 'images/men/4.png'),
(9, 'Cartoon Print T-Shirt', 'Fun kids tee', 24.99, 20.00, 'kids', 'images/kids/4.png'),
(10, 'Denim Jeans', 'Durable kids jeans', 29.99, 0.00, 'kids', 'images/kids/2.png'),
(11, 'Floral Summer Dress', 'Cute girls dress', 49.99, 30.00, 'kids', 'images/kids/3.png'),
(12, 'Light-up Sneakers', 'Kids favorite shoes', 39.99, 0.00, 'kids', 'images/kids/11.png');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'Ali', 'ali123@gmail.com', '$2y$10$eLNAIeZiCZ.SUxxBkW3GGu7e0vqO4I6NzPMJUJcslwcIzbaDTs.nO', 'user', 1, NULL, '2025-09-28 22:04:35', '2025-09-29 04:50:56'),
(5, 'admin2', 'admin2@emadstore.com', '$2y$10$S/UzdOHJUZAdkOtiewCJweossSRYSWSK4Q3ExjTcK5qv3TM1Zwsq.', 'admin', 1, NULL, '2025-09-28 22:23:14', '2025-09-28 22:23:14'),
(6, 'Mohammed', 'Mohammed123@gmail.com', '$2y$10$6Qi/1zSLISnoufecplhoM.rsvjXyUIe/FsVDcj1j4jZoXV2YTiqdq', 'user', 1, NULL, '2025-09-28 23:03:19', '2025-09-29 06:23:27'),
(7, 'EEEE', 'EEEE123@gmail.com', '$2y$10$oMr2SlqJJdN9NNa1RKwYKe53CTZFGzj/nm40yLjQl/OLZIVd9o1w2', 'admin', 1, NULL, '2025-09-28 23:17:41', '2025-09-28 23:21:36'),
(8, 'Ahmad', 'AHMADD123@gmail.com', '$2y$10$M/XLpglCIFgggj7Whq6HW.aGLb8GqVHzQ8MBwIOUemDiPUb07IeTy', 'admin', 1, NULL, '2025-09-28 23:27:37', '2025-09-28 23:28:40'),
(9, 'AAAA', 'AAAA123@gmail.com', '$2y$10$JEtpJz2lJ6j5/63NQnjfa.Tuc0NRb7C9m1YBrvGfO1h8/bSxd6r5m', 'admin', 1, '14307916f55bc2ea65b3b6e84d4f6b7ed7d0ae18482b3607b0478bbbd4ee1f56', '2025-09-29 06:10:35', '2025-09-29 07:10:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `captcha_sessions`
--
ALTER TABLE `captcha_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `captcha_sessions`
--
ALTER TABLE `captcha_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
