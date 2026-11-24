-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2026 at 11:19 PM
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
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `no` int(11) NOT NULL COMMENT '會員流水號',
  `uptime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '真實姓名',
  `member_code` char(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '會員代碼(英數混合9位)',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '暱稱(英文名)',
  `sex` int(2) DEFAULT NULL COMMENT '性別',
  `pid` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '身分證字號',
  `member_mode` tinyint(4) DEFAULT '1' COMMENT '會員狀態',
  `regtime` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '註冊時間',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '電子郵件',
  `photo` text COLLATE utf8mb4_unicode_ci COMMENT '大頭照',
  `psw` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密碼',
  `tel` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '市內電話',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手機號碼',
  `zipcode` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '郵遞區號',
  `city` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '居住縣市',
  `area` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '鄉鎮市區',
  `addr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '詳細地址',
  `cp_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '公司統編',
  `cp_title` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '公司抬頭',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `esend` int(4) DEFAULT '0' COMMENT '訂閱電子報',
  `email_check` int(4) DEFAULT '0' COMMENT '信箱驗證'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='會員資料表';

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`no`, `uptime`, `name`, `member_code`, `nickname`, `sex`, `pid`, `member_mode`, `regtime`, `email`, `photo`, `psw`, `tel`, `phone`, `zipcode`, `city`, `area`, `addr`, `cp_id`, `cp_title`, `birthday`, `esend`, `email_check`) VALUES
(1, '2026-08-31 23:29:12', '王智玄', 'A123456789', 'Sean', 1, 'A123456789', 1, '2026-05-26 14:36:09', 'alex01@test.com', 'default.jpg', 'pwd_hash_1', '02-21234567', '0911222333', '110', '台北市', '萬華區', '市府路1號', NULL, NULL, '1990-05-12', 1, 1),
(2, '2026-05-26 14:36:09', '林婉婷', 'X9Y8Z7W6V', 'Betty', 2, 'B223456789', 1, '2026-05-26 14:36:09', 'betty02@test.com', 'default.jpg', 'pwd_hash_2', '03-31234567', '0922333444', '330', '桃園市', '桃園區', '中正路1號', '12345678', '測試科技', '1992-08-24', 0, 1),
(3, '2026-05-26 14:36:09', '張家豪', 'K5J4H3G2F', 'Chris', 1, 'C123456789', 1, '2026-05-26 14:36:09', 'chris03@test.com', 'default.jpg', 'pwd_hash_3', '04-41234567', '0933444555', '407', '台中市', '西屯區', '台灣大道1號', NULL, NULL, '1985-11-03', 1, 1),
(4, '2026-05-26 14:36:09', '李佳穎', 'Q1W2E3R4T', 'Daisy', 2, 'D223456789', 1, '2026-05-26 14:36:09', 'daisy04@test.com', 'default.jpg', 'pwd_hash_4', '06-21234567', '0944555666', '708', '台南市', '安平區', '永華路1號', NULL, NULL, '1988-02-15', 1, 0),
(5, '2026-05-26 14:36:09', '王建宏', 'M9N8B7V6C', 'Eric', 1, 'E123456789', 1, '2026-05-26 14:36:09', 'eric05@test.com', 'default.jpg', 'pwd_hash_5', '07-71234567', '0955666777', '802', '高雄市', '苓雅區', '四維路1號', '87654321', '開發企業', '1995-09-30', 0, 1),
(6, '2026-05-26 14:36:09', '吳淑芬', 'Z1X2C3V4B', 'Fiona', 2, 'F223456789', 1, '2026-05-26 14:36:09', 'fiona06@test.com', 'default.jpg', 'pwd_hash_6', '02-22234567', '0912345678', '220', '新北市', '板橋區', '中山路1號', NULL, NULL, '1978-12-12', 1, 1),
(7, '2026-05-26 14:36:09', '劉冠宇', 'P0O9I8U7Y', 'George', 1, 'G123456789', 1, '2026-05-26 14:36:09', 'george07@test.com', 'default.jpg', 'pwd_hash_7', '03-51234567', '0923456789', '300', '新竹市', '東區', '中正路2號', NULL, NULL, '1993-04-18', 1, 1),
(8, '2026-05-26 14:36:09', '蔡佩珊', 'L1K2J3H4G', 'Helen', 2, 'H223456789', 1, '2026-05-26 14:36:09', 'helen08@test.com', 'default.jpg', 'pwd_hash_8', '04-71234567', '0934567890', '500', '彰化縣', '彰化市', '光復路1號', '23456789', '設計工坊', '1991-07-22', 0, 0),
(9, '2026-05-26 14:36:09', '楊宗翰', 'T5R4E3W2Q', 'Ian', 1, 'I123456789', 1, '2026-05-26 14:36:09', 'ian09@test.com', 'default.jpg', 'pwd_hash_9', '05-21234567', '0945678901', '600', '嘉義市', '東區', '中山路3號', NULL, NULL, '1987-10-05', 1, 1),
(10, '2026-05-26 14:36:09', '許雅筑', 'Y6U7I8O9P', 'Judy', 2, 'J223456789', 1, '2026-05-26 14:36:09', 'judy10@test.com', 'default.jpg', 'pwd_hash_10', '08-71234567', '0956789012', '900', '屏東縣', '屏東市', '自由路1號', NULL, NULL, '1996-01-28', 1, 1),
(11, '2026-05-26 14:36:09', '陳建彰', 'A9S8D7F6G', 'Kevin', 1, 'K123456789', 0, '2026-05-26 14:36:09', 'kevin11@test.com', 'default.jpg', 'pwd_hash_11', '03-91234567', '0967890123', '260', '宜蘭縣', '宜蘭市', '縣政北路1號', NULL, NULL, '1989-06-14', 0, 0),
(12, '2026-05-26 14:36:09', '林詩涵', 'H5J6K7L8Z', 'Linda', 2, 'L223456789', 1, '2026-05-26 14:36:09', 'linda12@test.com', 'default.jpg', 'pwd_hash_12', '03-81234567', '0978901234', '970', '花蓮縣', '花蓮市', '府前路1號', '34567890', '東部貿易', '1994-03-09', 1, 1),
(13, '2026-05-26 14:36:09', '張宇軒', 'X2C3V4B5N', 'Mike', 1, 'M123456789', 1, '2026-05-26 14:36:09', 'mike13@test.com', 'default.jpg', 'pwd_hash_13', '089-3123456', '0989012345', '950', '台東縣', '台東市', '中山路4號', NULL, NULL, '1990-11-20', 1, 1),
(14, '2026-05-26 14:36:09', '李佩君', 'M1N2B3V4C', 'Nancy', 2, 'N223456789', 1, '2026-05-26 14:36:09', 'nancy14@test.com', 'default.jpg', 'pwd_hash_14', '06-91234567', '0990123456', '880', '澎湖縣', '馬公市', '治平路1號', NULL, NULL, '1986-05-17', 0, 1),
(15, '2026-05-26 14:36:09', '王偉倫', 'Q9W8E7R6T', 'Oliver', 1, 'O123456789', 1, '2026-05-26 14:36:09', 'oliver15@test.com', 'default.jpg', 'pwd_hash_15', '082-3123456', '0910234567', '893', '金門縣', '金城鎮', '民生路1號', NULL, NULL, '1998-12-03', 1, 1),
(16, '2026-05-26 14:36:09', '吳佳玲', 'P1O2I3U4Y', 'Penny', 2, 'P223456789', 1, '2026-05-26 14:36:09', 'penny16@test.com', 'default.jpg', 'pwd_hash_16', '0836-22345', '0920345678', '209', '連江縣', '南竿鄉', '介壽村1號', '45678901', '離島觀光', '1993-07-11', 1, 0),
(17, '2026-05-26 14:36:09', '劉家豪', 'Z9X8C7V6B', 'Quincy', 1, 'Q123456789', 1, '2026-05-26 14:36:09', 'quincy17@test.com', 'default.jpg', 'pwd_hash_17', '02-23234567', '0930456789', '104', '台北市', '中山區', '南京東路1號', NULL, NULL, '1984-02-28', 0, 1),
(18, '2026-05-26 14:36:09', '蔡雅雯', 'L9K8J7H6G', 'Rachel', 2, 'R223456789', 1, '2026-05-26 14:36:09', 'rachel18@test.com', 'default.jpg', 'pwd_hash_18', '02-24234567', '0940567890', '242', '新北市', '新莊區', '中正路2號', NULL, NULL, '1997-09-08', 1, 1),
(19, '2026-05-26 14:36:09', '楊志明', 'T1R2E3W4Q', 'Steve', 1, 'S123456789', 1, '2026-05-26 14:36:09', 'steve19@test.com', 'default.jpg', 'pwd_hash_19', '04-25234567', '0950678901', '412', '台中市', '大里區', '國光路1號', NULL, NULL, '1991-04-14', 1, 1),
(20, '2026-05-26 14:36:09', '許婷婷', 'Y1U2I3O4P', 'Tina', 2, 'T223456789', -1, '2026-05-26 14:36:09', 'tina20@test.com', 'default.jpg', 'pwd_hash_20', '07-26234567', '0960789012', '830', '高雄市', '鳳山區', '光遠路1號', NULL, NULL, '1988-10-25', 0, 0),
(21, '2026-05-26 14:36:09', '陳冠廷', 'A5S4D3F2G', 'Ulysses', 1, 'U123456789', 1, '2026-05-26 14:36:09', 'ulysses21@test.com', 'default.jpg', 'pwd_hash_21', '03-47234567', '0970890123', '320', '桃園市', '中壢區', '環北路1號', '56789012', '資訊網通', '1995-06-06', 1, 1),
(22, '2026-05-26 14:36:09', '林婉如', 'H1J2K3L4Z', 'Vivian', 2, 'V223456789', 1, '2026-05-26 14:36:09', 'vivian22@test.com', 'default.jpg', 'pwd_hash_22', '06-28234567', '0980901234', '710', '台南市', '永康區', '中山南路1號', NULL, NULL, '1992-01-19', 1, 1),
(23, '2026-05-26 14:36:09', '張建彰', 'X8C7V6B5N', 'William', 1, 'W123456789', 1, '2026-05-26 14:36:09', 'william23@test.com', 'default.jpg', 'pwd_hash_23', '03-59234567', '0990012345', '302', '新竹縣', '竹北市', '光明六路1號', NULL, NULL, '1986-08-31', 0, 1),
(24, '2026-05-26 14:36:09', '李詩婷', 'M8N7B6V5C', 'Xena', 2, 'X223456789', 1, '2026-05-26 14:36:09', 'xena24@test.com', 'default.jpg', 'pwd_hash_24', '04-70234567', '0911123456', '510', '彰化縣', '員林市', '中山路2號', NULL, NULL, '1999-03-22', 1, 1),
(25, '2026-05-26 14:36:09', '王宗憲', 'Q4W5E6R7T', 'Yuri', 1, 'Y123456789', 1, '2026-05-26 14:36:09', 'yuri25@test.com', 'default.jpg', 'pwd_hash_25', '05-21234567', '0922234567', '621', '嘉義縣', '民雄鄉', '建國路1號', NULL, NULL, '1990-12-07', 1, 1),
(26, '2026-05-26 14:36:09', '吳欣潔', 'P8O7I6U5Y', 'Zoe', 2, 'Z223456789', 1, '2026-05-26 14:36:09', 'zoe26@test.com', 'default.jpg', 'pwd_hash_26', '08-72234567', '0933345678', '928', '屏東縣', '東港鎮', '光復路2號', '67890123', '海產行', '1987-05-29', 0, 1),
(27, '2026-05-26 14:36:09', '劉宇軒', 'Z2X3C4V5B', 'Adam', 1, 'A123456790', 1, '2026-05-26 14:36:09', 'adam27@test.com', 'default.jpg', 'pwd_hash_27', '02-25234567', '0944456789', '106', '台北市', '大安區', '復興南路1號', NULL, NULL, '1994-11-15', 1, 1),
(28, '2026-05-26 14:36:09', '蔡佳蓉', 'L2K3J4H5G', 'Bella', 2, 'B223456790', 1, '2026-05-26 14:36:09', 'bella28@test.com', 'default.jpg', 'pwd_hash_28', '02-26234567', '0955567890', '231', '新北市', '新店區', '北新路1號', NULL, NULL, '1989-07-04', 1, 1),
(29, '2026-05-26 14:36:09', '楊宗憲', 'T8R7E6W5Q', 'Colin', 1, 'C123456790', 1, '2026-05-26 14:36:09', 'colin29@test.com', 'default.jpg', 'pwd_hash_29', '04-27234567', '0966678901', '420', '台中市', '豐原區', '中正路3號', NULL, NULL, '1985-09-18', 0, 1),
(30, '2026-05-26 14:36:09', '許佩璇', 'Y8U7I6O5P', 'Diana', 2, 'D223456790', 1, '2026-05-26 14:36:09', 'diana30@test.com', 'default.jpg', 'pwd_hash_30', '07-28234567', '0977789012', '814', '高雄市', '仁武區', '中正路4號', '78901234', '物流實業', '1996-04-10', 1, 1),
(31, '2026-05-26 14:36:09', '陳家榮', 'A2S3D4F5G', 'Ethan', 1, 'E123456790', 1, '2026-05-26 14:36:09', 'ethan31@test.com', 'default.jpg', 'pwd_hash_31', '03-39234567', '0988890123', '334', '桃園市', '八德區', '介壽路1號', NULL, NULL, '1991-12-25', 1, 1),
(32, '2026-05-26 14:36:09', '林靜雯', 'H8J7K6L5Z', 'Faye', 2, 'F223456790', 0, '2026-05-26 14:36:09', 'faye32@test.com', 'default.jpg', 'pwd_hash_32', '06-20234567', '0999901234', '744', '台南市', '新市區', '南科三路1號', NULL, NULL, '1988-06-13', 0, 0),
(33, '2026-05-26 14:36:09', '張柏宇', 'X5C6V7B8N', 'Gavin', 1, 'G123456790', 1, '2026-05-26 14:36:09', 'gavin33@test.com', 'default.jpg', 'pwd_hash_33', '03-51234568', '0910112233', '310', '新竹縣', '竹東鎮', '中正路5號', NULL, NULL, '1993-02-14', 1, 1),
(34, '2026-05-26 14:36:09', '李雅婷', 'M2N3B4V5C', 'Hazel', 2, 'H223456790', 1, '2026-05-26 14:36:09', 'hazel34@test.com', 'default.jpg', 'pwd_hash_34', '04-72234567', '0920223344', '505', '彰化縣', '鹿港鎮', '中山路5號', '89012345', '文化創意', '1997-10-27', 1, 1),
(35, '2026-05-26 14:36:09', '王俊宏', 'Q2W3E4R5T', 'Ivan', 1, 'I123456790', 1, '2026-05-26 14:36:09', 'ivan35@test.com', 'default.jpg', 'pwd_hash_35', '05-23234567', '0930334455', '608', '嘉義縣', '水上鄉', '中山路6號', NULL, NULL, '1992-05-08', 0, 1),
(36, '2026-05-26 14:36:09', '吳詩婷', 'P2O3I4U5Y', 'Jane', 2, 'J223456790', 1, '2026-05-26 14:36:09', 'jane36@test.com', 'default.jpg', 'pwd_hash_36', '08-74234567', '0940445566', '946', '屏東縣', '恆春鎮', '中正路7號', NULL, NULL, '1986-09-12', 1, 1),
(37, '2026-05-26 14:36:09', '劉建宏', 'Z6X5C4V3B', 'Kyle', 1, 'K123456790', 1, '2026-05-26 14:36:09', 'kyle37@test.com', 'default.jpg', 'pwd_hash_37', '02-24234568', '0950556677', '200', '基隆市', '仁愛區', '忠一路1號', NULL, NULL, '1995-11-01', 1, 1),
(38, '2026-05-26 14:36:09', '蔡婉如', 'L6K5J4H3G', 'Lily', 2, 'L223456790', 1, '2026-05-26 14:36:09', 'lily38@test.com', 'default.jpg', 'pwd_hash_38', '03-95234567', '0960667788', '265', '宜蘭縣', '羅東鎮', '中正北路1號', '90123456', '觀光民宿', '1989-08-16', 0, 1),
(39, '2026-05-26 14:36:09', '楊偉哲', 'T6R5E4W3Q', 'Mark', 1, 'M123456790', 1, '2026-05-26 14:36:09', 'mark39@test.com', 'default.jpg', 'pwd_hash_39', '03-86234567', '0970778899', '973', '花蓮縣', '吉安鄉', '中正路8號', NULL, NULL, '1994-01-23', 1, 1),
(40, '2026-05-26 14:36:09', '許佳穎', 'Y6U5I4O3P', 'Nina', 2, 'N223456790', 1, '2026-05-26 14:36:09', 'nina40@test.com', 'default.jpg', 'pwd_hash_40', '089-3723456', '0980889900', '961', '台東縣', '成功鎮', '中華路1號', NULL, NULL, '1987-12-19', 1, 1),
(41, '2026-05-26 14:36:09', '陳志明', 'A6S5D4F3G', 'Oscar', 1, 'O123456790', 1, '2026-05-26 14:36:09', 'oscar41@test.com', 'default.jpg', 'pwd_hash_41', '02-27234567', '0990990011', '100', '台北市', '中正區', '忠孝西路1號', NULL, NULL, '1990-07-30', 0, 1),
(42, '2026-05-26 14:36:09', '林雅雯', 'H6J5K4L3Z', 'Paula', 2, 'P223456790', -1, '2026-05-26 14:36:09', 'paula42@test.com', 'default.jpg', 'pwd_hash_42', '02-28234567', '0911001122', '235', '新北市', '中和區', '景平路1號', NULL, NULL, '1998-05-04', 1, 0),
(43, '2026-05-26 14:36:09', '張俊傑', 'X6C5V4B3N', 'Quinn', 1, 'Q123456790', 1, '2026-05-26 14:36:09', 'quinn43@test.com', 'default.jpg', 'pwd_hash_43', '04-29234567', '0922112233', '436', '台中市', '清水區', '中正路9號', '01234567', '建材公司', '1985-03-15', 1, 1),
(44, '2026-05-26 14:36:09', '李惠雯', 'M6N5B4V3C', 'Rose', 2, 'R223456790', 1, '2026-05-26 14:36:09', 'rose44@test.com', 'default.jpg', 'pwd_hash_44', '07-20234567', '0933223344', '811', '高雄市', '楠梓區', '建楠路1號', NULL, NULL, '1993-10-09', 1, 1),
(45, '2026-05-26 14:36:09', '王柏宇', 'Q6W5E4R3T', 'Sam', 1, 'S123456790', 1, '2026-05-26 14:36:09', 'sam45@test.com', 'default.jpg', 'pwd_hash_45', '03-41234568', '0944334455', '324', '桃園市', '平鎮區', '中豐路1號', NULL, NULL, '1984-11-27', 0, 1),
(46, '2026-05-26 14:36:09', '吳婉婷', 'P6O5I4U3Y', 'Tracy', 2, 'T223456790', 1, '2026-05-26 14:36:09', 'tracy46@test.com', 'default.jpg', 'pwd_hash_46', '06-22234567', '0955445566', '714', '台南市', '玉井區', '中正路10號', NULL, NULL, '1996-08-02', 1, 1),
(47, '2026-05-26 14:36:09', '劉政宏', 'Z5X4C3V2B', 'Uriel', 1, 'U123456790', 1, '2026-05-26 14:36:09', 'uriel47@test.com', 'default.jpg', 'pwd_hash_47', '03-53234567', '0966556677', '303', '新竹縣', '湖口鄉', '中正路11號', '10987654', '機電工程', '1991-01-08', 1, 1),
(48, '2026-05-26 14:36:09', '蔡靜宜', 'L5K4J3H2G', 'Vicky', 2, 'V223456790', 1, '2026-05-26 14:36:09', 'vicky48@test.com', 'default.jpg', 'pwd_hash_48', '04-74234567', '0977667788', '514', '彰化縣', '溪湖鎮', '員鹿路1號', NULL, NULL, '1989-12-31', 0, 1),
(49, '2026-05-26 14:36:09', '楊建明', 'T5R4E3W2Z', 'Wayne', 1, 'W123456790', 1, '2026-05-26 14:36:09', 'wayne49@test.com', 'default.jpg', 'pwd_hash_49', '05-25234567', '0988778899', '613', '嘉義縣', '朴子市', '光復路3號', NULL, NULL, '1995-04-20', 1, 1),
(50, '2026-05-26 14:36:09', '許詩涵', 'Y5U4I3O2P', 'Yvonne', 2, 'X223456790', 1, '2026-05-26 14:36:09', 'yvonne50@test.com', 'default.jpg', 'pwd_hash_50', '08-76234567', '0999889900', '912', '屏東縣', '內埔鄉', '廣濟路1號', NULL, NULL, '1986-07-07', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`no`),
  ADD UNIQUE KEY `idx_member_code` (`member_code`),
  ADD UNIQUE KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT COMMENT '會員流水號', AUTO_INCREMENT=51;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
