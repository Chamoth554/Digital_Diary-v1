-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2025 at 03:42 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digital_diary`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_logs`
--

CREATE TABLE `daily_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `notes` text NOT NULL,
  `tasks` text NOT NULL,
  `time_spent` int(11) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `daily_logs`
--

INSERT INTO `daily_logs` (`id`, `user_id`, `date`, `notes`, `tasks`, `time_spent`, `file_path`, `status`) VALUES
(1, 3, '2025-01-26', ' java spring boot ', 'Create java spring boot project', 30, NULL, 'Approved'),
(2, 3, '2025-01-26', 'Frontend', 'Create figma design', 30, NULL, 'Approved'),
(3, 3, '2025-01-26', 'Backend', 'Create backend using java', 20, NULL, 'Rejected');

-- --------------------------------------------------------

--
-- Table structure for table `mentor_feedback`
--

CREATE TABLE `mentor_feedback` (
  `id` int(11) NOT NULL,
  `log_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `feedback_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Intern','Mentor','Admin') DEFAULT 'Intern'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(3, 'Chamoth', 'chamoth@gmail.com', '$2y$10$g4NwNEopWlLSfIu0vjCnaeq2dzt9Rzr94gpF5vZoWjDtTwlIkp9MG', 'Intern'),
(5, 'kumara', 'kumara@gmail.com', '$2y$10$aMm8toXH/u/DDborIP0KuO9mscYfheFdG5I3yEyWHaduE.eFyC9/u', 'Mentor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_logs`
--
ALTER TABLE `daily_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mentor_feedback`
--
ALTER TABLE `mentor_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_id` (`log_id`),
  ADD KEY `mentor_id` (`mentor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_logs`
--
ALTER TABLE `daily_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mentor_feedback`
--
ALTER TABLE `mentor_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_logs`
--
ALTER TABLE `daily_logs`
  ADD CONSTRAINT `daily_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `mentor_feedback`
--
ALTER TABLE `mentor_feedback`
  ADD CONSTRAINT `mentor_feedback_ibfk_1` FOREIGN KEY (`log_id`) REFERENCES `daily_logs` (`id`),
  ADD CONSTRAINT `mentor_feedback_ibfk_2` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
