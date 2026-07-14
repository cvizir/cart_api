-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 14, 2026 at 02:33 PM
-- Server version: 5.7.24
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demo_template`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `no` int(11) NOT NULL,
  `uptime` datetime NOT NULL,
  `admin_index` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_psw` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_pv` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ishow` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`no`, `uptime`, `admin_index`, `admin_code`, `admin_id`, `admin_psw`, `admin_pv`, `ishow`) VALUES
(1, '0000-00-00 00:00:00', '', '', '123', '123', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '6碼英數混合編碼',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT '父分類ID，頂層為NULL',
  `level` tinyint(4) NOT NULL COMMENT '分類層級：1, 2, 3',
  `sort_order` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_code`, `name`, `parent_id`, `level`, `sort_order`) VALUES
(1, 'ELC001', '3C電子', NULL, 1, 0),
(2, 'CLO001', '流行服飾', NULL, 1, 0),
(3, 'HOM001', '居家生活', NULL, 1, 0),
(4, 'SPO001', '戶外運動', NULL, 1, 0),
(5, 'BOK001', '圖書文具', NULL, 1, 0),
(6, 'ELC002', '電腦資訊', 1, 2, 0),
(7, 'ELC003', '手機通訊', 1, 2, 0),
(8, 'ELC004', '影音家電', 1, 2, 0),
(9, 'CLO002', '男裝', 2, 2, 0),
(10, 'CLO003', '女裝', 2, 2, 0),
(11, 'CLO004', '鞋包配件', 2, 2, 0),
(12, 'HOM002', '傢俱', 3, 2, 0),
(13, 'HOM003', '廚房用品', 3, 2, 0),
(14, 'HOM004', '衛浴用品', 3, 2, 0),
(15, 'SPO002', '健身器材', 4, 2, 0),
(16, 'SPO003', '露營用具', 4, 2, 0),
(17, 'SPO004', '運動服飾', 4, 2, 0),
(18, 'BOK002', '中文書', 5, 2, 0),
(19, 'BOK003', '外文書', 5, 2, 0),
(20, 'BOK004', '辦公文具', 5, 2, 0),
(21, 'CMP001', '筆記型電腦', 6, 3, 0),
(22, 'CMP002', '桌上型電腦', 6, 3, 0),
(23, 'CMP003', '電腦零組件', 6, 3, 0),
(24, 'PHN001', '智慧型手機', 7, 3, 0),
(25, 'PHN002', '平板電腦', 7, 3, 0),
(26, 'PHN003', '行動電源', 7, 3, 0),
(27, 'AUD001', '液晶電視', 8, 3, 0),
(28, 'AUD002', '藍牙喇叭', 8, 3, 0),
(29, 'AUD003', '真無線耳機', 8, 3, 0),
(30, 'MEN001', '上衣', 9, 3, 0),
(31, 'MEN002', '褲子', 9, 3, 0),
(32, 'MEN003', '外套', 9, 3, 0),
(33, 'WMN001', '洋裝', 10, 3, 0),
(34, 'WMN002', '裙子', 10, 3, 0),
(35, 'WMN003', '上衣', 10, 3, 0),
(36, 'ACS001', '運動鞋', 11, 3, 0),
(37, 'ACS002', '後背包', 11, 3, 0),
(38, 'ACS003', '手錶', 11, 3, 0),
(39, 'FNR001', '沙發', 12, 3, 0),
(40, 'FNR002', '床架', 12, 3, 0),
(41, 'FNR003', '衣櫃', 12, 3, 0),
(42, 'KTN001', '鍋具', 13, 3, 0),
(43, 'KTN002', '餐具', 13, 3, 0),
(44, 'KTN003', '保鮮盒', 13, 3, 0),
(45, 'BTH001', '毛巾', 14, 3, 0),
(46, 'BTH002', '沐浴乳', 14, 3, 0),
(47, 'BTH003', '牙刷', 14, 3, 0),
(48, 'FIT001', '啞鈴', 15, 3, 0),
(49, 'FIT002', '瑜珈墊', 15, 3, 0),
(50, 'FIT003', '跑步機', 15, 3, 0),
(51, 'CMP004', '帳篷', 16, 3, 0),
(52, 'CMP005', '睡袋', 16, 3, 0),
(53, 'CMP006', '露營椅', 16, 3, 0),
(54, 'SPF001', '排汗衫', 17, 3, 0),
(55, 'SPF002', '運動褲', 17, 3, 0),
(56, 'SPF003', '壓力褲', 17, 3, 0),
(57, 'CBK001', '文學小說', 18, 3, 0),
(58, 'CBK002', '商業理財', 18, 3, 0),
(59, 'CBK003', '心理勵志', 18, 3, 0),
(60, 'EBK001', 'Language', 19, 3, 0),
(61, 'EBK002', 'Business', 19, 3, 0),
(62, 'EBK003', 'Fiction', 19, 3, 0),
(63, 'STN001', '原子筆', 20, 3, 0),
(64, 'STN002', '筆記本', 20, 3, 0),
(65, 'STN003', '資料夾', 20, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `no` int(11) NOT NULL,
  `uptime` datetime NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sex` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pid` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mem_lv` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mem_lv_start` datetime NOT NULL,
  `mem_lv_end` datetime NOT NULL,
  `regtime` date NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zipcode` int(11) NOT NULL,
  `city` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addr` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cp_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cp_title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthday` date NOT NULL,
  `email_check` int(11) NOT NULL,
  `esend` int(11) NOT NULL,
  `wish_list` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login` datetime NOT NULL,
  `buy_total` int(11) NOT NULL,
  `mem_ps` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mem_mode` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`no`, `uptime`, `name`, `member_code`, `nickname`, `sex`, `pid`, `mem_lv`, `mem_lv_start`, `mem_lv_end`, `regtime`, `email`, `account`, `password`, `tel`, `phone`, `country`, `zipcode`, `city`, `area`, `addr`, `cp_id`, `cp_title`, `birthday`, `email_check`, `esend`, `wish_list`, `photo`, `last_login`, `buy_total`, `mem_ps`, `mem_mode`) VALUES
