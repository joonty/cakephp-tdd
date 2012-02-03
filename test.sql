-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 03, 2012 at 04:13 PM
-- Server version: 5.1.58
-- PHP Version: 5.3.6-13ubuntu3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `localtv`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE IF NOT EXISTS `attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(45) NOT NULL,
  `foreign_key` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` varchar(100) NOT NULL,
  `weight` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `menuitems`
--

CREATE TABLE IF NOT EXISTS `menuitems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned NOT NULL,
  `position` tinyint(3) unsigned NOT NULL,
  `text` varchar(100) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `page_id` int(10) unsigned DEFAULT '0',
  `url` varchar(200) DEFAULT NULL,
  `new_window` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_menuitems_menus1` (`menu_id`),
  KEY `fk_menuitems_pages1` (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`id`, `menu_id`, `position`, `text`, `title`, `page_id`, `url`, `new_window`, `created`, `modified`, `status`) VALUES
(1, 1, 0, 'First link', 'This is a link', 1, '', 0, '2012-02-03 14:03:24', '2012-02-03 14:03:24', 1),
(2, 1, 1, 'Google', 'Go to Google', 1, 'http://www.google.com', 1, '2012-02-03 14:03:56', '2012-02-03 14:03:56', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `station_id` int(10) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_menus_stations` (`station_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `station_id`, `created`, `modified`, `status`) VALUES
(1, 'Menu', 1, '2012-02-03 14:01:21', '2012-02-03 14:01:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` tinytext NOT NULL,
  `slug` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `station_id` int(10) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_UNIQUE` (`slug`),
  KEY `fk_pages_users1` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `description`, `slug`, `content`, `user_id`, `station_id`, `created`, `modified`, `status`) VALUES
(1, 'This is my page', 'Welcome to my page', 'mypage', '<p><strong>Hello</strong></p>\r\n<p><strong><br /></strong><em>This is some lovely text that uses HTML.</em></p>', 1, 1, '2012-01-30 12:07:23', '2012-01-30 12:07:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stations`
--

CREATE TABLE IF NOT EXISTS `stations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `host` varchar(70) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_UNIQUE` (`host`),
  KEY `fk_stations_users1` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `stations`
--

INSERT INTO `stations` (`id`, `name`, `host`, `user_id`, `created`, `modified`, `status`) VALUES
(1, 'Fun TV', 'funtv.co.uk', 1, '2012-01-30 09:44:51', '2012-01-30 09:44:51', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(40) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `password_UNIQUE` (`password`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created`, `modified`, `status`) VALUES
(3, 'andy', 'andy@22blue.co.uk', '32ad9416602acce9dc08cd738719b83fbd879ebe', NULL, NULL, 1),
(2, 'Jon Cairns', 'jon@22blue.co.uk', '579a299fb6cd8f69f5609ee382ceda799e793df7', '2012-01-30 15:13:21', '2012-01-30 15:13:21', 1),
(4, 'Erroll', 'erroll@22blue.co.uk', '2e901ed6cfc74419ba4d3a206f8fded426341e1f', '2012-01-31 09:59:56', '2012-01-31 09:59:56', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
