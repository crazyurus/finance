-- phpMyAdmin SQL Dump
-- version 4.2.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-01-27 14:49:24
-- 服务器版本： 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `finance`
--

-- --------------------------------------------------------

--
-- 表的结构 `ut_hr_log`
--

CREATE TABLE IF NOT EXISTS `ut_hr_log` (
  `log_id` int(11) NOT NULL,
  `oper_no` int(5) NOT NULL,
  `log_date` datetime NOT NULL,
  `log_ip` varchar(32) NOT NULL,
  `log_ua` text NOT NULL,
  `log_method` varchar(32) NOT NULL,
  `log_target` varchar(32) NOT NULL,
  `log_result` varchar(32) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ut_fin_acct`
--

CREATE TABLE IF NOT EXISTS `ut_fin_acct` (
`acct_id` int(3) NOT NULL,
  `acct_name` varchar(40) NOT NULL,
  `acct_no` varchar(40) NOT NULL,
  `acct_bank` varchar(50) NOT NULL,
  `acct_balance` decimal(16,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ut_fin_seqcheck`
--

CREATE TABLE IF NOT EXISTS `ut_fin_seqcheck` (
  `seq_no` varchar(20) NOT NULL,
  `action_date` datetime NOT NULL,
  `check_date` datetime NOT NULL,
  `check_no` int(5) NOT NULL,
  `order_id` varchar(25) DEFAULT NULL,
  `oper_no` int(5) NOT NULL,
  `subject_code` int(8) NOT NULL,
  `settle_flag` int(1) NOT NULL,
  `acct_id` int(3) NOT NULL,
  `action_amount` decimal(16,2) NOT NULL,
  `current_amount` decimal(16,2) NOT NULL,
  `check_stat` int(1) NOT NULL,
  `seq_mark` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ut_fin_seqtemp`
--

CREATE TABLE IF NOT EXISTS `ut_fin_seqtemp` (
  `id` int(5) NOT NULL,
  `seq_no` varchar(20) NOT NULL,
  `action_date` datetime NOT NULL,
  `order_id` varchar(25) DEFAULT NULL,
  `oper_no` int(5) NOT NULL,
  `subject_code` int(8) NOT NULL,
  `settle_flag` int(1) NOT NULL,
  `acct_id` int(3) NOT NULL,
  `action_amount` decimal(16,2) NOT NULL,
  `check_stat` int(1) NOT NULL,
  `seq_mark` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ut_fin_subject`
--

CREATE TABLE IF NOT EXISTS `ut_fin_subject` (
  `subject_no` int(8) NOT NULL,
  `higer_subject` int(8) NOT NULL,
  `settle_flag` int(1) NOT NULL,
  `subject_name` varchar(40) NOT NULL,
  `subject_stat` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ut_fin_subject`
--

INSERT INTO `ut_fin_subject` (`subject_no`, `higer_subject`, `settle_flag`, `subject_name`, `subject_stat`) VALUES
(10000000, 0, 1, '收入', '1'),
(11000000, 10000000, 1, '营业收入', '1'),
(11100000, 10000000, 1, '收客户款', '1'),
(11200000, 10000000, 1, '供应商退款', '1'),
(11300000, 10000000, 1, '营业外收入', '1'),
(20000000, 0, -1, '支出', '1'),
(21000000, 20000000, -1, '营业支出', '1'),
(21100000, 20000010, -1, '付供应商款', '1'),
(21200000, 20000010, -1, '退客户款', '1'),
(21300000, 20000010, -1, '付订单保险费', '1'),
(21400000, 20000010, -1, '付订单杂费', '1'),
(22000000, 20000000, -1, '行政支出', '1'),
(22100000, 22000000, -1, '付水电物业', '1'),
(22200000, 22000000, -1, '付通讯支出', '1'),
(22300000, 22000000, -1, '付人员工资', '1'),
(22400000, 22000000, -1, '付人员社保', '1'),
(22500000, 22000000, -1, '付其他费用', ''),
(23000000, 20000000, -1, '广告推广支出', '1'),
(23100000, 23000000, -1, '网络平台', '1'),
(23200000, 23000000, -1, '广告费', '1'),
(23300000, 23000000, -1, '网站建设', '1'),
(23400000, 23000000, -1, '其他推广支出', '1');

-- --------------------------------------------------------

--
-- 表的结构 `ut_hr_depts`
--

CREATE TABLE IF NOT EXISTS `ut_hr_depts` (
  `dept_no` int(5) NOT NULL,
  `dept_name` varchar(40) NOT NULL,
  `higer_dept` int(5) NOT NULL,
  `dept_addr` varchar(100) DEFAULT NULL,
  `dept_gps` varchar(256) DEFAULT NULL,
  `dept_stat` int(1) NOT NULL,
  `dept_mark` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ut_hr_depts`
--

INSERT INTO `ut_hr_depts` (`dept_no`, `dept_name`, `higer_dept`, `dept_addr`, `dept_gps`, `dept_stat`, `dept_mark`) VALUES
(10000, '总部', 0, NULL, NULL, 1, '根部门，必须存在');

-- --------------------------------------------------------

--
-- 表的结构 `ut_hr_opers`
--

CREATE TABLE IF NOT EXISTS `ut_hr_opers` (
`oper_no` int(5) NOT NULL,
  `oper_name` varchar(20) NOT NULL,
  `oper_pass` varchar(32) NOT NULL,
  `oper_dept_no` int(5) NOT NULL,
  `oper_post_no` int(5) NOT NULL,
  `oper_stat` int(1) NOT NULL,
  `oper_mobile` varchar(20) NOT NULL,
  `oper_cid` varchar(20) NOT NULL,
  `oper_bth` date NOT NULL,
  `oper_info` text,
  `oper_mark` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ut_hr_opers`
--
-- ALTER TABLE ut_hr_opers AUTO_INCREMENT = 1;

INSERT INTO `ut_hr_opers` (`oper_no`, `oper_name`, `oper_pass`, `oper_dept_no`, `oper_post_no`, `oper_stat`, `oper_mobile`, `oper_cid`, `oper_bth`, `oper_info`, `oper_mark`) VALUES
(10000, '管理员', '6dd9b85757b8a66fa26afb0489a07c31', 10000, 99999, 1, '00000000000', '000000000000000000', '2015-01-20', '', '超级管理员');

-- --------------------------------------------------------

--
-- 表的结构 `ut_hr_posts`
--

CREATE TABLE IF NOT EXISTS `ut_hr_posts` (
  `post_no` int(5) NOT NULL,
  `post_name` varchar(40) NOT NULL,
  `higer_post` int(5) NOT NULL,
  `post_stat` int(1) NOT NULL,
  `post_mark` varchar(200) DEFAULT NULL,
  `post_right` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ut_hr_posts`
--

INSERT INTO `ut_hr_posts` (`post_no`, `post_name`, `higer_post`, `post_stat`, `post_mark`, `post_right`) VALUES
(99999, '管理员', 0, 1, '管理员', '["1000","2000","3000","4000","5000"]');

-- --------------------------------------------------------

--
-- 表的结构 `ut_ord_info`
--

CREATE TABLE IF NOT EXISTS `ut_ord_info` (
  `ord_id` varchar(25) NOT NULL,
  `ord_date` datetime NOT NULL,
  `oper_no` int(5) NOT NULL,
  `dept_no` int(5) NOT NULL,
  `provider_name` varchar(40) NOT NULL,
  `order_area` int(6) NOT NULL,
  `order_local` varchar(10) DEFAULT NULL,
  `order_name` varchar(40) NOT NULL,
  `depart_date` date NOT NULL,
  `custom_name` varchar(40) NOT NULL,
  `person_no` int(5) NOT NULL,
  `sell_unit` decimal(16,2) NOT NULL,
  `sell_other` decimal(16,2) NOT NULL,
  `sell_total` decimal(16,2) NOT NULL,
  `selled_total` decimal(16,2) NOT NULL,
  `sell_difference` decimal(16,2) NOT NULL,
  `pay_unit` decimal(16,2) NOT NULL,
  `insurance_way` int(6) NOT NULL,
  `insurance_pay` decimal(16,2) NOT NULL,
  `pay_other` decimal(16,2) NOT NULL,
  `pay_total` decimal(16,2) NOT NULL,
  `payed_total` decimal(16,2) NOT NULL,
  `pay_difference` decimal(16,2) NOT NULL,
  `insurance_stat` int(6) DEFAULT NULL,
  `settle_stat` int(6) NOT NULL,
  `ord_mark` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ut_ord_para`
--

CREATE TABLE IF NOT EXISTS `ut_ord_para` (
  `para_id` int(6) NOT NULL,
  `para_name` varchar(20) NOT NULL,
  `para_table` varchar(20) NOT NULL,
  `para_column` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ut_ord_para`
--

INSERT INTO `ut_ord_para` (`para_id`, `para_name`, `para_table`, `para_column`) VALUES
(100000, '出境', 'ut_ord_info', 'order_area'),
(100001, '国内', 'ut_ord_info', 'order_area'),
(100003, '港澳', 'ut_ord_info', 'order_area'),
(100004, '台湾', 'ut_ord_info', 'order_area'),
(100010, '系统购买', 'ut_ord_info', 'insurance_way'),
(100011, '其他途径', 'ut_ord_info', 'insurance_way'),
(100012, '不需购买', 'ut_ord_info', 'insurance_way'),
(100020, '未购买', 'ut_ord_info', 'insurance_stat'),
(100021, '已购买', 'ut_ord_info', 'insurance_stat'),
(100022, '不需购买', 'ut_ord_info', 'insurance_stat'),
(100030, '未结算', 'ut_ord_info', 'settle_stat'),
(100031, '团队未结算', 'ut_ord_info', 'settle_stat'),
(100032, '已结算', 'ut_ord_info', 'settle_stat'),
(100033, '撤单', 'ut_ord_info', 'settle_stat');

-- --------------------------------------------------------

--
-- 表的结构 `ut_sys_index`
--

CREATE TABLE IF NOT EXISTS `ut_sys_index` (
  `sys_date` date NOT NULL,
  `sys_table_name` varchar(40) NOT NULL,
  `sys_count` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ut_sys_index`
--

INSERT INTO `ut_sys_index` (`sys_date`, `sys_table_name`, `sys_count`) VALUES
('2015-01-27', 'ut_ord_info', 0),
('2015-01-26', 'ut_fin_seqtemp', 0);

-- --------------------------------------------------------

--
-- 表的结构 `ut_sys_state`
--

CREATE TABLE IF NOT EXISTS `ut_sys_state` (
  `sys_stat` int(11) NOT NULL,
  `sys_date` date NOT NULL,
  `sys_status` varchar(10) NOT NULL,
  `sys_info` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ut_sys_state`
--

INSERT INTO `ut_sys_state` (`sys_stat`, `sys_date`, `sys_status`, `sys_info`) VALUES
(1, '2015-01-27', '1', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ut_hr_log`
--
ALTER TABLE `ut_hr_log`
 ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `ut_fin_acct`
--
ALTER TABLE `ut_fin_acct`
 ADD PRIMARY KEY (`acct_id`);

--
-- Indexes for table `ut_fin_seqcheck`
--
ALTER TABLE `ut_fin_seqcheck`
 ADD PRIMARY KEY (`seq_no`);

--
-- Indexes for table `ut_fin_seqtemp`
--
ALTER TABLE `ut_fin_seqtemp`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ut_fin_subject`
--
ALTER TABLE `ut_fin_subject`
 ADD PRIMARY KEY (`subject_no`);

--
-- Indexes for table `ut_hr_depts`
--
ALTER TABLE `ut_hr_depts`
 ADD PRIMARY KEY (`dept_no`);

--
-- Indexes for table `ut_hr_opers`
--
ALTER TABLE `ut_hr_opers`
 ADD PRIMARY KEY (`oper_no`);

--
-- Indexes for table `ut_hr_posts`
--
ALTER TABLE `ut_hr_posts`
 ADD PRIMARY KEY (`post_no`);

--
-- Indexes for table `ut_ord_info`
--
ALTER TABLE `ut_ord_info`
 ADD PRIMARY KEY (`ord_id`);

--
-- Indexes for table `ut_ord_para`
--
ALTER TABLE `ut_ord_para`
 ADD PRIMARY KEY (`para_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ut_fin_acct`
--
ALTER TABLE `ut_fin_acct`
MODIFY `acct_id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ut_hr_log`
--
ALTER TABLE `ut_hr_log`
MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ut_fin_seqtemp`
--
ALTER TABLE `ut_fin_seqtemp`
MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
