--
-- Table structure for table `app__activity_logs`
--

CREATE TABLE `app__activity_logs` (
  `id` int(22) NOT NULL,
  `user_id` int(22) NOT NULL,
  `path` varchar(256) NOT NULL,
  `method` varchar(256) NOT NULL,
  `browser` varchar(256) NOT NULL,
  `platform` varchar(256) NOT NULL,
  `ip_address` varchar(22) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `app__announcements`
--

CREATE TABLE `app__announcements` (
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
-- Table structure for table `app__connections`
--

CREATE TABLE `app__connections` (
  `year` year(4) NOT NULL,
  `name` varchar(256) NOT NULL,
  `description` varchar(256) NOT NULL,
  `database_driver` varchar(32) NOT NULL,
  `hostname` varchar(256) NOT NULL,
  `port` varchar(256) NOT NULL,
  `username` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `database_name` varchar(256) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__countries`
--

CREATE TABLE `app__countries` (
  `id` int(11) NOT NULL,
  `code` varchar(8) NOT NULL,
  `country` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `app__countries`
--

INSERT INTO `app__countries` (`id`, `code`, `country`, `status`) VALUES
(1, 'AF', 'Afghanistan', 1),
(2, 'AL', 'Albania', 1),
(3, 'DZ', 'Algeria', 1),
(4, 'AS', 'American Samoa', 1),
(5, 'AD', 'Andorra', 1),
(6, 'AO', 'Angola', 1),
(7, 'AI', 'Anguilla', 1),
(8, 'AQ', 'Antarctica', 1),
(9, 'AG', 'Antigua and Barbuda', 1),
(10, 'AR', 'Argentina', 1),
(11, 'AM', 'Armenia', 1),
(12, 'AW', 'Aruba', 1),
(13, 'AU', 'Australia', 1),
(14, 'AT', 'Austria', 1),
(15, 'AZ', 'Azerbaijan', 1),
(16, 'BS', 'Bahamas', 1),
(17, 'BH', 'Bahrain', 1),
(18, 'BD', 'Bangladesh', 1),
(19, 'BB', 'Barbados', 1),
(20, 'BY', 'Belarus', 1),
(21, 'BE', 'Belgium', 1),
(22, 'BZ', 'Belize', 1),
(23, 'BJ', 'Benin', 1),
(24, 'BM', 'Bermuda', 1),
(25, 'BT', 'Bhutan', 1),
(26, 'BO', 'Bolivia', 1),
(27, 'BA', 'Bosnia and Herzegovina', 1),
(28, 'BW', 'Botswana', 1),
(29, 'BV', 'Bouvet Island', 1),
(30, 'BR', 'Brazil', 1),
(31, 'IO', 'British Indian Ocean Territory', 1),
(32, 'BN', 'Brunei Darussalam', 1),
(33, 'BG', 'Bulgaria', 1),
(34, 'BF', 'Burkina Faso', 1),
(35, 'BI', 'Burundi', 1),
(36, 'KH', 'Cambodia', 1),
(37, 'CM', 'Cameroon', 1),
(38, 'CA', 'Canada', 1),
(39, 'CV', 'Cape Verde', 1),
(40, 'KY', 'Cayman Islands', 1),
(41, 'CF', 'Central African Republic', 1),
(42, 'TD', 'Chad', 1),
(43, 'CL', 'Chile', 1),
(44, 'CN', 'China', 1),
(45, 'CX', 'Christmas Island', 1),
(46, 'CC', 'Cocos (Keeling) Islands', 1),
(47, 'CO', 'Colombia', 1),
(48, 'KM', 'Comoros', 1),
(49, 'CG', 'Congo', 1),
(50, 'CD', 'Congo, the Democratic Republic o', 1),
(51, 'CK', 'Cook Islands', 1),
(52, 'CR', 'Costa Rica', 1),
(53, 'CI', 'Cote D''Ivoire', 1),
(54, 'HR', 'Croatia', 1),
(55, 'CU', 'Cuba', 1),
(56, 'CY', 'Cyprus', 1),
(57, 'CZ', 'Czech Republic', 1),
(58, 'DK', 'Denmark', 1),
(59, 'DJ', 'Djibouti', 1),
(60, 'DM', 'Dominica', 1),
(61, 'DO', 'Dominican Republic', 1),
(62, 'EC', 'Ecuador', 1),
(63, 'EG', 'Egypt', 1),
(64, 'SV', 'El Salvador', 1),
(65, 'GQ', 'Equatorial Guinea', 1),
(66, 'ER', 'Eritrea', 1),
(67, 'EE', 'Estonia', 1),
(68, 'ET', 'Ethiopia', 1),
(69, 'FK', 'Falkland Islands (Malvinas)', 1),
(70, 'FO', 'Faroe Islands', 1),
(71, 'FJ', 'Fiji', 1),
(72, 'FI', 'Finland', 1),
(73, 'FR', 'France', 1),
(74, 'GF', 'French Guiana', 1),
(75, 'PF', 'French Polynesia', 1),
(76, 'TF', 'French Southern Territories', 1),
(77, 'GA', 'Gabon', 1),
(78, 'GM', 'Gambia', 1),
(79, 'GE', 'Georgia', 1),
(80, 'DE', 'Germany', 1),
(81, 'GH', 'Ghana', 1),
(82, 'GI', 'Gibraltar', 1),
(83, 'GR', 'Greece', 1),
(84, 'GL', 'Greenland', 1),
(85, 'GD', 'Grenada', 1),
(86, 'GP', 'Guadeloupe', 1),
(87, 'GU', 'Guam', 1),
(88, 'GT', 'Guatemala', 1),
(89, 'GN', 'Guinea', 1),
(90, 'GW', 'Guinea-Bissau', 1),
(91, 'GY', 'Guyana', 1),
(92, 'HT', 'Haiti', 1),
(93, 'HM', 'Heard Island and Mcdonald Island', 1),
(94, 'VA', 'Holy See (Vatican City State)', 1),
(95, 'HN', 'Honduras', 1),
(96, 'HK', 'Hong Kong', 1),
(97, 'HU', 'Hungary', 1),
(98, 'IS', 'Iceland', 1),
(99, 'IN', 'India', 1),
(100, 'ID', 'Indonesia', 1),
(101, 'IR', 'Iran, Islamic Republic of', 1),
(102, 'IQ', 'Iraq', 1),
(103, 'IE', 'Ireland', 1),
(104, 'IL', 'Israel', 1),
(105, 'IT', 'Italy', 1),
(106, 'JM', 'Jamaica', 1),
(107, 'JP', 'Japan', 1),
(108, 'JO', 'Jordan', 1),
(109, 'KZ', 'Kazakhstan', 1),
(110, 'KE', 'Kenya', 1),
(111, 'KI', 'Kiribati', 1),
(112, 'KP', 'Korea, Democratic People''s Repub', 1),
(113, 'KR', 'Korea, Republic of', 1),
(114, 'KW', 'Kuwait', 1),
(115, 'KG', 'Kyrgyzstan', 1),
(116, 'LA', 'Lao People''s Democratic Republic', 1),
(117, 'LV', 'Latvia', 1),
(118, 'LB', 'Lebanon', 1),
(119, 'LS', 'Lesotho', 1),
(120, 'LR', 'Liberia', 1),
(121, 'LY', 'Libyan Arab Jamahiriya', 1),
(122, 'LI', 'Liechtenstein', 1),
(123, 'LT', 'Lithuania', 1),
(124, 'LU', 'Luxembourg', 1),
(125, 'MO', 'Macao', 1),
(126, 'MK', 'Macedonia, the Former Yugoslav R', 1),
(127, 'MG', 'Madagascar', 1),
(128, 'MW', 'Malawi', 1),
(129, 'MY', 'Malaysia', 1),
(130, 'MV', 'Maldives', 1),
(131, 'ML', 'Mali', 1),
(132, 'MT', 'Malta', 1),
(133, 'MH', 'Marshall Islands', 1),
(134, 'MQ', 'Martinique', 1),
(135, 'MR', 'Mauritania', 1),
(136, 'MU', 'Mauritius', 1),
(137, 'YT', 'Mayotte', 1),
(138, 'MX', 'Mexico', 1),
(139, 'FM', 'Micronesia, Federated States of', 1),
(140, 'MD', 'Moldova, Republic of', 1),
(141, 'MC', 'Monaco', 1),
(142, 'MN', 'Mongolia', 1),
(143, 'MS', 'Montserrat', 1),
(144, 'MA', 'Morocco', 1),
(145, 'MZ', 'Mozambique', 1),
(146, 'MM', 'Myanmar', 1),
(147, 'NA', 'Namibia', 1),
(148, 'NR', 'Nauru', 1),
(149, 'NP', 'Nepal', 1),
(150, 'NL', 'Netherlands', 1),
(151, 'AN', 'Netherlands Antilles', 1),
(152, 'NC', 'New Caledonia', 1),
(153, 'NZ', 'New Zealand', 1),
(154, 'NI', 'Nicaragua', 1),
(155, 'NE', 'Niger', 1),
(156, 'NG', 'Nigeria', 1),
(157, 'NU', 'Niue', 1),
(158, 'NF', 'Norfolk Island', 1),
(159, 'MP', 'Northern Mariana Islands', 1),
(160, 'NO', 'Norway', 1),
(161, 'OM', 'Oman', 1),
(162, 'PK', 'Pakistan', 1),
(163, 'PW', 'Palau', 1),
(164, 'PS', 'Palestinian Territory, Occupied', 1),
(165, 'PA', 'Panama', 1),
(166, 'PG', 'Papua New Guinea', 1),
(167, 'PY', 'Paraguay', 1),
(168, 'PE', 'Peru', 1),
(169, 'PH', 'Philippines', 1),
(170, 'PN', 'Pitcairn', 1),
(171, 'PL', 'Poland', 1),
(172, 'PT', 'Portugal', 1),
(173, 'PR', 'Puerto Rico', 1),
(174, 'QA', 'Qatar', 1),
(175, 'RE', 'Reunion', 1),
(176, 'RO', 'Romania', 1),
(177, 'RU', 'Russian Federation', 1),
(178, 'RW', 'Rwanda', 1),
(179, 'SH', 'Saint Helena', 1),
(180, 'KN', 'Saint Kitts and Nevis', 1),
(181, 'LC', 'Saint Lucia', 1),
(182, 'PM', 'Saint Pierre and Miquelon', 1),
(183, 'VC', 'Saint Vincent and the Grenadines', 1),
(184, 'WS', 'Samoa', 1),
(185, 'SM', 'San Marino', 1),
(186, 'ST', 'Sao Tome and Principe', 1),
(187, 'SA', 'Saudi Arabia', 1),
(188, 'SN', 'Senegal', 1),
(189, 'CS', 'Serbia and Montenegro', 1),
(190, 'SC', 'Seychelles', 1),
(191, 'SL', 'Sierra Leone', 1),
(192, 'SG', 'Singapore', 1),
(193, 'SK', 'Slovakia', 1),
(194, 'SI', 'Slovenia', 1),
(195, 'SB', 'Solomon Islands', 1),
(196, 'SO', 'Somalia', 1),
(197, 'ZA', 'South Africa', 1),
(198, 'GS', 'South Georgia and the South Sand', 1),
(199, 'ES', 'Spain', 1),
(200, 'LK', 'Sri Lanka', 1),
(201, 'SD', 'Sudan', 1),
(202, 'SR', 'Suriname', 1),
(203, 'SJ', 'Svalbard and Jan Mayen', 1),
(204, 'SZ', 'Swaziland', 1),
(205, 'SE', 'Sweden', 1),
(206, 'CH', 'Switzerland', 1),
(207, 'SY', 'Syrian Arab Republic', 1),
(208, 'TW', 'Taiwan, Province of China', 1),
(209, 'TJ', 'Tajikistan', 1),
(210, 'TZ', 'Tanzania, United Republic of', 1),
(211, 'TH', 'Thailand', 1),
(212, 'TL', 'Timor-Leste', 1),
(213, 'TG', 'Togo', 1),
(214, 'TK', 'Tokelau', 1),
(215, 'TO', 'Tonga', 1),
(216, 'TT', 'Trinidad and Tobago', 1),
(217, 'TN', 'Tunisia', 1),
(218, 'TR', 'Turkey', 1),
(219, 'TM', 'Turkmenistan', 1),
(220, 'TC', 'Turks and Caicos Islands', 1),
(221, 'TV', 'Tuvalu', 1),
(222, 'UG', 'Uganda', 1),
(223, 'UA', 'Ukraine', 1),
(224, 'AE', 'United Arab Emirates', 1),
(225, 'GB', 'United Kingdom', 1),
(226, 'US', 'United States', 1),
(227, 'UM', 'United States Minor Outlying Isl', 1),
(228, 'UY', 'Uruguay', 1),
(229, 'UZ', 'Uzbekistan', 1),
(230, 'VU', 'Vanuatu', 1),
(231, 'VE', 'Venezuela', 1),
(232, 'VN', 'Viet Nam', 1),
(233, 'VG', 'Virgin Islands, British', 1),
(234, 'VI', 'Virgin Islands, U.s.', 1),
(235, 'WF', 'Wallis and Futuna', 1),
(236, 'EH', 'Western Sahara', 1),
(237, 'YE', 'Yemen', 1),
(238, 'ZM', 'Zambia', 1),
(239, 'ZW', 'Zimbabwe', 1);

--
-- Table structure for table `app__ftp`
--

CREATE TABLE `app__ftp` (
  `site_id` int(11) NOT NULL,
  `hostname` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `username` varchar(64) NOT NULL,
  `password` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__groups`
--

CREATE TABLE `app__groups` (
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
(1, 'Global Administrator', 'Super Admin', '{"addons":["index","detail","install"],"addons\/ftp":["index"],"addons\/modules":["index","detail","delete"],"addons\/themes":["index","detail","customize","delete"],"administrative":["index"],"administrative\/account":["index","update"],"administrative\/activities":["index","read","truncate","delete","pdf","print"],"administrative\/cleaner":["index","clean"],"administrative\/countries":["index","create","read","update","delete","export","print","pdf"],"administrative\/connections":["index","create","read","update","delete","export","print","pdf"],"administrative\/groups":["index","create","read","update","delete","export","print","pdf"],"administrative\/groups\/adjust_privileges":["index","create","read","update","delete","export","print","pdf"],"administrative\/groups\/privileges":["index","create","update","read","delete"],"administrative\/menus":["index","create","read","update","delete","export","print","pdf"],"administrative\/settings":["index","update"],"administrative\/translations":["index","create","read","update","delete","export","print","pdf"],"administrative\/translations\/synchronize":["index"],"administrative\/translations\/translate":["index","delete_phrase"],"administrative\/updater":["index","update"],"administrative\/users":["index","create","read","update","delete","export","print","pdf"],"administrative\/users\/privileges":["index","update"],"administrative\/years":["index","create","read","update","delete","export","print","pdf"],"apis":["index"],"apis\/debug_tool":["index"],"apis\/services":["index","create","read","update","delete","export","print","pdf"],"cms":["index"],"cms\/blogs":["index","create","read","update","delete","export","print","pdf"],"cms\/blogs\/categories":["index","create","read","update","delete","export","print","pdf"],"cms\/galleries":["index","create","read","update","delete","export","print","pdf"],"cms\/pages":["index","create","read","update","delete","export","print","pdf"],"cms\/partials":["index"],"cms\/partials\/announcements":["index","create","read","update","delete","export","print","pdf"],"cms\/partials\/carousels":["index","create","read","update","delete","export","print","pdf"],"cms\/partials\/faqs":["index","create","read","update","delete","export","print","pdf"],"cms\/partials\/inquiries":["index","read","delete","export","print","pdf"],"cms\/partials\/media":["index"],"cms\/partials\/testimonials":["index","create","read","update","delete","export","print","pdf"],"cms\/peoples":["index","create","read","update","delete","export","print","pdf"],"dashboard":["index"]}', 1),
(2, 'Technical', 'Group user for technical support', '{"administrative":["index"],"administrative\/account":["index","update"],"apis":["index"],"apis\/debug_tool":["index"],"apis\/services":["index","create","read","update","delete","export","print","pdf"],"cms":["index"],"cms\/blogs":["index","create","read","update","delete","export","print","pdf"],"cms\/blogs\/categories":["index","create","read","update","delete","export","print","pdf"],"cms\/galleries":["index","create","read","update","delete","export","print","pdf"],"cms\/pages":["index","create","read","update","delete","export","print","pdf"],"cms\/partials":["index"],"cms\/partials\/announcements":["index","create","read","update","delete","export","print","pdf"],"cms\/partials\/carousels":["index","create","read","update","delete","export","print","pdf"],"cms\/partials\/faqs":["index","create","read","update","delete","export","print","pdf"],"cms\/partials\/inquiries":["index","read","delete","export","print","pdf"],"cms\/partials\/media":["index"],"cms\/partials\/testimonial":["index","create","read","update","delete","export","print","pdf"],"cms\/peoples":["index","create","read","update","delete","export","print","pdf"],"dashboard":["index"]}', 1),
(3, 'Subscriber', 'Group user for subscriber', '{"administrative":["index"],"administrative\/account":["index","update"],"dashboard":["index"]}', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app__groups_privileges`
--

CREATE TABLE `app__groups_privileges` (
  `path` varchar(256) NOT NULL,
  `privileges` longtext NOT NULL,
  `last_generated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `app__groups_privileges`
--

INSERT INTO `app__groups_privileges` (`path`, `privileges`, `last_generated`) VALUES
('addons', '["index","detail","install"]', NOW()),
('addons/ftp', '["index"]', NOW()),
('addons/modules', '["index","detail","delete"]', NOW()),
('addons/themes', '["index","detail","customize","delete"]', NOW()),
('administrative', '["index"]', NOW()),
('administrative/account', '["index","update"]', NOW()),
('administrative/activities', '["index","read","truncate","delete","pdf","print"]', NOW()),
('administrative/cleaner', '["index","clean"]', NOW()),
('administrative/countries', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('administrative/connections', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('administrative/groups', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('administrative/groups/adjust_privileges', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('administrative/groups/privileges', '["index","create","update","read","delete"]', NOW()),
('administrative/menus', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('administrative/settings', '["index","update"]', NOW()),
('administrative/translations', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('administrative/translations/synchronize', '["index"]', NOW()),
('administrative/translations/translate', '["index","delete_phrase"]', NOW()),
('administrative/updater', '["index","update"]', NOW()),
('administrative/users', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('administrative/users/privileges', '["index","update"]', NOW()),
('administrative/years', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('apis', '["index"]', NOW()),
('apis/debug_tool', '["index"]', NOW()),
('apis/services', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('cms', '["index"]', NOW()),
('cms/blogs', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('cms/blogs/categories', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('cms/galleries', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('cms/pages', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('cms/partials', '["index"]', NOW()),
('cms/partials/announcements', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('cms/partials/carousels', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('cms/partials/faqs', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('cms/partials/inquiries', '["index","read","delete","export","print","pdf"]', NOW()),
('cms/partials/media', '["index"]', NOW()),
('cms/partials/testimonials', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('cms/peoples', '["index","create","read","update","delete","export","print","pdf"]', NOW()),
('dashboard', '["index"]', NOW());

-- --------------------------------------------------------

--
-- Table structure for table `app__languages`
--

CREATE TABLE `app__languages` (
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
(1, 'Default (English)', 'Default language', 'en', 'en-US,en_US,en_US.UTF8,en-us,en,english', 1),
(2, 'Bahasa Indonesia', 'Terjemahan bahasa Indonesia', 'id', 'id-ID,id_ID,id_ID.UTF8,id-id,id,indonesian', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app__menus`
--

CREATE TABLE `app__menus` (
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
(1, 'header', 'Header Menu', 'Menu for navigation header (front end)', '[{"order":0,"children":[]},{"id":"1","icon":"mdi mdi-home","label":"Home","slug":"home","newtab":"0","order":1,"children":[]},{"id":"3","icon":"mdi mdi-newspaper","label":"News","slug":"blogs","newtab":"0","order":3,"children":[]},{"id":"4","icon":"mdi mdi-map-clock-outline","label":"Galleries","slug":"galleries","newtab":"0","order":4,"children":[]}]', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `app__rest_api`
--

CREATE TABLE IF NOT EXISTS `app__rest_api` (
  `id` int(11) NOT NULL,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `method` tinytext NOT NULL,
  `ip_range` text NOT NULL,
  `valid_until` date NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__sessions`
--

CREATE TABLE `app__sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
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
  `frontend_theme` varchar(32) NOT NULL,
  `backend_theme` varchar(32) NOT NULL,
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
  `default_map_tile` varchar(255) NOT NULL,
  `disqus_site_domain` varchar(128) NOT NULL,
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
  `smtp_password` varchar(512) NOT NULL,
  `action_sound` tinyint(1) NOT NULL COMMENT '1 = auto active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__shortlink`
--

CREATE TABLE `app__shortlink` (
  `hash` varchar(64) NOT NULL,
  `url` text NOT NULL,
  `session` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__users`
--

CREATE TABLE `app__users` (
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
-- Table structure for table `app__users_hash`
--

CREATE TABLE `app__users_hash` (
  `user_id` int(11) NOT NULL,
  `hash` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__users_privileges`
--

CREATE TABLE `app__users_privileges` (
  `user_id` int(11) NOT NULL,
  `sub_level_1` int(11) NOT NULL,
  `visible_menu` text NOT NULL,
  `access_year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `app__visitor_logs`
--

CREATE TABLE IF NOT EXISTS `app__visitor_logs` (
  `ip_address` varchar(32) NOT NULL,
  `timestamp` datetime NOT NULL,
  `browser` varchar(32) NOT NULL,
  `platform` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app__years`
--

CREATE TABLE `app__years` (
  `year` year(4) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
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

CREATE TABLE `blogs__categories` (
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

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `code` varchar(8) NOT NULL,
  `country` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
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
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `sender_full_name` varchar(64) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `messages` text NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth__login`
--

CREATE TABLE `oauth__login` (
  `user_id` int(11) NOT NULL,
  `service_provider` varchar(32) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
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

CREATE TABLE `pages__carousels` (
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

CREATE TABLE `pages__faqs` (
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

CREATE TABLE `peoples` (
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
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `testimonial_id` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `first_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `testimonial_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `testimonial_content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `language_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
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
-- Indexes for table `app__connections`
--
ALTER TABLE `app__connections`
  ADD PRIMARY KEY (`year`,`database_driver`) USING BTREE;

--
-- Indexes for table `app__countries`
--
ALTER TABLE `app__countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app__ftp`
--
ALTER TABLE `app__ftp`
  ADD PRIMARY KEY (`site_id`);

--
-- Indexes for table `app__groups`
--
ALTER TABLE `app__groups`
  ADD PRIMARY KEY (`group_id`) USING BTREE;

--
-- Indexes for table `app__groups_privileges`
--
ALTER TABLE `app__groups_privileges`
  ADD PRIMARY KEY (`path`);

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
-- Indexes for table `app__rest_api`
--
ALTER TABLE `app__rest_api`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `app__users_hash`
--
ALTER TABLE `app__users_hash`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `app__users_privileges`
--
ALTER TABLE `app__users_privileges`
  ADD PRIMARY KEY (`user_id`) USING BTREE;

--
-- Indexes for table `app__visitor_logs`
--
ALTER TABLE `app__visitor_logs`
  ADD PRIMARY KEY (`ip_address`,`timestamp`);

--
-- Indexes for table `app__years`
--
ALTER TABLE `app__years`
  ADD PRIMARY KEY (`year`);

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
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth__login`
--
ALTER TABLE `oauth__login`
  ADD PRIMARY KEY (`user_id`,`service_provider`) USING BTREE;

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
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`testimonial_id`),
  ADD KEY `language_id` (`language_id`);

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
-- AUTO_INCREMENT for table `app__countries`
--
ALTER TABLE `app__countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app__groups`
--
ALTER TABLE `app__groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `app__languages`
--
ALTER TABLE `app__languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `app__menus`
--
ALTER TABLE `app__menus`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `app__rest_api`
--
ALTER TABLE `app__rest_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `testimonial_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `app__ftp`
--
ALTER TABLE `app__ftp`
  ADD CONSTRAINT `app__ftp_ibfk_1` FOREIGN KEY (`site_id`) REFERENCES `app__settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `app__users`
--
ALTER TABLE `app__users`
  ADD CONSTRAINT `app__users_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `app__languages` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `app__users_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `app__groups` (`group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `app__users_hash`
--
ALTER TABLE `app__users_hash`
  ADD CONSTRAINT `app__users_hash_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `app__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `app__users_privileges`
--
ALTER TABLE `app__users_privileges`
  ADD CONSTRAINT `app__users_privileges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `app__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `app__connections`
--
ALTER TABLE `app__connections`
  ADD CONSTRAINT `app__connections_ibfk_1` FOREIGN KEY (`year`) REFERENCES `app__years` (`year`) ON UPDATE CASCADE;

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
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `app__languages` (`id`) ON UPDATE CASCADE;
