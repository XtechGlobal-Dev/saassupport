-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 06:37 AM
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
-- Database: `ticket_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'ABC Traders', 'abc@test.com', '3265987410', 'Akshya Nagar 1st Block 1st Cross, Rammurthy nagar, Bangalore-560016', '2025-05-08 05:07:51'),
(2, 'XYZ company', 'xyz@gmail.com', '7777888800', '651 Philip Village, Prohaskamouth, MT 37672', '2025-05-08 05:10:10');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'IT Support', 'test', '2025-05-08 04:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `priorities`
--

CREATE TABLE `priorities` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(20) DEFAULT 'warning',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `priorities`
--

INSERT INTO `priorities` (`id`, `name`, `description`, `color`, `created_at`) VALUES
(1, 'Critical', 'Critical', 'danger', '2025-05-08 10:57:03'),
(2, 'High', 'High', 'danger', '2025-05-08 10:57:29'),
(3, 'Medium', 'Medium', 'warning', '2025-05-08 10:57:55'),
(4, 'Low', 'Low', 'secondary', '2025-05-08 10:58:17');


-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Hardware Issue Fixing', 'Fix Hardware issues', '2025-05-08 05:01:59'),
(2, 'Network issue fix', 'Provide support for network related issues', '2025-05-08 05:02:42'),
(3, 'Software Development', 'We provide software build and maintenance service', '2025-05-09 10:54:36');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_attachments`
--

CREATE TABLE `ticket_attachments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_attachments`
--

INSERT INTO `ticket_attachments` (`id`, `ticket_id`, `file_path`, `file_name`, `file_type`, `created_at`) VALUES
(1, 1, '../uploads/681c3f0d3ffd7_Screenshot 2025-03-17 170506.png', 'Screenshot 2025-03-17 170506.png', 'image/png', '2025-05-08 05:20:13'),
(2, 3, '../uploads/681c8beaaca18_Screenshot 2025-03-17 171019.png', 'Screenshot 2025-03-17 171019.png', 'image/png', '2025-05-08 10:48:10'),
(7, 5, 'C:\\xampp\\htdocs\\php-ticket-app\\CascadeProjects/uploads/681db1e364485_Screenshot 2025-04-21 145358.png', 'Screenshot 2025-04-21 145358.png', 'image/png', '2025-05-09 07:42:27'),
(8, 5, 'C:\\xampp\\htdocs\\php-ticket-app\\CascadeProjects/uploads/681db1e36494f_Screenshot 2025-04-21 145345.png', 'Screenshot 2025-04-21 145345.png', 'image/png', '2025-05-09 07:42:27'),
(9, 11, 'C:\\xampp\\htdocs\\php-ticket-app\\CascadeProjects/uploads/681def2b5f694_Screenshot 2025-03-17 151502.png', 'Screenshot 2025-03-17 151502.png', 'image/png', '2025-05-09 12:03:55'),
(10, 11, 'C:\\xampp\\htdocs\\php-ticket-app\\CascadeProjects/uploads/681def2b6033e_Screenshot 2025-03-17 155110.png', 'Screenshot 2025-03-17 155110.png', 'image/png', '2025-05-09 12:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_ccs`
--

CREATE TABLE `ticket_ccs` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_ccs`
--

INSERT INTO `ticket_ccs` (`id`, `ticket_id`, `user_id`, `created_at`) VALUES
(25, 3, 1, '2025-05-09 09:50:59'),
(26, 3, 1, '2025-05-09 09:50:59'),
(27, 1, 1, '2025-05-09 09:51:12');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_status`
--

CREATE TABLE `ticket_status` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_status`
--

INSERT INTO `ticket_status` (`id`, `name`, `color`, `created_at`) VALUES
(1, 'Open', '#FF0000', '2025-05-09 09:39:19'),
(2, 'In Progress', '#FFA500', '2025-05-09 09:39:19'),
(3, 'Hold', '#FF4500', '2025-05-09 09:39:19'),
(4, 'Waiting for Customer Response', '#1E90FF', '2025-05-09 09:39:19'),
(5, 'Resolved', '#00FF00', '2025-05-09 09:39:19'),
(6, 'Closed', '#808080', '2025-05-09 09:39:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `priorities`
--
ALTER TABLE `priorities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);


--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sb_tickets`
--
ALTER TABLE `sb_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `priority_id` (`priority_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `fk_ticket_status` (`status_id`),
  ADD KEY `tickets_ibfk_1` (`contact_id`);

--
-- Indexes for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `ticket_ccs`
--
ALTER TABLE `ticket_ccs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ticket_status`
--
ALTER TABLE `ticket_status`
  ADD PRIMARY KEY (`id`);



--
-- Constraints for dumped tables
--

--
-- Constraints for table `sb_tickets`
--
ALTER TABLE `sb_tickets`
  ADD CONSTRAINT `fk_ticket_status` FOREIGN KEY (`status_id`) REFERENCES `ticket_status` (`id`),
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`priority_id`) REFERENCES `priorities` (`id`),
  ADD CONSTRAINT `tickets_ibfk_4` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `tickets_ibfk_5` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD CONSTRAINT `ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `sb_tickets` (`id`);

--
-- Constraints for table `ticket_ccs`
--
ALTER TABLE `ticket_ccs`
  ADD CONSTRAINT `ticket_ccs_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `sb_tickets` (`id`),
  ADD CONSTRAINT `ticket_ccs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
