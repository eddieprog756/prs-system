-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 11, 2024 at 08:58 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prs`
--

-- --------------------------------------------------------

--
-- Table structure for table `jobcards`
--

<<<<<<< HEAD
=======
DROP TABLE IF EXISTS `jobcards`;
>>>>>>> refs/remotes/prs-system/main
CREATE TABLE IF NOT EXISTS `jobcards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `JobCard_N0` varchar(50) NOT NULL,
  `Client_Name` varchar(100) NOT NULL,
  `Project_Name` varchar(100) NOT NULL,
  `Quantity` int NOT NULL,
  `Overall_Size` varchar(50) NOT NULL,
  `Delivery_Date` date NOT NULL,
  `Date_Delivered` date NOT NULL,
  `Job_Description` text NOT NULL,
  `Prepaired_By` varchar(100) NOT NULL,
  `Total_Charged` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('project','sales_done','manager_approved','studio_done','workshop_done','accounts_done') NOT NULL DEFAULT 'project',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jobcards`
--

INSERT INTO `jobcards` (`id`, `Date`, `Time`, `JobCard_N0`, `Client_Name`, `Project_Name`, `Quantity`, `Overall_Size`, `Delivery_Date`, `Date_Delivered`, `Job_Description`, `Prepaired_By`, `Total_Charged`, `created_at`, `status`) VALUES
(1, '2024-07-30', '14:58:00', '9797', 'Edward Tembo', 'PRS System', 2, '67', '2024-07-31', '2024-07-29', 'iohihiohihihio', 'Edward TEmbo', 900000.00, '2024-07-30 13:05:14', 'sales_done');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 1, '88b3b1b2828096bea647c16d96b57b7f80bdb6aa86fa1dfb53492275850758e1', '2024-09-09 18:04:21'),
(2, 1, '09cf4d33603c842c7f00fce37e8ddf22f9d5b9707c3ccc7c8e3040ecee998713', '2024-09-09 18:29:15'),
(3, 1, '23bbd7bf4cba715186c811c25bf5bc6a85f9daf591e851b7808ac78623cb551b', '2024-09-09 18:29:43'),
(4, 1, 'dabbb2527fa851bc4a9e2dc1607502a927a4f5ca62c43007fd8996a6df51af92', '2024-09-09 18:30:13'),
(5, 1, 'd1c18287e8429e74ec79d283d9a55b9755a1ed78eed493ac9110f0a3dd31a79e', '2024-09-09 20:09:54'),
(6, 1, '4f4fc156416ebd95db2303326257e3515f2cd3278201c38687a92290304eda34', '2024-09-09 20:10:05'),
(7, 1, '95352f532698f1a7ae3b3249a2ff71125bf6e222d535184cc6749f2b8edb3826', '2024-09-09 20:10:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','designer','sales') NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `full_name`, `created_at`) VALUES
(1, 'Eddie', '$2y$10$RW1b6YKAS02FVUFIMy2QD.cnHxNUKE5Qh0jjWgVIw18I7jSQX8ALS', 'admin', 'temboedward756@gmail.com', 'Admin User', '2024-07-30 01:04:03'),
(2, 'Designer', '$2y$10$cx6Y5TKXueLA.tKh2MVtwuIOTZbvTEwKshAcfxYx7/5EMU4O3SIf2', 'designer', 'designer@example.com', 'Designer User', '2024-07-30 01:04:03'),
(3, 'Sales', '$2y$10$OXPK8s1zquRE5wQmFSXAguB3Sdppq7Oh1cgeHV7Ngi93IL7UAv53y', 'sales', 'sales@example.com', 'Sales User', '2024-07-30 01:04:03');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
