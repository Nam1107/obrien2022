-- --------------------------------------------------------
-- Host:                         sql6.freesqldatabase.com
-- Server version:               5.5.62-0ubuntu0.14.04.1 - (Ubuntu)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table sql6525734.category
CREATE TABLE IF NOT EXISTS `category` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.category: ~3 rows (approximately)
INSERT INTO `category` (`ID`, `name`, `description`) VALUES
	(1, 'Fruits', NULL),
	(2, 'Vegetables', NULL),
	(3, NULL, NULL);

-- Dumping structure for table sql6525734.delivery_order
CREATE TABLE IF NOT EXISTS `delivery_order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) DEFAULT NULL,
  `shipper_id` bigint(20) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `delivered_date` datetime DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.delivery_order: ~4 rows (approximately)
INSERT INTO `delivery_order` (`id`, `order_id`, `shipper_id`, `created_by`, `created_date`, `delivered_date`, `status`, `description`) VALUES
	(109, 14, 4, 18, '2022-12-09 01:24:49', '2022-12-09 03:10:11', 'Success', 'Order has been delivered'),
	(110, 12, 4, 14, '2022-12-09 01:25:54', '2022-12-09 03:00:43', 'Success', 'Order has been delivered'),
	(111, 1, 4, 18, '2022-12-09 03:13:04', NULL, 'Fail', 'Customers do not answer the phone'),
	(112, 1, 4, 14, '2022-12-09 14:41:36', NULL, 'Fail', 'Product is being delivered to a wrong address'),
	(113, 2, 4, 14, '2022-12-09 14:56:15', '2022-12-09 14:57:39', 'Fail', 'Product is being delivered to a wrong address');

-- Dumping structure for table sql6525734.gallery
CREATE TABLE IF NOT EXISTS `gallery` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `productID` int(11) DEFAULT NULL,
  `URLImage` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.gallery: ~22 rows (approximately)
INSERT INTO `gallery` (`ID`, `productID`, `URLImage`) VALUES
	(2, 1, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(3, 1, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(5, 14, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(6, 14, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(7, 15, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(8, 15, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(9, 16, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(10, 16, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(11, 17, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(12, 17, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(13, 18, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(14, 18, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*'),
	(19, 19, '123'),
	(20, 19, '456'),
	(23, 21, '123'),
	(24, 21, '456'),
	(25, 20, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fbok_choy.jpg?alt=media&token=7b7d9e29-12ac-487b-a0d8-e5e8427bc37e'),
	(26, 20, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fbellpepper.jpg?alt=media&token=7696d858-909b-499a-bcff-98c4bea56a84'),
	(27, 20, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fbell_pepper.png?alt=media&token=cfd63b48-9400-4038-bbdb-212195b2f835'),
	(30, 20, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fchilli.jpg?alt=media&token=1f0f18e0-6032-4d1a-a7bb-248c77ac0fc4'),
	(31, 20, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Forange.jpg?alt=media&token=9770a552-b994-4447-832a-143b65bb9d30'),
	(32, 20, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fmango.jpg?alt=media&token=896762b0-aa0c-4d0c-9f97-d3c0344e09d4');

-- Dumping structure for table sql6525734.login_token
CREATE TABLE IF NOT EXISTS `login_token` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `token` varchar(500) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.login_token: ~7 rows (approximately)
INSERT INTO `login_token` (`ID`, `userID`, `token`, `createdAt`) VALUES
	(53, 15, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYnJpZW4iLCJleHAiOjE2Njk1MjgwMjYsImRhdGEiOnsiaWQiOjE1fX0.xnZNCGZuwMGG_GPggUeGSJCA7njH7yHLGnUCs52yMtU', '2022-11-27 12:46:06'),
	(60, 15, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYnJpZW4iLCJleHAiOjE2Njk2MTU4ODYsImRhdGEiOnsiaWQiOiIxNSJ9fQ.1C5CpA6IOuJkHlr9567YC1zOXxXr0AvETrpLGoeDfO8', '2022-11-28 13:10:26'),
	(61, 15, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYnJpZW4iLCJleHAiOjE2Njk2MTU5MDIsImRhdGEiOnsiaWQiOiIxNSJ9fQ.DjJ2pL921fSK4mAz77XP6XM94LdB-hNTSSamsr6smc0', '2022-11-28 13:10:42'),
	(62, 15, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYnJpZW4iLCJleHAiOjE2Njk2MTY1OTMsImRhdGEiOnsiaWQiOiIxNSJ9fQ.GfyyqZs_W-sGefRIdlHVhhwt-ev6tdFwu7vEkfqfMBY', '2022-11-28 13:22:13'),
	(63, 15, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYnJpZW4iLCJleHAiOjE2Njk2MjM5MjIsImRhdGEiOnsiaWQiOiIxNSJ9fQ.ycp3nCQG0CnH8nM6-kxh5fJMEo7G1DvC_37HT1DBJe4', '2022-11-28 15:24:22'),
	(82, 18, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYnJpZW4iLCJleHAiOjE2Njk3MzcwMzMsImRhdGEiOnsiaWQiOjE4fX0.JDR9j3y8MO0Frgux1jm9jV-RGsOPWdl8OGxEQPG0hv8', '2022-11-29 22:49:33'),
	(86, 15, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYnJpZW4iLCJleHAiOjE2Njk4OTk1ODEsImRhdGEiOnsiaWQiOjE1fX0.6Zt8nwCT1omQE-dVwa-zgqJZBv0MsTBPH8MZLFEfYCs', '2022-12-01 19:58:41'),
	(87, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYnJpZW4iLCJleHAiOjE2NzA2NTg3MTgsImRhdGEiOnsiaWQiOjE0fX0.UrBMpd8lB0g3z_zDUHJSRe0wKaeXoEE1Gb42l1H0ebI', '2022-12-09 14:51:58'),
	(88, 14, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYnJpZW4iLCJleHAiOjE2NzA2NTkwMDUsImRhdGEiOnsiaWQiOjE0fX0.FNwar-bXIiwTTm95NnEOACMxxt0SQllfapN1_NwnpOI', '2022-12-09 14:56:45');

-- Dumping structure for table sql6525734.order
CREATE TABLE IF NOT EXISTS `order` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `note` varchar(500) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `status` varchar(500) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.order: ~14 rows (approximately)
INSERT INTO `order` (`ID`, `userID`, `note`, `phone`, `address`, `status`, `createdAt`) VALUES
	(1, 14, NULL, 101042, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-10-11 18:05:38'),
	(2, 14, NULL, NULL, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-10-11 23:45:44'),
	(3, 14, 'Hello', 123, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-10-11 23:45:44'),
	(4, 4, NULL, NULL, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-10-23 01:48:54'),
	(5, 4, '5', 123456, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-10-23 01:57:55'),
	(6, 14, NULL, NULL, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-11-01 15:42:06'),
	(7, 14, '', 195745656, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-11-01 21:07:53'),
	(8, 14, '', 195745656, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-11-01 21:23:04'),
	(9, 14, '', 195745656, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-11-21 15:46:51'),
	(10, 15, 'hàng dễ nát', 343414908, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-11-27 14:52:19'),
	(11, 15, 'hàng dễ dập', 343414908, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-11-27 14:56:07'),
	(12, 15, 'hàng dễ nát', 343414908, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Rate', '2022-11-27 15:08:38'),
	(13, 15, 'giao nhanh không hỏng hết rau r', 343414908, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Ship', '2022-11-30 16:36:35'),
	(14, 15, 'hàng dễ dập', 343414908, 'đường Thanh Bình, Mộ Lao, Hà Đông, Hà Nội', 'To Rate', '2022-12-02 19:28:01');

-- Dumping structure for table sql6525734.orderDetail
CREATE TABLE IF NOT EXISTS `orderDetail` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `orderID` int(11) DEFAULT NULL,
  `productID` int(11) DEFAULT NULL,
  `unitPrice` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.orderDetail: ~25 rows (approximately)
INSERT INTO `orderDetail` (`ID`, `orderID`, `productID`, `unitPrice`, `quantity`, `createdAt`) VALUES
	(5, 3, 2, 12000, 6, '2022-12-09 01:19:11'),
	(6, 2, 2, 12000, 6, '2022-12-09 01:18:52'),
	(7, 1, 2, 12000, 6, '2022-12-09 01:18:14'),
	(8, 4, 2, 12000, 6, '2022-12-09 01:17:59'),
	(9, 5, 2, 12000, 6, '2022-10-23 01:57:56'),
	(10, 6, 2, 12000, 6, '2022-11-01 15:42:06'),
	(11, 6, 4, 12000, 6, '2021-11-01 15:42:07'),
	(12, 7, 2, 12000, 6, '2022-11-01 21:07:54'),
	(13, 7, 3, 12000, 6, '2022-11-01 21:07:54'),
	(14, 7, 4, 12000, 6, '2022-11-01 21:07:54'),
	(15, 7, 6, 12000, 6, '2022-11-01 21:07:54'),
	(16, 7, 9, 12000, 6, '2022-11-01 21:07:54'),
	(17, 7, 10, 12000, 6, '2022-11-01 21:07:54'),
	(18, 8, 2, 12000, 2, '2022-11-01 21:23:05'),
	(19, 8, 3, 12000, 2, '2022-11-01 21:23:05'),
	(20, 8, 4, 12000, 2, '2022-11-01 21:23:06'),
	(21, 9, 2, 12000, 6, '2022-11-21 15:46:51'),
	(22, 9, 6, 12000, 6, '2022-11-21 15:46:51'),
	(23, 10, 6, 12000, 2, '2022-11-27 14:52:19'),
	(24, 11, 3, 12000, 3, '2022-11-27 14:56:07'),
	(25, 12, 2, 12000, 3, '2022-11-27 15:08:38'),
	(26, 13, 3, 12000, 1, '2022-11-30 16:36:35'),
	(27, 13, 4, 12000, 2, '2022-11-30 16:36:36'),
	(28, 13, 6, 12000, 2, '2022-11-30 16:36:36'),
	(29, 14, 20, 113, 1, '2022-12-02 19:28:02');

-- Dumping structure for table sql6525734.product
CREATE TABLE IF NOT EXISTS `product` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `categoryID` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `rate` float DEFAULT '0',
  `numOfReviews` int(11) DEFAULT '0',
  `sold` float DEFAULT '0',
  `stock` float DEFAULT '0',
  `IsPublic` int(11) DEFAULT '0',
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `priceSale` int(11) DEFAULT NULL,
  `startSale` datetime DEFAULT NULL,
  `endSale` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.product: ~11 rows (approximately)
INSERT INTO `product` (`ID`, `categoryID`, `name`, `price`, `image`, `description`, `rate`, `numOfReviews`, `sold`, `stock`, `IsPublic`, `createdAt`, `updatedAt`, `priceSale`, `startSale`, `endSale`) VALUES
	(1, 1, 'Onion', 12000, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*', NULL, 4.75, 5, 21, 9, 0, '2022-10-03 03:02:39', '2022-10-04 11:02:48', 9000, '2022-10-02 00:27:22', '2022-10-04 00:00:00'),
	(2, 1, 'Potato', 12000, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fsweet_potatoes.jpg?alt=media&token=9ce69b29-5389-4261-8840-3f8d6990eaec', NULL, 0, 0, 73, 24, 1, '2022-10-03 03:02:39', '2022-11-28 22:43:06', 9000, '2022-10-01 01:10:49', '2022-10-03 00:00:00'),
	(3, 2, 'Grape', 12000, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fgrape.jpg?alt=media&token=86c77f4c-c97f-411b-9eec-97cd76a88a93', NULL, 5, 1, 42, 38, 1, '2022-10-03 03:02:39', '2022-11-28 23:09:50', 9000, '2022-10-05 01:11:06', '2022-10-09 01:11:06'),
	(4, 2, 'Ginger', 12000, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fginger.jpg?alt=media&token=4bcb4d8e-d589-4a0c-885a-42f85842e03a', NULL, 4.75, 3, 30, 22, 1, '2022-10-03 03:02:39', '2022-11-28 23:08:45', 9000, '2022-10-01 01:11:06', '2022-10-04 01:11:06'),
	(6, 2, 'Bok Choy', 12000, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fbok_choy.jpg?alt=media&token=607c91c4-7f5e-41a1-a23f-7bd0099675bc', NULL, 5, 1, 26, 408, 1, '2022-10-03 03:02:39', '2022-11-28 23:08:15', 9000, '2022-10-14 23:25:03', '2022-10-17 23:25:11'),
	(9, 1, 'Pomegranate', 12000, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fpomegranate.jpg?alt=media&token=3a9f1840-d95d-40ef-b60f-998ab445c944', NULL, 0, 0, 10, 136, 1, '2022-10-04 09:48:18', '2022-11-28 23:07:03', 9000, '2022-10-15 23:25:44', '2022-10-17 23:25:46'),
	(16, 1, 'kiwi', 120000, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*', '', 0, 0, 0, 36, 0, '2022-10-20 00:31:42', '2022-10-20 00:31:42', 9000, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(17, 3, 'kiwi', 120000, 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/assortment-of-colorful-ripe-tropical-fruits-top-royalty-free-image-995518546-1564092355.jpg?crop=0.982xw:0.736xh;0,0.189xh&resize=980:*', '', 0, 0, 0, 30, 1, '2022-10-29 00:52:19', '2022-11-18 21:54:06', 9000, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(20, 1, 'Apple', 113, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fapple.jpg?alt=media&token=6e82352e-db6a-4354-812e-0054dfbf1edb', 'Táo ngọt lémmm\n', 0, 0, 1, 0, 1, '2022-11-21 11:45:53', '2022-12-03 11:37:03', 100, '2022-11-27 12:00:00', '2022-11-30 12:00:00'),
	(21, 2, 'Chilli', 1300, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fchilli.jpg?alt=media&token=3fd62d75-ea97-44c9-a077-a97b751cd00f', 'Cay xè lưỡi', 0, 0, 0, 0, 1, '2022-11-21 11:47:29', '2022-12-02 19:17:59', 1000, '2022-11-27 12:00:00', '2022-11-29 12:00:00'),
	(22, 1, 'Watermelon', 100, 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fwatermelon.jpg?alt=media&token=e19e7681-f114-4f80-8e45-08307255e299', 'Dưa hấu ngon, bổ, rẻ', 0, 0, 0, 0, 1, '2022-11-28 19:17:27', '2022-11-28 19:39:25', 0, NULL, NULL);

-- Dumping structure for table sql6525734.review
CREATE TABLE IF NOT EXISTS `review` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `productID` int(11) DEFAULT NULL,
  `rate` int(11) DEFAULT NULL,
  `comment` varchar(500) DEFAULT NULL,
  `IsPublic` int(11) DEFAULT '1',
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.review: ~9 rows (approximately)
INSERT INTO `review` (`ID`, `userID`, `productID`, `rate`, `comment`, `IsPublic`, `createdAt`) VALUES
	(16, 14, 1, 4, 'Fontend khÃ´ng chá»‹u lÃ m', 0, '2022-10-16 02:28:15'),
	(17, 14, 1, 4, 'Giá»¥c mÃ£i cÅ©ng khÃ´ng chá»‹u lÃ m', 1, '2022-10-16 02:30:57'),
	(18, 14, 4, 4, 'TEst', 1, '2022-10-16 02:30:57'),
	(19, 14, 1, 5, '1', 1, '2022-10-16 09:15:56'),
	(20, 14, 1, 5, '1', 1, '2022-10-16 23:13:17'),
	(21, 14, 4, 5, 'test', 1, '2022-10-16 23:13:18'),
	(22, 15, 3, 5, 'Nho ngọt quá', 1, '2022-11-30 16:58:31'),
	(23, 15, 4, 5, 'Gừng thơm quá', 1, '2022-11-30 16:58:32'),
	(24, 15, 6, 5, 'Cải ngọt quá', 1, '2022-11-30 16:58:32');

-- Dumping structure for table sql6525734.shippingDetail
CREATE TABLE IF NOT EXISTS `shippingDetail` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `orderID` int(11) DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.shippingDetail: ~21 rows (approximately)
INSERT INTO `shippingDetail` (`ID`, `orderID`, `createdBy`, `description`, `createdAt`) VALUES
	(1, 4, NULL, 'Order has been created', '2022-10-23 01:48:54'),
	(2, 5, NULL, 'Order has been created', '2022-10-23 01:57:55'),
	(4, 6, NULL, 'Order has been created', '2022-11-01 15:42:06'),
	(5, 7, NULL, 'Order has been created', '2022-11-01 21:07:54'),
	(6, 8, NULL, 'Order has been created', '2022-11-01 21:23:04'),
	(8, 9, 0, 'Order has been created', '2022-11-21 15:46:51'),
	(9, 10, 0, 'Order has been created', '2022-11-27 14:52:19'),
	(10, 11, 0, 'Order has been created', '2022-11-27 14:56:07'),
	(11, 12, 0, 'Order has been created', '2022-11-27 15:08:38'),
	(19, 13, 0, 'Order has been created', '2022-11-30 16:36:35'),
	(22, 14, 0, 'Order has been created', '2022-12-02 19:28:02'),
	(27, 1, NULL, 'Order has been created', '2022-12-09 01:23:21'),
	(28, 2, NULL, 'Order has been created', '2022-12-09 01:23:28'),
	(29, 3, NULL, 'Order has been created', '2022-12-09 01:23:33'),
	(30, 14, NULL, 'Order is being shipped', '2022-12-09 01:24:49'),
	(31, 12, NULL, 'Order is being shipped', '2022-12-09 01:25:54'),
	(32, 12, NULL, 'Order has been delivered', '2022-12-09 03:00:43'),
	(33, 14, NULL, 'Order has been delivered', '2022-12-09 03:10:11'),
	(34, 1, NULL, 'Order is being shipped', '2022-12-09 03:13:04'),
	(35, 1, NULL, 'Customers do not answer the phone', '2022-12-09 14:38:59'),
	(36, 1, NULL, 'Order is being shipped', '2022-12-09 14:41:35'),
	(37, 1, NULL, 'Product is being delivered to a wrong address', '2022-12-09 14:53:26'),
	(38, 2, NULL, 'Order is being shipped', '2022-12-09 14:56:15'),
	(39, 2, NULL, 'Product is being delivered to a wrong address', '2022-12-09 14:57:39');

-- Dumping structure for table sql6525734.shoppingCart
CREATE TABLE IF NOT EXISTS `shoppingCart` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `productID` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.shoppingCart: ~2 rows (approximately)
INSERT INTO `shoppingCart` (`ID`, `userID`, `productID`, `quantity`) VALUES
	(30, 4, 3, 2),
	(41, 14, 22, 3);

-- Dumping structure for table sql6525734.slider
CREATE TABLE IF NOT EXISTS `slider` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(500) DEFAULT NULL,
  `URLImage` varchar(500) DEFAULT NULL,
  `URLPage` varchar(500) DEFAULT NULL,
  `sort` int(11) DEFAULT '0',
  `IsPublic` binary(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.slider: ~4 rows (approximately)
INSERT INTO `slider` (`ID`, `description`, `URLImage`, `URLPage`, `sort`, `IsPublic`) VALUES
	(1, 'Happy International Women\'s Day 20-10', 'https://bizweb.dktcdn.net/thumb/1024x1024/100/065/538/files/thiet-ke-khong-ten-1bbdfe8a-e534-4bc1-a0ea-33a1c805f8ed.jpg?v=1646026267314', NULL, 1, _binary 0x31),
	(2, 'Flash sale up 50%', '', '', 1, _binary 0x31),
	(3, '20-10 Flash sale', '', '', 1, _binary 0x31),
	(4, '20-10 Super shopping day', '', '', 1, _binary 0x31);

-- Dumping structure for table sql6525734.stock_expiry
CREATE TABLE IF NOT EXISTS `stock_expiry` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL DEFAULT '0',
  `productID` int(11) DEFAULT '0',
  `quantity` int(11) DEFAULT NULL,
  `description` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.stock_expiry: ~1 rows (approximately)
INSERT INTO `stock_expiry` (`ID`, `userID`, `productID`, `quantity`, `description`, `createdAt`) VALUES
	(1, 0, 17, 15, '5', '2022-11-19 02:41:23');

-- Dumping structure for table sql6525734.stock_input
CREATE TABLE IF NOT EXISTS `stock_input` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL DEFAULT '0',
  `productID` int(11) DEFAULT '0',
  `quantity` int(11) DEFAULT '0',
  `description` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.stock_input: ~1 rows (approximately)
INSERT INTO `stock_input` (`ID`, `userID`, `productID`, `quantity`, `description`, `createdAt`) VALUES
	(1, 0, 17, 9, NULL, '2022-11-19 02:23:50');

-- Dumping structure for table sql6525734.tbl_role
CREATE TABLE IF NOT EXISTS `tbl_role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_iubw515ff0ugtm28p8g3myt0h` (`role_name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.tbl_role: 3 rows
/*!40000 ALTER TABLE `tbl_role` DISABLE KEYS */;
INSERT INTO `tbl_role` (`id`, `role_name`) VALUES
	(1, 'ROLE_USER'),
	(3, 'ROLE_ADMIN'),
	(2, 'ROLE_SHIPPER');
