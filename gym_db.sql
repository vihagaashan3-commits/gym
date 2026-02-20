-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2026 at 11:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gym_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `date`, `status`) VALUES
(4, 34, '2026-02-19', 'present'),
(5, 35, '2026-02-15', 'present'),
(6, 35, '2026-02-17', 'present'),
(7, 35, '2026-02-01', 'present'),
(8, 35, '2026-02-19', 'absent');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('pending','confirmed','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `trainer_id`, `day`, `start_time`, `end_time`, `status`) VALUES
(1, 37, 1, 'Monday', '14:38:00', '14:38:00', 'pending'),
(2, 37, 2, 'Friday', '15:21:00', '15:21:00', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `diet_plans`
--

CREATE TABLE `diet_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` enum('underweight','normal','overweight') NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diet_plans`
--

INSERT INTO `diet_plans` (`id`, `name`, `category`, `description`) VALUES
(1, 'intermittent', 'underweight', 'eat');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `status` enum('pending','paid') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `plan_id`, `status`, `payment_date`, `created_at`) VALUES
(2, 34, 1, 'paid', '2026-02-19 04:29:05', '2026-02-19 04:29:05'),
(3, 35, 1, 'paid', '2026-02-19 04:52:48', '2026-02-19 04:52:48'),
(4, 35, 3, 'pending', '2026-02-19 05:27:39', '2026-02-19 05:27:39'),
(5, 37, 3, 'paid', '2026-02-19 06:01:23', '2026-02-19 06:01:23'),
(6, 37, 3, 'pending', '2026-02-19 06:04:28', '2026-02-19 06:04:28');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `plan_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `plan_name`, `price`, `duration`, `created_at`) VALUES
(1, 'demo', 1000.00, 2, '2026-02-17 10:27:52'),
(2, 'demo', 1000.00, 1, '2026-02-17 10:27:52'),
(3, 'demo2', 7000.00, 3, '2026-02-18 11:59:37'),
(4, 'demo4', 2345.00, 7, '2026-02-19 05:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `specialization` enum('muscle_building','weight_loss','cardio','flexibility') DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`id`, `full_name`, `specialization`, `phone`, `email`, `profile_pic`) VALUES
(1, 'Trainer1', 'weight_loss', '0712345678', 'Trainer@gmail.com', '1771491869_user.png'),
(2, 'Trainer 4', 'cardio', '0742226057', 'Trainer@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trainer_schedule`
--

CREATE TABLE `trainer_schedule` (
  `id` int(11) NOT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_schedule`
--

INSERT INTO `trainer_schedule` (`id`, `trainer_id`, `day`, `start_time`, `end_time`) VALUES
(1, NULL, 'Monday', '03:31:00', '19:35:00'),
(2, 1, 'Monday', '14:42:00', '14:42:00'),
(3, 1, 'Monday', '15:13:00', '15:13:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff','customer') DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `specialization` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `phone`, `profile_pic`, `created_at`, `reset_token`, `reset_token_expiry`, `specialization`) VALUES
(27, 'System Admin', 'admin@gym.com', '$2y$10$crZCRVe79YlpABAMad5mBOt/0RT7AYrWsKGBAEWPB/MDaOBSiIi5y', 'admin', '0000000000', NULL, '2026-02-19 04:08:11', NULL, NULL, NULL),
(32, 'Trainer1', 'trainer@gym.com', '$2y$10$v84zkKlep/Ph4wIIXqljqeSsoIhj8AHfouQz9TFXFf3y/RwS4a4pq', 'staff', '0712345678', '1771494047_order.png', '2026-02-19 04:18:25', NULL, NULL, NULL),
(34, 'customer2', 'customer2@gmail.com', '$2y$10$.UjhnWiQRxweNXEoHqGQ0.mfZn1yh03b39Gh6uc4d4QrjYamRajoy', 'customer', '0712345678', '1771476178_pngwing_com__13_.png', '2026-02-19 04:28:41', NULL, NULL, NULL),
(35, 'Customer3', 'customer3@gmail.com', '$2y$10$yZQbUjfmIsu/eJXZwsZhkOE2ea.dmMXqADM/vSMouBKTaSMIz2Ahi', 'customer', '0742226057', '1771476797_pngwing_com__10_.png', '2026-02-19 04:47:02', NULL, NULL, NULL),
(36, 'chamodya diwyanjalee', 'pdchamodiwya@gmail.com', '$2y$10$87jdgBgIlhgHwTPjZfeGOuBV/miSjkX/o0UDZDmrwybFblZ8XbTr2', 'customer', '0742226056', NULL, '2026-02-19 05:40:33', NULL, NULL, NULL),
(37, 'customer4', 'customer4@gmail.com', '$2y$10$Vf4BAFItK.AoeXkF.YUvmeWsJlnx2mxQOmSbDXRuMLTkZDuFoX6y2', 'customer', '0742226057', '1771480866_pngwing_com__13_.png', '2026-02-19 05:59:59', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `workout_plans`
--

CREATE TABLE `workout_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` enum('underweight','normal','overweight') NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_plans`
--

INSERT INTO `workout_plans` (`id`, `name`, `category`, `description`) VALUES
(1, 'demo1', 'underweight', 'dont eat');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `diet_plans`
--
ALTER TABLE `diet_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainer_schedule`
--
ALTER TABLE `trainer_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `diet_plans`
--
ALTER TABLE `diet_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `trainer_schedule`
--
ALTER TABLE `trainer_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `workout_plans`
--
ALTER TABLE `workout_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_schedule`
--
ALTER TABLE `trainer_schedule`
  ADD CONSTRAINT `trainer_schedule_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
