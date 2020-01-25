-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2020 at 02:46 PM
-- Server version: 5.6.15-log
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `m3`
--

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE IF NOT EXISTS `programs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `program_id` varchar(31) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `info` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `extended_info` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `description` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `company` tinytext COLLATE utf8mb4_hungarian_ci NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `country` varchar(3) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `creators` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `contributors` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `genre` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `quality` varchar(15) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `pg` varchar(15) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `duration` varchar(15) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `ratio` varchar(15) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `hasSubtitle` tinyint(1) NOT NULL,
  `isSeries` tinyint(1) NOT NULL,
  `seriesId` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `episode` smallint(5) unsigned NOT NULL,
  `episodes` smallint(5) unsigned NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `program_id` (`program_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
