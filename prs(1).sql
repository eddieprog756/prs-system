-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 04, 2024 at 06:45 PM
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('project','sales_done','manager_approved','studio_done','workshop_done','accounts_done') NOT NULL DEFAULT 'project',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jobcards`
--

INSERT INTO `jobcards` (`id`, `Date`, `Time`, `JobCard_N0`, `Client_Name`, `Project_Name`, `Quantity`, `Overall_Size`, `Delivery_Date`, `Job_Description`, `Prepaired_By`, `Total_Charged`, `created_at`, `status`) VALUES
(8, '2024-11-04', '17:36:50', 'JCN09801', 'NBM', 'Posters and Books', 68, '700', '2024-11-25', 'Posters and Books for National Bank.', 'Sales User', '320000.00', '2024-11-03 22:00:00', 'project'),
(9, '2024-11-04', '17:48:04', 'JCN09802', 'NBS Bank', 'Stationery', 800, '800', '2024-10-15', 'Send Stationery to reserve', 'Sales User', '2600000.00', '2024-11-03 22:00:00', 'project'),
(6, '2024-11-01', '19:19:38', 'JCN09799', 'Deloitte & Touche', 'Banners', 24, '600', '2024-11-28', 'Banners for Projects', 'Admin User', '800000.00', '2024-10-31 22:00:00', 'manager_approved'),
(7, '2024-11-01', '19:47:07', 'JCN09800', 'TNM', 'TNM Poster', 33, '900', '2024-11-28', 'We need bruh ', 'Admin User', '120000.00', '2024-10-31 22:00:00', 'project');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

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
(7, 1, '95352f532698f1a7ae3b3249a2ff71125bf6e222d535184cc6749f2b8edb3826', '2024-09-09 20:10:23'),
(8, 1, 'f69f75f1e3057073b70c87f52899969e377a67c2eb3f4593e1cc5e6e9558e3ba', '2024-09-11 20:50:26'),
(9, 1, '04d6a86e254f96f3259286aa7778aecaaff0b2212d6156a07141f51b23bbf90d', '2024-09-11 20:50:33'),
(10, 1, '8b527fe81e75e7e944b8355d34279a88d634c62ec760e775ee1508a0c3467431', '2024-10-13 16:16:03'),
(11, 1, 'd472a2a816df7c5df5a237dc573f37441dc9d0beeaaacf62d88472db795f5ad5', '2024-10-13 16:16:10'),
(12, 1, '490bfdfb4cce2224ab510a766bec5bc33b010c690b73e1729dace89bf9041b84', '2024-10-13 16:16:15'),
(13, 1, '6395d78fe8fe2e9604bcc68380536e41ef44f971e275b18a82e33d82253df579', '2024-10-13 16:16:20'),
(14, 1, '6f6f099e817c2d1a1bab864d8962e9bac02c2d8a2ec944ebef3dd0e11443282f', '2024-10-13 16:16:25'),
(15, 4, 'c0f975c56478897cef01358a85ebea5f7d82e1299b23cf8fd0ff3f01217f9175', '2024-10-13 22:02:07'),
(16, 4, 'd1b58ece495377d250c9f96acd17940c4d21ad9c4eae4aa104274b62eb814a7d', '2024-11-01 12:32:58'),
(17, 4, '15c48bb1dc4727d23d5005abaae08e126d20abbd2fc6e20a5d56fb8303eef866', '2024-11-01 12:36:50');

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `full_name`, `created_at`) VALUES
(1, 'Eddie', '$2y$10$RW1b6YKAS02FVUFIMy2QD.cnHxNUKE5Qh0jjWgVIw18I7jSQX8ALS', 'admin', 'temboedward756@gmail.com', 'Admin User', '2024-07-29 23:04:03'),
(2, 'Designer', '$2y$10$cx6Y5TKXueLA.tKh2MVtwuIOTZbvTEwKshAcfxYx7/5EMU4O3SIf2', 'workshop', 'codeverse.mw@gmail.com', 'Designer User', '2024-07-29 23:04:03'),
(3, 'Edward', '$2y$10$OXPK8s1zquRE5wQmFSXAguB3Sdppq7Oh1cgeHV7Ngi93IL7UAv53y', 'sales', 'sales@example.com', 'Sales User', '2024-07-29 23:04:03'),
(4, 'George', '$2y$10$JvK252GaytCEkYFo8PnnhOR2O6qDvUep0/2VLzBc2woxbEXRNpGuO', 'sales', 'temboedward756+12@gmail.com', 'Edward', '2024-09-18 12:56:48'),
(10, 'Mr G2', '$2y$10$U0DqEu8wLt7aFlmehQOvEOVocIXNlyUftuiLTXbqGU1h/PmQX0sRG', 'designer', 'georgekumwenda0+mrg@gmail.com', 'George Kumwenda', '2024-11-01 17:39:56');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
