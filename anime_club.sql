-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 19, 2017 at 05:50 AM
-- Server version: 5.6.34-log
-- PHP Version: 5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anime_club`
--

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE IF NOT EXISTS `collection` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `coll_desc` varchar(500) DEFAULT NULL,
  `apr` int(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `collection`
--

INSERT INTO `collection` (`id`, `user_id`, `name`, `coll_desc`, `apr`) VALUES
(1, 1, 'Cosplay Photos', 'Photos of cosplay.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL,
  `img_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_nm` varchar(50) DEFAULT NULL,
  `body` varchar(500) DEFAULT NULL,
  `flag` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `img_id`, `user_id`, `user_nm`, `body`, `flag`) VALUES
(1, 1, 1, 'admin', 'We look great in this photo!', 0),
(2, 2, 0, 'ANONYMOUS', 'Cool photo!', 0);

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `id` int(11) NOT NULL,
  `collect_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `img_desc` varchar(500) DEFAULT NULL,
  `apr` int(1) DEFAULT NULL,
  `flag` int(1) DEFAULT NULL,
  `imgurl` varchar(50) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`id`, `collect_id`, `user_id`, `name`, `img_desc`, `apr`, `flag`, `imgurl`) VALUES
(1, 1, 1, 'Saiyuki', 'Cosplay from Saiyuki.', 1, 0, '1cosplay 1.jpg'),
(2, 1, 1, 'Comic Heroes', 'Cosplay of Comic Book characters.', 1, 0, '2cosplay 2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `log_name` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `valid` int(11) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `log_name`, `password`, `email`, `valid`, `type`) VALUES
(1, 'Name', 'admin', '$2y$10$CZfGmP80TUx8p17iLeUa0uzLqdnB0Fp0ts3tM.rTkAchxKt0FTnB.', 'user@address.com', 1, 'P'),
(2, 'Name 2', 'admin 2', '$2y$10$.oAgpiM.bWIT7zUpoK./Z.icaf69TzSe73qw28J1mKLH1gvPXiQ9e', 'user2@address.com', 1, 'A'),
(3, 'Name 3', 'user 3', '$2y$10$ZWy36j9naic9IuDhxXoO0OBNS369iSuFcypyTK4WbFSXUEG19.rjq', 'user3@address.com', 1, 'S');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `collection`
--
ALTER TABLE `collection`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `collection`
--
ALTER TABLE `collection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
