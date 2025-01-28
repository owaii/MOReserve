-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 15, 2025 at 11:45 AM
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
-- Database: `more`
--

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `number` bigint(20) DEFAULT NULL,
  `date` varchar(5) NOT NULL,
  `holderName` varchar(100) NOT NULL,
  `cvv` int(3) NOT NULL,
  `status` enum('active','inactive','removed') NOT NULL,
  `created` date DEFAULT curdate(),
  `limits` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`id`, `userID`, `number`, `date`, `holderName`, `cvv`, `status`, `created`, `limits`) VALUES
(1, 1, 7000000000000000, '12/24', 'Adam Iwanski', 659, 'active', '2024-12-11', NULL),
(4, 4, 7000000000000001, '12/24', 'Wiktor Kosmala', 997, 'active', '2024-12-11', NULL),
(6, 6, 7000000000000002, '12/24', 'Brysio Brysio', 895, 'active', '2024-12-11', NULL),
(7, 7, 7345414041355151, '12/24', 'mik mik', 620, 'active', '2024-12-11', NULL),
(8, 8, 7173082434916847, '12/24', 'p pp', 909, 'active', '2024-12-15', NULL),
(9, 9, 7321064184841881, '12/29', 'Mikolaj Brysacz', 278, 'active', '2024-12-15', NULL),
(10, 8, 7524503296786948, '1/30', 'p pp', 315, '', '2025-01-15', 100),
(11, 8, 7372203578207533, '1/30', 'p pp', 921, '', '2025-01-15', 100);

--
-- Triggers `cards`
--
DELIMITER $$
CREATE TRIGGER `before_insert_cards` BEFORE INSERT ON `cards` FOR EACH ROW BEGIN
    IF NEW.number IS NULL OR NEW.number = 0 THEN
        SET NEW.number = @start_number;
        SET @start_number = @start_number + 1;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `friendID` int(11) NOT NULL,
  `transactions` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `userID`, `friendID`, `transactions`) VALUES
(8, 6, 1, 0),
(9, 6, 6, 0),
(10, 6, 4, 0),
(11, 8, 1, 0),
(14, 8, 6, 0),
(15, 8, 4, 0),
(16, 8, 7, 0),
(17, 8, 8, 0),
(19, 8, 9, 0);

-- --------------------------------------------------------

--
-- Table structure for table `personal`
--

