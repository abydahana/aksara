-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 11, 2020 at 01:06 AM
-- Server version: 5.5.68-MariaDB
-- PHP Version: 7.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `abydahana__aksara`
--

-- --------------------------------------------------------

--
-- Table structure for table `app__activity_logs`
--

CREATE TABLE IF NOT EXISTS `app__activity_logs` (
  `id` int(22) NOT NULL,
  `user_id` int(22) NOT NULL,
  `module` varchar(256) NOT NULL,
  `submodule` varchar(256) NOT NULL,
  `controller` varchar(256) NOT NULL,
  `method` varchar(256) NOT NULL,
  `page` varchar(256) NOT NULL,
  `browser` varchar(256) NOT NULL,
  `platform` varchar(256) NOT NULL,
  `ip_address` varchar(22) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `app__announcements`
--

CREATE TABLE IF NOT EXISTS `app__announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `announcement_slug` varchar(256) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `placement` tinyint(1) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_timestamp` datetime NOT NULL,
  `updated_timestamp` datetime NOT NULL,
  `language_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__groups`
--

CREATE TABLE IF NOT EXISTS `app__groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `group_description` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `group_privileges` longtext NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `app__groups`
--

INSERT INTO `app__groups` (`group_id`, `group_name`, `group_description`, `group_privileges`, `status`) VALUES
(1, 'Global Administrator', 'Super Admin', '{"administrative":{"administrative":{"administrative":["index"]},"account":{"account":["index","update"]},"activities":{"activities":["index","read","truncate","delete","pdf","print"]},"countries":{"countries":["index","create","read","update","delete","export","print","pdf"]},"groups":{"groups":["index","create","read","update","delete","export","print","pdf"],"privileges":["index","create","update","read","delete"],"adjust_privileges":["index","create","read","update","delete","export","print","pdf"]},"inquiries":{"inquiries":["index","read","delete","export","print","pdf"]},"menus":{"menus":["index","create","read","update","delete","export","print","pdf"]},"settings":{"settings":["index","update"]},"translations":{"translate":["index","delete_phrase"],"synchronize":["index"],"translations":["index","create","read","update","delete","export","print","pdf"]},"users":{"users":["index","create","read","update","delete","export","print","pdf"],"privileges":["index","update"]}},"apis":{"apis":{"apis":["index"]},"clients":{"clients":["index","create","read","update","delete","export","print","pdf"]},"debug_tool":{"debug_tool":["index"]},"permissions":{"permissions":["index","create","read","update","delete","export","print","pdf"]},"services":{"services":["index","create","read","update","delete","export","print","pdf"]}},"cms":{"cms":{"cms":["index"]},"blogs":{"blogs":["index","create","read","update","delete","export","print","pdf"],"categories":["index","create","read","update","delete","export","print","pdf"]},"galleries":{"galleries":["index","create","read","update","delete","export","print","pdf"]},"pages":{"pages":["index","create","read","update","delete","export","print","pdf"]},"partials":{"faqs":["index","create","read","update","delete","export","print","pdf"],"media":["index"],"partials":["index"],"carousels":["index","create","read","update","delete","export","print","pdf"],"testimonials":["index","create","read","update","delete","export","print","pdf"],"announcements":["index","create","read","update","delete","export","print","pdf"]},"peoples":{"peoples":["index","create","read","update","delete","export","print","pdf"]}},"dashboard":{"dashboard":{"dashboard":["index"]}}}', 1),
(2, 'Technical', 'Group user for technical support', '{"administrative":{"administrative":{"administrative":["index"]},"account":{"account":["index"]},"inquiries":{"inquiries":["index","read","delete","export","print","pdf"]}},"cms":{"cms":{"cms":["index"]},"blogs":{"blogs":["index","create","read","update","delete","export","print","pdf"],"categories":["index","create","read","update","delete","export","print","pdf"]},"galleries":{"galleries":["index","create","read","update","delete","export","print","pdf"]},"pages":{"pages":["index","create","read","update","delete","export","print","pdf"]},"partials":{"faqs":["index","create","read","update","delete","export","print","pdf"],"media":["index"],"partials":["index"],"carousels":["index","create","read","update","delete","export","print","pdf"],"testimonials":["index","create","read","update","delete","export","print","pdf"],"announcements":["index","create","read","update","delete","export","print","pdf"]},"peoples":{"peoples":["index","create","read","update","delete","export","print","pdf"]}},"dashboard":{"dashboard":{"dashboard":["index"]}}}', 1),
(3, 'Subscriber', 'Group user for subscriber', '{"administrative":{"administrative":{"administrative":["index"]},"account":{"account":["index"]}},"dashboard":{"dashboard":{"dashboard":["index"]}}}', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app__groups_privileges`
--

CREATE TABLE IF NOT EXISTS `app__groups_privileges` (
  `module` varchar(256) NOT NULL,
  `submodule` varchar(256) NOT NULL,
  `controller` varchar(256) NOT NULL,
  `privileges` longtext NOT NULL,
  `last_generated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `app__groups_privileges`
--

INSERT INTO `app__groups_privileges` (`module`, `submodule`, `controller`, `privileges`, `last_generated`) VALUES
('administrative', 'account', 'account', '["index","update"]', '2020-11-23 11:08:45'),
('administrative', 'activities', 'activities', '["index","read","truncate","delete","pdf","print"]', '2020-12-02 20:39:46'),
('administrative', 'administrative', 'administrative', '["index"]', '2019-09-02 13:20:17'),
('administrative', 'countries', 'countries', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('administrative', 'groups', 'adjust_privileges', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('administrative', 'groups', 'groups', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('administrative', 'groups', 'privileges', '["index","create","update","read","delete"]', '2019-09-02 13:20:17'),
('administrative', 'menus', 'menus', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('administrative', 'settings', 'settings', '["index","update"]', '2019-09-02 13:20:17'),
('administrative', 'translations', 'synchronize', '["index"]', '2019-09-02 13:20:17'),
('administrative', 'translations', 'translate', '["index","delete_phrase"]', '2020-11-26 21:41:36'),
('administrative', 'translations', 'translations', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('administrative', 'users', 'privileges', '["index","update"]', '2015-02-18 13:44:00'),
('administrative', 'users', 'users', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('apis', 'apis', 'apis', '["index"]', '2020-07-17 03:56:44'),
('apis', 'clients', 'clients', '["index","create","read","update","delete","export","print","pdf"]', '2020-07-17 03:56:46'),
('apis', 'debug_tool', 'debug_tool', '["index"]', '2020-07-17 03:56:48'),
('apis', 'permissions', 'permissions', '["index","create","read","update","delete","export","print","pdf"]', '2020-07-17 03:56:47'),
('apis', 'services', 'services', '["index","create","read","update","delete","export","print","pdf"]', '2020-11-26 16:29:32'),
('cms', 'blogs', 'blogs', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('cms', 'blogs', 'categories', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('cms', 'cms', 'cms', '["index"]', '2019-09-02 13:20:17'),
('cms', 'galleries', 'galleries', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('cms', 'pages', 'pages', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('cms', 'partials', 'announcements', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('cms', 'partials', 'carousels', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('cms', 'partials', 'faqs', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('cms', 'partials', 'media', '["index"]', '2019-09-02 13:20:17'),
('cms', 'partials', 'partials', '["index"]', '2019-09-02 13:20:17'),
('cms', 'partials', 'testimonials', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('cms', 'peoples', 'peoples', '["index","create","read","update","delete","export","print","pdf"]', '2019-09-02 13:20:17'),
('dashboard', 'dashboard', 'dashboard', '["index"]', '2019-09-02 13:20:17');

-- --------------------------------------------------------

--
-- Table structure for table `app__languages`
--

CREATE TABLE IF NOT EXISTS `app__languages` (
  `id` int(11) NOT NULL,
  `language` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(32) NOT NULL,
  `locale` varchar(64) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `app__languages`
--

INSERT INTO `app__languages` (`id`, `language`, `description`, `code`, `locale`, `status`) VALUES
(1, 'Default (English)', 'Default language', 'english', 'en-US,en_US,en_US.UTF8,en-us,en,english', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app__menus`
--

CREATE TABLE IF NOT EXISTS `app__menus` (
  `menu_id` int(11) NOT NULL,
  `menu_placement` varchar(22) COLLATE utf8_unicode_ci NOT NULL,
  `menu_label` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `menu_description` text COLLATE utf8_unicode_ci NOT NULL,
  `serialized_data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `group_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `app__menus`
--

INSERT INTO `app__menus` (`menu_id`, `menu_placement`, `menu_label`, `menu_description`, `serialized_data`, `group_id`, `status`) VALUES
(1, 'header', 'Header Menu', 'Menu for navigation header (front end)', '[{\"order\":0,\"children\":[]},{\"id\":\"1\",\"icon\":\"mdi mdi-home\",\"label\":\"Home\",\"slug\":\"welcome\",\"newtab\":\"0\",\"order\":1,\"children\":[]},{\"id\":\"3\",\"icon\":\"mdi mdi-newspaper\",\"label\":\"News\",\"slug\":\"blogs\",\"newtab\":\"0\",\"order\":3,\"children\":[]},{\"id\":\"4\",\"icon\":\"mdi mdi-map-clock-outline\",\"label\":\"Galleries\",\"slug\":\"galleries\",\"newtab\":\"0\",\"order\":4,\"children\":[]}]', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `app__sessions`
--

CREATE TABLE IF NOT EXISTS `app__sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__settings`
--

CREATE TABLE `app__settings` (
	`id` int(11) NOT NULL, 
	`app_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
	`app_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
	`app_logo` varchar(256) NOT NULL, 
	`app_icon` varchar(256) NOT NULL, 
	`app_theme` varchar(32) NOT NULL, 
	`app_language` int(11) NOT NULL, 
	`office_name` varchar(255) NOT NULL, 
	`office_phone` varchar(32) NOT NULL, 
	`office_email` varchar(64) NOT NULL, 
	`office_fax` varchar(32) NOT NULL, 
	`office_address` text NOT NULL, 
	`office_map` text NOT NULL, 
	`one_device_login` tinyint(1) NOT NULL, 
	`username_changes` tinyint(1) NOT NULL, 
	`frontend_registration` tinyint(1) NOT NULL, 
	`default_membership_group` int(11) NOT NULL, 
	`auto_active_registration` tinyint(1) NOT NULL COMMENT '1 = auto active', 
	`google_analytics_key` varchar(32) NOT NULL, 
	`openlayers_search_provider` varchar(10) NOT NULL, 
	`openlayers_search_key` varchar(128) NOT NULL, 
	`facebook_app_id` varchar(22) NOT NULL, 
	`facebook_app_secret` varchar(512) NOT NULL, 
	`google_client_id` varchar(255) NOT NULL, 
	`google_client_secret` varchar(512) NOT NULL, 
	`twitter_username` varchar(64) NOT NULL, 
	`instagram_username` varchar(64) NOT NULL, 
	`whatsapp_number` varchar(16) NOT NULL, 
	`smtp_email_masking` varchar(255) NOT NULL, 
	`smtp_sender_masking` varchar(64) NOT NULL, 
	`smtp_host` varchar(255) NOT NULL, 
	`smtp_port` int(5) NOT NULL, 
	`smtp_username` varchar(64) NOT NULL, 
	`smtp_password` varchar(512) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__shortlink`
--

CREATE TABLE IF NOT EXISTS `app__shortlink` (
  `hash` varchar(64) NOT NULL,
  `url` text NOT NULL,
  `session` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__users`
--

CREATE TABLE IF NOT EXISTS `app__users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(32) NOT NULL,
  `first_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `bio` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `photo` varchar(255) NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(16) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `language_id` int(11) NOT NULL,
  `country` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `registered_date` date NOT NULL,
  `last_login` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `app__users_privileges`
--

CREATE TABLE IF NOT EXISTS `app__users_privileges` (
  `user_id` int(11) NOT NULL,
  `sub_level_1` int(11) NOT NULL,
  `visible_menu` text NOT NULL,
  `access_year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `app__users_privileges`
--

CREATE TABLE `app__users_hash` (
	`user_id` int(11) NOT NULL, 
	`hash` varchar(64) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE IF NOT EXISTS `blogs` (
  `post_id` int(11) NOT NULL,
  `post_title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `post_slug` varchar(256) NOT NULL,
  `post_excerpt` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `post_content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `post_category` int(11) NOT NULL,
  `post_tags` text NOT NULL,
  `created_timestamp` datetime NOT NULL,
  `updated_timestamp` datetime NOT NULL,
  `author` int(11) NOT NULL,
  `headline` tinyint(1) NOT NULL,
  `featured_image` varchar(256) NOT NULL,
  `language_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `blogs__categories`
--

CREATE TABLE IF NOT EXISTS `blogs__categories` (
  `category_id` int(11) NOT NULL,
  `category_title` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `category_slug` varchar(32) NOT NULL,
  `category_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `category_image` varchar(256) NOT NULL,
  `language_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `blogs__categories`
--

INSERT INTO `blogs__categories` (`category_id`, `category_title`, `category_slug`, `category_description`, `category_image`, `language_id`, `status`) VALUES
(1, 'Uncategorized', 'uncategorized', 'Uncategorized category', 'placeholder.png', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL,
  `code` varchar(8) NOT NULL,
  `country` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE IF NOT EXISTS `galleries` (
  `gallery_id` int(11) NOT NULL,
  `gallery_images` longtext NOT NULL,
  `gallery_title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gallery_slug` varchar(256) NOT NULL,
  `gallery_description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gallery_attributes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gallery_tags` longtext NOT NULL,
  `created_timestamp` datetime NOT NULL,
  `updated_timestamp` datetime NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `author` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth__login`
--

CREATE TABLE IF NOT EXISTS `oauth__login` (
  `user_id` int(11) NOT NULL,
  `service_provider` varchar(32) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` int(11) NOT NULL,
  `page_title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `page_slug` varchar(256) NOT NULL,
  `page_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `page_content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `carousel_id` int(11) NOT NULL,
  `faq_id` int(11) NOT NULL,
  `created_timestamp` datetime NOT NULL,
  `updated_timestamp` datetime NOT NULL,
  `author` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `pages__carousels`
--

CREATE TABLE IF NOT EXISTS `pages__carousels` (
  `carousel_id` int(11) NOT NULL,
  `carousel_title` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `carousel_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `carousel_content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_timestamp` datetime NOT NULL,
  `updated_timestamp` datetime NOT NULL,
  `language_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pages__faqs`
--

CREATE TABLE IF NOT EXISTS `pages__faqs` (
  `faq_id` int(11) NOT NULL,
  `faq_title` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `faq_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `faq_content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_timestamp` datetime NOT NULL,
  `updated_timestamp` datetime NOT NULL,
  `language_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `peoples`
--

CREATE TABLE IF NOT EXISTS `peoples` (
  `people_id` int(11) NOT NULL,
  `first_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `people_slug` varchar(256) NOT NULL,
  `position` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) NOT NULL,
  `mobile` varchar(16) NOT NULL,
  `instagram` varchar(255) NOT NULL,
  `facebook` varchar(64) NOT NULL,
  `twitter` varchar(64) NOT NULL,
  `biography` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `photo` varchar(256) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `rest__clients`
--

CREATE TABLE IF NOT EXISTS `rest__clients` (
  `user_id` int(11) NOT NULL,
  `api_key` varchar(24) NOT NULL,
  `ip_range` text NOT NULL COMMENT 'separate with comma',
  `valid_until` date NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rest__permissions`
--

CREATE TABLE IF NOT EXISTS `rest__permissions` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `method` tinytext NOT NULL COMMENT 'GET, POST, PUT and DELETE',
  `parameter` text NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rest__services`
--

CREATE TABLE IF NOT EXISTS `rest__services` (
  `id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE IF NOT EXISTS `testimonials` (
  `testimonial_id` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `first_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `testimonial_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `testimonial_content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `language_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries`(
    `id` INT(11) NOT NULL,
    `sender_full_name` VARCHAR(64) NOT NULL,
    `sender_email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `messages` TEXT NOT NULL,
    `timestamp` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app__activity_logs`
--
ALTER TABLE `app__activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_app__activity_logs_to_app__users` (`user_id`);

--
-- Indexes for table `app__announcements`
--
ALTER TABLE `app__announcements`
  ADD PRIMARY KEY (`announcement_id`,`announcement_slug`) USING BTREE,
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `app__groups`
--
ALTER TABLE `app__groups`
  ADD PRIMARY KEY (`group_id`) USING BTREE;

--
-- Indexes for table `app__groups_privileges`
--
ALTER TABLE `app__groups_privileges`
  ADD PRIMARY KEY (`module`,`submodule`,`controller`);

--
-- Indexes for table `app__languages`
--
ALTER TABLE `app__languages`
  ADD PRIMARY KEY (`id`,`code`) USING BTREE;

--
-- Indexes for table `app__menus`
--
ALTER TABLE `app__menus`
  ADD PRIMARY KEY (`menu_id`,`menu_placement`,`group_id`) USING BTREE;

--
-- Indexes for table `app__sessions`
--
ALTER TABLE `app__sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app__sessions_timestamp` (`timestamp`);

--
-- Indexes for table `app__settings`
--
ALTER TABLE `app__settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_language` (`app_language`);

--
-- Indexes for table `app__shortlink`
--
ALTER TABLE `app__shortlink`
  ADD PRIMARY KEY (`hash`);

--
-- Indexes for table `app__users`
--
ALTER TABLE `app__users`
  ADD PRIMARY KEY (`user_id`) USING BTREE,
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `language_id` (`language_id`,`group_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `app__users_privileges`
--
ALTER TABLE `app__users_privileges`
  ADD PRIMARY KEY (`user_id`) USING BTREE;

--
-- Indexes for table `app__users_privileges`
--
ALTER TABLE `app__users_hash`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`post_id`) USING BTREE,
  ADD KEY `fk_blogs_to_app__users` (`author`),
  ADD KEY `fk_blogs_to_blogs__categries` (`post_category`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `blogs__categories`
--
ALTER TABLE `blogs__categories`
  ADD PRIMARY KEY (`category_id`,`category_slug`) USING BTREE,
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`gallery_id`,`gallery_slug`) USING BTREE,
  ADD KEY `fk_galleries_to_app__users` (`author`);

--
-- Indexes for table `oauth__login`
--
ALTER TABLE `oauth__login`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`,`page_slug`) USING BTREE,
  ADD KEY `fk_pages_to_app__users` (`author`);

--
-- Indexes for table `pages__carousels`
--
ALTER TABLE `pages__carousels`
  ADD PRIMARY KEY (`carousel_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `pages__faqs`
--
ALTER TABLE `pages__faqs`
  ADD PRIMARY KEY (`faq_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `peoples`
--
ALTER TABLE `peoples`
  ADD PRIMARY KEY (`people_id`);

--
-- Indexes for table `rest__clients`
--
ALTER TABLE `rest__clients`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `rest__permissions`
--
ALTER TABLE `rest__permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `url_id` (`service_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `rest__services`
--
ALTER TABLE `rest__services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`testimonial_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app__activity_logs`
--
ALTER TABLE `app__activity_logs`
  MODIFY `id` int(22) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app__announcements`
--
ALTER TABLE `app__announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app__groups`
--
ALTER TABLE `app__groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app__languages`
--
ALTER TABLE `app__languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app__menus`
--
ALTER TABLE `app__menus`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app__settings`
--
ALTER TABLE `app__settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app__users`
--
ALTER TABLE `app__users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `blogs__categories`
--
ALTER TABLE `blogs__categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `gallery_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pages__carousels`
--
ALTER TABLE `pages__carousels`
  MODIFY `carousel_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pages__faqs`
--
ALTER TABLE `pages__faqs`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `peoples`
--
ALTER TABLE `peoples`
  MODIFY `people_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rest__permissions`
--
ALTER TABLE `rest__permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rest__services`
--
ALTER TABLE `rest__services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `testimonial_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `app__activity_logs`
--
ALTER TABLE `app__activity_logs`
  ADD CONSTRAINT `app__activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `app__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `app__announcements`
--
ALTER TABLE `app__announcements`
  ADD CONSTRAINT `app__announcements_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `app__languages` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `app__users`
--
ALTER TABLE `app__users`
  ADD CONSTRAINT `app__users_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `app__languages` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `app__users_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `app__groups` (`group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `app__users_privileges`
--
ALTER TABLE `app__users_privileges`
  ADD CONSTRAINT `app__users_privileges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `app__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `app__users_privileges`
--
ALTER TABLE `app__users_hash`
  ADD CONSTRAINT `app__users_hash_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `app__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_2` FOREIGN KEY (`author`) REFERENCES `app__users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `blogs_ibfk_3` FOREIGN KEY (`language_id`) REFERENCES `app__languages` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `blogs_ibfk_4` FOREIGN KEY (`post_category`) REFERENCES `blogs__categories` (`category_id`) ON UPDATE CASCADE;

--
-- Constraints for table `blogs__categories`
--
ALTER TABLE `blogs__categories`
  ADD CONSTRAINT `blogs__categories_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `app__languages` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `galleries_ibfk_1` FOREIGN KEY (`author`) REFERENCES `app__users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `oauth__login`
--
ALTER TABLE `oauth__login`
  ADD CONSTRAINT `oauth__login_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `app__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`author`) REFERENCES `app__users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `pages__carousels`
--
ALTER TABLE `pages__carousels`
  ADD CONSTRAINT `pages__carousels_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `app__languages` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pages__faqs`
--
ALTER TABLE `pages__faqs`
  ADD CONSTRAINT `pages__faqs_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `app__languages` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest__clients`
--
ALTER TABLE `rest__clients`
  ADD CONSTRAINT `rest__clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `app__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rest__permissions`
--
ALTER TABLE `rest__permissions`
  ADD CONSTRAINT `rest__permissions_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `rest__clients` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rest__permissions_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `rest__services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `app__languages` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