(1, '2017-02-08 10:23:36', '郭芳根', '8ZG8740O7D5', '', '1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00', '1', '', '', '2', '3', '', 112, '臺北市', '北投區', '承德路七段206號1樓', '', '', '0000-00-00', 0, 0, '', '', '0000-00-00 00:00:00', 0, '4', ''),
(2, '2017-02-08 11:18:47', '吳捷興', '4CD5794FIB5', '', '1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00', '11', '', '', '22', '33', '', 111, '臺北市', '士林區', '致遠三路125號4樓', '', '', '0000-00-00', 0, 0, '', '', '0000-00-00 00:00:00', 0, '44', ''),
(3, '2017-02-09 10:26:12', '王智玄', '5QN2F918774', '', '1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00', 'cvizir@gmail.com', 'sean', '12345', '02-23081233', '0912345678', '', 108, '臺北市', '萬華區', '大埔街7號', '', '', '0000-00-00', 0, 0, '', '', '0000-00-00 00:00:00', 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `orderlist`
--

CREATE TABLE `orderlist` (
  `no` int(11) NOT NULL,
  `order_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orderlist_num` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uptime` datetime NOT NULL,
  `donate_date` date NOT NULL,
  `member_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int(11) NOT NULL,
  `order_ps` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_state` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orderlist`
--

INSERT INTO `orderlist` (`no`, `order_code`, `orderlist_num`, `uptime`, `donate_date`, `member_no`, `member_code`, `amount`, `order_ps`, `order_state`) VALUES
(2, 'UT6GI4W1W6', '111', '2017-02-08 11:19:33', '2017-02-08', '', '4CD5794FIB5', 1000, '備註', ''),
(3, '8H8Z7937RG', '456', '2017-02-08 10:22:09', '2017-02-07', '', '8ZG8740O7D5', 1999, '備註', ''),
(7, 'UK2Y675R42', '2345', '2017-02-10 12:13:45', '2017-02-10', '', '5QN2F918774', 1688, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_code` (`category_code`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `orderlist`
--
ALTER TABLE `orderlist`
  ADD PRIMARY KEY (`no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orderlist`
--
ALTER TABLE `orderlist`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
