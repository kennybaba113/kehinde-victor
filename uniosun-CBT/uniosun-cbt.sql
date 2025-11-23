-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2025 at 01:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uniosun-cbt`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `email`, `password`, `created_at`) VALUES
(2, 'admin', 'admin@example.com', '$2y$10$tx3lFGG8fRhp4SMGQx4Coek2EKfYuPu4cNLuTdI1FxY45X1IDtSyC', '2025-11-13 22:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_title` varchar(150) NOT NULL,
  `level` enum('100','200','300','400','500','600') NOT NULL,
  `semester` enum('First','Second') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `department`, `course_code`, `course_title`, `level`, `semester`) VALUES
(1, 'Accounting', 'ACC101', 'Introduction to Accounting I', '100', 'First'),
(2, 'Accounting', 'ACC102', 'Introduction to Accounting II', '100', 'Second'),
(3, 'Accounting', 'ACC201', 'Financial Accounting I', '200', 'First'),
(4, 'Accounting', 'ACC202', 'Financial Accounting II', '200', 'Second'),
(5, 'Accounting', 'ACC203', 'Cost Accounting I', '200', 'First'),
(6, 'Accounting', 'ACC204', 'Cost Accounting II', '200', 'Second'),
(7, 'Accounting', 'ACC301', 'Advanced Financial Accounting I', '300', 'First'),
(8, 'Accounting', 'ACC302', 'Advanced Financial Accounting II', '300', 'Second'),
(10, 'Anatomy', 'ANA 205', 'upper limbs', '100', 'First'),
(11, 'Anatomy', 'ANA 205', 'upper limbs', '100', 'First'),
(12, 'Anatomy', 'ANA 205', 'upper limbs', '100', 'First'),
(13, 'Anatomy', 'ANA 206', 'upper limbs', '100', 'First'),
(14, 'Anatomy', 'ANA 205', 'EMBROYLOGY', '200', 'First');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `department_name` varchar(150) NOT NULL,
  `max_level` int(11) NOT NULL DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `department_name`, `max_level`) VALUES
(0, 'Anatomy', 200),
(0, 'Physiology', 400);

-- --------------------------------------------------------

--
-- Table structure for table `department_change_requests`
--

CREATE TABLE `department_change_requests` (
  `id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `old_department` varchar(150) NOT NULL,
  `new_department` varchar(150) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `date_requested` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_reviewed` timestamp NULL DEFAULT NULL,
  `admin_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_change_requests`
--

INSERT INTO `department_change_requests` (`id`, `matric_number`, `old_department`, `new_department`, `document_path`, `status`, `date_requested`, `date_reviewed`, `admin_comment`) VALUES
(1, '2023/49154', 'Anatomy', 'Physiology', 'uploads/deptchange_202349154_1763813812.jpg', 'rejected', '2025-11-22 12:16:52', '2025-11-22 12:20:55', ''),
(2, '2023/49154', 'Anatomy', 'Physiology', 'uploads/deptchange_202349154_1763813829.jpg', 'rejected', '2025-11-22 12:17:09', '2025-11-22 12:20:59', '');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `faculty` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `rank` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.png',
  `gender` enum('Male','Female') DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `staff_id`, `full_name`, `email`, `department`, `faculty`, `phone_number`, `rank`, `profile_picture`, `gender`, `password`, `status`, `created_at`) VALUES
(7, '123456', 'kehinde victor', 'krhindevictor@gmail.com', 'Anatomy', 'Health science', '07040432828', 'Doctor', 'uploads/profile_123456.jpg', 'Male', '$2y$10$YSeJ75np62Uu.nJAUhRGgu9orEVu6/dnwdavH5J9MABMMdKNe.gmC', 1, '2025-10-30 10:58:18'),
(14, '222222', 'kehinde victor', 'kehindevictor70@gmail.com', 'Anatomy', NULL, NULL, NULL, 'default.png', 'Male', '$2y$10$kmT.cRfj1tyRBETwLSFhX.1Ps8kHK4dFBXc3Z5EioEmO2yHXbN2HO', 1, '2025-11-15 14:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `level_name` varchar(10) NOT NULL,
  `level_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`id`, `level_name`, `level_value`) VALUES
(1, '100', 100),
(2, '200', 200),
(3, '300', 300),
(4, '400', 400),
(5, '500', 500),
(6, '600', 600);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `question_type` enum('Objective','Theory') NOT NULL,
  `question_text` text NOT NULL,
  `option_a` text DEFAULT NULL,
  `option_b` text DEFAULT NULL,
  `option_c` text DEFAULT NULL,
  `option_d` text DEFAULT NULL,
  `correct_answers` text DEFAULT NULL,
  `marks` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `question_number` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `test_id`, `question_type`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answers`, `marks`, `created_at`, `is_active`, `question_number`) VALUES