CREATE TABLE `personal` (
  `id` int(11) NOT NULL,
  `mothersMaidenName` varchar(200) NOT NULL,
  `country` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `street` varchar(100) NOT NULL,
  `buildingNumber` varchar(10) NOT NULL,
  `apartmentNumber` varchar(10) NOT NULL,
  `postal` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal`
--

INSERT INTO `personal` (`id`, `mothersMaidenName`, `country`, `city`, `street`, `buildingNumber`, `apartmentNumber`, `postal`) VALUES
(1, 'Joanna  Iwanska', 'Poland', 'Warszawa', 'Polna', '7', '7', '19-389'),
(4, 'Joanna Kosmala', 'Poland', 'Warszawa', 'Polna', '7', '7', '28-174'),
(6, 'Mikolajka Brysacz', 'Poland', 'Warszawa', 'Polna', '78', '7', '78-217'),
(7, 'mik mik', 'Poland', 'Warszawa', 'Polna', '218', '787', '87-847'),
(8, 'ifesaji jifjsifj', 'Poland', 'Warszawa', 'Polna', '7', '7', '28-748'),
(9, 'Mikolaja iasfn', 'Poland', 'fsf', 'dsdfsdf', '423', '324', '42-342');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `toUserID` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT 'przelew',
  `created` date DEFAULT curdate(),
  `time` time NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `userID`, `toUserID`, `amount`, `description`, `created`, `time`) VALUES
(2, 6, 1, 100.00, 'Money transfer', '2024-12-11', '00:00:00'),
(3, 7, 6, 5.00, 'Money transfer', '2024-12-11', '00:00:00'),
(4, 6, 4, 100.00, 'Money transfer', '2024-12-11', '00:00:00'),
(5, 8, 1, 120.00, 'Money transfer', '2024-12-15', '00:00:00'),
(6, 8, 6, 50.00, 'Money transfer', '2024-12-15', '00:00:00'),
(7, 8, 7, 200.00, 'Money transfer', '2024-12-15', '00:00:00'),
(8, 8, 4, 85.00, 'Money transfer', '2024-12-14', '00:00:00'),
(9, 8, 7, 150.00, 'Money transfer', '2024-12-14', '00:00:00'),
(10, 8, 6, 60.00, 'Money transfer', '2024-12-14', '00:00:00'),
(11, 8, 1, 95.00, 'Money transfer', '2024-12-13', '00:00:00'),
(12, 8, 4, 45.00, 'Money transfer', '2024-12-13', '00:00:00'),
(13, 8, 6, 180.00, 'Money transfer', '2024-12-13', '00:00:00'),
(14, 8, 7, 100.00, 'Money transfer', '2024-12-12', '00:00:00'),
(15, 8, 1, 75.00, 'Money transfer', '2024-12-12', '00:00:00'),
(16, 8, 4, 30.00, 'Money transfer', '2024-12-12', '00:00:00'),
(17, 8, 6, 110.00, 'Money transfer', '2024-12-11', '00:00:00'),
(18, 8, 1, 40.00, 'Money transfer', '2024-12-11', '00:00:00'),
(19, 8, 7, 155.00, 'Money transfer', '2024-12-11', '00:00:00'),
(20, 8, 6, 60.00, 'Money transfer', '2024-12-10', '00:00:00'),
(21, 8, 1, 120.00, 'Money transfer', '2024-12-10', '00:00:00'),
(22, 8, 4, 25.00, 'Money transfer', '2024-12-10', '00:00:00'),
(23, 8, 7, 90.00, 'Money transfer', '2024-12-09', '00:00:00'),
(24, 8, 6, 70.00, 'Money transfer', '2024-12-09', '00:00:00'),
(25, 8, 1, 150.00, 'Money transfer', '2024-12-09', '00:00:00'),
(26, 8, 6, 125.00, 'Money transfer', '2024-12-08', '00:00:00'),
(27, 8, 4, 30.00, 'Money transfer', '2024-12-08', '00:00:00'),
(28, 8, 7, 180.00, 'Money transfer', '2024-12-08', '00:00:00'),
(29, 8, 1, 65.00, 'Money transfer', '2024-12-07', '00:00:00'),
(30, 8, 4, 90.00, 'Money transfer', '2024-12-07', '00:00:00'),
(31, 8, 6, 180.00, 'Money transfer', '2024-12-07', '00:00:00'),
(32, 8, 7, 135.00, 'Money transfer', '2024-12-06', '00:00:00'),
(33, 8, 1, 45.00, 'Money transfer', '2024-12-06', '00:00:00'),
(34, 8, 4, 80.00, 'Money transfer', '2024-12-06', '00:00:00'),
(35, 8, 6, 100.00, 'Money transfer', '2024-12-05', '00:00:00'),
(36, 8, 1, 60.00, 'Money transfer', '2024-12-05', '00:00:00'),
(37, 8, 7, 140.00, 'Money transfer', '2024-12-05', '00:00:00'),
(38, 8, 6, 130.00, 'Money transfer', '2024-12-04', '00:00:00'),
(39, 8, 1, 50.00, 'Money transfer', '2024-12-04', '00:00:00'),
(40, 8, 4, 95.00, 'Money transfer', '2024-12-04', '00:00:00'),
(43, 8, 1, 400.00, 'przelew', '2024-12-17', '23:20:42'),
(45, 8, 1, 354.00, 'Payment for services', '2024-12-17', '10:00:00'),
(46, 8, 1, 279.00, 'Refund process', '2024-12-17', '10:30:00'),
(47, 8, 1, 312.00, 'Monthly subscription', '2024-12-17', '11:00:00'),
(48, 8, 1, 228.00, 'Product purchase', '2024-12-17', '11:30:00'),
(49, 8, 1, 490.00, 'Service fee', '2024-12-17', '12:00:00'),
(50, 8, 1, 179.00, 'One-time payment', '2024-12-17', '12:30:00'),
(51, 8, 1, 310.00, 'Invoice settlement', '2024-12-17', '13:00:00'),
(52, 8, 1, 245.00, 'Loan repayment', '2024-12-17', '13:30:00'),
(53, 8, 1, 423.00, 'Utility payment', '2024-12-17', '14:00:00'),
(54, 8, 1, 195.00, 'Membership fee', '2024-12-17', '14:30:00'),
(55, 8, 1, 312.00, 'Penalty charge', '2024-12-17', '15:00:00'),
(56, 8, 1, 268.00, 'Bonus payout', '2024-12-17', '15:30:00'),
(57, 8, 1, 379.00, 'Donation received', '2024-12-17', '16:00:00'),
(58, 8, 1, 187.00, 'Expense reimbursement', '2024-12-17', '16:30:00'),
(59, 8, 1, 402.00, 'Salary transfer', '2024-12-17', '17:00:00'),
(60, 8, 1, 275.00, 'Payment adjustment', '2024-12-17', '17:30:00'),
(61, 8, 1, 350.00, 'Overtime payout', '2024-12-17', '18:00:00'),
(62, 8, 1, 192.00, 'Gift card', '2024-12-17', '18:30:00'),
(63, 8, 1, 440.00, 'Bill settlement', '2024-12-17', '19:00:00'),
(64, 8, 1, 315.00, 'Tax refund', '2024-12-17', '19:30:00'),
(65, 8, 1, 354.00, 'Payment for services', '2024-12-18', '10:00:00'),
(66, 8, 1, 279.00, 'Refund process', '2024-12-18', '10:30:00'),
(67, 8, 1, 312.00, 'Monthly subscription', '2024-12-18', '11:00:00'),
(68, 8, 1, 228.00, 'Product purchase', '2024-12-18', '11:30:00'),
(69, 8, 1, 490.00, 'Service fee', '2024-12-18', '12:00:00'),
(70, 8, 1, 179.00, 'One-time payment', '2024-12-18', '12:30:00'),
(71, 8, 1, 310.00, 'Invoice settlement', '2024-12-18', '13:00:00'),
(72, 8, 1, 245.00, 'Loan repayment', '2024-12-18', '13:30:00'),
(73, 8, 1, 423.00, 'Utility payment', '2024-12-18', '14:00:00'),
(74, 8, 1, 195.00, 'Membership fee', '2024-12-18', '14:30:00'),
(75, 8, 1, 312.00, 'Penalty charge', '2024-12-18', '15:00:00'),
(76, 8, 1, 268.00, 'Bonus payout', '2024-12-18', '15:30:00'),
(77, 8, 1, 379.00, 'Donation received', '2024-12-18', '16:00:00'),
(78, 8, 1, 187.00, 'Expense reimbursement', '2024-12-18', '16:30:00'),
(79, 8, 1, 402.00, 'Salary transfer', '2024-12-18', '17:00:00'),
(80, 8, 1, 275.00, 'Payment adjustment', '2024-12-18', '17:30:00'),
(81, 8, 1, 350.00, 'Overtime payout', '2024-12-18', '18:00:00'),
(82, 8, 1, 192.00, 'Gift card', '2024-12-18', '18:30:00'),
(83, 8, 1, 440.00, 'Bill settlement', '2024-12-18', '19:00:00'),
(84, 8, 1, 315.00, 'Tax refund', '2024-12-18', '19:30:00'),
(85, 8, 9, 200.00, 'Money transfer', '2024-12-18', '00:55:30'),
(86, 8, 4, 100.00, 'Money transfer', '2024-12-18', '00:56:21'),
(87, 8, 6, 100.00, 'Money transfer', '2024-12-18', '00:59:21'),
(88, 8, 6, 100.00, 'Money transfer', '2024-12-18', '01:01:25'),
(89, 8, 1, 2.00, 'Money transfer', '2025-01-14', '18:57:22'),
(90, 8, 1, 95.00, 'Money transfer', '2025-01-14', '18:57:36'),
(91, 8, 6, 2.00, 'Money transfer', '2025-01-15', '11:17:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `personalID` int(11) DEFAULT NULL,
  `icon` varchar(255) DEFAULT 'astrid.webp',
  `username` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `pesel` char(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phoneNumber` varchar(15) NOT NULL,
  `created` date DEFAULT curdate(),
  `login` date DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `personalID`, `icon`, `username`, `email`, `name`, `surname`, `pesel`, `password`, `phoneNumber`, `created`, `login`, `balance`) VALUES
