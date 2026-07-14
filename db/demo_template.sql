-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:3306
-- 產生時間： 2026-07-14 08:07:15
-- 伺服器版本： 5.7.24
-- PHP 版本： 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `demo_template`
--

-- --------------------------------------------------------

--
-- 資料表結構 `admin`
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
-- 傾印資料表的資料 `admin`
--

INSERT INTO `admin` (`no`, `uptime`, `admin_index`, `admin_code`, `admin_id`, `admin_psw`, `admin_pv`, `ishow`) VALUES
(1, '0000-00-00 00:00:00', '', '', '123', '123', '', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `members`
--

CREATE TABLE `members` (
  `no` int(11) NOT NULL COMMENT '會員流水號',
  `uptime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '真實姓名',
  `member_code` char(9) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '會員代碼(英數混合9位)',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '暱稱(英文名)',
  `sex` enum('M','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '性別',
  `pid` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '身分證字號',
  `member_mode` tinyint(4) DEFAULT '1' COMMENT '會員狀態',
  `regtime` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '註冊時間',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '電子郵件',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.jpg' COMMENT '大頭照',
  `psw` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密碼',
  `tel` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '市內電話',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手機號碼',
  `zipcode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '郵遞區號',
  `city` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '居住縣市',
  `area` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '鄉鎮市區',
  `addr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '詳細地址',
  `cp_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '公司統編',
  `cp_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '公司抬頭',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `esend` tinyint(4) DEFAULT '1' COMMENT '訂閱電子報',
  `email_check` tinyint(4) DEFAULT '0' COMMENT '信箱驗證'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='會員資料表';

--
-- 傾印資料表的資料 `members`
--

INSERT INTO `members` (`no`, `uptime`, `name`, `member_code`, `nickname`, `sex`, `pid`, `member_mode`, `regtime`, `email`, `photo`, `psw`, `tel`, `phone`, `zipcode`, `city`, `area`, `addr`, `cp_id`, `cp_title`, `birthday`, `esend`, `email_check`) VALUES
(1, '2026-05-26 14:37:53', '王智玄', 'A1B2C3D4E', 'Sean', 'M', 'A123456789', 1, '2026-05-26 14:36:09', 'alex01@test.com', 'default.jpg', 'pwd_hash_1', '02-21234567', '0911222333', '110', '台北市', '萬華區', '市府路1號', NULL, NULL, '1990-05-12', 1, 1),
(2, '2026-05-26 14:36:09', '林婉婷', 'X9Y8Z7W6V', 'Betty', 'F', 'B223456789', 1, '2026-05-26 14:36:09', 'betty02@test.com', 'default.jpg', 'pwd_hash_2', '03-31234567', '0922333444', '330', '桃園市', '桃園區', '中正路1號', '12345678', '測試科技', '1992-08-24', 0, 1),
(3, '2026-05-26 14:36:09', '張家豪', 'K5J4H3G2F', 'Chris', 'M', 'C123456789', 1, '2026-05-26 14:36:09', 'chris03@test.com', 'default.jpg', 'pwd_hash_3', '04-41234567', '0933444555', '407', '台中市', '西屯區', '台灣大道1號', NULL, NULL, '1985-11-03', 1, 1),
(4, '2026-05-26 14:36:09', '李佳穎', 'Q1W2E3R4T', 'Daisy', 'F', 'D223456789', 1, '2026-05-26 14:36:09', 'daisy04@test.com', 'default.jpg', 'pwd_hash_4', '06-21234567', '0944555666', '708', '台南市', '安平區', '永華路1號', NULL, NULL, '1988-02-15', 1, 0),
(5, '2026-05-26 14:36:09', '王建宏', 'M9N8B7V6C', 'Eric', 'M', 'E123456789', 1, '2026-05-26 14:36:09', 'eric05@test.com', 'default.jpg', 'pwd_hash_5', '07-71234567', '0955666777', '802', '高雄市', '苓雅區', '四維路1號', '87654321', '開發企業', '1995-09-30', 0, 1),
(6, '2026-05-26 14:36:09', '吳淑芬', 'Z1X2C3V4B', 'Fiona', 'F', 'F223456789', 1, '2026-05-26 14:36:09', 'fiona06@test.com', 'default.jpg', 'pwd_hash_6', '02-22234567', '0912345678', '220', '新北市', '板橋區', '中山路1號', NULL, NULL, '1978-12-12', 1, 1),
(7, '2026-05-26 14:36:09', '劉冠宇', 'P0O9I8U7Y', 'George', 'M', 'G123456789', 1, '2026-05-26 14:36:09', 'george07@test.com', 'default.jpg', 'pwd_hash_7', '03-51234567', '0923456789', '300', '新竹市', '東區', '中正路2號', NULL, NULL, '1993-04-18', 1, 1),
(8, '2026-05-26 14:36:09', '蔡佩珊', 'L1K2J3H4G', 'Helen', 'F', 'H223456789', 1, '2026-05-26 14:36:09', 'helen08@test.com', 'default.jpg', 'pwd_hash_8', '04-71234567', '0934567890', '500', '彰化縣', '彰化市', '光復路1號', '23456789', '設計工坊', '1991-07-22', 0, 0),
(9, '2026-05-26 14:36:09', '楊宗翰', 'T5R4E3W2Q', 'Ian', 'M', 'I123456789', 1, '2026-05-26 14:36:09', 'ian09@test.com', 'default.jpg', 'pwd_hash_9', '05-21234567', '0945678901', '600', '嘉義市', '東區', '中山路3號', NULL, NULL, '1987-10-05', 1, 1),
(10, '2026-05-26 14:36:09', '許雅筑', 'Y6U7I8O9P', 'Judy', 'F', 'J223456789', 1, '2026-05-26 14:36:09', 'judy10@test.com', 'default.jpg', 'pwd_hash_10', '08-71234567', '0956789012', '900', '屏東縣', '屏東市', '自由路1號', NULL, NULL, '1996-01-28', 1, 1),
(11, '2026-05-26 14:36:09', '陳建彰', 'A9S8D7F6G', 'Kevin', 'M', 'K123456789', 0, '2026-05-26 14:36:09', 'kevin11@test.com', 'default.jpg', 'pwd_hash_11', '03-91234567', '0967890123', '260', '宜蘭縣', '宜蘭市', '縣政北路1號', NULL, NULL, '1989-06-14', 0, 0),
(12, '2026-05-26 14:36:09', '林詩涵', 'H5J6K7L8Z', 'Linda', 'F', 'L223456789', 1, '2026-05-26 14:36:09', 'linda12@test.com', 'default.jpg', 'pwd_hash_12', '03-81234567', '0978901234', '970', '花蓮縣', '花蓮市', '府前路1號', '34567890', '東部貿易', '1994-03-09', 1, 1),
(13, '2026-05-26 14:36:09', '張宇軒', 'X2C3V4B5N', 'Mike', 'M', 'M123456789', 1, '2026-05-26 14:36:09', 'mike13@test.com', 'default.jpg', 'pwd_hash_13', '089-3123456', '0989012345', '950', '台東縣', '台東市', '中山路4號', NULL, NULL, '1990-11-20', 1, 1),
(14, '2026-05-26 14:36:09', '李佩君', 'M1N2B3V4C', 'Nancy', 'F', 'N223456789', 1, '2026-05-26 14:36:09', 'nancy14@test.com', 'default.jpg', 'pwd_hash_14', '06-91234567', '0990123456', '880', '澎湖縣', '馬公市', '治平路1號', NULL, NULL, '1986-05-17', 0, 1),
(15, '2026-05-26 14:36:09', '王偉倫', 'Q9W8E7R6T', 'Oliver', 'M', 'O123456789', 1, '2026-05-26 14:36:09', 'oliver15@test.com', 'default.jpg', 'pwd_hash_15', '082-3123456', '0910234567', '893', '金門縣', '金城鎮', '民生路1號', NULL, NULL, '1998-12-03', 1, 1),
(16, '2026-05-26 14:36:09', '吳佳玲', 'P1O2I3U4Y', 'Penny', 'F', 'P223456789', 1, '2026-05-26 14:36:09', 'penny16@test.com', 'default.jpg', 'pwd_hash_16', '0836-22345', '0920345678', '209', '連江縣', '南竿鄉', '介壽村1號', '45678901', '離島觀光', '1993-07-11', 1, 0),
(17, '2026-05-26 14:36:09', '劉家豪', 'Z9X8C7V6B', 'Quincy', 'M', 'Q123456789', 1, '2026-05-26 14:36:09', 'quincy17@test.com', 'default.jpg', 'pwd_hash_17', '02-23234567', '0930456789', '104', '台北市', '中山區', '南京東路1號', NULL, NULL, '1984-02-28', 0, 1),
(18, '2026-05-26 14:36:09', '蔡雅雯', 'L9K8J7H6G', 'Rachel', 'F', 'R223456789', 1, '2026-05-26 14:36:09', 'rachel18@test.com', 'default.jpg', 'pwd_hash_18', '02-24234567', '0940567890', '242', '新北市', '新莊區', '中正路2號', NULL, NULL, '1997-09-08', 1, 1),
(19, '2026-05-26 14:36:09', '楊志明', 'T1R2E3W4Q', 'Steve', 'M', 'S123456789', 1, '2026-05-26 14:36:09', 'steve19@test.com', 'default.jpg', 'pwd_hash_19', '04-25234567', '0950678901', '412', '台中市', '大里區', '國光路1號', NULL, NULL, '1991-04-14', 1, 1),
(20, '2026-05-26 14:36:09', '許婷婷', 'Y1U2I3O4P', 'Tina', 'F', 'T223456789', -1, '2026-05-26 14:36:09', 'tina20@test.com', 'default.jpg', 'pwd_hash_20', '07-26234567', '0960789012', '830', '高雄市', '鳳山區', '光遠路1號', NULL, NULL, '1988-10-25', 0, 0),
(21, '2026-05-26 14:36:09', '陳冠廷', 'A5S4D3F2G', 'Ulysses', 'M', 'U123456789', 1, '2026-05-26 14:36:09', 'ulysses21@test.com', 'default.jpg', 'pwd_hash_21', '03-47234567', '0970890123', '320', '桃園市', '中壢區', '環北路1號', '56789012', '資訊網通', '1995-06-06', 1, 1),
(22, '2026-05-26 14:36:09', '林婉如', 'H1J2K3L4Z', 'Vivian', 'F', 'V223456789', 1, '2026-05-26 14:36:09', 'vivian22@test.com', 'default.jpg', 'pwd_hash_22', '06-28234567', '0980901234', '710', '台南市', '永康區', '中山南路1號', NULL, NULL, '1992-01-19', 1, 1),
(23, '2026-05-26 14:36:09', '張建彰', 'X8C7V6B5N', 'William', 'M', 'W123456789', 1, '2026-05-26 14:36:09', 'william23@test.com', 'default.jpg', 'pwd_hash_23', '03-59234567', '0990012345', '302', '新竹縣', '竹北市', '光明六路1號', NULL, NULL, '1986-08-31', 0, 1),
(24, '2026-05-26 14:36:09', '李詩婷', 'M8N7B6V5C', 'Xena', 'F', 'X223456789', 1, '2026-05-26 14:36:09', 'xena24@test.com', 'default.jpg', 'pwd_hash_24', '04-70234567', '0911123456', '510', '彰化縣', '員林市', '中山路2號', NULL, NULL, '1999-03-22', 1, 1),
(25, '2026-05-26 14:36:09', '王宗憲', 'Q4W5E6R7T', 'Yuri', 'M', 'Y123456789', 1, '2026-05-26 14:36:09', 'yuri25@test.com', 'default.jpg', 'pwd_hash_25', '05-21234567', '0922234567', '621', '嘉義縣', '民雄鄉', '建國路1號', NULL, NULL, '1990-12-07', 1, 1),
(26, '2026-05-26 14:36:09', '吳欣潔', 'P8O7I6U5Y', 'Zoe', 'F', 'Z223456789', 1, '2026-05-26 14:36:09', 'zoe26@test.com', 'default.jpg', 'pwd_hash_26', '08-72234567', '0933345678', '928', '屏東縣', '東港鎮', '光復路2號', '67890123', '海產行', '1987-05-29', 0, 1),
(27, '2026-05-26 14:36:09', '劉宇軒', 'Z2X3C4V5B', 'Adam', 'M', 'A123456790', 1, '2026-05-26 14:36:09', 'adam27@test.com', 'default.jpg', 'pwd_hash_27', '02-25234567', '0944456789', '106', '台北市', '大安區', '復興南路1號', NULL, NULL, '1994-11-15', 1, 1),
(28, '2026-05-26 14:36:09', '蔡佳蓉', 'L2K3J4H5G', 'Bella', 'F', 'B223456790', 1, '2026-05-26 14:36:09', 'bella28@test.com', 'default.jpg', 'pwd_hash_28', '02-26234567', '0955567890', '231', '新北市', '新店區', '北新路1號', NULL, NULL, '1989-07-04', 1, 1),
(29, '2026-05-26 14:36:09', '楊宗憲', 'T8R7E6W5Q', 'Colin', 'M', 'C123456790', 1, '2026-05-26 14:36:09', 'colin29@test.com', 'default.jpg', 'pwd_hash_29', '04-27234567', '0966678901', '420', '台中市', '豐原區', '中正路3號', NULL, NULL, '1985-09-18', 0, 1),
(30, '2026-05-26 14:36:09', '許佩璇', 'Y8U7I6O5P', 'Diana', 'F', 'D223456790', 1, '2026-05-26 14:36:09', 'diana30@test.com', 'default.jpg', 'pwd_hash_30', '07-28234567', '0977789012', '814', '高雄市', '仁武區', '中正路4號', '78901234', '物流實業', '1996-04-10', 1, 1),
(31, '2026-05-26 14:36:09', '陳家榮', 'A2S3D4F5G', 'Ethan', 'M', 'E123456790', 1, '2026-05-26 14:36:09', 'ethan31@test.com', 'default.jpg', 'pwd_hash_31', '03-39234567', '0988890123', '334', '桃園市', '八德區', '介壽路1號', NULL, NULL, '1991-12-25', 1, 1),
(32, '2026-05-26 14:36:09', '林靜雯', 'H8J7K6L5Z', 'Faye', 'F', 'F223456790', 0, '2026-05-26 14:36:09', 'faye32@test.com', 'default.jpg', 'pwd_hash_32', '06-20234567', '0999901234', '744', '台南市', '新市區', '南科三路1號', NULL, NULL, '1988-06-13', 0, 0),
(33, '2026-05-26 14:36:09', '張柏宇', 'X5C6V7B8N', 'Gavin', 'M', 'G123456790', 1, '2026-05-26 14:36:09', 'gavin33@test.com', 'default.jpg', 'pwd_hash_33', '03-51234568', '0910112233', '310', '新竹縣', '竹東鎮', '中正路5號', NULL, NULL, '1993-02-14', 1, 1),
(34, '2026-05-26 14:36:09', '李雅婷', 'M2N3B4V5C', 'Hazel', 'F', 'H223456790', 1, '2026-05-26 14:36:09', 'hazel34@test.com', 'default.jpg', 'pwd_hash_34', '04-72234567', '0920223344', '505', '彰化縣', '鹿港鎮', '中山路5號', '89012345', '文化創意', '1997-10-27', 1, 1),
(35, '2026-05-26 14:36:09', '王俊宏', 'Q2W3E4R5T', 'Ivan', 'M', 'I123456790', 1, '2026-05-26 14:36:09', 'ivan35@test.com', 'default.jpg', 'pwd_hash_35', '05-23234567', '0930334455', '608', '嘉義縣', '水上鄉', '中山路6號', NULL, NULL, '1992-05-08', 0, 1),
(36, '2026-05-26 14:36:09', '吳詩婷', 'P2O3I4U5Y', 'Jane', 'F', 'J223456790', 1, '2026-05-26 14:36:09', 'jane36@test.com', 'default.jpg', 'pwd_hash_36', '08-74234567', '0940445566', '946', '屏東縣', '恆春鎮', '中正路7號', NULL, NULL, '1986-09-12', 1, 1),
(37, '2026-05-26 14:36:09', '劉建宏', 'Z6X5C4V3B', 'Kyle', 'M', 'K123456790', 1, '2026-05-26 14:36:09', 'kyle37@test.com', 'default.jpg', 'pwd_hash_37', '02-24234568', '0950556677', '200', '基隆市', '仁愛區', '忠一路1號', NULL, NULL, '1995-11-01', 1, 1),
(38, '2026-05-26 14:36:09', '蔡婉如', 'L6K5J4H3G', 'Lily', 'F', 'L223456790', 1, '2026-05-26 14:36:09', 'lily38@test.com', 'default.jpg', 'pwd_hash_38', '03-95234567', '0960667788', '265', '宜蘭縣', '羅東鎮', '中正北路1號', '90123456', '觀光民宿', '1989-08-16', 0, 1),
(39, '2026-05-26 14:36:09', '楊偉哲', 'T6R5E4W3Q', 'Mark', 'M', 'M123456790', 1, '2026-05-26 14:36:09', 'mark39@test.com', 'default.jpg', 'pwd_hash_39', '03-86234567', '0970778899', '973', '花蓮縣', '吉安鄉', '中正路8號', NULL, NULL, '1994-01-23', 1, 1),
(40, '2026-05-26 14:36:09', '許佳穎', 'Y6U5I4O3P', 'Nina', 'F', 'N223456790', 1, '2026-05-26 14:36:09', 'nina40@test.com', 'default.jpg', 'pwd_hash_40', '089-3723456', '0980889900', '961', '台東縣', '成功鎮', '中華路1號', NULL, NULL, '1987-12-19', 1, 1),
(41, '2026-05-26 14:36:09', '陳志明', 'A6S5D4F3G', 'Oscar', 'M', 'O123456790', 1, '2026-05-26 14:36:09', 'oscar41@test.com', 'default.jpg', 'pwd_hash_41', '02-27234567', '0990990011', '100', '台北市', '中正區', '忠孝西路1號', NULL, NULL, '1990-07-30', 0, 1),
(42, '2026-05-26 14:36:09', '林雅雯', 'H6J5K4L3Z', 'Paula', 'F', 'P223456790', -1, '2026-05-26 14:36:09', 'paula42@test.com', 'default.jpg', 'pwd_hash_42', '02-28234567', '0911001122', '235', '新北市', '中和區', '景平路1號', NULL, NULL, '1998-05-04', 1, 0),
(43, '2026-05-26 14:36:09', '張俊傑', 'X6C5V4B3N', 'Quinn', 'M', 'Q123456790', 1, '2026-05-26 14:36:09', 'quinn43@test.com', 'default.jpg', 'pwd_hash_43', '04-29234567', '0922112233', '436', '台中市', '清水區', '中正路9號', '01234567', '建材公司', '1985-03-15', 1, 1),
(44, '2026-05-26 14:36:09', '李惠雯', 'M6N5B4V3C', 'Rose', 'F', 'R223456790', 1, '2026-05-26 14:36:09', 'rose44@test.com', 'default.jpg', 'pwd_hash_44', '07-20234567', '0933223344', '811', '高雄市', '楠梓區', '建楠路1號', NULL, NULL, '1993-10-09', 1, 1),
(45, '2026-05-26 14:36:09', '王柏宇', 'Q6W5E4R3T', 'Sam', 'M', 'S123456790', 1, '2026-05-26 14:36:09', 'sam45@test.com', 'default.jpg', 'pwd_hash_45', '03-41234568', '0944334455', '324', '桃園市', '平鎮區', '中豐路1號', NULL, NULL, '1984-11-27', 0, 1),
(46, '2026-05-26 14:36:09', '吳婉婷', 'P6O5I4U3Y', 'Tracy', 'F', 'T223456790', 1, '2026-05-26 14:36:09', 'tracy46@test.com', 'default.jpg', 'pwd_hash_46', '06-22234567', '0955445566', '714', '台南市', '玉井區', '中正路10號', NULL, NULL, '1996-08-02', 1, 1),
(47, '2026-05-26 14:36:09', '劉政宏', 'Z5X4C3V2B', 'Uriel', 'M', 'U123456790', 1, '2026-05-26 14:36:09', 'uriel47@test.com', 'default.jpg', 'pwd_hash_47', '03-53234567', '0966556677', '303', '新竹縣', '湖口鄉', '中正路11號', '10987654', '機電工程', '1991-01-08', 1, 1),
(48, '2026-05-26 14:36:09', '蔡靜宜', 'L5K4J3H2G', 'Vicky', 'F', 'V223456790', 1, '2026-05-26 14:36:09', 'vicky48@test.com', 'default.jpg', 'pwd_hash_48', '04-74234567', '0977667788', '514', '彰化縣', '溪湖鎮', '員鹿路1號', NULL, NULL, '1989-12-31', 0, 1),
(49, '2026-05-26 14:36:09', '楊建明', 'T5R4E3W2Z', 'Wayne', 'M', 'W123456790', 1, '2026-05-26 14:36:09', 'wayne49@test.com', 'default.jpg', 'pwd_hash_49', '05-25234567', '0988778899', '613', '嘉義縣', '朴子市', '光復路3號', NULL, NULL, '1995-04-20', 1, 1),
(50, '2026-05-26 14:36:09', '許詩涵', 'Y5U4I3O2P', 'Yvonne', 'F', 'X223456790', 1, '2026-05-26 14:36:09', 'yvonne50@test.com', 'default.jpg', 'pwd_hash_50', '08-76234567', '0999889900', '912', '屏東縣', '內埔鄉', '廣濟路1號', NULL, NULL, '1986-07-07', 1, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `news`
--

CREATE TABLE `news` (
  `no` int(11) NOT NULL,
  `news_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `news_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uptime` datetime NOT NULL,
  `wttime` date NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `you_tube_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `web_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `photo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ps` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `click_num` int(6) NOT NULL,
  `m_sort` int(2) NOT NULL,
  `top_ishow` int(2) NOT NULL,
  `ishow` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `orderlist`
--

CREATE TABLE `orderlist` (
  `no` int(11) NOT NULL,
  `order_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uptime` datetime NOT NULL,
  `buytime` datetime NOT NULL,
  `member_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_cpy` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_num` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `atm_num` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_num` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_info` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int(11) NOT NULL,
  `invoice_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_tel` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_get` int(2) NOT NULL,
  `invoice_country` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_store` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_city` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_area` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_addr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_zipcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receives_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receives_tel` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receives_mphone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receives_country` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receives_city` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receives_area` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receives_addr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receives_zipcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receives_time` int(11) NOT NULL,
  `invoice_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_num` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transportation` int(8) NOT NULL,
  `pay_mode` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pay_mode_price` int(11) NOT NULL,
  `pay_info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `get_mode` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ps` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_ps` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `transportation_ps` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_price` int(6) NOT NULL,
  `other_ps` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` int(11) NOT NULL,
  `counter_state` int(2) NOT NULL,
  `order_state` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_date` date NOT NULL,
  `pre_post_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `pdcat`
--

CREATE TABLE `pdcat` (
  `no` int(11) NOT NULL,
  `pdcat_code` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '第三層子分類代碼',
  `pdcat_m_code` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '關聯：第二層中分類代碼',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分類名稱/說明',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '連結網址',
  `link_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '連結開啟方式',
  `m_sort` int(11) DEFAULT '0' COMMENT '排序',
  `ishow` tinyint(1) DEFAULT '1' COMMENT '是否顯示 (1: 顯示, 0: 隱藏)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='第三層子分類表';

--
-- 傾印資料表的資料 `pdcat`
--

INSERT INTO `pdcat` (`no`, `pdcat_code`, `pdcat_m_code`, `name`, `url`, `link_type`, `m_sort`, `ishow`) VALUES
(1, 'C0000001', 'M0000001', '大聯盟服飾', '/category/sports/baseball/mlb', '', 1, 1),
(2, 'C0000002', 'M0000001', '經典賽紀念品', '/category/sports/baseball/wbc', '', 2, 1),
(3, 'C0000003', 'M0000001', '棒球手套', '/category/sports/baseball/glove', '', 3, 1),
(4, 'C0000004', 'M0000002', '專業跑鞋', '/category/sports/marathon/shoes', '', 1, 1),
(5, 'C0000005', 'M0000002', '排汗機能服', '/category/sports/marathon/clothes', '', 2, 1),
(6, 'C0000006', 'M0000002', '能量膠與補給', '/category/sports/marathon/food', '', 3, 1),
(7, 'C0000007', 'M0000003', '瑜珈墊', '/category/sports/fitness/yoga', '', 1, 1),
(8, 'C0000008', 'M0000003', '阻力帶', '/category/sports/fitness/band', '', 2, 1),
(9, 'C0000009', 'M0000003', '運動水壺', '/category/sports/fitness/bottle', '', 3, 1),
(10, 'C0000010', 'M0000004', '官方手燈', '/category/idol/cheer/lightstick', '', 1, 1),
(11, 'C0000011', 'M0000004', '手燈保護套', '/category/idol/cheer/cover', '', 2, 1),
(12, 'C0000012', 'M0000004', '會員專屬禮', '/category/idol/cheer/vip', '', 3, 1),
(13, 'C0000013', 'M0000005', '紀念T恤', '/category/idol/concert/tshirt', '', 1, 1),
(14, 'C0000014', 'M0000005', '紀念提袋', '/category/idol/concert/bag', '', 2, 1),
(15, 'C0000015', 'M0000005', '限定隨機小卡', '/category/idol/concert/photocard', '', 3, 1),
(16, 'C0000016', 'M0000006', '韓版正規專輯', '/category/idol/media/album', '', 1, 1),
(17, 'C0000017', 'M0000006', '演唱會DVD', '/category/idol/media/dvd', '', 2, 1),
(18, 'C0000018', 'M0000006', '特別版單曲', '/category/idol/media/single', '', 3, 1),
(19, 'C0000019', 'M0000007', '單人套餐券', '/category/food/yakiniku/single', '', 1, 1),
(20, 'C0000020', 'M0000007', '雙人和牛券', '/category/food/yakiniku/double', '', 2, 1),
(21, 'C0000021', 'M0000007', '精品燒肉禮盒', '/category/food/yakiniku/gift', '', 3, 1),
(22, 'C0000022', 'M0000008', '經典湯底包', '/category/food/hotpot/soup', '', 1, 1),
(23, 'C0000023', 'M0000008', '四人同行券', '/category/food/hotpot/ticket', '', 2, 1),
(24, 'C0000024', 'M0000008', '頂級肉品盤', '/category/food/hotpot/meat', '', 3, 1),
(25, 'C0000025', 'M0000009', '日式壽司券', '/category/food/international/sushi', '', 1, 1),
(26, 'C0000026', 'M0000009', '韓式料理券', '/category/food/international/korean', '', 2, 1),
(27, 'C0000027', 'M0000009', '義式餐廳券', '/category/food/international/italian', '', 3, 1),
(28, 'C0000028', 'M0000010', 'Vue3實戰指南', '/category/books/frontend/vue', '', 1, 1),
(29, 'C0000029', 'M0000010', 'JavaScript核心', '/category/books/frontend/js', '', 2, 1),
(30, 'C0000030', 'M0000010', 'UI元件庫設計', '/category/books/frontend/ui', '', 3, 1),
(31, 'C0000031', 'M0000011', 'Node.js開發', '/category/books/backend/node', '', 1, 1),
(32, 'C0000032', 'M0000011', 'MySQL效能調校', '/category/books/backend/mysql', '', 2, 1),
(33, 'C0000033', 'M0000011', 'API安全防護', '/category/books/backend/api', '', 3, 1),
(34, 'C0000034', 'M0000012', 'JLPT日檢單字', '/category/books/language/jlpt_vocab', '', 1, 1),
(35, 'C0000035', 'M0000012', 'JLPT日檢文法', '/category/books/language/jlpt_grammar', '', 2, 1),
(36, 'C0000036', 'M0000012', 'TOPIK韓檢', '/category/books/language/topik', '', 3, 1),
(37, 'C0000037', 'M0000013', 'iPhone 162', '88888888', '', 2, 0),
(38, 'C0000038', 'M0000013', 'iPhone 16 Plus', '/category/games/ar/sticker', '', 1, 1),
(39, 'C0000039', 'M0000013', 'iPhone 16 Pro', '/category/games/ar/figure', '', 3, 1),
(40, 'C0000040', 'M0000013', ' iPhone 16 Pro Max', '/category/games/console/switch', '', 1, 1),
(41, 'C0000041', 'M0000014', 'PS5遊戲', '/category/games/console/ps5', '', 2, 1),
(42, 'C0000042', 'M0000014', 'PC平台序號', '/category/games/console/pc', '', 3, 1),
(43, 'C0000043', 'M0000015', '機械鍵盤', '/category/games/hardware/keyboard', '', 1, 1),
(44, 'C0000044', 'M0000015', '高DPI電競滑鼠', '/category/games/hardware/mouse', '', 2, 1),
(45, 'C0000045', 'M0000015', '高更新率螢幕', '/category/games/hardware/monitor', '', 3, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `pdcat_m`
--

CREATE TABLE `pdcat_m` (
  `no` int(11) NOT NULL,
  `pdcat_m_code` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '第二層中分類代碼',
  `pdcat_t_code` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '關聯：第一層主分類代碼',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分類名稱/說明',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '連結網址',
  `link_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '連結開啟方式',
  `m_sort` int(11) DEFAULT '0' COMMENT '排序',
  `ishow` tinyint(1) DEFAULT '1' COMMENT '是否顯示 (1: 顯示, 0: 隱藏)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='第二層中分類表';

--
-- 傾印資料表的資料 `pdcat_m`
--

INSERT INTO `pdcat_m` (`no`, `pdcat_m_code`, `pdcat_t_code`, `name`, `url`, `link_type`, `m_sort`, `ishow`) VALUES
(1, 'M0000001', 'T0000001', '棒球專區', '/category/sports/baseball', '', 1, 1),
(2, 'M0000002', 'T0000001', '馬拉松裝備', '/category/sports/marathon', '', 2, 1),
(3, 'M0000003', 'T0000001', '戶外健身', '/category/sports/fitness', '', 3, 1),
(4, 'M0000004', 'T0000002', '官方應援物', '/category/idol/cheer', '', 1, 1),
(5, 'M0000005', 'T0000002', '巡迴演唱會', '/category/idol/concert', '', 2, 1),
(6, 'M0000006', 'T0000002', '實體影音', '/category/idol/media', '', 3, 1),
(7, 'M0000007', 'T0000003', '頂級日式燒肉', '/category/food/yakiniku', '', 1, 1),
(8, 'M0000008', 'T0000003', '麻辣火鍋名店', '/category/food/hotpot', '', 2, 1),
(9, 'M0000009', 'T0000003', '精選異國料理', '/category/food/international', '', 3, 1),
(10, 'M0000010', 'T0000004', '前端開發技術', '/category/books/frontend', '', 1, 1),
(11, 'M0000011', 'T0000004', '後端與資料庫', '/category/books/backend', '', 2, 1),
(12, 'M0000012', 'T0000004', '語言檢定教材', '/category/books/language', '', 3, 1),
(13, 'M0000013', 'T0000005', 'IPHONE', '/category/games/ar', '', 1, 1),
(14, 'M0000014', 'T0000005', 'IPAD', '/category/games/console', '', 2, 1),
(15, 'M0000015', 'T0000005', 'MacBook', '/category/games/hardware', '', 3, 1),
(16, 'M0000016', 'T0000005', 'APPLE WATCH', NULL, '', 0, 1),
(17, 'M0000017', 'T0000005', 'Apple 周邊', NULL, '', 5, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `pdcat_t`
--

CREATE TABLE `pdcat_t` (
  `no` int(11) NOT NULL,
  `pdcat_t_code` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '第一層主分類代碼',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分類名稱/說明',
  `url` text COLLATE utf8mb4_unicode_ci COMMENT '連結網址',
  `link_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '連結開啟方式 (例如 _self, _blank)',
  `m_sort` int(11) DEFAULT '0' COMMENT '排序 (數字越小越前面)',
  `ishow` tinyint(1) DEFAULT '1' COMMENT '是否顯示 (1: 顯示, 0: 隱藏)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='第一層主分類表';

--
-- 傾印資料表的資料 `pdcat_t`
--

INSERT INTO `pdcat_t` (`no`, `pdcat_t_code`, `name`, `url`, `link_type`, `m_sort`, `ishow`) VALUES
(1, 'T0000001', '戶外休閒', '/category/sports', '', 1, 1),
(2, 'T0000002', '影音周邊', '/category/idol', '', 2, 1),
(3, 'T0000003', '餐飲票券', '/category/food', '', 3, 1),
(4, 'T0000004', '資訊書籍', '/category/books', '', 4, 1),
(5, 'T0000005', 'APPLE', '/category/games', '', 5, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `no` int(11) NOT NULL,
  `uptime` datetime NOT NULL,
  `product_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_num` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_pd_date` date NOT NULL,
  `bar_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isbn` int(11) NOT NULL,
  `pd_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_stock` int(6) NOT NULL,
  `pd_size` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_age` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_author` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_publishing` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_publish_date` date NOT NULL,
  `pd_weight` int(8) NOT NULL,
  `pd_series` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_mode` int(2) NOT NULL,
  `pdcat_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `o_price` int(5) NOT NULL,
  `price` int(5) NOT NULL,
  `hot_item` int(2) NOT NULL,
  `you_tube_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_info` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_get` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `push_product_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ps` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `m_sort` int(2) NOT NULL,
  `ishow` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `qa`
--

CREATE TABLE `qa` (
  `no` int(11) NOT NULL,
  `qa_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uptime` datetime NOT NULL,
  `cat_id` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ans` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `m_sort` int(11) NOT NULL,
  `ishow` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `qa`
--

INSERT INTO `qa` (`no`, `qa_code`, `uptime`, `cat_id`, `question`, `ans`, `m_sort`, `ishow`) VALUES
(4, 'ETr0cfYG96k', '2016-12-27 09:08:26', 'b', '購物流程', '會員登入&rarr; 將欲購買商品放到購物車&rarr; 確認訂單明細&rarr; 選擇付費方式&rarr; 確認寄送資料&rarr; 確認訂單資料&rarr; 完成購物<br />\r\n', 1, 1),
(5, '3N844Gs29vy', '2016-12-27 09:11:21', 'b', '付款方式', '目前提供以下幾種付款方式：<br />\r\n1.「<span style=\"font-size:16px;\"><strong>信用卡</strong></span>」：接受VISA， Master，JCB， 聯合信用卡<br />\r\n2.「<span style=\"font-size:16px;\"><strong>ATM轉帳</strong></span>」：<br />\r\n<span style=\"font-size:16px;\"><strong>銀行名稱：台北富邦商業銀行(瑞湖分行)<br />\r\n代碼：012<br />\r\n帳號：741102000706<br />\r\n戶名：三暉圖書發行有限公司</strong></span><br />\r\n<span style=\"color:#000000;\"><strong>※為了讓您在匯款或ATM金融卡轉帳後能儘快收到訂購的商品，請匯款完至「我的帳戶&rarr;訂單查詢&rarr;選擇該筆訂單點選「</strong></span><span style=\"font-size:16px;\"><span style=\"color:#0000ff;\"><strong>訂單回填</strong></span></span><span style=\"color:#000000;\"><strong>」，回填相關匯款資料，我們將快速處理，讓您於最短時間內收到商品。</strong></span><br />\r\n<br />\r\n3.「<span style=\"font-size:16px;\"><strong>郵政劃撥</strong></span>」（限台灣地區）：<br />\r\n劃撥帳號：14957898<br />\r\n戶名：三暉圖書發行有限公司<br />\r\n※為了讓您在匯款或ATM金融卡轉帳後能儘快收到訂購的商品，請匯款完至「我的帳戶/訂單查詢/選擇該筆訂單點選「訂單回填」，回填相關匯款資料，我們將快速處理，讓您於最短時間內收到商品。<br />\r\n※郵政劃撥如有匯款收據證明，可以協助上傳風車寶貝官方LINE帳號【ID：@lkn8907y】，我們將快速處理，讓您於最短時間內收到商品。<br />\r\n<br />\r\n4.「<span style=\"font-size:16px;\"><strong>超商代碼付款</strong></span>」：選擇超商代碼付款並取得超商代碼&rarr;使用超商機台列印繳費單&rarr;持繳費單至超商櫃檯繳費&rarr;交易完成。『<span style=\"font-size:16px;\"><a href=\"https://shopping.windmill.com.tw/greenworld/greenworld.html\" target=\"_blank\">操作說明請點我</a></span>』<br />\r\n※因超商付款為隔日入帳，您的訂單將於隔日核款後進行出貨。<br />\r\n※使用超商付款，因滿600元免收手續費;若要退款將會酌收26元手續費。<br />\r\n<br />\r\n5.「<span style=\"font-size:16px;\"><strong>微信支付</strong></span>」（限海外訂單）：訂單成立確認運費後，點選付款按鈕可選擇掃描微信條碼進行付款。<br />\r\n您也可以掃描以下微信條碼進行付款&darr;<br />\r\n<img alt=\"\" src=\"https://shopping.windmill.com.tw/images/cke/images/cke_20180312102656.jpg\" style=\"max-width:320px\" width=\"100%\" /><br />\r\n', 2, 1),
(13, 'Ji5KcCAKzG5', '2016-12-27 17:47:10', 'm', '會員權益', '1.加入風車寶貝，即享有基本會員權益；商品會員價與不定期優惠活動。\r\n<div>\r\n	2.全站商品基本會員可享79折起，金卡會員可享75折起，白金會員可享69折起。</div>\r\n<div>\r\n	3.商品情報誌：不定期透過電子報通知最新商品活動及優惠資訊。</div>\r\n', 1, 1),
(6, '6mBrYwV4q90', '2016-12-27 09:15:24', 'b', '訂單成立', '在風車寶貝線上購物網完成訂單後，系統會在網頁上顯示相關訂購成功訊息，並以電子郵件的方式通知您該筆訂單已經成立。<br />\r\n請於購物車訂單結帳後，『七天內』完成匯款跟付款通知。訂單保留7天，若未完成，訂單將會被系統取消。 ', 3, 1),
(7, '7m5wBTF72Yd', '2016-12-27 09:16:03', 'b', '配送/運費計算方式', '免運計算以單張訂單單號為主，恕無法合併訂單免運費計算。<br />\r\n1.台灣本島（收件者在台灣本島）<br />\r\n會員於風車寶貝線上購物網訂購商品，其單筆訂單實際消費金額滿NT$800元，即可免NT$80元運費。<br />\r\n2.台灣離島（收件者在台灣離島）<br />\r\n會員於風車寶貝線上購物網訂購商品，離島地區單筆訂單運費為NT$130元，實際消費金額滿NT$800元，運費為NT$50元。<br />\r\n3.國外訂單（收件者在國外）<br />\r\n訂單成立後，需等待我們工作人員統計該訂單包裝後重量回填運費價格後，再一併進行付款結帳；除特定地區可運費到付（可加入我們的即時通訊軟體詢問可到付地區），訂單核款確認後即安排相關出貨事宜。', 4, 1),
(8, '8CKkTuxN8HD', '2016-12-27 09:17:07', 'b', '更改訂單（數量、寄件資料）', '您可以透過以下方式和客服人員確認修改資料：<br />\r\n1.加入風車寶貝LINE@：ID：@lkn8907y （<a href=\"https://line.me/ti/p/%40lkn8907y\">https://line.me/ti/p/%40lkn8907y</a>）<br />\r\n2.加入風車寶貝FB粉絲團，傳訊於客服人員（<a href=\"https://bit.ly/1UngEmj\">https://bit.ly/1UngEmj</a>）<br />\r\n3.加入WeChat：ID：windmill-baby （<a href=\"https://shopping.windmill.com.tw/WeChat/index.html\">https://shopping.windmill.com.tw/WeChat/index.html</a>）<br />\r\n4.透過官網<a href=\"https://shopping.windmill.com.tw/contact.php\">聯絡我們</a>，與我們聯絡<br />\r\n請於客服人員聯絡時，告知您的訂單編號及修改項目<br />\r\n', 5, 1),
(9, 'X2I7mNV45fR', '2016-12-27 09:44:11', 'b', '退貨辦法', '<span style=\"font-size:14px;\">若您需要辦理退換貨，請與客服人員聯繫，將有專員為您處理。<br />\r\n客服專線：02-2695-9502 #232</span><br />\r\n<h3>\r\n	<span style=\"color:#ff0000;\"><span style=\"font-size:16px;\">※貼心提醒※</span></span></h3>\r\n<span style=\"font-size:14px;\">1.若您買錯商品或因商品本身有瑕疵想要辦理退貨，請您於商品鑑賞期（以收到商品起至寄出商品郵戳為憑）7 天內辦理退貨。</span><span style=\"font-size:16px;\">（<strong>海外訂單不適用7天鑑賞期條款</strong>）</span>\r\n<div>\r\n	<span style=\"font-size:14px;\">2.建議您於收到商品時，先檢查商品的品項是否符合?數量是否正確?商品本身是否完整?是否缺少附件?以便即時處理並可保障您的消費權益，逾鑑賞期將不予受理，敬請見諒。</span></div>\r\n<div>\r\n	<span style=\"font-size:16px;\"><strong>3.於七天鑑賞期內要求退貨：若因個人因素要求辦理退貨，郵資必須自行負擔，退回的款項將扣除運費。</strong></span></div>\r\n<div>\r\n	<span style=\"font-size:14px;\">4.瑕疵商品處理方式：若您收到的商品有瑕疵或是收到不是您訂購的商品，請將瑕疵商品狀況照片、問題、聯絡方式一同寄至</span><span style=\"font-size:16px;\"><strong>客服信箱</strong></span><span style=\"font-size:14px;\">或加入</span><span style=\"font-size:16px;\"><strong>LINE@（@lkn8907y）</strong></span>或&nbsp;<span style=\"font-size:16px;\"><strong>風車寶貝線上購物網FB粉絲團（http://bit.ly/1UngEmj）</strong></span><span style=\"font-size:14px;\">，我們將會立即與您聯繫及為您申請換貨處理。</span></div>\r\n<div>\r\n	<span style=\"font-size:14px;\">5.影音商品（如錄音帶、錄影帶、CD、VCD、DVD&hellip;&hellip;等），我們不接受退貨，僅提供因商品本身瑕疵或毀損問題，提供該商品之更換。</span></div>\r\n<div>\r\n	<span style=\"font-size:14px;\">6.進口外文書商品恕不接受退貨，僅提供因商品本身瑕疵或毀損問題，提供該商品之更換。</span></div>\r\n<div>\r\n	<span style=\"font-size:14px;\">7.因不滿意欲退貨之商品，請保持其商品完整性，商品一經判定有毀損，恕不接受退貨。</span></div>\r\n<div>\r\n	<span style=\"font-size:14px;\">8.活動贈品一旦送出 ，恕不接受更換。</span></div>\r\n<div>\r\n	<span style=\"font-size:16px;\"><strong>9.退貨後，訂單實際消費金額低於免運費資格者，將在退款金額中扣除運費。</strong></span></div>\r\n<div>\r\n	<span style=\"font-size:14px;\">10.退貨準備物品：預退回之商品、當次消費之出貨明細表、發票。</span></div>\r\n<div>\r\n	<span style=\"font-size:14px;\">11.退貨後，實際消費金額低於活動贈品資格者，請將贈品一併寄回，以上要件均請備妥，缺一恕不受理。</span></div>\r\n', 6, 1),
(12, '00tNUZ42FyP', '2016-12-27 10:36:35', 'b', '確認訂單付款後，多久可以收到商品', '<div>\r\n	平均時間約7天工作天(不包含假日)內，可收到商品。</div>\r\n', 8, 1),
(11, 'YB8DSDVxQ3F', '2016-12-27 10:34:19', 'b', '查詢商品配送進度', '請至「我的帳戶/訂單查詢」中查詢。', 7, 1),
(14, 'fz5QTmR6KWy', '2016-12-27 17:49:42', 'm', '帳號無法登入', '1.若在加入會員完成時，出現 「您設定的帳號已有人使用，請重新設定」的訊息，表示您的帳號與其他會員重複，請重新設定您的帳號。\r\n<div>\r\n	2.在您輸入的帳號正確與網路連線正常的狀況下，您的帳號無法登入風車寶貝線上購物網時，請來電客服 02-2695-9502 分機232，我們將有專人為您服務。</div>\r\n', 2, 1),
(15, 'BE190zbM0sT', '2016-12-27 17:50:46', 'm', '修改/查詢帳號密碼問題', '1.修改會員資料及密碼請至「我的帳戶/資料修改」中做修改。密碼修改後，請使用新的密碼登入。\r\n<div>\r\n	2.查詢帳密請至「我的帳戶/忘記密碼」中做查詢。完成查詢後，系統會寄送密碼至您的E-mail 信箱。</div>\r\n', 3, 1),
(16, 'GWSaxFZIi4B', '2016-12-27 17:52:12', 'm', '查詢購物明細紀錄', '請至「我的帳戶/訂單查詢」中做查詢。', 4, 1),
(17, '0B0VEfq1Xej', '2016-12-27 17:53:02', 'm', '訂閱/取消電子報', '請至「我的帳戶/訂閱電子報」中訂閱；若您不想收到電子報，可於電子報信件中，點選下方『取消訂閱』取消訂閱，日後我們將不再發送此刊物至您的電子信箱中。', 5, 1),
(18, 'r4q46G2gxEm', '2016-12-27 17:53:47', 'm', '折價卷使用說明', '1.折價卷為【風車寶貝線上購物網】的活動贈品之一，會員可於有效期限內，至【風車寶貝線上購物網】購物時使用。\r\n<div>\r\n	2.折價券使用規則</div>\r\n<div>\r\n	（1）須為【風車寶貝線上購物網】之會員方可使用。</div>\r\n<div>\r\n	（2）須於折價券有效期限內方可進行折抵。</div>\r\n<div>\r\n	（3）折價券選擇及使用是以每筆訂單為單位，每筆訂單限使用一張折價券，且運費金額無法計入折價券門檻計算。</div>\r\n<div>\r\n	（4）限於【風車寶貝線上購物網】訂購時使用。</div>\r\n<div>\r\n	（5）折價券均有使用效期之限制，提醒您於使用效期前使用，逾期則視為棄權。</div>\r\n<div>\r\n	（6）滿額贈品部分，依折抵後實際結帳金額為準，若折抵後未達滿額贈品之標準，無法選取滿額贈品。</div>\r\n<div>\r\n	3.取消訂單或退貨，會退還「折價券」（仍在使用期限內）嗎？</div>\r\n<div>\r\n	（1）活動及生日贈與之折價券一旦使用後立即失效；特殊折讓之折價券則將自動歸戶。</div>\r\n<div>\r\n	（2）<strong>折價券一旦使用後立即失效，若取消訂單或辦理退貨，僅會退還您購物實際支付之金額，已分配折抵金額將無法歸還與還原折抵條件，折價券不得重複使用</strong>。</div>\r\n<div>\r\n	4.折價券為活動贈品無償贈予，【風車寶貝線上購物網】保留隨時變更、修改或中止折價券用辦法之權利。</div>\r\n<div>\r\n	&nbsp;</div>\r\n', 6, 1),
(19, 'q6MSmDQG402', '2017-01-12 16:45:02', 'm', '會員升級方式', '若您為一般會員，消費滿2000元即可晉升為金卡會員；<br />\r\n若您為金卡會員，於等級效期內消費滿3000元即可晉升為白金會員。<br />\r\n等級效期內若未達等級累積最低消費金額，將於效期後自動降一階級：金卡等級累積最低消費金額2000元；白金等級累積最低消費金額5000元。', 1, 1),
(20, 'kI55b2evY30', '2017-02-21 09:45:52', 'b', '特殊訂購需求', '若有特殊訂購需求，歡迎您加入<a href=\"https://line.me/ti/p/%40lkn8907y\" target=\"_blank\">LINE@</a>（@lkn8907y）、<a href=\"http://bit.ly/1UngEmj\" target=\"_blank\">風車寶貝線上購物網FB粉絲團</a>，我們將會立即與您聯繫及為您處理相關服務。', 9, 1),
(21, 'TSzzTJUDpWV', '2017-02-21 10:36:46', 'b', '刷卡失敗如何重新付款？', '可至「訂單查詢」找尋該筆訂單，點選編輯「查看訂單」，於付款方式右側點選「信用卡付款 GO」，即可重新付款。<br />\r\n', 3, 1),
(22, 'Ez8yON6oou7', '2017-02-21 13:06:17', 'm', '信箱沒有收到會員驗證信？', '若您註冊時尚未收到會員驗證信件，請至「我的帳戶」，選擇「補寄確認信」，輸入註冊時所填寫的電子信箱，即可再次收取驗證信件。', 2, 1),
(23, 'Bl47g3qiQ9q', '2017-02-21 13:10:32', 'm', '會員驗證信件無法點選？', '若您的認證信件是在垃圾郵件中，請將信件移至收件匣即可進行驗證。', 2, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `spec_group`
--

CREATE TABLE `spec_group` (
  `no` int(11) NOT NULL,
  `spec_group_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdcat_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spec_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ishow` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `spec_group`
--

INSERT INTO `spec_group` (`no`, `spec_group_code`, `pdcat_code`, `spec_code`, `name`, `ishow`) VALUES
(1, 'A5fhfg', 'C0000040', '7560cr,k89aoE,LkNIDZ,4xtH06,czUkAX', '容量', 1),
(2, '3DRZie', 'C0000040', 'nYIU3f,GHO8vy,MrMYeG,610fj9', '顏色', 1),
(3, '7hLlD6', '', '', '', 0);

-- --------------------------------------------------------

--
-- 資料表結構 `spec_info`
--

CREATE TABLE `spec_info` (
  `no` int(11) NOT NULL,
  `spec_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spec_type` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `show_type` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ishow` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `spec_info`
--

INSERT INTO `spec_info` (`no`, `spec_code`, `text`, `img_url`, `color`, `spec_type`, `show_type`, `ishow`) VALUES
(1, 'nYIU3f', '紅色', '', '#FF000', 'capacity', 'code', 1),
(2, '610fj9', '黃色', '', '', '', '', 1),
(3, 'GHO8vy', '銀色', '', '', 'color', '', 1),
(4, 'MrMYeG', '銀色', '', '', '', '', 1),
(5, 'k89aoE', '1T', '', '', '', '', 1),
(6, '4xtH06', '512M', '', '', '', '', 1),
(7, 'LkNIDZ', '256M', '', '', '', '', 1),
(8, '7560cr', '128M', '', '', '', '', 1),
(9, 'czUkAX', '64M', '', '', '', '', 1);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`no`);

--
-- 資料表索引 `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`no`),
  ADD UNIQUE KEY `idx_member_code` (`member_code`),
  ADD UNIQUE KEY `idx_email` (`email`);

--
-- 資料表索引 `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`no`),
  ADD KEY `no` (`no`);

--
-- 資料表索引 `orderlist`
--
ALTER TABLE `orderlist`
  ADD PRIMARY KEY (`no`);

--
-- 資料表索引 `pdcat`
--
ALTER TABLE `pdcat`
  ADD PRIMARY KEY (`pdcat_code`),
  ADD KEY `idx_pdcat_m_code` (`pdcat_m_code`),
  ADD KEY `no` (`no`);

--
-- 資料表索引 `pdcat_m`
--
ALTER TABLE `pdcat_m`
  ADD PRIMARY KEY (`pdcat_m_code`),
  ADD KEY `idx_pdcat_t_code` (`pdcat_t_code`),
  ADD KEY `no` (`no`);

--
-- 資料表索引 `pdcat_t`
--
ALTER TABLE `pdcat_t`
  ADD PRIMARY KEY (`pdcat_t_code`),
  ADD KEY `no` (`no`);

--
-- 資料表索引 `qa`
--
ALTER TABLE `qa`
  ADD PRIMARY KEY (`no`);

--
-- 資料表索引 `spec_group`
--
ALTER TABLE `spec_group`
  ADD PRIMARY KEY (`no`);

--
-- 資料表索引 `spec_info`
--
ALTER TABLE `spec_info`
  ADD PRIMARY KEY (`no`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `admin`
--
ALTER TABLE `admin`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `members`
--
ALTER TABLE `members`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT COMMENT '會員流水號', AUTO_INCREMENT=51;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `news`
--
ALTER TABLE `news`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `orderlist`
--
ALTER TABLE `orderlist`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pdcat`
--
ALTER TABLE `pdcat`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pdcat_m`
--
ALTER TABLE `pdcat_m`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pdcat_t`
--
ALTER TABLE `pdcat_t`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `qa`
--
ALTER TABLE `qa`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `spec_group`
--
ALTER TABLE `spec_group`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `spec_info`
--
ALTER TABLE `spec_info`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `pdcat`
--
ALTER TABLE `pdcat`
  ADD CONSTRAINT `fk_pdcat_mcode` FOREIGN KEY (`pdcat_m_code`) REFERENCES `pdcat_m` (`pdcat_m_code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `pdcat_m`
--
ALTER TABLE `pdcat_m`
  ADD CONSTRAINT `fk_pdcat_m_tcode` FOREIGN KEY (`pdcat_t_code`) REFERENCES `pdcat_t` (`pdcat_t_code`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
