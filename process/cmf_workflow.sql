-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2018-08-04 12:08:21
-- 服务器版本： 5.5.47
-- PHP Version: 7.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `process`
--

-- --------------------------------------------------------

--
-- 表的结构 `cmf_workflow`
--

CREATE TABLE `cmf_workflow` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL COMMENT '流程名称',
  `Introduce` varchar(500) DEFAULT NULL COMMENT '流程简介',
  `CreateTime` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `FlowNodes` varchar(2000) NOT NULL COMMENT '流程节点json表示',
  `DepartmentID` int(11) DEFAULT NULL COMMENT '可用部门ID',
  `RoleID` int(11) DEFAULT NULL COMMENT '可用角色ID',
  `TypeID` int(11) NOT NULL COMMENT '流程类型id',
  `Status` tinyint(4) NOT NULL COMMENT '状态：1:正常 2:删除',
  `UserID` varchar(500) DEFAULT NULL COMMENT '可用用户',
  `OrderRule` varchar(1000) NOT NULL COMMENT '编号规则'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='工作流程表';

--
-- 转存表中的数据 `cmf_workflow`
--

INSERT INTO `cmf_workflow` (`ID`, `Name`, `Introduce`, `CreateTime`, `FlowNodes`, `DepartmentID`, `RoleID`, `TypeID`, `Status`, `UserID`, `OrderRule`) VALUES
(1, '事假', '用户事假申请', 1521537310, '[{\"type\":2,\"role\":2,\"self\":1,\"need\":1},{\"type\":2,\"role\":4,\"copy\":4,\"need\":[{\"field\":\"TimeBetween1Total\",\"type\":1,\"value\":3}],\"needtype\":1}]', NULL, 1, 1, 0, NULL, '[{\"type\":1,\"value\":\"SJ\"},{\"type\":2,\"datetype\":2},{\"type\":3,\"length\":3}]'),
(2, '合同审批', '合同审批', 1521537331, '[{\"type\":1,\"role\":\"2,5\",\"self\":0,\"need\":1},{\"type\":2,\"role\":4,\"copy\":4,\"need\":[{\"field\":\"Decimal1\",\"type\":1,\"value\":100000},{\"field\":\"Title2\",\"type\":2,\"value\":\"\\u591a\\u5f69\\u8d35\\u5dde\\u5370\\u8c61\\u7f51\\u7edc\\u4f20\\u5a92\\u80a1\\u4efd\\u6709\\u9650\\u516c\\u53f8\"}],\"needtype\":2}]', NULL, 1, 4, 0, NULL, '[{\"type\":1,\"value\":\"HT\"},{\"type\":2,\"datetype\":2},{\"type\":3,\"length\":3}]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cmf_workflow`
--
ALTER TABLE `cmf_workflow`
  ADD PRIMARY KEY (`ID`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cmf_workflow`
--
ALTER TABLE `cmf_workflow`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
