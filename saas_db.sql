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
-- Database: `saas_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `admin_id`, `email`) VALUES
(20, 53, 'technical_agent@xtcglobal.com');

-- --------------------------------------------------------

--
-- Table structure for table `membership_counter`
--

CREATE TABLE `membership_counter` (
  `user_id` int(11) DEFAULT NULL,
  `count` int(11) NOT NULL,
  `date` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `membership_counter`
--

INSERT INTO `membership_counter` (`user_id`, `count`, `date`) VALUES
(53, 14, '04-25'),
(53, 6, '05-25'),
(58, 5, '05-25');

-- --------------------------------------------------------

--
-- Table structure for table `messenger`
--

CREATE TABLE `messenger` (
  `token` varchar(255) NOT NULL,
  `page_id` varchar(255) NOT NULL,
  `page_token` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `name` varchar(50) NOT NULL,
  `value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`name`, `value`) VALUES
('customer-settings', '{\"piping\":[],\"training\":[]}'),
('last_cron', '1746460803'),
('last_cron_1860', '1746514803');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT '',
  `password` varchar(100) NOT NULL,
  `membership` varchar(255) NOT NULL,
  `membership_expiration` varchar(10) NOT NULL,
  `token` varchar(50) NOT NULL,
  `last_activity` datetime NOT NULL,
  `creation_time` datetime NOT NULL,
  `email_confirmed` tinyint(1) NOT NULL,
  `phone_confirmed` tinyint(1) NOT NULL,
  `customer_id` varchar(255) NOT NULL,
  `extra` varchar(255) DEFAULT NULL,
  `credits` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `password`, `membership`, `membership_expiration`, `token`, `last_activity`, `creation_time`, `email_confirmed`, `phone_confirmed`, `customer_id`, `extra`, `credits`) VALUES
(52, 'test', 'test', 'test1@test.com', NULL, '$2y$10$covEORix6lLTDE6Fe.ZGFOtQ7qjKp0u5gNr17wx10R3ahFrPogwD.', '0', '', 'bcd370e6c7963f1694c53c0c8ead9ea427e90d62', '2025-04-11 05:10:32', '2025-04-11 05:10:32', 0, 0, '', '', 0.05),
(53, 'Ahsan', 'Elahi', 'ahsan@xtecglobal.com', '12345678', '$2y$10$nOOoKW9RJUtfEyIM5wldz.K/CbOh4T5Ue1moo2RLK1VAmwvTTwXjS', '0', '', '02cca27b663e5549617dde45a0ece2221dc94c8d', '2025-04-11 06:04:56', '2025-04-11 06:04:56', 0, 0, '', '', 0.05),
(54, 'Shilpa', 'Dhiman', 'shilpa.dhiman@xtecglobal.com', NULL, '$2y$10$wn2y0Gyj5jaExdPs.E42muBVGlCDl0qD33cIy2Gk3Fzj99D82AGZK', '0', '11-04-25', 'f704aac1d92ca458b8f23dd91cdc57a1349a80b0', '2025-04-11 06:19:33', '2025-04-11 06:19:33', 0, 0, '', '', 0.05),
(55, 'Ahsan', 'Personal', 'ahsanxtecglobal@gmail.com', NULL, '$2y$10$ZW4eAhDOWxKD7uYXSnl1O.P1SCQm.W5NFgYSr0kjGamJ4Mj7EKwVm', '0', '', '202a3b4269a7c7efb3f3ac14a7e9a40db9353b3f', '2025-04-14 01:28:25', '2025-04-14 01:28:25', 0, 0, '', '', 0.05),
(56, 'Komal', 'Sighn', 'komal@xtecglobal.com', NULL, '$2y$10$WCtet5T5a33fh3ioRVLTTeBUdJ9d7yJ/ZmXKXHPHp0h8YdLSXcj6G', '0', '', '85b27535bf2a313158f049cb1a80aebf7715d655', '2025-04-14 03:31:33', '2025-04-14 03:31:33', 0, 0, '', '', 0.05),
(57, 'Federico', 'Schiocchet', 's@gmail.com', NULL, '$2y$10$OET1Ush7t8bygEDECgcCdOK.xi/E/X.wQK6S0KNiILmChvF4Qc8DK', '0', '', 'a37ca4da3648380e4af5da18ab00a532b9f86318', '2025-04-14 08:54:56', '2025-04-14 08:54:56', 0, 0, '', '', 0.05),
(58, 'Kirandeep', 'Singh', 'kirandeep.singh@xtecglobal.com', NULL, '$2y$10$MA4YOdkY2fQoFH07zXit9eU8QkEdh6BfDqu/NShmPQioTe2bImmn.', '0', '', 'e43f2dcf8bb2028766bc733da37ff74543d699b5', '2025-05-06 07:45:30', '2025-05-06 07:45:30', 0, 0, '', '', 0.05);

