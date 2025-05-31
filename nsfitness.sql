-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2024 at 01:06 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nsfitness`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(101, 'admin', 'password'),
(102, 'admin', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `trainer_id` varchar(20) NOT NULL,
  `package_id` int(20) NOT NULL,
  `batch` varchar(20) NOT NULL,
  `transaction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `gender`, `dob`, `trainer_id`, `package_id`, `batch`, `transaction_id`) VALUES
('20231', 'ADNAN', 'M', '2003-02-28', '1', 1, 'EVENING', 1001),
('20232', 'POOJA', 'F', '1999-05-13', '3', 2, 'EVENING', 1002),
('20233', 'SUHAS', 'M', '2003-12-01', '2', 6, 'MORNING', 1003),
('20234', 'RAJU', 'M', '2001-10-30', '1', 1, 'MORNING', 1004),
('20236', 'SUHASINI', 'F', '1990-01-20', '3', 6, 'MORNING', 2001);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `duration` varchar(20) NOT NULL,
  `price` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `duration`, `price`) VALUES
(1, 'MONTHLY WEIGHT TRAINING', '1 MONTH', '500'),
(2, 'QUATERLY WEIGHT TRAINING', '3 MONTHS', '1200'),
(3, 'MONTHLY + CARDIO', '1 MONTH', '800'),
(4, 'QUATERLY + CARDIO', '3 MONTHS', '2000'),
(6, 'GOLD: WT + CARDIO + PT', '3 MONTHS', '5000');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `id` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(20) NOT NULL,
  `experience` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`id`, `name`, `dob`, `gender`, `experience`) VALUES
('1', 'SHARIFF', '2002-07-08', 'M', '2 YEARS'),
('2', 'JAYRAM', '1989-12-05', 'M', '5 YEARS'),
('3', 'RANJITA', '1995-02-01', 'F', '2 YEARS');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `mode` varchar(20) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `admin_id`, `mode`, `date`) VALUES
(1001, 101, 'cash [500]', '2023-01-05'),
(1002, 102, 'UPI [800]', '2023-01-05'),
(1003, 102, 'CASH [5000]', '2023-01-07'),
(1004, 101, 'cash [500]', '2023-02-01'),
(1005, 101, 'CARD', '2023-02-03'),
(2001, 101, 'cash', '2023-02-03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainer` (`trainer_id`),
  ADD KEY `package` (`package_id`),
  ADD KEY `transaction` (`transaction_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `admin` (`admin_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `trainer` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`transaction_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
