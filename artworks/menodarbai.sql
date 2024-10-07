-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2024 at 02:52 PM
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
-- Database: `menodarbai`
--

-- --------------------------------------------------------

--
-- Table structure for table `autoriai`
--

CREATE TABLE `autoriai` (
  `autorius_id` int(11) NOT NULL,
  `vardas` varchar(100) NOT NULL,
  `pavarde` varchar(100) NOT NULL,
  `el_pastas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `autoriai`
--

INSERT INTO `autoriai` (`autorius_id`, `vardas`, `pavarde`, `el_pastas`) VALUES
(79, 'menininkas', 'menininkas', 'menininkas1@gmail.com'),
(85, 'menininkas', 'menininkas', 'menininkas1@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `kuriniai`
--

CREATE TABLE `kuriniai` (
  `kurinys_id` int(11) NOT NULL,
  `pavadinimas` varchar(100) NOT NULL,
  `aprasymas` text DEFAULT NULL,
  `kaina` decimal(10,2) DEFAULT NULL,
  `autorius_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kuriniai`
--

INSERT INTO `kuriniai` (`kurinys_id`, `pavadinimas`, `aprasymas`, `kaina`, `autorius_id`) VALUES
(21, 'aaa', '', 500.00, 79),
(22, 'bbbbbb', '', 550.00, 79),
(23, 'ccccc', '', 2000.00, 79),
(24, 'dddddd', 'ispudingas', 60000.00, 79);

-- --------------------------------------------------------

--
-- Table structure for table `pardavimai`
--

CREATE TABLE `pardavimai` (
  `pardavimas_id` int(11) NOT NULL,
  `kurinys_id` int(11) DEFAULT NULL,
  `pirkimo_data` date DEFAULT NULL,
  `kaina` decimal(10,2) DEFAULT NULL,
  `pirkejas_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pardavimai`
--

INSERT INTO `pardavimai` (`pardavimas_id`, `kurinys_id`, `pirkimo_data`, `kaina`, `pirkejas_id`) VALUES
(8, 24, '2024-05-23', 60000.00, 82);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(85, 'menininkas', '$2y$10$5w/HjsI6gPAtRl.F/Cqf9.1DXroezUFl.h7FNKTe4WFoTM1IM29Hm', 'skaitytojas', '2024-10-07 12:49:12'),
(86, 'autorius', '$2y$10$YQBvPv/udlOOob4Di3D3VOQDUQOyBwem8rPR6tE9DYwcvwtf.nt3G', 'redaguotojas', '2024-10-07 12:49:12'),
(87, 'vadybininkas', '$2y$10$roi2NoEu8yLs3XMEOZ5keON6kPHR./B3aiFNuDUJuacx4FC4h.HLC', 'administratorius', '2024-10-07 12:49:12'),
(88, 'pirkejas', '$2y$10$v11PpKDNZIef2c.ML1.HZe4vcIYn/t8MFAWzWVymJVtsymjqkIEq2', 'skaitytojas', '2024-10-07 12:49:12'),
(89, 'adminas', '$2y$10$zpIbnLYSONbnYQB0CJuXC.XYqIudP4v120yfeVL982zOjcCIGCmy.', 'administratorius', '2024-10-07 12:49:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `autoriai`
--
ALTER TABLE `autoriai`
  ADD PRIMARY KEY (`autorius_id`);

--
-- Indexes for table `kuriniai`
--
ALTER TABLE `kuriniai`
  ADD PRIMARY KEY (`kurinys_id`),
  ADD KEY `autorius_id` (`autorius_id`);

--
-- Indexes for table `pardavimai`
--
ALTER TABLE `pardavimai`
  ADD PRIMARY KEY (`pardavimas_id`),
  ADD KEY `kurinys_id` (`kurinys_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `autoriai`
--
ALTER TABLE `autoriai`
  MODIFY `autorius_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `kuriniai`
--
ALTER TABLE `kuriniai`
  MODIFY `kurinys_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `pardavimai`
--
ALTER TABLE `pardavimai`
  MODIFY `pardavimas_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kuriniai`
--
ALTER TABLE `kuriniai`
  ADD CONSTRAINT `kuriniai_ibfk_1` FOREIGN KEY (`autorius_id`) REFERENCES `autoriai` (`autorius_id`);

--
-- Constraints for table `pardavimai`
--
ALTER TABLE `pardavimai`
  ADD CONSTRAINT `pardavimai_ibfk_1` FOREIGN KEY (`kurinys_id`) REFERENCES `kuriniai` (`kurinys_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