/*!40000 ALTER TABLE `tbl_role` ENABLE KEYS */;

-- Dumping structure for table sql6525734.user
CREATE TABLE IF NOT EXISTS `user` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `role` int(11) DEFAULT '0',
  `phone` varchar(20) DEFAULT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.user: ~10 rows (approximately)
INSERT INTO `user` (`ID`, `email`, `password`, `role`, `phone`, `firstName`, `lastName`, `name`, `avatar`, `createdAt`, `updatedAt`) VALUES
	(4, 'user2@gmail.com', '$2y$10$bA8Am.ByZJftVt9wmpc17uuko5dLdGPHPmk1zodB0psCFVzNwtR52', 2, '123456', 'HAHA', '123', 'HAHA 123', 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fdipper.png?alt=media&token=339fd039-592e-4f53-9f4d-cf9280b63983', '1900-01-29 19:38:48', '2022-11-28 17:59:21'),
	(7, 'user3@gmail.com', '$2y$10$YMju2RW67zOBnVAEOW.nwepAq1FxTd246UdZb7wrPxkWZoX8F5sFK', 2, NULL, 'Tran', 'Nam', '2', 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg', NULL, NULL),
	(8, 'user4@gmail.com', '$2y$10$FP6qjFXCZRUY9TPdGbhhoOLeYCdClW3NRbP3ZSCScKZWxK.xF/EAy', 2, NULL, 'Tran', 'Nam', 'Tran Nam', 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg', NULL, NULL),
	(9, 'user5@gmail.com', '$2y$10$sanH3Hw0zn.XYhqWHvZUKuk3X.rhka0wo7Hj2a/4.yhUMC.S2UPCi', 2, NULL, 'Tran', 'Nam', 'Tran Nam', 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg', '2022-10-02 22:46:17', '2022-11-28 18:11:57'),
	(10, 'user6@gmail.com', '$2y$10$2r.T/0l08IV8SkPH5xQO3eR5xYYEJxpznTpJpKi.4R0x5QBVT3yFa', 2, NULL, 'Tran', 'Nam', 'Tran Nam', 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg', '2022-10-02 22:48:38', '2022-11-28 17:36:59'),
	(11, 'user7@gmail.com', '$2y$10$H1cFFm9yDvmULJS5nV6b7u9jI0uMN1OjGh3pbrvZzw.SoHNkHTqRC', 2, NULL, 'tran', 'Nam', 'tran Nam', 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg', '2022-10-03 07:55:32', '2022-11-28 17:31:20'),
	(14, 'admin@gmail.com', '$2y$10$ozHLrLdVkIsxREPYG3DhOuf9KIgoDb0va9kbohCplrWFFBxlWuPTS', 3, '0123456', 'Alu', 'Card', 'Alu Card', 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg', '2022-10-04 07:42:19', '2022-11-28 21:40:55'),
	(15, 'quang@gmail.com', '$2y$10$7bu8L7nKWsoLE2k6zy0pRuXVtemYmnvgjYByT0mA5Ndrc4oRdhNGS', 3, NULL, 'Phạm ', 'Quang', 'Phạm  Quang', 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fdipper.png?alt=media&token=475846a9-2785-4aa3-8c9b-0af5952c38a0', '2022-11-27 12:43:34', '2022-11-28 23:11:24'),
	(18, 'teenhainam2603@gmail.com', '$2y$10$S6B/HHwH0KL4tQPbAXDR5eeLDGDwAnqIHOvopjNUaidQeWNTTlc7m', 3, NULL, '', 'Nam', 'Nam', 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg', '2022-11-29 00:42:46', '2022-11-30 14:36:21'),
	(20, 'phamq720@gmail.com', '$2y$10$uDa9u1rwrxhcyNc7GLWeaOXW8P4kPiM7R5nslC2Fozn2WCjEZ7EWS', 3, NULL, 'Sin ', 'Ha', 'Sin  Ha', 'https://firebasestorage.googleapis.com/v0/b/obrien-1482c.appspot.com/o/files%2Fdipper.png?alt=media&token=339fd039-592e-4f53-9f4d-cf9280b63983', '2022-12-01 17:38:24', '2022-12-02 20:18:23');

-- Dumping structure for table sql6525734.wishList
CREATE TABLE IF NOT EXISTS `wishList` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `productID` int(11) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table sql6525734.wishList: ~8 rows (approximately)
INSERT INTO `wishList` (`ID`, `userID`, `productID`, `createdAt`) VALUES
	(1, 14, 1, '2022-10-22 20:54:18'),
	(2, 14, 2, '2022-10-22 20:54:49'),
	(3, 4, 4, '2022-10-25 00:59:26'),
	(4, 4, 3, '2022-10-22 20:54:49'),
	(5, 15, 6, '2022-11-27 16:38:29'),
	(7, 15, 4, '2022-11-28 15:34:40'),
	(8, 15, 9, '2022-11-28 15:38:50'),
	(9, 15, 20, '2022-11-29 15:59:01');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
