-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2025 at 11:57 AM
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
-- Database: `hd_nexleon_db`
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
(1, 98, 'test@agent.com');

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
(98, 203, '07-25'),


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
('email_subject_follow_up', 'Still exploring Nexleon Helpdesk?'),
('email_subject_follow_up_2', 'Unlock full potential with a premium plan'),
('email_subject_membership_100', 'Your Nexleon Helpdesk account has been suspended'),
('email_subject_membership_90', 'You’ve used 90% of your message quota'),
('email_subject_no_credits', 'You’ve run out of message credits'),
('email_subject_reset_password', 'Reset your Nexleon Helpdesk password'),
('email_subject_verification_code_email', 'Verify your email address'),
('email_subject_welcome', 'Welcome to Nexleon Helpdesk, {user_name}!'),
('email_template_follow_up', 'Hi {user_name},\nIt’s been a week since you joined Nexleon Helpdesk!\nIf you haven’t fully explored our dashboard or features yet, now’s a great time. Let us know if you need help or consider upgrading for more advanced tools.'),
('email_template_follow_up_2', 'Hello {user_name},\nYou’re 30 days in — ready to go beyond the basics?\nUpgrade now to access more message capacity, multiple agents, and premium support. Our team is here to help you scale your support operation.'),
('email_template_membership_100', 'Hi {user_name},\nYour account has been suspended due to exceeded usage limits or an expired membership. Please log in to upgrade or renew your plan.'),
('email_template_membership_90', 'Hello {user_name},\nYou’ve used 90% of your allowed messages for this period. Please consider upgrading your plan or monitoring your usage to avoid service interruptions.'),
('email_template_no_credits', 'Hi {user_name},\nYour message credits are depleted. Please top up or upgrade your plan to continue communicating with your users.'),
('email_template_reset_password', 'Hi {user_name},\nTo reset your password, click the link below:\n{link}\nIf you didn’t request this, you can safely ignore it.'),
('email_template_verification_code_email', 'Your email verification code is: {code}\nEnter this in the Nexleon Helpdesk app to complete your verification.'),
('email_template_welcome', 'Hi {user_name},\nThanks for signing up with Nexleon Helpdesk. We’re here to help — feel free to explore your dashboard or reach out for assistance.'),
('last_cron', '1746460803'),
('last_cron_1860', '1746514803'),
('memberships', '[{\"id\":\"free\",\"price\":\"0\",\"currency\":\"usd\",\"period\":\"\",\"name\":\"Free\",\"quota\":\"100223\",\"quota_agents\":\"6\"},{\"id\":\"FcWLt\",\"price\":\"300\",\"currency\":\"usd\",\"period\":\"year\",\"name\":\"Plan One Test\",\"quota\":600000,\"quota_agents\":6},{\"id\":\"3zjob\",\"price\":\"400\",\"currency\":\"usd\",\"period\":\"month\",\"name\":\"Plan Test Two\",\"quota\":1000000,\"quota_agents\":\"10\"}]'),
('template_verification_code_phone', 'Your Nexleon Helpdesk verification code is: {code}.\nEnter this code in the app to verify your phone number.'),
('user-settings', '{\"text_embed_code\":\"Add the Nexleon chat widget to your website by pasting the code snippet below just before the closing <\\/body> tag on each page where you want the chat to appear.\\nOnce added, reload your site to see the chat in the bottom-right corner.\\nYou can manage widget settings from the dashboard.\",\"disclaimer\":\"\",\"text_welcome\":\"D Welcome message\",\"text_welcome_title\":\"Welcome Aboard!\",\"text_welcome_image\":\"https:\\/\\/hd.nexleon.com\\/account\\/media\\/logo.svg\",\"text_suspended\":\"Your account has been temporarily suspended because your message quota has been exceeded or your membership has expired.\\\\n\\\\nTo continue using Nexleon Helpdesk, please upgrade your plan or renew your subscription.\",\"text_suspended_title\":\"Account Suspended\",\"text_invoice\":\"Nexleon Pty Ltd.\\nsupport@nexleon.com\",\"color\":\"#155cfd\",\"color-2\":\"#0069f0\",\"registration-field-1\":\"Company Name\",\"registration-field-2\":\"Phone Number\",\"registration-field-3\":\"Job Title\",\"registration-field-4\":\"Referral Code\",\"css\":\"\",\"js\":\"\",\"css-front\":\"\",\"js-front\":\"\",\"referral-commission\":\"20\",\"referral-text\":\"Refer others and earn 20% of their subscription payments — paid monthly via PayPal.\\\\n\\\\nYour referral link is available in your dashboard.\",\"disable-apps\":\"false\",\"webhook-url\":\"\"}'),
('white-label', '50');

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
(98, 'Kirandeep', 'Singh', 'kirandeep.singh@xtecglobal.com', NULL, '$2y$10$lEYjIWMZde0vHom1BjZriehtQ28HUshBYLPlGxWHeu6vrENvo3ZiS', '0', '', '53d3e8898c45fd81c128c679d43bfff3b2efbee8', '2025-07-04 09:09:22', '2025-07-04 09:09:22', 0, 0, '', '', 0.05)
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
(538, 98, 'email_limit', '25-07-25|1'),
(539, 98, 'active_membership_cache', '[{\"id\":\"free\",\"price\":\"0\",\"currency\":\"usd\",\"period\":\"\",\"name\":\"Free\",\"quota\":\"100223\",\"quota_agents\":\"6\",\"credits\":0.05,\"expiration\":false,\"count\":\"203\",\"count_agents\":\"2\"},1753525989]');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `users_data`
--
ALTER TABLE `users_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=540;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
