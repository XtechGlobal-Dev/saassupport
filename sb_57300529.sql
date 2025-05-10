-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 01:43 PM
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
-- Database: `sb_57300529`
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
(1, 'Critical', 'Critical', '#FF0000', '2025-05-08 10:57:03'),
(2, 'High', 'High', '#FF0000', '2025-05-08 10:57:29'),
(3, 'Medium', 'Medium', '#FF4500', '2025-05-08 10:57:55'),
(4, 'Low', 'Low', '#808080', '2025-05-08 10:58:17');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'admin', 'Full system administrator', '2025-05-08 04:09:05'),
(2, 'manager', 'Department manager', '2025-05-08 04:09:05'),
(3, 'agent', 'Support agent', '2025-05-08 04:09:05'),
(5, 'executive', 'Executive', '2025-05-08 05:00:37');

-- --------------------------------------------------------

--
-- Table structure for table `sb_articles`
--

CREATE TABLE `sb_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(191) NOT NULL,
  `content` text NOT NULL,
  `editor_js` text NOT NULL,
  `nav` text DEFAULT NULL,
  `link` varchar(191) DEFAULT NULL,
  `category` varchar(191) DEFAULT NULL,
  `parent_category` varchar(191) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `update_time` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sb_conversations`
--

CREATE TABLE `sb_conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `creation_time` datetime NOT NULL,
  `status_code` tinyint(4) DEFAULT 0,
  `department` tinyint(4) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `source` varchar(2) DEFAULT NULL,
  `extra` varchar(191) DEFAULT NULL,
  `extra_2` varchar(191) DEFAULT NULL,
  `extra_3` varchar(191) DEFAULT NULL,
  `tags` varchar(191) DEFAULT NULL,
  `converted_to_ticket` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sb_conversations`
--

