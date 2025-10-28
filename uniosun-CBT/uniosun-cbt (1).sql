-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2025 at 01:35 PM
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
(8, 'Accounting', 'ACC302', 'Advanced Financial Accounting II', '300', 'Second');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `level` enum('100','200','300','400','500','600') NOT NULL,
  `department` enum('Accounting','Agricultural Economics','Agricultural Extension and Rural Development','Agronomy','Animal Science','Anatomy','Arabic and Islamic Studies','Architecture','Banking and Finance','Biochemistry','Biological Sciences','Biomedical Engineering','Botany','Building','Business Administration','Chemical Engineering','Chemistry','Christian Religious Studies','Civil Engineering','Common and Islamic Law','Computer Science','Computer Engineering','Crop Production','Cyber Security','Dentistry','Economics','Education and Accounting','Education and Biology','Education and Chemistry','Education and Economics','Education and English','Education and Geography','Education and Guidance and Counselling','Education and History','Education and Integrated Science','Education and Mathematics','Education and Physics','Education and Political Science','Education and Social Studies','Educational Management','Electrical and Electronics Engineering','English and International Studies','Entrepreneurship','Environmental Management','Estate Management','Fisheries and Wildlife Management','Food Science and Technology','Forestry','French','Geography','Geology','Guidance and Counselling','History and International Studies','Home Economics','Human Nutrition and Dietetics','Industrial Chemistry','Industrial Relations and Personnel Management','Information and Communication Technology','Information Science','Insurance','International Relations','Law','Library and Information Science','Linguistics','Linguistics and Communication Studies','Marketing','Mass Communication','Mathematics','Mechanical Engineering','Medical Laboratory Science','Medicine and Surgery','Microbiology','Nursing Science','Nutrition and Dietetics','Peace and Conflict Studies','Performing Arts','Philosophy','Physics with Electronics','Physiology','Plant Biology','Political Science','Public Administration','Public Health','Psychology','Quantity Surveying','Religious Studies','Sociology','Software Engineering','Statistics','Surveying and Geoinformatics','Teacher Education Science','Theatre Arts','Tourism Studies','Urban and Regional Planning','Vocational and Technical Education','Wildlife and Ecotourism Management','Yoruba') NOT NULL,
  `semester` enum('first','second') NOT NULL,
  `courses` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_accounts`
--

CREATE TABLE `student_accounts` (
  `id` int(11) NOT NULL,
  `matric_number` varchar(50) NOT NULL,
  `password` varchar(225) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_accounts`
--

INSERT INTO `student_accounts` (`id`, `matric_number`, `password`, `created_at`) VALUES
(1, '2023/49152', '$2y$10$N4cyyucFtbsPLHldrPN.p.LWxNBer4oeYIS0tM.2salV5WfxpTxKW', '2025-10-28 09:51:47'),
(2, '2024/45914', '$2y$10$Bed9mcRkZQHwv7YdWHHzYuhB3OYH82KKibJPPLXDwNNQMzWxSNQuy', '2025-10-28 09:53:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_accounts`
--
ALTER TABLE `student_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