(1, 1, 'astrid.webp', 'adam1', 'adam@gmail.com', 'Adam', 'Iwanski', '11111111111', '$2y$10$.GOynrhLykl7SVQd4/VgzeJ0VV84XkTFW21i3PQ8mivkZsF.mkZe2', '111 111 111', '2024-12-11', NULL, 197.00),
(4, 4, 'astrid.webp', 'wiktor', 'wiktor@gmail.com', 'Wiktor', 'Kosmala', '22222222222', '$2y$10$JVJUcAsOBSosdQuZip07p.oc/lu18QDeEmmQxsdcm8g..HgBG3lBG', '222 222 222', '2024-12-11', NULL, 300.00),
(6, 6, 'astrid.webp', ' brysio', 'brysio@gmail.com', 'Brysio', 'Brysio', '33333333333', '$2y$10$3hKUzJzK129kXaj/8eCHHeXwYxtWbzdn2tu5jCCyGlpLVhhj5fwbi', '333 333 333', '2024-12-11', '2024-12-11', 207.00),
(7, 7, 'astrid.webp', 'mik', 'mi@gmail.com', 'mik', 'mik', '14151251251', '$2y$10$BCrBlcotJLoukfY1TGaD.OEARJh7siVy9tfgAKUsjwKtwQsGPNMQ2', '281 471 824', '2024-12-11', '2024-12-11', 5.00),
(8, 8, 'astrid.webp', 'adam', 'p2@gmail.com', 'p', 'pp', '23151125125', '$2y$10$PEID82IK.rWMTJg6SdTfFeUQW/U2XQiWoMJfh6Y3vBJ.vpxrl.6aq', '235 235 325', '2024-12-15', '2025-01-14', 1.00),
(9, 9, 'astrid.webp', 'miko', 'm@gmail.com', 'Mikolaj', 'Brysacz', '35235235325', '$2y$10$5xtaggt0q.1qOP1fbqlK..wlsgJSZXuRI1zNMME.zr8iomB2YIjyq', '812 748 127', '2024-12-15', '2024-12-15', 200.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `number` (`number`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `friendID` (`friendID`);

--
-- Indexes for table `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `toUserID` (`toUserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `pesel` (`pesel`),
  ADD UNIQUE KEY `phoneNumber` (`phoneNumber`),
  ADD KEY `personalID` (`personalID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `personal`
--
ALTER TABLE `personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friendID`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`toUserID`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`personalID`) REFERENCES `personal` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
