-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2025 at 12:22 PM
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
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `file_paths`
--

CREATE TABLE `file_paths` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp(),
  `file_paths` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file_paths`
--

INSERT INTO `file_paths` (`id`, `name`, `path`, `description`, `uploaded_at`, `file_paths`) VALUES
(15, 'ad.pdf', '', 'ad', '2025-01-21 20:33:38', 'uploads/ad.pdf'),
(16, 'aakashphoto.pdf', '', 'asda', '2025-01-21 20:44:06', 'uploads/aakashphoto.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `author` int(11) NOT NULL,
  `date` date NOT NULL,
  `added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `author`, `date`, `added`) VALUES
(1, 'trial', '<p>trial....(edited)</p>', 1, '2024-12-16', '2024-12-16 23:18:19'),
(2, 'aa', '<p>aa</p>', 1, '2024-12-16', '2024-12-16 23:18:34'),
(3, 'aa', '<p>aa</p>', 1, '2024-12-11', '2024-12-16 23:42:40'),
(4, 'aa', '<p>aa</p>', 1, '2024-12-16', '2024-12-16 23:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(10) UNSIGNED NOT NULL,
  `subject_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`) VALUES
(1, 'Mathematics'),
(2, 'Science'),
(3, 'History'),
(4, 'English');

-- --------------------------------------------------------

--
-- Table structure for table `subject_marks`
--

CREATE TABLE `subject_marks` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `subject_id` int(10) UNSIGNED NOT NULL,
  `ca1_marks` int(11) DEFAULT 0,
  `ca2_marks` int(11) DEFAULT 0,
  `ut1_marks` int(11) DEFAULT 0,
  `term_test_marks` int(11) DEFAULT 0,
  `project_marks` int(11) DEFAULT 0,
  `total_marks` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_marks`
--

INSERT INTO `subject_marks` (`id`, `user_id`, `subject_id`, `ca1_marks`, `ca2_marks`, `ut1_marks`, `term_test_marks`, `project_marks`, `total_marks`) VALUES
(1, 3, 1, 3, 3, 3, 3, 3, 15),
(2, 4, 1, 3, 3, 3, 3, 3, 15),
(3, 5, 1, 33, 3, 3, 3, 3, 45),
(4, 6, 1, 3, 3, 3, 3, 3, 15),
(5, 7, 1, 3, 3, 3, 3, 3, 15),
(6, 3, 2, 2, 2, 2, 2, 2, 10),
(7, 4, 2, 2, 2, 2, 2, 2, 10),
(8, 5, 2, 2, 2, 2, 2, 2, 10),
(9, 6, 2, 2, 2, 2, 2, 2, 10),
(10, 7, 2, 2, 2, 2, 2, 2, 10),
(11, 3, 3, 3, 3, 3, 3, 3, 15),
(12, 4, 3, 3, 3, 3, 3, 3, 15),
(13, 5, 3, 3, 3, 3, 3, 3, 15),
(14, 6, 3, 3, 3, 3, 3, 3, 15),
(15, 7, 3, 3, 13, 3, 3, 3, 25),
(16, 3, 4, 3, 3, 3, 3, 3, 15),
(17, 4, 4, 3, 3, 3, 3, 3, 15),
(18, 5, 4, 3, 3, 3, 3, 3, 15),
(19, 6, 4, 3, 3, 3, 3, 3, 15),
(20, 7, 4, 3, 3, 3, 3, 3, 15);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `added` datetime NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `active`, `added`, `role`) VALUES
(1, 'admin', 'admin@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2024-12-16 21:07:32', 'admin'),
(3, 'aakash', 'aakash@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2024-12-16 21:13:17', 'user'),
(4, 'adesh', 'adesh@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2024-12-16 21:22:14', 'user'),
(5, '1', '1@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2025-01-05 12:01:26', 'user'),
(6, '2', '2@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2025-01-05 12:02:12', 'user'),
(7, '3', '3@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2025-01-05 12:02:32', 'user'),
(8, 'admin1', 'admin1@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2025-01-05 14:36:02', 'admin'),
(9, 'admin2', 'admin2@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2025-01-05 14:36:58', 'admin'),
(10, 'admin3', 'admin3@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2025-01-05 14:37:20', 'admin'),
(11, 'admin4', 'admin4@mail.com', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 1, '2025-01-05 14:37:43', 'admin'),
(12, 'ades', 'aa@mail.com', 'secret', 0, '2025-01-22 21:01:33', 'user'),
(16, 'John Doedgd', 'joasdhn@example.com', '$2y$10$3hAhpVRQZQk3vCh7SJnoSOG1TzrFhjkkzuUrWvj87vVFzNCXVJQ3i', 1, '2025-01-29 16:15:54', 'user'),
(17, 'Jane Smithdfg', 'jaasdane@example.com', '$2y$10$hBINl9Ezp3nhv1kkRSeh..ex9VVNKOfAbY2yDLZVG0RTRwya0l09i', 1, '2025-01-29 16:15:54', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `video_paths`
--

CREATE TABLE `video_paths` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `video_paths` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_paths`
--

INSERT INTO `video_paths` (`id`, `name`, `video_paths`, `description`, `uploaded_at`) VALUES
(5, 'Ball dropping.mp4', 'uploads/videos/Ball dropping.mp4', 'awdawd', '2025-01-21 21:09:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file_paths`
--
ALTER TABLE `file_paths`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject_marks`
--
ALTER TABLE `subject_marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_paths`
--
ALTER TABLE `video_paths`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `file_paths`
--
ALTER TABLE `file_paths`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subject_marks`
--
ALTER TABLE `subject_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `video_paths`
--
ALTER TABLE `video_paths`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subject_marks`
--
ALTER TABLE `subject_marks`
  ADD CONSTRAINT `subject_marks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_marks_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
