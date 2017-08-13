-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `villagehall`;
CREATE DATABASE `villagehall` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `villagehall`;

DROP TABLE IF EXISTS `booking`;
CREATE TABLE `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomid` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `length` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `roomid` (`roomid`),
  KEY `userid` (`userid`),
  CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`roomid`) REFERENCES `rooms` (`id`),
  CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `booking` (`id`, `roomid`, `date`, `length`, `userid`, `status`) VALUES
(1,	1,	1501439811,	3900,	1,	3),
(2,	2,	1502439811,	7553,	1,	2),
(3,	1,	1503439811,	4600,	1,	1),
(4,	2,	1504439811,	8960,	1,	2);

DROP TABLE IF EXISTS `hall`;
CREATE TABLE `hall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `servername` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `hall` (`id`, `name`, `servername`) VALUES
(1,	'Lidlington',	'lidlington');

DROP TABLE IF EXISTS `prebooking`;
CREATE TABLE `prebooking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `guuid` varchar(128) NOT NULL,
  `roomid` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `length` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `roomid` (`roomid`),
  CONSTRAINT `prebooking_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`),
  CONSTRAINT `prebooking_ibfk_2` FOREIGN KEY (`roomid`) REFERENCES `rooms` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `prebooking` (`id`, `userid`, `guuid`, `roomid`, `date`, `length`) VALUES
(1,	2,	'9bba17b1-9439-46ab-b584-e4af501a879f',	1,	1505732400,	7200);

DROP TABLE IF EXISTS `privs`;
CREATE TABLE `privs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `ulevel` int(11) NOT NULL,
  `hlevel` int(11) NOT NULL,
  `alevel` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `privs` (`id`, `userid`, `ulevel`, `hlevel`, `alevel`) VALUES
(1,	1,	10,	10,	10);

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hallid` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hallid` (`hallid`),
  CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hallid`) REFERENCES `hall` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `rooms` (`id`, `hallid`, `name`, `size`) VALUES
(1,	1,	'Main Hall',	100),
(2,	1,	'Commitee Room',	10);

DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `uuid` varchar(36) NOT NULL,
  `expires` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL,
  KEY `userid` (`userid`),
  CONSTRAINT `session_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `email` varchar(127) NOT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `address1` varchar(127) DEFAULT NULL,
  `address2` varchar(127) DEFAULT NULL,
  `town` varchar(127) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user` (`id`, `name`, `email`, `phone`, `address1`, `address2`, `town`, `postcode`) VALUES
(1,	'Chris Allison',	'obscure+name@gmail.com',	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	'Chris Allison',	'obscure+othername@gmail.com',	NULL,	NULL,	NULL,	NULL,	NULL);

-- 2017-08-12 16:19:28