-- --------------------------------------------------------

--
-- Table structure for table `users_data`
--

CREATE TABLE `users_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `slug` varchar(255) NOT NULL DEFAULT '0',
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_data`
--

INSERT INTO `users_data` (`id`, `user_id`, `slug`, `value`) VALUES
(329, 55, 'active_membership_cache', '[{\"price\":0,\"name\":\"Free\",\"id\":\"0\",\"period\":\"\",\"currency\":\"\",\"quota\":100,\"credits\":0.05,\"expiration\":false,\"count\":0,\"count_agents\":\"1\"},1744680506]'),
(336, 57, 'active_membership_cache', '[{\"price\":0,\"name\":\"Free\",\"id\":\"0\",\"period\":\"\",\"currency\":\"\",\"quota\":100,\"credits\":0.05,\"expiration\":false,\"count\":0,\"count_agents\":\"1\"},1744707298]'),
(345, 52, 'active_membership_cache', '[{\"price\":0,\"name\":\"Free\",\"id\":\"0\",\"period\":\"\",\"currency\":\"\",\"quota\":100,\"credits\":0.05,\"expiration\":false,\"count\":0,\"count_agents\":\"1\"},1744771836]'),
(347, 54, 'active_membership_cache', '[{\"price\":0,\"name\":\"Free\",\"id\":\"0\",\"period\":\"\",\"currency\":\"\",\"quota\":100,\"credits\":0.05,\"expiration\":\"11-04-25\",\"count\":0,\"count_agents\":\"1\"},1744778690]'),
(357, 56, 'active_membership_cache', '[{\"price\":0,\"name\":\"Free\",\"id\":\"0\",\"period\":\"\",\"currency\":\"\",\"quota\":100,\"credits\":0.05,\"expiration\":false,\"count\":0,\"count_agents\":\"1\"},1746163175]'),
(358, 53, 'active_membership_cache', '[{\"price\":0,\"name\":\"Free\",\"id\":\"0\",\"period\":\"\",\"currency\":\"\",\"quota\":100,\"credits\":0.05,\"expiration\":false,\"count\":\"6\",\"count_agents\":\"2\"},1746596609]'),
(359, 58, 'active_membership_cache', '[{\"price\":0,\"name\":\"Free\",\"id\":\"0\",\"period\":\"\",\"currency\":\"\",\"quota\":100,\"credits\":0.05,\"expiration\":false,\"count\":\"5\",\"count_agents\":\"1\"},1746933542]');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp`
--

CREATE TABLE `whatsapp` (
  `token` varchar(255) NOT NULL,
  `phone_number_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `FK_agents_users` (`admin_id`);

--
-- Indexes for table `membership_counter`
--
ALTER TABLE `membership_counter`
  ADD KEY `FK_membership_counter_users` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `users_data`
--
ALTER TABLE `users_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UD1` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users_data`
--
ALTER TABLE `users_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=360;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agents`
--
ALTER TABLE `agents`
  ADD CONSTRAINT `FK_agents_users` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `membership_counter`
--
ALTER TABLE `membership_counter`
  ADD CONSTRAINT `FK_membership_counter_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_data`
--
ALTER TABLE `users_data`
  ADD CONSTRAINT `UD1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
