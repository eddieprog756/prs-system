-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 01, 2024 at 07:44 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

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

DROP TABLE IF EXISTS `jobcards`;
CREATE TABLE IF NOT EXISTS `jobcards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `JobCard_N0` varchar(50) NOT NULL,
  `Client_Name` varchar(100) NOT NULL,
  `Project_Name` varchar(100) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Overall_Size` varchar(50) NOT NULL,
  `Delivery_Date` date NOT NULL,
  `Job_Description` text NOT NULL,
  `Prepaired_By` varchar(100) NOT NULL,
  `Total_Charged` decimal(10,2) NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('project','sales_done','manager_approved','studio_done','workshop_done','accounts_done') NOT NULL DEFAULT 'project',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jobcards`
--

INSERT INTO `jobcards` (`id`, `Date`, `Time`, `JobCard_N0`, `Client_Name`, `Project_Name`, `Quantity`, `Overall_Size`, `Delivery_Date`, `Job_Description`, `Prepaired_By`, `Total_Charged`, `payment_proof`, `created_at`, `status`) VALUES
(8, '2024-11-04', '17:36:50', 'JCN09801', 'NBM', 'Posters and Books', 68, '700', '2024-11-25', 'Posters and Books for National Bank.', 'Sales User', '320000.00', NULL, '2024-11-03 22:00:00', 'accounts_done'),
(9, '2024-11-04', '17:48:04', 'JCN09802', 'NBS Bank', 'Stationery', 800, '800', '2024-10-15', 'Send Stationery to reserve', 'Sales User', '2600000.00', NULL, '2024-11-03 22:00:00', 'manager_approved'),
(6, '2024-11-01', '19:19:38', 'JCN09799', 'Deloitte & Touche', 'Banners', 24, '600', '2024-11-28', 'Banners for Projects', 'Admin User', '800000.00', NULL, '2024-10-31 22:00:00', 'manager_approved'),
(7, '2024-11-01', '19:47:07', 'JCN09800', 'TNM', 'TNM Poster', 33, '900', '2024-11-28', 'We need bruh ', 'Admin User', '120000.00', NULL, '2024-10-31 22:00:00', 'accounts_done'),
(14, '2024-11-10', '08:39:25', 'JCN09803', 'Liverpool Trust', 'Banners and Rollers', 20, '900', '2024-12-26', 'Printed Banners and Rollers ', 'Sales User', '120000.00', NULL, '2024-11-09 22:00:00', 'manager_approved'),
(18, '2024-12-01', '14:57:58', 'JCN09805', 'Eicher Industries', 'Handouts', 100, '1212', '2024-12-24', 'For Training new hires', 'Sales User', '1200000.00', './uploads/payment_proofs/WhatsApp Image 2024-06-26 at 01.58.50_422cf255.jpg', '2024-11-30 22:00:00', 'sales_done');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `code`, `expires_at`) VALUES
(1, 1, '$2y$10$DwRPQwiUSnneFcsFfaXEBeVlLEm8prHaCgskkRJc7rDb5TK5A9Ol.', '2024-11-17 19:53:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','designer','sales','studio','workshop','accounts') DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_pic` varchar(255) DEFAULT './Images/default_profile.JPG',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `full_name`, `created_at`, `profile_pic`, `status`) VALUES
(1, 'Manager', '$2y$10$igujLlK2ji5RElX/m25Qzuw4iH3w3w0QXv/AIFiMoGDABInRUSJc6', 'admin', 'temboedward756@gmail.com', 'Manager User', '2024-07-29 23:04:03', './Images/afsff.png', 1),
(2, 'Designer', '$2y$10$cx6Y5TKXueLA.tKh2MVtwuIOTZbvTEwKshAcfxYx7/5EMU4O3SIf2', 'workshop', 'codeverse.mw@gmail.com', 'Designer User', '2024-07-29 23:04:03', './Images/default_profile.JPG', 0),
(3, 'Adminstator', '$2y$10$OXPK8s1zquRE5wQmFSXAguB3Sdppq7Oh1cgeHV7Ngi93IL7UAv53y', 'sales', 'sales@example.com', 'Sales User', '2024-07-29 23:04:03', './Images/afsff.png', 1),
(4, 'George', '$2y$10$JvK252GaytCEkYFo8PnnhOR2O6qDvUep0/2VLzBc2woxbEXRNpGuO', 'workshop', 'temboedward756+12@gmail.com', 'Edward', '2024-09-18 12:56:48', './Images/default_profile.JPG', 0),
(10, 'Mr G2', '$2y$10$U0DqEu8wLt7aFlmehQOvEOVocIXNlyUftuiLTXbqGU1h/PmQX0sRG', 'workshop', 'georgekumwenda0+mrg@gmail.com', 'George Kumwenda', '2024-11-01 17:39:56', './Images/default_profile.JPG', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