(22, 8, 'Objective', 'jyeseetetr', 'swytrwrt', 'tswywwsyrr', 'drrdjrjt', 'yesesueuu', 'A', 1, '2025-11-08 20:29:07', 1, 1),
(23, 8, 'Objective', ',xfcyrrdurxurdur', 'wa3qa3qq', 'rtsaeasadd', 'rsrsseeessr', 'rsesdrsxrdsdrs', 'B', 1, '2025-11-08 20:45:47', 1, 2),
(24, 8, 'Objective', 'tgtfsrsdssdsrsrs', 'rsrsfsrsdsrdsdsx', 'rfsfsrsrsrsdsdsds', 'srsrfsrsasssrrs', 'rsrsfssfsfsf', 'B', 1, '2025-11-08 20:46:14', 1, 3),
(25, 8, 'Objective', 'tadrydrdffffgzx', 'tysyscfscyscfscf', 'fa', 'ac', 'fcac', 'B,C', 1, '2025-11-08 20:46:34', 1, 4),
(26, 8, 'Objective', 'ttydasrdfrrftascs', 'stryscrscfcsrcscs', 'ytsdrdsrdfrdfg', 'effcc', 'cefecfce', 'B,D', 1, '2025-11-08 20:46:56', 1, 5),
(27, 8, 'Objective', 'jtdcytjyrcdyrt', 'acfcfa', 'scdfvfs', 'vsf', 'aCAfv', 'B', 1, '2025-11-08 20:47:17', 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `test_id` int(11) NOT NULL,
  `score` int(11) DEFAULT 0,
  `total_questions` int(11) DEFAULT 0,
  `correct_answers` int(11) DEFAULT 0,
  `date_taken` datetime DEFAULT current_timestamp(),
  `duration_used` int(11) DEFAULT 0,
  `status` enum('completed','in_progress') DEFAULT 'completed',
  `submitted_via` varchar(50) DEFAULT 'manual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `matric_number`, `test_id`, `score`, `total_questions`, `correct_answers`, `date_taken`, `duration_used`, `status`, `submitted_via`) VALUES
(44, '2023/49152', 8, 1, 1, 0, '2025-11-08 12:43:13', 1, 'completed', 'web'),
(45, '2023/55555', 8, 4, 6, 4, '2025-11-08 12:49:00', 1, 'completed', 'web'),
(46, '2023/55555', 8, 4, 6, 4, '2025-11-08 12:49:01', 1, 'completed', 'web');

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `id` int(11) NOT NULL,
  `semester_name` enum('First','Second') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`id`, `semester_name`) VALUES
(1, 'First'),
(8, 'Second');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `level` varchar(10) NOT NULL,
  `department` varchar(50) NOT NULL,
  `semester` enum('First','Second') NOT NULL DEFAULT 'First',
  `courses` varchar(100) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `matric_number`, `full_name`, `email`, `gender`, `level`, `department`, `semester`, `courses`, `password`, `status`, `created_at`, `reset_token`, `token_expiry`) VALUES
(7, '2023/49152', 'Kehinde victor maphellous', 'kehindevictor70@gmail.com', 'male', '400', 'Anatomy', 'First', NULL, '$2y$10$mpm0qgSUDTJrBWXbM4dMCeKMFzvNhS4BTYa7Qe6037gX3KsVHmroC', 'active', '2025-11-18 17:15:18', '465387', '2025-11-18 19:46:39'),
(8, '2023/49154', 'kehinde victor', 'kehindevvvv11@gmail.com', 'male', '300', 'Anatomy', 'First', NULL, '$2y$10$y/1rZvZ1lg/R1JUKdJg/quU6m0utadkba9CCIvCCjnP/ZwrqYgN/.', 'active', '2025-11-19 13:50:32', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_accounts`
--

CREATE TABLE `student_accounts` (
  `id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `password` varchar(225) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `level` varchar(10) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `department` varchar(100) NOT NULL,
  `semester` enum('first','second') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `course_code` varchar(20) DEFAULT NULL,
  `course_title` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `level` enum('100','200','300','400','500','600') DEFAULT NULL,
  `semester` enum('First','Second') DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `max_students` int(11) NOT NULL,
  `allow_results` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `lecturer_id`, `course_code`, `course_title`, `department`, `level`, `semester`, `duration`, `max_students`, `allow_results`, `is_active`, `created_at`) VALUES