INSERT INTO `sb_conversations` (`id`, `user_id`, `title`, `creation_time`, `status_code`, `department`, `agent_id`, `source`, `extra`, `extra_2`, `extra_3`, `tags`, `converted_to_ticket`) VALUES
(1, 2, '', '2025-05-06 07:55:24', 2, NULL, NULL, '', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sb_messages`
--

CREATE TABLE `sb_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `creation_time` datetime NOT NULL,
  `status_code` tinyint(4) DEFAULT 0,
  `attachments` text DEFAULT NULL,
  `payload` text DEFAULT NULL,
  `conversation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `sb_messages`
--

INSERT INTO `sb_messages` (`id`, `user_id`, `message`, `creation_time`, `status_code`, `attachments`, `payload`, `conversation_id`) VALUES
(1, 2, 'hi 11', '2025-05-06 07:55:24', 0, '', '', 1),
(2, 1, 'yes', '2025-05-06 07:55:42', 2, '', '', 1),
(3, 2, 'msg', '2025-05-06 07:56:16', 0, '', '', 1),
(4, 1, '11', '2025-05-06 07:56:29', 2, '', '', 1),
(5, 2, '44', '2025-05-06 07:56:46', 0, '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sb_reports`
--

CREATE TABLE `sb_reports` (
  `id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `creation_time` date NOT NULL,
  `external_id` int(11) DEFAULT NULL,
  `extra` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sb_settings`
--

CREATE TABLE `sb_settings` (
  `name` varchar(191) NOT NULL,
  `value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sb_settings`
--

INSERT INTO `sb_settings` (`name`, `value`) VALUES
('active_agents_conversations', '{\"1\":[0,1746873979]}'),
('active_apps', '[\"dialogflow\",\"whatsapp\",\"telegram\",\"messenger\",\"viber\",\"tickets\",\"line\"]'),
('aecommerce-carts-last-clean', '\"10\"'),
('cron', '\"05\"'),
('pusher-online-users', '[[],1746855098]');

-- --------------------------------------------------------

--
-- Table structure for table `sb_tickets`
--

CREATE TABLE `sb_tickets` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `priority_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `tags` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `creation_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_id` int(11) NOT NULL DEFAULT 1,
  `conversation_id` int(11) NOT NULL,
  `last_reply` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sb_tickets`
--

INSERT INTO `sb_tickets` (`id`, `subject`, `contact_id`, `assigned_to`, `priority_id`, `service_id`, `department_id`, `tags`, `description`, `creation_time`, `updated_at`, `status_id`, `conversation_id`, `last_reply`) VALUES
(1, 'First Test ticket', 1, 2, 1, 2, 1, '', 'Internet connection broke.', '2025-05-08 05:20:13', '2025-05-09 09:51:12', 5, 0, NULL),
(3, 'Test ticket 2', 1, 1, 4, 1, 1, '', 'Printer needs to be replaced', '2025-05-08 10:31:51', '2025-05-10 07:06:00', 3, 0, NULL),
(5, 'Test ticket 3', 2, 1, 3, 1, 1, '', 'test', '2025-05-08 11:30:57', '2025-05-10 07:05:57', 6, 0, NULL),
(6, 'Test ticket 4', 1, 1, 3, 1, 1, '', 'test task', '2025-05-09 09:52:08', '2025-05-09 09:52:32', 3, 0, NULL),
(8, 'Guest ticket', 1, 1, 4, 1, 1, '', 'First Guest ticket', '2025-05-09 10:15:31', '2025-05-09 10:18:32', 1, 0, NULL),
(9, 'Test Guest ticket', NULL, 1, 4, 1, 1, '', 'First guest ticket', '2025-05-09 10:33:15', '2025-05-09 10:33:15', 1, 0, NULL),
(10, 'New Guest ticket', NULL, 1, 2, 3, 1, '', 'test 55', '2025-05-09 11:25:15', '2025-05-10 07:03:42', 2, 0, NULL),
(11, 'Test ticket 3', 2, 1, 2, 1, 1, '', 'test', '2025-05-09 12:03:31', '2025-05-10 07:03:49', 2, 0, NULL),
(12, 'Test ticket 3', 2, 1, 2, 1, 1, '', 'test', '2025-05-09 12:03:31', '2025-05-09 12:03:43', 1, 0, NULL),
(13, 'Test ticket 3', 2, 1, 2, 1, 1, '', 'test', '2025-05-09 12:03:31', '2025-05-10 07:06:23', 5, 0, NULL),
(14, 'Test ticket 3', 2, 1, 1, 1, 1, '', 'test', '2025-05-09 12:03:31', '2025-05-10 07:04:26', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sb_users`
--

CREATE TABLE `sb_users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `profile_image` varchar(191) DEFAULT NULL,
  `user_type` varchar(10) NOT NULL,
  `creation_time` datetime NOT NULL,
  `token` varchar(50) NOT NULL,
  `last_activity` datetime DEFAULT NULL,
  `typing` int(11) DEFAULT -1,
  `department` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sb_users`
--

INSERT INTO `sb_users` (`id`, `first_name`, `last_name`, `password`, `email`, `profile_image`, `user_type`, `creation_time`, `token`, `last_activity`, `typing`, `department`) VALUES
(1, 'Kirandeep', 'Singh', '$2y$10$yJM/PIvwmEh6EwMnjMx71OPsqr5m9Ntm99e/qoy2F6zzLjmr0YtIG', 'kirandeep.singh@xtecglobal.com', 'http://localhost/saassupport//script/media/user.svg', 'admin', '2025-05-06 07:45:30', '8d36f1340432b1f24bee5657d20ded1949fad08c', '2025-05-06 07:56:29', -1, NULL),
(2, 'User', '#12687', '', NULL, 'http://localhost/saassupport//script/media/user.svg', 'lead', '2025-05-06 07:55:24', 'bc6fca0bb92e278871ace69a781688c10d8de526', '2025-05-06 07:56:46', -1, NULL),
(3, 'Bot', '', '', NULL, 'http://localhost/saassupport//script/media/user.svg', 'bot', '2025-05-06 07:55:31', '8c5b36d14084d21060703a637603dcdd7e9846f7', '2025-05-06 07:55:31', -1, NULL),
(4, 'User', '2', '$2y$10$p31oQRw7VpJPIyzXUhJ8qOHepvpe/P8DqLLLvaSdd440T/q2GH5QO', 'user2@test.com', 'http://localhost/saassupport/script/uploads/10-05-25/3505181.png', 'user', '2025-05-10 03:41:14', '7ecb2a140f187a167eea24e3c5574ab79bc34a2e', '2025-05-10 03:41:14', -1, NULL),
(5, 'a', '', '', NULL, 'http://localhost/saassupport/script/uploads/10-05-25/9777641.png', 'user', '2025-05-10 10:09:59', '52e96ec7366439bcff9cfeb38c02b2b54ebc99e0', '2025-05-10 10:09:59', -1, NULL),
(6, 'a', '', '', NULL, 'http://localhost/saassupport/script/uploads/10-05-25/3672620.png', 'user', '2025-05-10 11:13:30', 'f835526a563688545f6b78015ef722e5662e27da', '2025-05-10 11:13:30', -1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sb_users_data`
--

CREATE TABLE `sb_users_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sb_users_data`
--

INSERT INTO `sb_users_data` (`id`, `user_id`, `slug`, `name`, `value`) VALUES
(1, 2, 'browser', 'Browser', 'Chrome'),
(2, 2, 'browser_language', 'Language', 'EN'),
(3, 2, 'os', 'OS', 'Windows 10'),
(4, 2, 'current_url', 'Current URL', 'http://localhost/chat.php');

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
(4, 'Waiting for Customer', '#1E90FF', '2025-05-09 09:39:19'),
(5, 'Answered', '#00FF00', '2025-05-09 09:39:19'),
(6, 'Closed', '#808080', '2025-05-09 09:39:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@ticket-system.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Admin', 1, 'active', '2025-05-08 04:09:05', '2025-05-08 04:09:05'),
(2, 'manager', 'manager@test.com', '$2y$10$ZS97dMREy6Kud6H5jYkm3ujmDsJSdJ6NrqTrECZ.IL6oYmhUlwUqG', 'Manager', 2, 'inactive', '2025-05-08 05:06:08', '2025-05-09 09:00:41');

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
-- Indexes for table `sb_articles`
--
ALTER TABLE `sb_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `sb_conversations`
--
ALTER TABLE `sb_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sb_messages`
--
ALTER TABLE `sb_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `conversation_id` (`conversation_id`);

--
-- Indexes for table `sb_reports`
--
ALTER TABLE `sb_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sb_settings`
--
ALTER TABLE `sb_settings`
  ADD PRIMARY KEY (`name`);

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
-- Indexes for table `sb_users`
--
ALTER TABLE `sb_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `sb_users_data`
--
ALTER TABLE `sb_users_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sb_users_data_index` (`user_id`,`slug`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `priorities`
--
ALTER TABLE `priorities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sb_articles`
--
ALTER TABLE `sb_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sb_conversations`
--
ALTER TABLE `sb_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sb_messages`
--
ALTER TABLE `sb_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sb_reports`
--
ALTER TABLE `sb_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sb_tickets`
--
ALTER TABLE `sb_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sb_users`
--
ALTER TABLE `sb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sb_users_data`
--
ALTER TABLE `sb_users_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ticket_ccs`
--
ALTER TABLE `ticket_ccs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `ticket_status`
--
ALTER TABLE `ticket_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sb_articles`
--
ALTER TABLE `sb_articles`
  ADD CONSTRAINT `sb_articles_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `sb_articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sb_conversations`
--
ALTER TABLE `sb_conversations`
  ADD CONSTRAINT `sb_conversations_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `sb_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sb_conversations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `sb_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sb_messages`
--
ALTER TABLE `sb_messages`
  ADD CONSTRAINT `sb_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sb_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sb_messages_ibfk_2` FOREIGN KEY (`conversation_id`) REFERENCES `sb_conversations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sb_users_data`
--
ALTER TABLE `sb_users_data`
  ADD CONSTRAINT `sb_users_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sb_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD CONSTRAINT `ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`);

--
-- Constraints for table `ticket_ccs`
--
ALTER TABLE `ticket_ccs`
  ADD CONSTRAINT `ticket_ccs_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  ADD CONSTRAINT `ticket_ccs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
