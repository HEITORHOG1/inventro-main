SET sql_mode = '';

--
-- Table structure for table `attendance_tbl`
--

CREATE TABLE `attendance_tbl` (
  `attandence_id` int(11) NOT NULL,
  `date` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `in_time` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `out_time` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `staytime` time NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_tbl`
--

CREATE TABLE `bank_tbl` (
  `id` int(11) NOT NULL,
  `bank_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `bank_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `account_no` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `branch_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `area` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_tbl`
--

CREATE TABLE `category_tbl` (
  `id` int(11) NOT NULL,
  `category_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `is_menu` int(2) DEFAULT NULL,
  `is_front` int(2) DEFAULT NULL,
  `ordering` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `country_tbl`
--

CREATE TABLE `country_tbl` (
  `country_id` int(11) NOT NULL,
  `sortname` varchar(150) NOT NULL,
  `country_name` varchar(150) NOT NULL,
  `phonecode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `country_tbl`
--

INSERT INTO `country_tbl` (`country_id`, `sortname`, `country_name`, `phonecode`) VALUES
(2, 'AL', 'Albania', 355),
(3, 'DZ', 'Algeria', 213),
(4, 'AS', 'American Samoa', 1684),
(5, 'AD', 'Andorra', 376),
(6, 'AO', 'Angola', 244),
(7, 'AI', 'Anguilla', 1264),
(8, 'AQ', 'Antarctica', 0),
(9, 'AG', 'Antigua And Barbuda', 1268),
(10, 'AR', 'Argentina', 54),
(11, 'AM', 'Armenia', 374),
(12, 'AW', 'Aruba', 297),
(13, 'AU', 'Australia', 61),
(14, 'AT', 'Austria', 43),
(15, 'AZ', 'Azerbaijan', 994),
(16, 'BS', 'Bahamas The', 1242),
(17, 'BH', 'Bahrain', 973),
(18, 'BD', 'Bangladesh', 880),
(19, 'BB', 'Barbados', 1246),
(20, 'BY', 'Belarus', 375),
(21, 'BE', 'Belgium', 32),
(22, 'BZ', 'Belize', 501),
(23, 'BJ', 'Benin', 229),
(24, 'BM', 'Bermuda', 1441),
(25, 'BT', 'Bhutan', 975),
(26, 'BO', 'Bolivia', 591),
(27, 'BA', 'Bosnia and Herzegovina', 387),
(28, 'BW', 'Botswana', 267),
(29, 'BV', 'Bouvet Island', 0),
(30, 'BR', 'Brazil', 55),
(31, 'IO', 'British Indian Ocean Territory', 246),
(32, 'BN', 'Brunei', 673),
(33, 'BG', 'Bulgaria', 359),
(34, 'BF', 'Burkina Faso', 226),
(35, 'BI', 'Burundi', 257),
(36, 'KH', 'Cambodia', 855),
(37, 'CM', 'Cameroon', 237),
(38, 'CA', 'Canada', 1),
(39, 'CV', 'Cape Verde', 238),
(40, 'KY', 'Cayman Islands', 1345),
(41, 'CF', 'Central African Republic', 236),
(42, 'TD', 'Chad', 235),
(43, 'CL', 'Chile', 56),
(44, 'CN', 'China', 86),
(45, 'CX', 'Christmas Island', 61),
(46, 'CC', 'Cocos (Keeling) Islands', 672),
(47, 'CO', 'Colombia', 57),
(48, 'KM', 'Comoros', 269),
(49, 'CG', 'Republic Of The Congo', 242),
(50, 'CD', 'Democratic Republic Of The Congo', 242),
(51, 'CK', 'Cook Islands', 682),
(52, 'CR', 'Costa Rica', 506),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)', 225),
(54, 'HR', 'Croatia (Hrvatska)', 385),
(55, 'CU', 'Cuba', 53),
(56, 'CY', 'Cyprus', 357),
(57, 'CZ', 'Czech Republic', 420),
(58, 'DK', 'Denmark', 45),
(59, 'DJ', 'Djibouti', 253),
(60, 'DM', 'Dominica', 1767),
(61, 'DO', 'Dominican Republic', 1809),
(62, 'TP', 'East Timor', 670),
(63, 'EC', 'Ecuador', 593),
(64, 'EG', 'Egypt', 20),
(65, 'SV', 'El Salvador', 503),
(66, 'GQ', 'Equatorial Guinea', 240),
(67, 'ER', 'Eritrea', 291),
(68, 'EE', 'Estonia', 372),
(69, 'ET', 'Ethiopia', 251),
(70, 'XA', 'External Territories of Australia', 61),
(71, 'FK', 'Falkland Islands', 500),
(72, 'FO', 'Faroe Islands', 298),
(73, 'FJ', 'Fiji Islands', 679),
(74, 'FI', 'Finland', 358),
(75, 'FR', 'France', 33),
(76, 'GF', 'French Guiana', 594),
(77, 'PF', 'French Polynesia', 689),
(78, 'TF', 'French Southern Territories', 0),
(79, 'GA', 'Gabon', 241),
(80, 'GM', 'Gambia The', 220),
(81, 'GE', 'Georgia', 995),
(82, 'DE', 'Germany', 49),
(83, 'GH', 'Ghana', 233),
(84, 'GI', 'Gibraltar', 350),
(85, 'GR', 'Greece', 30),
(86, 'GL', 'Greenland', 299),
(87, 'GD', 'Grenada', 1473),
(88, 'GP', 'Guadeloupe', 590),
(89, 'GU', 'Guam', 1671),
(90, 'GT', 'Guatemala', 502),
(91, 'XU', 'Guernsey and Alderney', 44),
(92, 'GN', 'Guinea', 224),
(93, 'GW', 'Guinea-Bissau', 245),
(94, 'GY', 'Guyana', 592),
(95, 'HT', 'Haiti', 509),
(96, 'HM', 'Heard and McDonald Islands', 0),
(97, 'HN', 'Honduras', 504),
(98, 'HK', 'Hong Kong S.A.R.', 852),
(99, 'HU', 'Hungary', 36),
(100, 'IS', 'Iceland', 354),
(101, 'IN', 'India', 91),
(102, 'ID', 'Indonesia', 62),
(103, 'IR', 'Iran', 98),
(104, 'IQ', 'Iraq', 964),
(105, 'IE', 'Ireland', 353),
(106, 'IL', 'Israel', 972),
(107, 'IT', 'Italy', 39),
(108, 'JM', 'Jamaica', 1876),
(109, 'JP', 'Japan', 81),
(110, 'XJ', 'Jersey', 44),
(111, 'JO', 'Jordan', 962),
(112, 'KZ', 'Kazakhstan', 7),
(113, 'KE', 'Kenya', 254),
(114, 'KI', 'Kiribati', 686),
(115, 'KP', 'Korea North', 850),
(116, 'KR', 'Korea South', 82),
(117, 'KW', 'Kuwait', 965),
(118, 'KG', 'Kyrgyzstan', 996),
(119, 'LA', 'Laos', 856),
(120, 'LV', 'Latvia', 371),
(121, 'LB', 'Lebanon', 961),
(122, 'LS', 'Lesotho', 266),
(123, 'LR', 'Liberia', 231),
(124, 'LY', 'Libya', 218),
(125, 'LI', 'Liechtenstein', 423),
(126, 'LT', 'Lithuania', 370),
(127, 'LU', 'Luxembourg', 352),
(128, 'MO', 'Macau S.A.R.', 853),
(129, 'MK', 'Macedonia', 389),
(130, 'MG', 'Madagascar', 261),
(131, 'MW', 'Malawi', 265),
(132, 'MY', 'Malaysia', 60),
(133, 'MV', 'Maldives', 960),
(134, 'ML', 'Mali', 223),
(135, 'MT', 'Malta', 356),
(136, 'XM', 'Man (Isle of)', 44),
(137, 'MH', 'Marshall Islands', 692),
(138, 'MQ', 'Martinique', 596),
(139, 'MR', 'Mauritania', 222),
(140, 'MU', 'Mauritius', 230),
(141, 'YT', 'Mayotte', 269),
(142, 'MX', 'Mexico', 52),
(143, 'FM', 'Micronesia', 691),
(144, 'MD', 'Moldova', 373),
(145, 'MC', 'Monaco', 377),
(146, 'MN', 'Mongolia', 976),
(147, 'MS', 'Montserrat', 1664),
(148, 'MA', 'Morocco', 212),
(149, 'MZ', 'Mozambique', 258),
(150, 'MM', 'Myanmar', 95),
(151, 'NA', 'Namibia', 264),
(152, 'NR', 'Nauru', 674),
(153, 'NP', 'Nepal', 977),
(154, 'AN', 'Netherlands Antilles', 599),
(155, 'NL', 'Netherlands The', 31),
(156, 'NC', 'New Caledonia', 687),
(157, 'NZ', 'New Zealand', 64),
(158, 'NI', 'Nicaragua', 505),
(159, 'NE', 'Niger', 227),
(160, 'NG', 'Nigeria', 234),
(161, 'NU', 'Niue', 683),
(162, 'NF', 'Norfolk Island', 672),
(163, 'MP', 'Northern Mariana Islands', 1670),
(164, 'NO', 'Norway', 47),
(165, 'OM', 'Oman', 968),
(166, 'PK', 'Pakistan', 92),
(167, 'PW', 'Palau', 680),
(168, 'PS', 'Palestinian Territory Occupied', 970),
(169, 'PA', 'Panama', 507),
(170, 'PG', 'Papua new Guinea', 675),
(171, 'PY', 'Paraguay', 595),
(172, 'PE', 'Peru', 51),
(173, 'PH', 'Philippines', 63),
(174, 'PN', 'Pitcairn Island', 0),
(175, 'PL', 'Poland', 48),
(176, 'PT', 'Portugal', 351),
(177, 'PR', 'Puerto Rico', 1787),
(178, 'QA', 'Qatar', 974),
(179, 'RE', 'Reunion', 262),
(180, 'RO', 'Romania', 40),
(181, 'RU', 'Russia', 70),
(182, 'RW', 'Rwanda', 250),
(183, 'SH', 'Saint Helena', 290),
(184, 'KN', 'Saint Kitts And Nevis', 1869),
(185, 'LC', 'Saint Lucia', 1758),
(186, 'PM', 'Saint Pierre and Miquelon', 508),
(187, 'VC', 'Saint Vincent And The Grenadines', 1784),
(188, 'WS', 'Samoa', 684),
(189, 'SM', 'San Marino', 378),
(190, 'ST', 'Sao Tome and Principe', 239),
(191, 'SA', 'Saudi Arabia', 966),
(192, 'SN', 'Senegal', 221),
(193, 'RS', 'Serbia', 381),
(194, 'SC', 'Seychelles', 248),
(195, 'SL', 'Sierra Leone', 232),
(196, 'SG', 'Singapore', 65),
(197, 'SK', 'Slovakia', 421),
(198, 'SI', 'Slovenia', 386),
(199, 'XG', 'Smaller Territories of the UK', 44),
(200, 'SB', 'Solomon Islands', 677),
(201, 'SO', 'Somalia', 252),
(202, 'ZA', 'South Africa', 27),
(203, 'GS', 'South Georgia', 0),
(204, 'SS', 'South Sudan', 211),
(205, 'ES', 'Spain', 34),
(206, 'LK', 'Sri Lanka', 94),
(207, 'SD', 'Sudan', 249),
(208, 'SR', 'Suriname', 597),
(209, 'SJ', 'Svalbard And Jan Mayen Islands', 47),
(210, 'SZ', 'Swaziland', 268),
(211, 'SE', 'Sweden', 46),
(212, 'CH', 'Switzerland', 41),
(213, 'SY', 'Syria', 963),
(214, 'TW', 'Taiwan', 886),
(215, 'TJ', 'Tajikistan', 992),
(216, 'TZ', 'Tanzania', 255),
(217, 'TH', 'Thailand', 66),
(218, 'TG', 'Togo', 228),
(219, 'TK', 'Tokelau', 690),
(220, 'TO', 'Tonga', 676),
(221, 'TT', 'Trinidad And Tobago', 1868),
(222, 'TN', 'Tunisia', 216),
(223, 'TR', 'Turkey', 90),
(224, 'TM', 'Turkmenistan', 7370),
(225, 'TC', 'Turks And Caicos Islands', 1649),
(226, 'TV', 'Tuvalu', 688),
(227, 'UG', 'Uganda', 256),
(228, 'UA', 'Ukraine', 380),
(229, 'AE', 'United Arab Emirates', 971),
(230, 'GB', 'United Kingdom', 44),
(231, 'US', 'United States', 1),
(232, 'UM', 'United States Minor Outlying Islands', 1),
(233, 'UY', 'Uruguay', 598),
(234, 'UZ', 'Uzbekistan', 998),
(235, 'VU', 'Vanuatu', 678),
(236, 'VA', 'Vatican City State (Holy See)', 39),
(237, 'VE', 'Venezuela', 58),
(238, 'VN', 'Vietnam', 84),
(239, 'VG', 'Virgin Islands (British)', 1284),
(240, 'VI', 'Virgin Islands (US)', 1340),
(241, 'WF', 'Wallis And Futuna Islands', 681),
(242, 'EH', 'Western Sahara', 212),
(243, 'YE', 'Yemen', 967),
(244, 'YU', 'Yugoslavia', 38),
(245, 'ZM', 'Zambia', 260),
(246, 'ZW', 'Zimbabwe', 263);

-- --------------------------------------------------------

--
-- Table structure for table `customer_tbl`
--

CREATE TABLE `customer_tbl` (
  `id` int(11) NOT NULL,
  `customerid` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_closing`
--

CREATE TABLE `daily_closing` (
  `id` int(11) NOT NULL,
  `last_day_closing` float NOT NULL,
  `cash_in` float NOT NULL,
  `cash_out` float NOT NULL,
  `date` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `amount` float NOT NULL,
  `adjustment` float DEFAULT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `department_tbl`
--

CREATE TABLE `department_tbl` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `department_description` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designation_tbl`
--

CREATE TABLE `designation_tbl` (
  `designation_id` int(11) NOT NULL,
  `designation_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `designation_description` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_tbl`
--

CREATE TABLE `employee_tbl` (
  `employee_id` int(11) NOT NULL,
  `em_first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_designation` int(11) NOT NULL,
  `em_department` int(11) NOT NULL,
  `em_phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `em_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_salary` float DEFAULT NULL,
  `em_country` int(11) NOT NULL,
  `em_city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_zip` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `em_image` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details`
--

CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_details_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `product_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `discount` decimal(10,0) DEFAULT NULL,
  `total_price` decimal(10,0) DEFAULT NULL,
  `discount_amount` decimal(10,0) DEFAULT NULL,
  `tax` decimal(10,0) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_tbl`
--

CREATE TABLE `invoice_tbl` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `customer_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `invoice` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_different` int(11) DEFAULT NULL,
  `is_inhouse` int(2) NOT NULL COMMENT '1=In and 2 = out',
  `shipping_method` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `payment_method` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `bank_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `invoice_discount` float NOT NULL,
  `total_discount` float NOT NULL,
  `total_amount` float NOT NULL,
  `paid_amount` float NOT NULL,
  `due_amount` float NOT NULL,
  `total_tax` float NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 = pending, 1 = Processing and 2 = Delivered',
  `created_by` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `phrase` varchar(100) NOT NULL,
  `english` varchar(255) NOT NULL,
  `yoruba` text,
  `odia` text,
  `xx` text,
  `spanish` text,
  `arabic` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `phrase`, `english`, `yoruba`, `odia`, `xx`, `spanish`, `arabic`) VALUES
(2, 'login', 'Login', NULL, NULL, NULL, NULL, NULL),
(3, 'email', 'Email Address', NULL, NULL, NULL, NULL, NULL),
(4, 'password', 'Password', NULL, NULL, NULL, NULL, NULL),
(5, 'reset', 'Reset', NULL, NULL, NULL, NULL, NULL),
(6, 'dashboard', 'Dashboard', NULL, NULL, NULL, NULL, NULL),
(7, 'home', 'Home', NULL, NULL, NULL, NULL, NULL),
(8, 'profile', 'Profile', NULL, NULL, NULL, NULL, NULL),
(9, 'profile_setting', 'Profile Setting', NULL, NULL, NULL, NULL, NULL),
(10, 'firstname', 'First Name', NULL, NULL, NULL, NULL, NULL),
(11, 'lastname', 'Last Name', NULL, NULL, NULL, NULL, NULL),
(12, 'about', 'About', NULL, NULL, NULL, NULL, 'ar_ar'),
(13, 'preview', 'Preview', NULL, NULL, NULL, NULL, NULL),
(14, 'image', 'Image', NULL, NULL, NULL, NULL, NULL),
(15, 'save', 'Save', NULL, NULL, NULL, NULL, NULL),
(16, 'upload_successfully', 'Upload Successfully!', NULL, NULL, NULL, NULL, NULL),
(17, 'user_added_successfully', 'User Added Successfully!', NULL, NULL, NULL, NULL, NULL),
(18, 'please_try_again', 'Please Try Again...', NULL, NULL, NULL, NULL, NULL),
(19, 'inbox_message', 'Inbox Messages', NULL, NULL, NULL, NULL, NULL),
(20, 'sent_message', 'Sent Message', NULL, NULL, NULL, NULL, NULL),
(21, 'message_details', 'Message Details', NULL, NULL, NULL, NULL, NULL),
(22, 'new_message', 'New Message', NULL, NULL, NULL, NULL, NULL),
(23, 'receiver_name', 'Receiver Name', NULL, NULL, NULL, NULL, NULL),
(24, 'sender_name', 'Sender Name', NULL, NULL, NULL, NULL, NULL),
(25, 'subject', 'Subject', NULL, NULL, NULL, NULL, NULL),
(26, 'message', 'Message', NULL, NULL, NULL, NULL, NULL),
(27, 'message_sent', 'Message Sent!', NULL, NULL, NULL, NULL, NULL),
(28, 'ip_address', 'IP Address', NULL, NULL, NULL, NULL, NULL),
(29, 'last_login', 'Last Login', NULL, NULL, NULL, NULL, NULL),
(30, 'last_logout', 'Last Logout', NULL, NULL, NULL, NULL, NULL),
(31, 'status', 'Status', NULL, NULL, NULL, NULL, NULL),
(32, 'delete_successfully', 'Delete Successfully!', NULL, NULL, NULL, NULL, NULL),
(33, 'send', 'Send', NULL, NULL, NULL, NULL, NULL),
(34, 'date', 'Date', NULL, NULL, NULL, NULL, NULL),
(35, 'action', 'Action', NULL, NULL, NULL, NULL, NULL),
(36, 'sl_no', 'SL No.', NULL, NULL, NULL, NULL, NULL),
(37, 'are_you_sure', 'Are you sure?', NULL, NULL, NULL, NULL, NULL),
(38, 'application_setting', 'Application Setting', NULL, NULL, NULL, NULL, NULL),
(39, 'application_title', 'Application Title', NULL, NULL, NULL, NULL, NULL),
(40, 'address', 'Address', NULL, NULL, NULL, NULL, NULL),
(41, 'phone', 'Phone', NULL, NULL, NULL, NULL, NULL),
(42, 'favicon', 'Favicon', NULL, NULL, NULL, NULL, NULL),
(43, 'logo', 'Logo', NULL, NULL, NULL, NULL, NULL),
(44, 'language', 'Language', NULL, NULL, NULL, NULL, NULL),
(45, 'left_to_right', 'Left To Right', NULL, NULL, NULL, NULL, NULL),
(46, 'right_to_left', 'Right To Left', NULL, NULL, NULL, NULL, NULL),
(47, 'footer_text', 'Footer Text', NULL, NULL, NULL, NULL, NULL),
(48, 'site_align', 'Application Alignment', NULL, NULL, NULL, NULL, NULL),
(49, 'welcome_back', 'Welcome Back!', NULL, NULL, NULL, NULL, NULL),
(50, 'please_contact_with_admin', 'Please Contact With Admin', NULL, NULL, NULL, NULL, NULL),
(51, 'incorrect_email_or_password', 'Incorrect Email/Password', NULL, NULL, NULL, NULL, NULL),
(52, 'select_option', 'Select Option', NULL, NULL, NULL, NULL, NULL),
(53, 'ftp_setting', 'Data Synchronize [FTP Setting]', NULL, NULL, NULL, NULL, NULL),
(54, 'hostname', 'Host Name', NULL, NULL, NULL, NULL, NULL),
(55, 'username', 'User Name', NULL, NULL, NULL, NULL, NULL),
(56, 'ftp_port', 'FTP Port', NULL, NULL, NULL, NULL, NULL),
(57, 'ftp_debug', 'FTP Debug', NULL, NULL, NULL, NULL, NULL),
(58, 'project_root', 'Project Root', NULL, NULL, NULL, NULL, NULL),
(59, 'update_successfully', 'Update Successfully', NULL, NULL, NULL, NULL, NULL),
(60, 'save_successfully', 'Saved Successfully!', NULL, NULL, NULL, NULL, NULL),
(61, 'delete_successfully', 'Delete Successfully!', NULL, NULL, NULL, NULL, NULL),
(62, 'internet_connection', 'Internet Connection', NULL, NULL, NULL, NULL, NULL),
(63, 'ok', 'Ok', NULL, NULL, NULL, NULL, NULL),
(64, 'not_available', 'Not Available', NULL, NULL, NULL, NULL, NULL),
(65, 'available', 'Available', NULL, NULL, NULL, NULL, NULL),
(66, 'outgoing_file', 'Outgoing File', NULL, NULL, NULL, NULL, NULL),
(67, 'incoming_file', 'Incoming File', NULL, NULL, NULL, NULL, NULL),
(68, 'data_synchronize', 'Data Synchronize', NULL, NULL, NULL, NULL, NULL),
(69, 'unable_to_upload_file_please_check_configuration', 'Unable to upload file! please check configuration', NULL, NULL, NULL, NULL, NULL),
(70, 'please_configure_synchronizer_settings', 'Please configure synchronizer settings', NULL, NULL, NULL, NULL, NULL),
(71, 'download_successfully', 'Download Successfully', NULL, NULL, NULL, NULL, NULL),
(72, 'unable_to_download_file_please_check_configuration', 'Unable to download file! please check configuration', NULL, NULL, NULL, NULL, NULL),
(73, 'data_import_first', 'Data Import First', NULL, NULL, NULL, NULL, NULL),
(74, 'data_import_successfully', 'Data Import Successfully!', NULL, NULL, NULL, NULL, NULL),
(75, 'unable_to_import_data_please_check_config_or_sql_file', 'Unable to import data! please check configuration / SQL file.', NULL, NULL, NULL, NULL, NULL),
(76, 'download_data_from_server', 'Download Data from Server', NULL, NULL, NULL, NULL, NULL),
(77, 'data_import_to_database', 'Data Import To Database', NULL, NULL, NULL, NULL, NULL),
(79, 'data_upload_to_server', 'Data Upload to Server', NULL, NULL, NULL, NULL, NULL),
(80, 'please_wait', 'Please Wait...', NULL, NULL, NULL, NULL, NULL),
(81, 'ooops_something_went_wrong', ' Ooops something went wrong...', NULL, NULL, NULL, NULL, NULL),
(82, 'module_permission_list', 'Module Permission List', NULL, NULL, NULL, NULL, NULL),
(83, 'user_permission', 'User Permission', NULL, NULL, NULL, NULL, NULL),
(84, 'add_module_permission', 'Add Module Permission', NULL, NULL, NULL, NULL, NULL),
(85, 'module_permission_added_successfully', 'Module Permission Added Successfully!', NULL, NULL, NULL, NULL, NULL),
(86, 'update_module_permission', 'Update Module Permission', NULL, NULL, NULL, NULL, NULL),
(87, 'download', 'Download', NULL, NULL, NULL, NULL, NULL),
(88, 'module_name', 'Module Name', NULL, NULL, NULL, NULL, NULL),
(89, 'create', 'Create', NULL, NULL, NULL, NULL, NULL),
(90, 'read', 'Read', NULL, NULL, NULL, NULL, NULL),
(91, 'update', 'Update', NULL, NULL, NULL, NULL, NULL),
(92, 'delete', 'Delete', NULL, NULL, NULL, NULL, NULL),
(93, 'module_list', 'Module List', NULL, NULL, NULL, NULL, NULL),
(94, 'add_module', 'Add Module', NULL, NULL, NULL, NULL, NULL),
(95, 'directory', 'Module Direcotory', NULL, NULL, NULL, NULL, NULL),
(96, 'description', 'Description', NULL, NULL, NULL, NULL, NULL),
(97, 'image_upload_successfully', 'Image Upload Successfully!', NULL, NULL, NULL, NULL, NULL),
(98, 'module_added_successfully', 'Module Added Successfully', NULL, NULL, NULL, NULL, NULL),
(99, 'inactive', 'Inactive', NULL, NULL, NULL, NULL, NULL),
(100, 'active', 'Active', NULL, NULL, NULL, NULL, NULL),
(101, 'user_list', 'User List', NULL, NULL, NULL, NULL, NULL),
(102, 'see_all_message', 'See All Messages', NULL, NULL, NULL, NULL, NULL),
(103, 'setting', 'Setting', NULL, NULL, NULL, NULL, NULL),
(104, 'logout', 'Logout', NULL, NULL, NULL, NULL, NULL),
(105, 'admin', 'Admin', NULL, NULL, NULL, NULL, NULL),
(106, 'add_user', 'Add User', NULL, NULL, NULL, NULL, NULL),
(107, 'user', 'User', NULL, NULL, NULL, NULL, NULL),
(108, 'module', 'Module', NULL, NULL, NULL, NULL, NULL),
(109, 'new', 'New', NULL, NULL, NULL, NULL, NULL),
(110, 'inbox', 'Inbox', NULL, NULL, NULL, NULL, NULL),
(111, 'sent', 'Sent', NULL, NULL, NULL, NULL, NULL),
(112, 'synchronize', 'Synchronize', NULL, NULL, NULL, NULL, NULL),
(113, 'data_synchronizer', 'Data Synchronizer', NULL, NULL, NULL, NULL, NULL),
(114, 'module_permission', 'Module Permission', NULL, NULL, NULL, NULL, NULL),
(115, 'backup_now', 'Backup Now!', NULL, NULL, NULL, NULL, NULL),
(116, 'restore_now', 'Restore Now!', NULL, NULL, NULL, NULL, NULL),
(117, 'backup_and_restore', 'Backup and Restore', NULL, NULL, NULL, NULL, NULL),
(118, 'captcha', 'Captcha Word', NULL, NULL, NULL, NULL, NULL),
(119, 'database_backup', 'Database Backup', NULL, NULL, NULL, NULL, NULL),
(120, 'restore_successfully', 'Restore Successfully', NULL, NULL, NULL, NULL, NULL),
(121, 'backup_successfully', 'Backup Successfully', NULL, NULL, NULL, NULL, NULL),
(122, 'filename', 'File Name', NULL, NULL, NULL, NULL, NULL),
(123, 'file_information', 'File Information', NULL, NULL, NULL, NULL, NULL),
(124, 'size', 'size', NULL, NULL, NULL, NULL, NULL),
(125, 'backup_date', 'Backup Date', NULL, NULL, NULL, NULL, NULL),
(126, 'overwrite', 'Overwrite', NULL, NULL, NULL, NULL, NULL),
(127, 'invalid_file', 'Invalid File!', NULL, NULL, NULL, NULL, NULL),
(128, 'invalid_module', 'Invalid Module', NULL, NULL, NULL, NULL, NULL),
(129, 'remove_successfully', 'Remove Successfully!', NULL, NULL, NULL, NULL, NULL),
(130, 'install', 'Install', NULL, NULL, NULL, NULL, NULL),
(131, 'uninstall', 'Uninstall', NULL, NULL, NULL, NULL, NULL),
(132, 'tables_are_not_available_in_database', 'Tables are not available in database.sql', NULL, NULL, NULL, NULL, NULL),
(133, 'no_tables_are_registered_in_config', 'No tables are registerd in config.php', NULL, NULL, NULL, NULL, NULL),
(134, 'enquiry', 'Enquiry', NULL, NULL, NULL, NULL, NULL),
(135, 'read_unread', 'Read/Unread', NULL, NULL, NULL, NULL, NULL),
(136, 'enquiry_information', 'Enquiry Information', NULL, NULL, NULL, NULL, NULL),
(137, 'user_agent', 'User Agent', NULL, NULL, NULL, NULL, NULL),
(138, 'checked_by', 'Checked By', NULL, NULL, NULL, NULL, NULL),
(139, 'new_enquiry', 'New Enquiry', NULL, NULL, NULL, NULL, NULL),
(140, 'hrm', 'HRM', NULL, NULL, NULL, NULL, NULL),
(141, 'test_module', 'Test Module', NULL, NULL, NULL, NULL, NULL),
(142, 'test_user', 'Test User', NULL, NULL, NULL, NULL, NULL),
(143, 'testmodule', 'Testmodule', NULL, NULL, NULL, NULL, NULL),
(692, 'inventory', 'Inventory', NULL, NULL, NULL, NULL, NULL),
(693, 'product_inventory', 'Product inventory', NULL, NULL, NULL, NULL, NULL),
(694, 'account', 'Account', NULL, NULL, NULL, NULL, ''),
(695, 'test', 'Test', NULL, NULL, NULL, NULL, NULL),
(696, 'customer', 'Customer', NULL, NULL, NULL, NULL, NULL),
(697, 'expense', 'Expense', NULL, NULL, NULL, NULL, NULL),
(698, 'hrm', 'HRM', NULL, NULL, NULL, NULL, NULL),
(699, 'invoice', 'Invoice', NULL, NULL, NULL, NULL, NULL),
(700, 'item', 'Item', NULL, NULL, NULL, NULL, NULL),
(701, 'purchase', 'Purchase', NULL, NULL, NULL, NULL, NULL),
(702, 'report', 'Report', NULL, NULL, NULL, NULL, NULL),
(703, 'stock', 'Stock', NULL, NULL, NULL, NULL, NULL),
(704, 'Supplier', 'Supplier', NULL, NULL, NULL, NULL, NULL),
(705, 'customer_list', 'Customer List', NULL, NULL, NULL, NULL, NULL),
(706, 'customer_name', 'Customer Name', NULL, NULL, NULL, NULL, NULL),
(707, 'mobile', 'Mobile', NULL, NULL, NULL, NULL, NULL),
(708, 'address', 'Address', NULL, NULL, NULL, NULL, NULL),
(709, 'add_new', 'Add New', NULL, NULL, NULL, NULL, NULL),
(710, 'ad', 'Add', NULL, NULL, NULL, NULL, NULL),
(711, 'customer_update', 'Customer Update', NULL, NULL, NULL, NULL, NULL),
(712, 'supplier_list', 'Supplier List', NULL, NULL, NULL, NULL, NULL),
(713, 'supplier_name', 'Supplier Name', NULL, NULL, NULL, NULL, NULL),
(714, 'supplier_add', 'Supplier Add', NULL, NULL, NULL, NULL, NULL),
(715, 'supplier_edit', 'Supplier Edit', NULL, NULL, NULL, NULL, NULL),
(716, 'previous_balance', 'Previous Balance', NULL, NULL, NULL, NULL, NULL),
(717, 'inventory', 'Inventory', NULL, NULL, NULL, NULL, NULL),
(718, 'product_inventory', 'Product inventory', NULL, NULL, NULL, NULL, NULL),
(719, 'account', 'Account', NULL, NULL, NULL, NULL, NULL),
(720, 'test', 'Test', NULL, NULL, NULL, NULL, NULL),
(721, 'customer', 'Customer', NULL, NULL, NULL, NULL, NULL),
(722, 'expense', 'Expense', NULL, NULL, NULL, NULL, NULL),
(723, 'hrm', 'HRM', NULL, NULL, NULL, NULL, NULL),
(724, 'invoice', 'Invoice', NULL, NULL, NULL, NULL, NULL),
(725, 'item', 'Item', NULL, NULL, NULL, NULL, NULL),
(726, 'purchase', 'Purchase', NULL, NULL, NULL, NULL, NULL),
(727, 'report', 'Report', NULL, NULL, NULL, NULL, NULL),
(728, 'stock', 'Stock', NULL, NULL, NULL, NULL, NULL),
(729, 'Supplier', 'Supplier', NULL, NULL, NULL, NULL, NULL),
(730, 'department', 'Department', NULL, NULL, NULL, NULL, NULL),
(731, 'designation', 'Designation', NULL, NULL, NULL, NULL, NULL),
(732, 'add_employee', 'Add Employee', NULL, NULL, NULL, NULL, NULL),
(733, 'manage_employee', 'Manage Employee', NULL, NULL, NULL, NULL, NULL),
(734, 'department_name', 'Department name', NULL, NULL, NULL, NULL, NULL),
(735, 'department_description', 'Department description', NULL, NULL, NULL, NULL, NULL),
(736, 'designation', 'Designation', NULL, NULL, NULL, NULL, NULL),
(737, 'employee', 'Employee', NULL, NULL, NULL, NULL, NULL),
(738, 'name', 'Name', NULL, NULL, NULL, NULL, NULL),
(739, 'country', 'Country', NULL, NULL, NULL, NULL, NULL),
(740, 'city', 'City', NULL, NULL, NULL, NULL, NULL),
(741, 'zip', 'Zip', NULL, NULL, NULL, NULL, NULL),
(742, 'salary', 'Salary', NULL, NULL, NULL, NULL, NULL),
(743, 'address', 'Address', NULL, NULL, NULL, NULL, NULL),
(744, 'salary', 'Salary', NULL, NULL, NULL, NULL, NULL),
(745, 'amount', 'Amount', NULL, NULL, NULL, NULL, NULL),
(746, 'attendance', 'Attendance', NULL, NULL, NULL, NULL, NULL),
(747, 'in_time', 'In Time', NULL, NULL, NULL, NULL, NULL),
(748, 'out_time', 'Out Time', NULL, NULL, NULL, NULL, NULL),
(749, 'date', 'Date', NULL, NULL, NULL, NULL, NULL),
(750, 'stay_time', 'Stay Time', NULL, NULL, NULL, NULL, NULL),
(751, 'attendance_report', 'Attendance Report', NULL, NULL, NULL, NULL, NULL),
(752, 'submit', 'Submit', NULL, NULL, NULL, NULL, NULL),
(753, 'item', 'Item', NULL, NULL, NULL, NULL, NULL),
(754, 'purchase', 'Purchase', NULL, NULL, NULL, NULL, NULL),
(755, 'report', 'Report', NULL, NULL, NULL, NULL, NULL),
(756, 'stock', 'Stock', NULL, NULL, NULL, NULL, NULL),
(757, 'Supplier', 'Supplier', NULL, NULL, NULL, NULL, NULL),
(758, 'add_item', 'Add Item', NULL, NULL, NULL, NULL, NULL),
(759, 'item_list', 'Item List', NULL, NULL, NULL, NULL, NULL),
(760, 'unit', 'Unit', NULL, NULL, NULL, NULL, NULL),
(761, 'category', 'Category', NULL, NULL, NULL, NULL, NULL),
(762, 'new_purchase', 'New Purchase', NULL, NULL, NULL, NULL, NULL),
(763, 'purchase_list', 'Purchase List', NULL, NULL, NULL, NULL, NULL),
(764, 'add_invoice', 'Add Invoice', NULL, NULL, NULL, NULL, NULL),
(765, 'invoice_list', 'Invoice List', NULL, NULL, NULL, NULL, NULL),
(766, 'invoice_id', 'Invoice ID', NULL, NULL, NULL, NULL, NULL),
(767, 'paid', 'Paid', NULL, NULL, NULL, NULL, NULL),
(769, 'total_amount', 'Total Aamount', NULL, NULL, NULL, NULL, NULL),
(770, 'record_not_found', 'Record not found', NULL, NULL, NULL, NULL, NULL),
(771, 'add_menu          ', 'Add Menu', NULL, NULL, NULL, NULL, NULL),
(772, 'menu              ', 'Role Permission', NULL, NULL, NULL, NULL, NULL),
(773, 'menu_list', 'Menu List', NULL, NULL, NULL, NULL, NULL),
(774, 'add_role', 'Add Role', NULL, NULL, NULL, NULL, NULL),
(775, 'role_list', 'Role List', NULL, NULL, NULL, NULL, NULL),
(776, 'role_assign', 'User Assign Role', NULL, NULL, NULL, NULL, NULL),
(777, 'assigned_userrole_list', 'Assigned List', NULL, NULL, NULL, NULL, NULL),
(778, 'accounts', 'Accounts', NULL, NULL, NULL, NULL, NULL),
(779, 'payment_or_receive', 'Payment or Receive', NULL, NULL, NULL, NULL, NULL),
(780, 'manage_transaction', 'Manage Transaction', NULL, NULL, NULL, NULL, NULL),
(781, 'payment_received_transaction', 'Payment received transaction', NULL, NULL, NULL, NULL, NULL),
(782, 'choose_transaction', 'Choose transaction', NULL, NULL, NULL, NULL, NULL),
(783, 'payment', 'Payment', NULL, NULL, NULL, NULL, NULL),
(784, 'receive', 'Receive', NULL, NULL, NULL, NULL, NULL),
(785, 'transaction_category', 'Transaction Category', NULL, NULL, NULL, NULL, NULL),
(786, 'transaction_mode', 'Transaction mode', NULL, NULL, NULL, NULL, NULL),
(787, 'bank', 'Bank', NULL, NULL, NULL, NULL, NULL),
(788, 'add_bank', 'Add Bank', NULL, NULL, NULL, NULL, NULL),
(789, 'bank_list', 'Bank List', NULL, NULL, NULL, NULL, NULL),
(790, 'bank_name', 'Bank Name', NULL, NULL, NULL, NULL, NULL),
(791, 'account_no', 'Account No ', NULL, NULL, NULL, NULL, NULL),
(792, 'branch_name', 'Branch Name', NULL, NULL, NULL, NULL, NULL),
(793, 'salary_generat_list', 'Salary generate list', NULL, NULL, NULL, NULL, NULL),
(794, 'salary_setup', 'Salary Setup', NULL, NULL, NULL, NULL, NULL),
(795, 'purchase_date', 'Purchase Date', NULL, NULL, NULL, NULL, NULL),
(796, 'chalan_no', 'Chalan No', NULL, NULL, NULL, NULL, NULL),
(797, 'details', 'Details', NULL, NULL, NULL, NULL, NULL),
(798, 'payment_type', 'Payment Type', NULL, NULL, NULL, NULL, NULL),
(799, 'cash_payment', 'Cash Payment', NULL, NULL, NULL, NULL, NULL),
(800, 'bank_payment', 'Bank Payment', NULL, NULL, NULL, NULL, NULL),
(801, 'due_payment', 'Due Payment', NULL, NULL, NULL, NULL, NULL),
(802, 'item_name', 'Item Name', NULL, NULL, NULL, NULL, NULL),
(803, 'unit_qty', 'Unit Qty', NULL, NULL, NULL, NULL, NULL),
(804, 'box_qty', 'Box Qty', NULL, NULL, NULL, NULL, NULL),
(805, 'rate', 'Rate', NULL, NULL, NULL, NULL, NULL),
(806, 'total', 'Total ', NULL, NULL, NULL, NULL, NULL),
(807, 'discount', 'Discount', NULL, NULL, NULL, NULL, NULL),
(808, 'grand_total', 'Grand Total', NULL, NULL, NULL, NULL, NULL),
(809, 'edit_purchase', 'Edit Purchase', NULL, NULL, NULL, NULL, NULL),
(810, 'filter', 'Filter', NULL, NULL, NULL, NULL, NULL),
(811, 'from_date', 'From Date', NULL, NULL, NULL, NULL, NULL),
(812, 'to_date', 'To Date', NULL, NULL, NULL, NULL, NULL),
(813, 'find', 'Find', NULL, NULL, NULL, NULL, NULL),
(814, 'purchase_id', 'Purchase Id', NULL, NULL, NULL, NULL, NULL),
(815, 'sl', 'Sl', NULL, NULL, NULL, NULL, NULL),
(816, 'qty', 'Qty', NULL, NULL, NULL, NULL, NULL),
(817, 'price', 'Price', NULL, NULL, NULL, NULL, NULL),
(818, 'purchase_price', 'Purchase Price', NULL, NULL, NULL, NULL, NULL),
(819, 'sale_price', 'Sale Price', NULL, NULL, NULL, NULL, NULL),
(820, 'cartoon_qty', 'Cartoon Qty', NULL, NULL, NULL, NULL, NULL),
(821, 'item_model', 'Item Model', NULL, NULL, NULL, NULL, NULL),
(822, 'item_code', 'Item Code', NULL, NULL, NULL, NULL, NULL),
(823, 'category_name', 'Category  Name', NULL, NULL, NULL, NULL, NULL),
(824, 'parent_category', 'Parent Category', NULL, NULL, NULL, NULL, NULL),
(825, 'category_list', 'Category List', NULL, NULL, NULL, NULL, NULL),
(826, 'add_category', 'Add Category', NULL, NULL, NULL, NULL, NULL),
(827, 'unit_list', 'Unit List', NULL, NULL, NULL, NULL, NULL),
(828, 'add_unit', 'Add Unit', NULL, NULL, NULL, NULL, NULL),
(829, 'unit_name', 'Unit Name', NULL, NULL, NULL, NULL, NULL),
(830, 'total_price', 'Total Price', NULL, NULL, NULL, NULL, NULL),
(831, 'payment_amount', 'Payment Amount', NULL, NULL, NULL, NULL, NULL),
(832, 'select_name', 'Select Name', NULL, NULL, NULL, NULL, NULL),
(833, 'select_one', 'Select One', NULL, NULL, NULL, NULL, NULL),
(834, 'receipt_amount', 'Receipt Amount', NULL, NULL, NULL, NULL, NULL),
(835, 'isreceipt', 'Received/Payment', NULL, NULL, NULL, NULL, NULL),
(836, 'supplier_ledger', 'Supplier Ledger', NULL, NULL, NULL, NULL, NULL),
(837, 'total_creadit', 'Total Creadit', NULL, NULL, NULL, NULL, NULL),
(838, 'total_debit', 'Total Debit', NULL, NULL, NULL, NULL, NULL),
(839, 'balance', 'Balance', NULL, NULL, NULL, NULL, NULL),
(840, 'stock_report_supplier_wise', 'Stock report (supplier wise)', NULL, NULL, NULL, NULL, NULL),
(841, 'stock_report', 'Stock report', NULL, NULL, NULL, NULL, NULL),
(842, 'purchase_report', 'Purchase Report', NULL, NULL, NULL, NULL, NULL),
(843, 'stock_report_product_wise', 'Stock report product wise', NULL, NULL, NULL, NULL, NULL),
(844, 'list', 'list', NULL, NULL, NULL, NULL, NULL),
(845, 'sl', 'LS', NULL, NULL, NULL, NULL, NULL),
(846, 'generate', 'Generate', NULL, NULL, NULL, NULL, NULL),
(847, 'salary', 'Salary', NULL, NULL, NULL, NULL, NULL),
(848, 'month', 'Month', NULL, NULL, NULL, NULL, NULL),
(849, 'by', 'By', NULL, NULL, NULL, NULL, NULL),
(850, 'paid', 'Paid', NULL, NULL, NULL, NULL, NULL),
(851, 'pay_now', 'Pay now', NULL, NULL, NULL, NULL, NULL),
(852, 'receipt', 'Receipt', NULL, NULL, NULL, NULL, NULL),
(853, 'add', 'Add', NULL, NULL, NULL, NULL, NULL),
(854, 'department', 'Department', NULL, NULL, NULL, NULL, NULL),
(855, 'close', 'Close', NULL, NULL, NULL, NULL, NULL),
(856, 'note', 'Note', NULL, NULL, NULL, NULL, NULL),
(857, 'account_adjustment', 'Account Adjustment', NULL, NULL, NULL, NULL, NULL),
(858, 'add', 'Add', NULL, NULL, NULL, NULL, NULL),
(859, 'payment_date', 'Payment Date', NULL, NULL, NULL, NULL, NULL),
(860, 'payment_type', 'Payment Type', NULL, NULL, NULL, NULL, NULL),
(861, 'bank_ledger', 'Bank Ledger', NULL, NULL, NULL, NULL, NULL),
(862, 'ledger', 'Ledger', NULL, NULL, NULL, NULL, NULL),
(863, 'transactionid', 'Transaction ID', NULL, NULL, NULL, NULL, NULL),
(864, 'paid_amount', 'Paid Amount', NULL, NULL, NULL, NULL, NULL),
(865, 'invoice_discount', 'Invoice Discount', NULL, NULL, NULL, NULL, NULL),
(866, 'total_discount', 'Total Discount', NULL, NULL, NULL, NULL, NULL),
(867, 'debit', 'Debit', NULL, NULL, NULL, NULL, NULL),
(868, 'credit', 'Credit', NULL, NULL, NULL, NULL, NULL),
(869, 'due', 'Due', NULL, NULL, NULL, NULL, NULL),
(870, 'customer_ledger', 'Customer Ledger', NULL, NULL, NULL, NULL, NULL),
(871, 'sales_report', 'Sales report', NULL, NULL, NULL, NULL, NULL),
(872, 'sales', 'Sales', NULL, NULL, NULL, NULL, NULL),
(873, 'invoice_details', 'Invoice Details', NULL, NULL, NULL, NULL, NULL),
(874, 'bank_adjustment', 'Bank Adjustment', NULL, NULL, NULL, NULL, NULL),
(875, 'currency_name', 'Currency Name', NULL, NULL, NULL, NULL, NULL),
(876, 'currency_icon', 'Currency Icon', NULL, NULL, NULL, NULL, NULL),
(877, 'currency_rate', 'Currency Rate', NULL, NULL, NULL, NULL, NULL),
(878, 'currency_position', 'Position', NULL, NULL, NULL, NULL, NULL),
(879, 'currency_add', 'Currency Add', NULL, NULL, NULL, NULL, NULL),
(880, 'currency_edit', 'Currency Edit', NULL, NULL, NULL, NULL, NULL),
(881, 'currency_list', 'Currency List', NULL, NULL, NULL, NULL, NULL),
(882, 'currency', 'Currency', NULL, NULL, NULL, NULL, NULL),
(883, 'cash', 'Cash', NULL, NULL, NULL, NULL, NULL),
(884, 'book', 'Book', NULL, NULL, NULL, NULL, NULL),
(885, 'cash_book', 'Cash book', NULL, NULL, NULL, NULL, NULL),
(886, 'cash_payment', 'Cash Payment', NULL, NULL, NULL, NULL, NULL),
(887, 'bank_payment', 'Bank Payment', NULL, NULL, NULL, NULL, NULL),
(888, 'due_payment', 'Due Payment', NULL, NULL, NULL, NULL, NULL),
(889, 'spasni', '', NULL, NULL, NULL, NULL, NULL),
(890, 'odia', '', NULL, NULL, NULL, NULL, NULL),
(891, 'quantity', 'Quantity', NULL, NULL, NULL, NULL, NULL),
(892, 'thank_you_very_much', 'Thank you very much for choosing us. It was a pleasure to have worked with you.', NULL, NULL, NULL, NULL, NULL),
(893, 'received_amount', 'Received Amount', NULL, NULL, NULL, NULL, NULL),
(894, 'left', 'Left', NULL, NULL, NULL, NULL, NULL),
(895, 'right', 'Right', NULL, NULL, NULL, NULL, NULL),
(896, 'edit_phrase', 'Edit Phrase', NULL, NULL, NULL, NULL, NULL),
(897, 'add_new_phrase', 'Add New Phrase', NULL, NULL, NULL, NULL, NULL),
(898, 'add_language', 'Add Language', NULL, NULL, NULL, NULL, NULL),
(899, 'add_language_name', 'Add Language Name', NULL, NULL, NULL, NULL, NULL),
(900, 'add_language_name', 'Add Language Name', NULL, NULL, NULL, NULL, NULL),
(901, 'add_phrase', 'Add Phrase', NULL, NULL, NULL, NULL, NULL),
(902, 'phrase_name', 'Phrase Name', NULL, NULL, NULL, NULL, NULL),
(903, 'phrase', 'Phrase', NULL, NULL, NULL, NULL, NULL),
(904, 'add_phrase_name', 'Add Phrase Name', NULL, NULL, NULL, NULL, NULL),
(905, 'about_me', 'About Me', NULL, NULL, NULL, NULL, ''),
(906, 'choose_file', 'Choose File', NULL, NULL, NULL, NULL, NULL),
(907, 'more_info', 'More Info', NULL, NULL, NULL, NULL, NULL),
(908, 'purchase_and_sales_report', 'Purchase and sales report', NULL, NULL, NULL, NULL, NULL),
(909, 'select_employee', 'Select Employee', NULL, NULL, NULL, NULL, NULL),
(910, 'main_salary', 'Main Salary', NULL, NULL, NULL, NULL, NULL),
(911, 'earnings', 'Earnings', NULL, NULL, NULL, NULL, NULL),
(912, 'cancel', 'Cancel', NULL, NULL, NULL, NULL, NULL),
(913, 'menu_title', 'Menu Title', NULL, NULL, NULL, NULL, NULL),
(914, 'page_url', 'Page URL', NULL, NULL, NULL, NULL, NULL),
(915, 'module_name', 'Module Name', NULL, NULL, NULL, NULL, NULL),
(916, 'parent_menu', 'Parent Menu', NULL, NULL, NULL, NULL, NULL),
(917, 'role_name', 'Role Name', NULL, NULL, NULL, NULL, NULL),
(918, 'select_deselect', 'Select/Deselect', NULL, NULL, NULL, NULL, NULL),
(919, 'can_create', 'Can Create', NULL, NULL, NULL, NULL, NULL),
(920, 'all', 'All', NULL, NULL, NULL, NULL, NULL),
(921, 'can_read', 'Can Read', NULL, NULL, NULL, NULL, NULL),
(922, 'can_edit', 'Can Edit', NULL, NULL, NULL, NULL, NULL),
(923, 'can_delete', 'Can Delete', NULL, NULL, NULL, NULL, NULL),
(924, 'user_name', 'User Name', NULL, NULL, NULL, NULL, NULL),
(925, 'assigned_role', 'Assigned Role', NULL, NULL, NULL, NULL, NULL),
(926, 'menu_list', 'Menu List', NULL, NULL, NULL, NULL, NULL),
(927, 'product_name', 'Product Name', NULL, NULL, NULL, NULL, NULL),
(928, 'product_model', 'Product Model', NULL, NULL, NULL, NULL, NULL),
(929, 'category_name', 'Category Name', NULL, NULL, NULL, NULL, NULL),
(930, 'sales_price', 'Sales Price', NULL, NULL, NULL, NULL, NULL),
(931, 'purchase_price', 'Purchase Price', NULL, NULL, NULL, NULL, NULL),
(932, 'total_sales', 'Total Sales', NULL, NULL, NULL, NULL, NULL),
(933, 'total_purchase', 'Total Purchase', NULL, NULL, NULL, NULL, NULL),
(934, 'select_product', 'Select Product', NULL, NULL, NULL, NULL, NULL),
(935, 'search', 'Search', NULL, NULL, NULL, NULL, NULL),
(936, 'select_supplier', 'Select Supplier', NULL, NULL, NULL, NULL, NULL),
(937, 'add_pos_invoice', 'Add POS Invoice', NULL, NULL, NULL, NULL, NULL),
(938, 'return', 'Return', NULL, NULL, NULL, NULL, NULL),
(939, 'payment_now', 'Pay Now', NULL, NULL, NULL, NULL, NULL),
(940, 'supplier_return', 'Supplier Return', NULL, NULL, NULL, NULL, NULL),
(941, 'customer_return', 'Customer Return', NULL, NULL, NULL, NULL, NULL),
(942, 'sold_qty', 'Sold Qty', NULL, NULL, NULL, NULL, NULL),
(943, 'return_qty', 'Return Qty', NULL, NULL, NULL, NULL, NULL),
(944, 'reason', 'Reason', NULL, NULL, NULL, NULL, NULL),
(945, 'deduction', 'Deduction', NULL, NULL, NULL, NULL, NULL),
(946, 'customer_return_list', 'Customer Return List', NULL, NULL, NULL, NULL, NULL),
(947, 'purchase_qty', 'Purchase Qty', NULL, NULL, NULL, NULL, NULL),
(948, 'supplier_return_list', 'Supplier Return List', NULL, NULL, NULL, NULL, NULL),
(949, 'customer_return_details', 'Customer Return Details', NULL, NULL, NULL, NULL, NULL),
(950, 'cash_closing', 'Cash Closing', NULL, NULL, NULL, NULL, NULL),
(951, 'last_closing_balance', 'Last Closing Balance', NULL, NULL, NULL, NULL, NULL),
(952, 'closing_list', 'Closing List', NULL, NULL, NULL, NULL, NULL),
(953, 'cash_already_closed_for_this_day', 'Today\'s Cash Already Closed ', NULL, NULL, NULL, NULL, NULL),
(954, 'adjustment', 'Adjustment', NULL, NULL, NULL, NULL, NULL),
(955, 'import_csv', 'Import CSV', NULL, NULL, NULL, NULL, NULL),
(956, 'download_sample_file', 'Download sample file', NULL, NULL, NULL, NULL, NULL),
(957, 'imported_successfully', 'Imported Successfully', NULL, NULL, NULL, NULL, NULL),
(958, 'updated_successfully', 'Updated Successfully', NULL, NULL, NULL, NULL, NULL),
(959, 'payment_now', 'Payment Now', NULL, NULL, NULL, NULL, NULL),
(960, 'upload_csv_file', 'Upload csv file', NULL, NULL, NULL, NULL, NULL),
(961, 'upload', 'Upload', NULL, NULL, NULL, NULL, NULL),
(962, 'bank_book', 'Bank book', NULL, NULL, NULL, NULL, NULL),
(963, 'from_date', 'From date', NULL, NULL, NULL, NULL, NULL),
(964, 'deposit', 'Deposit', NULL, NULL, NULL, NULL, NULL),
(965, 'withdraw', 'Withdraw', NULL, NULL, NULL, NULL, NULL),
(966, 'stock_in', 'Stock In', NULL, NULL, NULL, NULL, NULL),
(968, 'stock_out', 'Stock Out', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_tbl`
--

CREATE TABLE `ledger_tbl` (
  `id` bigint(20) NOT NULL,
  `transaction_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_type` int(11) NOT NULL COMMENT '1=payment and 2= receive',
  `transaction_category` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ledger_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_no` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receipt_no` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` float NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cheque_bank_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source_bank` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_c` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_capital` int(2) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(2) NOT NULL DEFAULT '1',
  `relation_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_transaction` int(11) NOT NULL DEFAULT '0' COMMENT '0 = default and 1=from account transaction'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `location_tbl`
--

CREATE TABLE `location_tbl` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_by` int(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `picture_tbl`
--

CREATE TABLE `picture_tbl` (
  `id` int(11) NOT NULL,
  `from_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_purchase`
--

CREATE TABLE `product_purchase` (
  `purchase_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `chalan_no` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `grand_total_amount` float NOT NULL,
  `purchase_date` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `purchase_details` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `discount` decimal(6,2) NOT NULL DEFAULT '0.00',
  `payment_type` tinyint(4) NOT NULL,
  `bank_id` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_purchase_details`
--

CREATE TABLE `product_purchase_details` (
  `id` int(11) NOT NULL,
  `purchase_detail_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `purchase_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `product_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `rate` float NOT NULL,
  `total_amount` float NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_return`
--

CREATE TABLE `product_return` (
  `id` int(11) NOT NULL,
  `return_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `purchase_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `supplier_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `return_date` date NOT NULL,
  `deduction` float NOT NULL,
  `invoice_discount` float NOT NULL,
  `total_amount` float NOT NULL,
  `reason` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `paymet_type` tinyint(4) NOT NULL,
  `bank_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(2) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_tbl`
--

CREATE TABLE `product_tbl` (
  `id` int(11) NOT NULL,
  `product_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `purchase_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `old_price` decimal(10,0) DEFAULT NULL,
  `product_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `unit` int(11) DEFAULT NULL,
  `model` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `cartoon_qty` int(11) NOT NULL,
  `supplier_id` varchar(20) NOT NULL,
  `offer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `top_offer` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '1=top dealz, 2 = top accessories and 3 = discount',
  `tag` text COLLATE utf8_unicode_ci,
  `is_specification` int(2) DEFAULT NULL COMMENT '1=yes and 2= no',
  `is_exclusive` int(2) DEFAULT NULL COMMENT '1 = Yes and 2 = No',
  `exclusive_date` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_unit`
--

CREATE TABLE `product_unit` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_details`
--

CREATE TABLE `return_details` (
  `id` int(11) NOT NULL,
  `return_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `product_id` bigint(25) NOT NULL,
  `return_qty` float NOT NULL,
  `sold_pur_qty` float NOT NULL,
  `price` float NOT NULL,
  `amount` float NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_generat_tbl`
--

CREATE TABLE `salary_generat_tbl` (
  `generat_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `salary_amount` float NOT NULL,
  `salary_month` int(11) NOT NULL,
  `generate_date` date NOT NULL,
  `generate_by` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_payment_history`
--

CREATE TABLE `salary_payment_history` (
  `payment_id` int(11) NOT NULL,
  `generate_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `paid_amount` float NOT NULL,
  `payment_note` text COLLATE utf8_unicode_ci NOT NULL,
  `payment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_tbl`
--

CREATE TABLE `salary_tbl` (
  `salary_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `salary_amount` float NOT NULL,
  `salary_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_menu_item`
--

CREATE TABLE `sec_menu_item` (
  `menu_id` int(11) NOT NULL,
  `menu_title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_url` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_menu` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `createby` int(11) NOT NULL,
  `createdate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sec_menu_item`
--

INSERT INTO `sec_menu_item` (`menu_id`, `menu_title`, `page_url`, `module`, `parent_menu`, `status`, `createby`, `createdate`) VALUES
(132, 'Add Menu', 'menu/menu_setting/index', 'Menu', 0, 1, 1, '2020-01-18 00:00:00'),
(133, 'Menu List', 'menu/menu_setting/menu_list', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(134, 'Add Role', 'menu/crole/add_role', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(135, 'Role List', 'menu/crole/role_list', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(136, 'User Assign Role', 'menu/crole/role_assign', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(139, 'Assigned List', 'menu/crole/assigned_role_list', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(140, 'department', 'hrm/department/index', 'hrm', 0, 1, 1, NULL),
(141, 'designation', 'hrm/designation/index', 'hrm', 0, 1, 1, NULL),
(142, 'salary', 'hrm/salary', 'hrm', 0, 1, 1, NULL),
(143, 'salary_setup', 'hrm/salary/salary_setup', 'hrm', 142, 1, 1, NULL),
(144, 'salary_generat_list', 'hrm/salary/salary_generat_list', 'hrm', 142, 1, 1, NULL),
(145, 'attendance', 'hrm/attendance', 'hrm', 0, 1, 1, NULL),
(146, 'attendance_report', 'hrm/attendance/report', 'hrm', 145, 1, 1, NULL),
(147, 'employee', 'hrm/employee/index', 'hrm', 0, 1, 1, NULL),
(148, 'add_employee', 'hrm/employee/add_employee', 'hrm', 147, 1, 1, NULL),
(149, 'manage_employee', 'hrm/employee/manage_employee', 'hrm', 147, 1, 1, NULL),
(150, 'bank', 'bank/Bank/bank_list', 'bank', 0, 1, 1, NULL),
(151, 'bank', 'bank/Bank/bank_list', 'bank', 150, 1, 1, NULL),
(152, 'add_bank', 'bank/Bank/bank_list', 'bank', 150, 1, 1, NULL),
(153, 'bank_ledger', 'bank/Bank/bank_ledger', 'bank', 150, 1, 1, NULL),
(154, 'bank_adjustment', 'bank/Bank/bank_adjustment', 'bank', 150, 1, 1, NULL),
(155, 'item', 'item/item', 'item', 0, 1, 1, NULL),
(156, 'unit', 'item/Unit/unit_form', 'item', 155, 1, 1, NULL),
(157, 'add_unit', 'item/Unit/unit_form', 'item', 155, 1, 1, NULL),
(158, 'category', 'item/Category/category_form', 'item', 155, 1, 1, NULL),
(159, 'add_category', 'item/Category/category_form', 'item', 155, 1, 1, NULL),
(160, 'add_item', 'item/Item/item_form', 'item', 155, 1, 1, NULL),
(161, 'item_list', 'item/Item/item_list', 'item', 155, 1, 1, NULL),
(162, 'purchase', 'purchase/Purchase/', 'purchase', 0, 1, 1, NULL),
(163, 'new_purchase', 'purchase/Purchase/create_purchase', 'purchase', 162, 1, 1, NULL),
(164, 'purchase_list', 'purchase/Purchase/purchase_list', 'purchase', 162, 1, 1, NULL),
(165, 'accounts', 'accounts/index', 'accounts', 0, 1, 1, NULL),
(166, 'payment_or_receive', 'accounts/Account/payment_receive_form', 'accounts', 165, 1, 1, NULL),
(167, 'manage_transaction', 'accounts/Account/manage_transaction', 'accounts', 165, 1, 1, NULL),
(168, 'account_adjustment', 'accounts/Account/account_adjustment', 'accounts', 165, 1, 1, NULL),
(169, 'customer', 'customer/index', 'customer', 0, 1, 1, NULL),
(170, 'customer_list', 'customer/customer_info/index', 'customer', 169, 1, 1, NULL),
(171, 'customer_ledger', 'customer/customer_info/customerledger', 'customer', 169, 1, 1, NULL),
(172, 'report', 'report/index', 'report', 0, 1, 1, NULL),
(173, 'purchase_report', 'report/report/purchase_report', 'report', 172, 1, 1, NULL),
(174, 'sales_report', 'report/report/sales_report', 'report', 172, 1, 1, NULL),
(175, 'cash_book', 'report/report/cash_book', 'report', 172, 1, 1, NULL),
(176, 'stock', 'stock', 'stock', 0, 1, 1, NULL),
(177, 'stock_report', 'stock/stock/index', 'stock', 176, 1, 1, NULL),
(178, 'stock_report_supplier_wise', 'stock/stock/stock_report_supplier_wise', 'stock', 176, 1, 1, NULL),
(179, 'stock_report_product_wise', 'stock/stock/stock_report_product_wise', 'stock', 176, 1, 1, NULL),
(180, 'supplier', 'supplier', 'supplier', 0, 1, 1, NULL),
(181, 'supplier_list', 'supplier/supplierlist/index', 'supplier', 180, 1, 1, NULL),
(182, 'supplier_ledger', 'supplier/supplierlist/supplierledger', 'supplier', 180, 1, 1, NULL),
(183, 'invoice', 'invoice', 'invoice', 0, 1, 1, NULL),
(184, 'add_invoice', 'invoice/CInvoice/index', 'invoice', 183, 1, 1, NULL),
(185, 'invoice_list', 'invoice/CInvoice/invoice_list', 'invoice', 183, 1, 1, NULL),
(186, 'Role Permission', 'menu/crole/add_role', 'menu', 0, 1, 1, NULL),
(187, 'add_pos_invoice', 'add_pos', 'invoice', 183, 1, 1, NULL),
(188, 'cash_closing', 'closing_form', 'accounts', 165, 1, 1, NULL),
(189, 'closing_list', 'closing_list', 'accounts', 165, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sec_role_permission`
--

CREATE TABLE `sec_role_permission` (
  `id` bigint(20) NOT NULL,
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `can_access` tinyint(1) NOT NULL,
  `can_create` tinyint(1) NOT NULL,
  `can_edit` tinyint(1) NOT NULL,
  `can_delete` tinyint(1) NOT NULL,
  `createby` int(11) NOT NULL,
  `createdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_role_tbl`
--

CREATE TABLE `sec_role_tbl` (
  `role_id` int(11) NOT NULL,
  `role_name` text NOT NULL,
  `role_description` text NOT NULL,
  `create_by` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `role_status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sec_user_access_tbl`
--

CREATE TABLE `sec_user_access_tbl` (
  `role_acc_id` int(11) NOT NULL,
  `fk_role_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `address` text,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `logo` varchar(200) DEFAULT NULL,
  `favicon` varchar(200) DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `site_align` varchar(50) DEFAULT NULL,
  `currency` varchar(5) NOT NULL,
  `footer_text` varchar(255) DEFAULT NULL,
  `timezone` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `title`, `address`, `email`, `phone`, `logo`, `favicon`, `language`, `site_align`, `currency`, `footer_text`, `timezone`) VALUES
(2, 'Baron Infracon Ltd', 'MODEL TOWN', 'KASHIFCH100@GMAIL.COM', '03214359566', 'admin_assets/img/icons/2025-03-21/37d13a0e03c0bcddd0e72c6234f5756a.png', 'admin_assets/img/icons/2025-03-21/9467abcd0ef136048d725bf6d075fb20.png', 'english', NULL, '4', 'Â©2021', 'Asia/Colombo');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_tbl`
--

CREATE TABLE `supplier_tbl` (
  `id` int(11) NOT NULL,
  `supplier_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `status` int(11) NOT NULL,
  `created_by` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_currency`
--

CREATE TABLE `tbl_currency` (
  `currencyid` int(11) NOT NULL,
  `currencyname` varchar(100) NOT NULL,
  `curr_icon` text CHARACTER SET utf8 NOT NULL,
  `curr_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `position` int(11) NOT NULL DEFAULT '0' COMMENT '0=left,1=right'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_currency`
--

INSERT INTO `tbl_currency` (`currencyid`, `currencyname`, `curr_icon`, `curr_rate`, `position`) VALUES
(4, 'Dollar', '$', '1.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` bigint(20) NOT NULL,
  `transaction_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `date_of_transaction` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_category` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_mode` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `received_amount` float NOT NULL,
  `pay_amount` float DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `relation_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `from_invoice_data` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cheque_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cheque_issue_date` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cheque_bank_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `firstname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `about` text,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  `ip_address` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_admin` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=super_admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_tbl`
--
ALTER TABLE `attendance_tbl`
  ADD PRIMARY KEY (`attandence_id`);

--
-- Indexes for table `bank_tbl`
--
ALTER TABLE `bank_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_tbl`
--
ALTER TABLE `category_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `country_tbl`
--
ALTER TABLE `country_tbl`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `customer_tbl`
--
ALTER TABLE `customer_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_closing`
--
ALTER TABLE `daily_closing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_tbl`
--
ALTER TABLE `department_tbl`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `designation_tbl`
--
ALTER TABLE `designation_tbl`
  ADD PRIMARY KEY (`designation_id`);

--
-- Indexes for table `employee_tbl`
--
ALTER TABLE `employee_tbl`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_tbl`
--
ALTER TABLE `invoice_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ledger_tbl`
--
ALTER TABLE `ledger_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location_tbl`
--
ALTER TABLE `location_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `picture_tbl`
--
ALTER TABLE `picture_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_purchase`
--
ALTER TABLE `product_purchase`
  ADD PRIMARY KEY (`purchase_id`);

--
-- Indexes for table `product_purchase_details`
--
ALTER TABLE `product_purchase_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_return`
--
ALTER TABLE `product_return`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_tbl`
--
ALTER TABLE `product_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_unit`
--
ALTER TABLE `product_unit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `return_details`
--
ALTER TABLE `return_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salary_generat_tbl`
--
ALTER TABLE `salary_generat_tbl`
  ADD PRIMARY KEY (`generat_id`);

--
-- Indexes for table `salary_payment_history`
--
ALTER TABLE `salary_payment_history`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `salary_tbl`
--
ALTER TABLE `salary_tbl`
  ADD PRIMARY KEY (`salary_id`);

--
-- Indexes for table `sec_menu_item`
--
ALTER TABLE `sec_menu_item`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `sec_role_permission`
--
ALTER TABLE `sec_role_permission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sec_role_tbl`
--
ALTER TABLE `sec_role_tbl`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `sec_user_access_tbl`
--
ALTER TABLE `sec_user_access_tbl`
  ADD PRIMARY KEY (`role_acc_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_tbl`
--
ALTER TABLE `supplier_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_currency`
--
ALTER TABLE `tbl_currency`
  ADD PRIMARY KEY (`currencyid`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_tbl`
--
ALTER TABLE `attendance_tbl`
  MODIFY `attandence_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_tbl`
--
ALTER TABLE `bank_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category_tbl`
--
ALTER TABLE `category_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `country_tbl`
--
ALTER TABLE `country_tbl`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `customer_tbl`
--
ALTER TABLE `customer_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_closing`
--
ALTER TABLE `daily_closing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_tbl`
--
ALTER TABLE `department_tbl`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designation_tbl`
--
ALTER TABLE `designation_tbl`
  MODIFY `designation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_tbl`
--
ALTER TABLE `employee_tbl`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_tbl`
--
ALTER TABLE `invoice_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=970;

--
-- AUTO_INCREMENT for table `ledger_tbl`
--
ALTER TABLE `ledger_tbl`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location_tbl`
--
ALTER TABLE `location_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `picture_tbl`
--
ALTER TABLE `picture_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_purchase_details`
--
ALTER TABLE `product_purchase_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_return`
--
ALTER TABLE `product_return`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_tbl`
--
ALTER TABLE `product_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_unit`
--
ALTER TABLE `product_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_details`
--
ALTER TABLE `return_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_generat_tbl`
--
ALTER TABLE `salary_generat_tbl`
  MODIFY `generat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_payment_history`
--
ALTER TABLE `salary_payment_history`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_tbl`
--
ALTER TABLE `salary_tbl`
  MODIFY `salary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sec_menu_item`
--
ALTER TABLE `sec_menu_item`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT for table `sec_role_permission`
--
ALTER TABLE `sec_role_permission`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sec_role_tbl`
--
ALTER TABLE `sec_role_tbl`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sec_user_access_tbl`
--
ALTER TABLE `sec_user_access_tbl`
  MODIFY `role_acc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `supplier_tbl`
--
ALTER TABLE `supplier_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_currency`
--
ALTER TABLE `tbl_currency`
  MODIFY `currencyid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;