(8, 7, 'ANA 206', 'upper limbs', 'Anatomy', '100', 'First', 1, 2, 1, 0, '2025-11-08 20:28:39');

-- --------------------------------------------------------

--
-- Table structure for table `test_answers`
--

CREATE TABLE `test_answers` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `student_answer` text DEFAULT NULL,
  `marks` float DEFAULT 0,
  `score` float DEFAULT 0,
  `status` enum('correct','wrong','pending') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_answers`
--

INSERT INTO `test_answers` (`id`, `submission_id`, `test_id`, `question_id`, `matric_number`, `student_answer`, `marks`, `score`, `status`) VALUES
(183, 53, 8, 22, '2023/49152', 'B', 1, 1, ''),
(184, 55, 8, 22, '2023/55555', 'A', 1, 1, ''),
(185, 55, 8, 23, '2023/55555', 'B', 1, 1, ''),
(186, 55, 8, 24, '2023/55555', 'B', 1, 1, ''),
(187, 55, 8, 25, '2023/55555', 'B,D', 1, 0, ''),
(188, 55, 8, 26, '2023/55555', 'A', 1, 0, ''),
(189, 55, 8, 27, '2023/55555', 'B', 1, 1, ''),
(190, 56, 8, 22, '2023/55555', 'A', 1, 1, ''),
(191, 56, 8, 23, '2023/55555', 'B', 1, 1, ''),
(192, 56, 8, 24, '2023/55555', 'B', 1, 1, ''),
(193, 56, 8, 25, '2023/55555', 'B,D', 1, 0, ''),
(194, 56, 8, 26, '2023/55555', 'A', 1, 0, ''),
(195, 56, 8, 27, '2023/55555', 'B', 1, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `test_submissions`
--

CREATE TABLE `test_submissions` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `date_taken` datetime DEFAULT current_timestamp(),
  `duration_used` int(11) DEFAULT 0,
  `total_score` float DEFAULT 0,
  `total_marks` float DEFAULT 0,
  `status` enum('in progress','completed') DEFAULT 'in progress',
  `submitted_via` varchar(20) DEFAULT 'online'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_submissions`
--

INSERT INTO `test_submissions` (`id`, `test_id`, `matric_number`, `date_taken`, `duration_used`, `total_score`, `total_marks`, `status`, `submitted_via`) VALUES
(48, 6, '2023/55555', '2025-11-08 07:11:00', 0, 0, 0, '', 'web'),
(51, 3, '2023/49152', '2025-11-07 14:23:52', 0, 0, 0, '', 'web'),
(52, 8, '2023/49152', '2025-11-08 12:42:10', 0, 1, 0, '', 'web'),
(53, 8, '2023/49152', '2025-11-08 12:43:13', 1, 1, 1, 'completed', 'web'),
(54, 8, '2023/55555', '2025-11-08 12:48:17', 0, 0, 0, '', 'web'),
(55, 8, '2023/55555', '2025-11-08 12:48:59', 1, 4, 6, 'completed', 'web'),
(56, 8, '2023/55555', '2025-11-08 12:49:00', 1, 4, 6, 'completed', 'web');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_change_requests`
--
ALTER TABLE `department_change_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `level_value` (`level_value`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matric_number` (`matric_number`),
  ADD UNIQUE KEY `matric_number_2` (`matric_number`);

--
-- Indexes for table `student_accounts`
--
ALTER TABLE `student_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_answers`
--
ALTER TABLE `test_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_submissions`
--
ALTER TABLE `test_submissions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `department_change_requests`
--
ALTER TABLE `department_change_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `student_accounts`
--
ALTER TABLE `student_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `test_answers`
--
ALTER TABLE `test_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT for table `test_submissions`
--
ALTER TABLE `test_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
