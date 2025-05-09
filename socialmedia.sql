-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2025 at 09:28 AM
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
-- Database: `socialmedia`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `aID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `updateType` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`aID`, `title`, `updateType`, `description`, `timestamp`) VALUES
(6, 'aaa', 'System Configure', 'hi', '2025-03-04 08:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `banneduser`
--

CREATE TABLE `banneduser` (
  `UID` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `BID` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chathistory`
--

CREATE TABLE `chathistory` (
  `cID` int(255) NOT NULL,
  `senderID` int(255) NOT NULL,
  `receiverID` int(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `message` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `comID` int(255) NOT NULL,
  `FID` int(255) NOT NULL,
  `UID` int(255) NOT NULL,
  `time` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `message` longtext NOT NULL,
  `l` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feed`
--

CREATE TABLE `feed` (
  `FID` int(255) NOT NULL,
  `UID` int(255) NOT NULL,
  `Fimage` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `Fname` varchar(255) NOT NULL,
  `l` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feed`
--

INSERT INTO `feed` (`FID`, `UID`, `Fimage`, `time`, `Fname`, `l`) VALUES
(28, 25, 'uploads/67c538edd7a12_Screenshot 2025-01-06 181835.png', '2025-03-03 05:06:53', 'ff', 0),
(34, 24, 'uploads/67c6535f8b943_previousImg.png', '2025-03-04 01:11:59', 'aa', 1);

-- --------------------------------------------------------

--
-- Table structure for table `follow`
--

CREATE TABLE `follow` (
  `UID` int(255) NOT NULL,
  `following` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `follow`
--

INSERT INTO `follow` (`UID`, `following`) VALUES
(25, 24),
(24, 25);

-- --------------------------------------------------------

--
-- Table structure for table `g`
--

CREATE TABLE `g` (
  `gID` int(255) NOT NULL,
  `gName` longtext NOT NULL,
  `gImg` longtext NOT NULL,
  `create_by` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `g`
--

INSERT INTO `g` (`gID`, `gName`, `gImg`, `create_by`) VALUES
(21, 'group assignment', 'uploads/67c2ad59c3ecc_IMG_20250118_224107_743.webp', 24),
(22, 'try', 'uploads/67c568d0b76c2_Amiya.1024.3187474-ezgif.com-webp-to-jpg-converter.jpg', 24),
(23, 'aaa', 'uploads/67c56ed9184d3_Screenshot 2025-01-07 134638.png', 24);

-- --------------------------------------------------------

--
-- Table structure for table `groupmember`
--

CREATE TABLE `groupmember` (
  `gID` int(255) NOT NULL,
  `UID` int(255) NOT NULL,
  `joinTime` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `role` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groupmember`
--

INSERT INTO `groupmember` (`gID`, `UID`, `joinTime`, `role`) VALUES
(21, 24, '2025-03-01 06:46:49.811106', 'creator'),
(21, 25, '2025-03-03 08:29:33.316421', 'member'),
(22, 24, '2025-03-03 08:31:12.753886', 'creator'),
(23, 24, '2025-03-03 08:56:57.107111', 'creator'),
(23, 25, '2025-03-03 08:56:57.108736', 'member');

-- --------------------------------------------------------

--
-- Table structure for table `groupmessage`
--

CREATE TABLE `groupmessage` (
  `gID` int(255) NOT NULL,
  `UID` int(255) NOT NULL,
  `time` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `message` longtext NOT NULL,
  `mID` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likecom`
--

CREATE TABLE `likecom` (
  `UID` int(11) NOT NULL,
  `FID` int(11) NOT NULL,
  `comID` int(11) NOT NULL,
  `l` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likecom`
--

INSERT INTO `likecom` (`UID`, `FID`, `comID`, `l`) VALUES
(23, 0, 83, 0);

-- --------------------------------------------------------

--
-- Table structure for table `likepost`
--

CREATE TABLE `likepost` (
  `UID` int(255) NOT NULL,
  `FID` int(255) NOT NULL,
  `l` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(1) NOT NULL,
  `unread_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `userid`, `username`, `message`, `created_at`, `status`, `unread_count`) VALUES
(144, 23, '2', '2 is following you.', '2025-02-27 10:42:29', 'y', 0),
(145, 24, '1', '1 is following you.', '2025-02-27 10:42:38', 'y', 0),
(146, 23, '3', '3 is following you.', '2025-03-01 05:46:34', 'y', 0),
(147, 24, '3', '3 is following you.', '2025-03-01 05:46:35', 'y', 0),
(148, 25, '2', '2 is following you.', '2025-03-01 05:50:50', 'y', 0),
(149, 23, '2', '2 added you to group assignment.', '2025-03-01 06:46:49', 'y', 0),
(150, 25, '2', '2 added you to group assignment.', '2025-03-01 06:46:49', 'y', 0),
(151, 25, '2', '2 removed you from ', '2025-03-03 06:08:49', 'y', 0),
(152, 25, '2', '2 removed you from group assignment', '2025-03-03 06:09:44', 'y', 0),
(153, 25, '2', '2 removed you from ', '2025-03-03 06:23:59', 'n', 0),
(154, 25, '2', '2 removed you from group assignment', '2025-03-03 06:24:39', 'n', 0),
(155, 25, '2', '2 removed you from group assignment', '2025-03-03 06:27:42', 'n', 0),
(156, 23, '2', '2 removed you from group assignment', '2025-03-03 08:27:16', 'y', 0),
(157, 25, '2', '2 removed you from group assignment', '2025-03-03 08:29:27', 'n', 0),
(158, 25, '2', '2 added you to group assignment.', '2025-03-03 08:29:33', 'n', 1),
(159, 23, '2', '2 removed you from group assignment', '2025-03-03 08:30:25', 'y', 0),
(160, 23, '2', '2 added you to group assignment.', '2025-03-03 08:30:47', 'y', 0),
(161, 23, '2', '2 added you to try.', '2025-03-03 08:31:12', 'y', 0),
(162, 25, 'Admin', 'Admin has deleted post \"ddd\" due to \"reason\".', '2025-03-03 08:33:08', 'n', 0),
(163, 24, 'Admin', 'Admin has deleted post \"hei yay yayyiyayay\" due to \"reason\".', '2025-03-03 08:33:10', 'y', 0),
(164, 23, 'Admin', 'Admin has deleted post \"amiyaaa\" due to \"reason\".', '2025-03-03 08:33:14', 'y', 0),
(165, 23, 'Admin', 'Admin has deleted post \"aa\" due to \"reason\".', '2025-03-03 08:33:48', 'y', 0),
(166, 23, '2', '2 added you to aaa.', '2025-03-03 08:56:57', 'y', 0),
(167, 25, '2', '2 added you to aaa.', '2025-03-03 08:56:57', 'n', 1),
(168, 23, '2', '2 removed you from aaa', '2025-03-03 08:58:21', 'y', 0),
(169, 23, '2', '2 added you to aaa.', '2025-03-03 08:58:28', 'y', 0),
(170, 23, 'Admin', 'Admin has deleted post \"no content\" due to \"aaaaaaaaa\".', '2025-03-04 00:52:27', 'y', 0),
(171, 24, 'Admin', 'Admin has deleted post \"a\" due to \"aaa\".', '2025-03-04 01:09:59', 'y', 0),
(172, 23, '2', '2 unfollowed you.', '2025-03-05 05:55:43', 'y', 0),
(173, 23, '2', '2 is following you.', '2025-03-05 05:55:44', 'y', 0),
(174, 24, '1', '1 added you to aaa.', '2025-03-05 06:06:12', 'n', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reportpost`
--

CREATE TABLE `reportpost` (
  `RID` int(11) NOT NULL,
  `Title` varchar(999) NOT NULL,
  `Content` longtext NOT NULL,
  `Reason` varchar(999) NOT NULL,
  `FID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reportuser`
--

CREATE TABLE `reportuser` (
  `UID` int(11) NOT NULL,
  `ReportedUName` varchar(255) NOT NULL,
  `reason` longtext NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `storiesID` int(255) NOT NULL,
  `UID` int(255) NOT NULL,
  `img` mediumtext NOT NULL,
  `postTime` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `expiryTime` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`storiesID`, `UID`, `img`, `postTime`, `expiryTime`) VALUES
(15, 24, 'stories/67c6c5b54065c_heman-hey.gif', '2025-03-04 09:19:49.000000', '2025-03-05 09:19:49.000000'),
(17, 24, 'stories/67c7f56da0b80_heman-hey.gif', '2025-03-05 06:55:41.000000', '2025-03-06 06:55:41.000000');

-- --------------------------------------------------------

--
-- Table structure for table `udata`
--

CREATE TABLE `udata` (
  `UID` int(255) NOT NULL,
  `userName` varchar(999) NOT NULL,
  `emailAddress` varchar(999) DEFAULT NULL,
  `phoneNumber` varchar(255) DEFAULT NULL,
  `password` varchar(999) NOT NULL,
  `joinDate` date NOT NULL DEFAULT current_timestamp(),
  `role` varchar(999) DEFAULT NULL,
  `profilePic` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `udata`
--

INSERT INTO `udata` (`UID`, `userName`, `emailAddress`, `phoneNumber`, `password`, `joinDate`, `role`, `profilePic`) VALUES
(24, '2', '2@gmail.com', NULL, '$2y$10$KIzO4P8vkXMb3FKaudryKuWabdx3utmrrZwfiXv.HoiayZt2b23BK', '2025-02-27', NULL, 'picture/profile_24.gif'),
(25, '3', '3@gmail.com', NULL, '$2y$10$8QzKE8Ye/4a6NDnoFJIKP.ByGrs4FrLP27uh0B/HXu4EJRqJmIebW', '2025-03-01', NULL, ''),
(26, '4', '4@gmail.com', NULL, '$2y$10$dgHlG//wqxTA5MWscw6RRuI/.CEIN1ztB.VBNqS9i3.f..JZRLuXG', '2025-03-05', NULL, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`aID`);

--
-- Indexes for table `banneduser`
--
ALTER TABLE `banneduser`
  ADD PRIMARY KEY (`BID`),
  ADD KEY `UID` (`UID`);

--
-- Indexes for table `chathistory`
--
ALTER TABLE `chathistory`
  ADD PRIMARY KEY (`cID`),
  ADD KEY `senderID` (`senderID`),
  ADD KEY `receiverID` (`receiverID`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comID`),
  ADD KEY `FID` (`FID`),
  ADD KEY `UID` (`UID`);

--
-- Indexes for table `feed`
--
ALTER TABLE `feed`
  ADD PRIMARY KEY (`FID`),
  ADD KEY `UID` (`UID`);

--
-- Indexes for table `follow`
--
ALTER TABLE `follow`
  ADD KEY `UID` (`UID`),
  ADD KEY `following` (`following`);

--
-- Indexes for table `g`
--
ALTER TABLE `g`
  ADD PRIMARY KEY (`gID`),
  ADD KEY `create_by` (`create_by`);

--
-- Indexes for table `groupmember`
--
ALTER TABLE `groupmember`
  ADD KEY `gID` (`gID`),
  ADD KEY `UID` (`UID`);

--
-- Indexes for table `groupmessage`
--
ALTER TABLE `groupmessage`
  ADD PRIMARY KEY (`mID`),
  ADD KEY `gID` (`gID`),
  ADD KEY `UID` (`UID`);

--
-- Indexes for table `likepost`
--
ALTER TABLE `likepost`
  ADD KEY `FID` (`FID`),
  ADD KEY `UID` (`UID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reportpost`
--
ALTER TABLE `reportpost`
  ADD PRIMARY KEY (`RID`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`storiesID`),
  ADD KEY `UID` (`UID`);

--
-- Indexes for table `udata`
--
ALTER TABLE `udata`
  ADD PRIMARY KEY (`UID`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`,`phoneNumber`) USING HASH,
  ADD UNIQUE KEY `userName` (`userName`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `aID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `banneduser`
--
ALTER TABLE `banneduser`
  MODIFY `BID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chathistory`
--
ALTER TABLE `chathistory`
  MODIFY `cID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `comID` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feed`
--
ALTER TABLE `feed`
  MODIFY `FID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `g`
--
ALTER TABLE `g`
  MODIFY `gID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `groupmessage`
--
ALTER TABLE `groupmessage`
  MODIFY `mID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `reportpost`
--
ALTER TABLE `reportpost`
  MODIFY `RID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `storiesID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `udata`
--
ALTER TABLE `udata`
  MODIFY `UID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `banneduser`
--
ALTER TABLE `banneduser`
  ADD CONSTRAINT `banneduser_ibfk_1` FOREIGN KEY (`UID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chathistory`
--
ALTER TABLE `chathistory`
  ADD CONSTRAINT `chathistory_ibfk_1` FOREIGN KEY (`senderID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chathistory_ibfk_2` FOREIGN KEY (`receiverID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`FID`) REFERENCES `feed` (`FID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`UID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feed`
--
ALTER TABLE `feed`
  ADD CONSTRAINT `feed_ibfk_1` FOREIGN KEY (`UID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `follow`
--
ALTER TABLE `follow`
  ADD CONSTRAINT `follow_ibfk_1` FOREIGN KEY (`UID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `follow_ibfk_2` FOREIGN KEY (`following`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `g`
--
ALTER TABLE `g`
  ADD CONSTRAINT `g_ibfk_1` FOREIGN KEY (`create_by`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `groupmember`
--
ALTER TABLE `groupmember`
  ADD CONSTRAINT `groupmember_ibfk_1` FOREIGN KEY (`gID`) REFERENCES `g` (`gID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `groupmember_ibfk_2` FOREIGN KEY (`UID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `groupmessage`
--
ALTER TABLE `groupmessage`
  ADD CONSTRAINT `groupmessage_ibfk_1` FOREIGN KEY (`gID`) REFERENCES `g` (`gID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `groupmessage_ibfk_2` FOREIGN KEY (`UID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `likepost`
--
ALTER TABLE `likepost`
  ADD CONSTRAINT `likepost_ibfk_1` FOREIGN KEY (`FID`) REFERENCES `feed` (`FID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likepost_ibfk_2` FOREIGN KEY (`UID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`UID`) REFERENCES `udata` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
