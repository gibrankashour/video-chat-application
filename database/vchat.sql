-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2023 at 08:32 PM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vchat`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profileImage` varchar(255) NOT NULL DEFAULT 'assets/images/defaultImage.jpg',
  `sessionID` varchar(255) NOT NULL,
  `connectionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `username`, `name`, `email`, `password`, `profileImage`, `sessionID`, `connectionID`) VALUES
(1, 'Gobier', 'gobier', 'gibran@test.com', '$2y$10$Ct8SDIBdCNRS4/8v7IT/seF6vnZWREh/Xe6ADrN/TK/E9nhIyN/HW', 'assets/images/defaultImage.jpg', '4coc3ie1rla906jdjp25c3nfh2', 171),
(2, 'Abo Jad', 'abojad', 'abojad@test.com', '$2y$10$Ct8SDIBdCNRS4/8v7IT/seF6vnZWREh/Xe6ADrN/TK/E9nhIyN/HW', 'assets/images/defaultImage.jpg', '96ri2eomr5jbqrgjbl04lat80q', 111),
(3, 'Gibran Kashour', 'gibran', 'gibran321@hotmail.com', '$2y$10$Ct8SDIBdCNRS4/8v7IT/seF6vnZWREh/Xe6ADrN/TK/E9nhIyN/HW', 'assets/images/defaultImage.jpg', '67rpmqll8ggj1h3rdup59orj4p', 168);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
