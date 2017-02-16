-- --------------------------------------------------------
-- Host:                         115.236.177.85
-- Server version:               5.6.15-log - Source distribution
-- Server OS:                    Linux
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2017-02-15 15:21:25
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for bus
DROP DATABASE IF EXISTS `bus`;
CREATE DATABASE IF NOT EXISTS `bus` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `bus`;


-- Dumping structure for table bus.car
DROP TABLE IF EXISTS `car`;
CREATE TABLE IF NOT EXISTS `car` (
  `carno` int(10) NOT NULL AUTO_INCREMENT,
  `cardesc` varchar(50) NOT NULL,
  `seatnum` int(11) NOT NULL DEFAULT '35',
  PRIMARY KEY (`carno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table bus.order
DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `orderno` varchar(32) NOT NULL,
  `status` int(10) unsigned NOT NULL,
  `ondate` datetime NOT NULL,
  `sno` int(11) NOT NULL,
  `seatno` varchar(100) NOT NULL,
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `startpos` varchar(50) NOT NULL,
  `endpos` varchar(50) NOT NULL,
  `carno` int(11) NOT NULL,
  PRIMARY KEY (`orderno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table bus.sales
DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `index` binary(20) NOT NULL,
  PRIMARY KEY (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table bus.schedule
DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `sno` int(10) NOT NULL AUTO_INCREMENT,
  `carno` int(10) NOT NULL,
  `timestart` time NOT NULL,
  `timeend` time NOT NULL,
  PRIMARY KEY (`sno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table bus.seat_order_status
DROP TABLE IF EXISTS `seat_order_status`;
CREATE TABLE IF NOT EXISTS `seat_order_status` (
  `index` varchar(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `time` datetime NOT NULL,
  PRIMARY KEY (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table bus.seat_real_status
DROP TABLE IF EXISTS `seat_real_status`;
CREATE TABLE IF NOT EXISTS `seat_real_status` (
  `index` varchar(12) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table bus.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) NOT NULL,
  `nickname` varchar(32) NOT NULL,
  `logo` varchar(200) NOT NULL,
  `password` varchar(64) NOT NULL,
  `sex` varchar(4) NOT NULL,
  `homeaddr` varchar(200) NOT NULL,
  `comaddr` varchar(200) NOT NULL,
  `worktime` time NOT NULL,
  `offtime` time NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table bus.user_order
DROP TABLE IF EXISTS `user_order`;
CREATE TABLE IF NOT EXISTS `user_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `order` varbinary(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `Index 2` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
