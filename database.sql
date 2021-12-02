CREATE TABLE `userpanel_countries` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`code` varchar(3) COLLATE utf8_persian_ci NOT NULL,
	`name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
	`dialing_code` varchar(3) COLLATE utf8_persian_ci NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `code` (`code`),
	KEY `code_2` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

CREATE TABLE `userpanel_usertypes` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `userpanel_usertypes_options` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`usertype` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`value` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `usertype` (`usertype`,`name`),
	KEY `usertypes` (`usertype`),
	CONSTRAINT `userpanel_usertypes_options_ibfk_1` FOREIGN KEY (`usertype`) REFERENCES `userpanel_usertypes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `userpanel_usertypes_permissions` (
	`type` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`type`,`name`),
	CONSTRAINT `userpanel_usertypes_permissions_ibfk_1` FOREIGN KEY (`type`) REFERENCES `userpanel_usertypes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `userpanel_usertypes_priorities` (
	`parent` int(11) NOT NULL,
	`child` int(11) NOT NULL,
	PRIMARY KEY (`parent`,`child`),
	KEY `child` (`child`),
	CONSTRAINT `userpanel_usertypes_priorities_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `userpanel_usertypes` (`id`) ON DELETE CASCADE,
	CONSTRAINT `userpanel_usertypes_priorities_ibfk_2` FOREIGN KEY (`child`) REFERENCES `userpanel_usertypes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

CREATE TABLE `userpanel_users` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL,
	`lastname` varchar(100) NOT NULL,
	`email` varchar(100) NOT NULL,
	`cellphone` varchar(14) NOT NULL,
	`password` varchar(255) NOT NULL,
	`type` int(11) NOT NULL,
	`phone` varchar(14) DEFAULT NULL,
	`city` varchar(100) DEFAULT NULL,
	`country` int(11) DEFAULT NULL,
	`zip` VARCHAR(11) DEFAULT NULL,
	`address` varchar(255) DEFAULT NULL,
	`web` varchar(255) DEFAULT NULL,
	`lastonline` int(11) NOT NULL DEFAULT '0',
	`remember_token` varchar(32) DEFAULT NULL,
	`credit` int(11) NOT NULL DEFAULT '0',
	`avatar` varchar(255) DEFAULT NULL,
	`registered_at` int(10) unsigned NOT NULL,
	`has_custom_permissions` tinyint(3) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(4) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `email` (`email`),
	UNIQUE KEY `cellphone` (`cellphone`),
	UNIQUE KEY `remember_token` (`remember_token`),
	KEY `type` (`type`),
	KEY `country` (`country`),
	CONSTRAINT `userpanel_users_ibfk_1` FOREIGN KEY (`type`) REFERENCES `userpanel_usertypes` (`id`),
	CONSTRAINT `userpanel_users_ibfk_2` FOREIGN KEY (`country`) REFERENCES `userpanel_countries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `userpanel_users_permissions` (
	`user_id` int(11) NOT NULL,
	`permission` varchar(255) NOT NULL,
	PRIMARY KEY (`user_id`,`permission`),
	CONSTRAINT `userpanel_users_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `userpanel_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `userpanel_users_options` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`value` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `user_2` (`user`,`name`),
	KEY `user` (`user`),
	CONSTRAINT `userpanel_users_options_ibfk_1` FOREIGN KEY (`user`) REFERENCES `userpanel_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `userpanel_users_socialnetworks` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user` int(11) NOT NULL,
	`network` tinyint(4) NOT NULL,
	`url` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `user` (`user`,`network`),
	CONSTRAINT `userpanel_users_socialnetworks_ibfk_1` FOREIGN KEY (`user`) REFERENCES `userpanel_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `userpanel_resetpwd_token` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`sent_at` int(11) NOT NULL,
	`user` int(11) NOT NULL,
	`token` varchar(255) NOT NULL,
	`ip` varchar(15) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `token` (`token`),
	KEY `user` (`user`),
	CONSTRAINT `userpanel_resetpwd_token_ibfk_1` FOREIGN KEY (`user`) REFERENCES `userpanel_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `userpanel_logs` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user` int(11) DEFAULT NULL,
	`ip` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`time` int(11) NOT NULL,
	`title` varchar(255) NOT NULL,
	`type` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`parameters` text NOT NULL,
	PRIMARY KEY (`id`),
	KEY `user` (`user`),
	KEY `type` (`type`),
	KEY `time` (`time`),
	KEY `user_2` (`type`,`time`,`user`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `options` (`name`, `value`, `autoload`) VALUES
('packages.userpanel.frontend.logo', 'Jey<i class="clip-user-6"></i>Support', 1),
('packages.userpanel.frontend.copyright', '2019 © Jalno by <a href="https://www.jeyserver.com">JeyServer</a>.', 1),
('userpanel.resetpwd.mis-chance.count', '5', 0),
('userpanel.resetpwd.mis-chance.period', '3600', 0),
('packages.userpanel.register', '{"enable":true,"type":2}', 0),
('packages.userpanel.date', '{"calendar":"jdate"}', 1);

INSERT INTO `userpanel_countries` (`id`, `code`, `name`, `dialing_code`) VALUES
(1, 'AF', 'Afghanistan', '93'),
(3, 'AL', 'Albania', '355'),
(4, 'DZ', 'Algeria', '213'),
(5, 'AS', 'American Samoa', '1'),
(6, 'AD', 'Andorra', '376'),
(7, 'AO', 'Angola', '244'),
(8, 'AI', 'Anguilla', '1'),
(9, 'AQ', 'Antarctica', '672'),
(10, 'AG', 'Antigua And Barbuda', '1'),
(11, 'AR', 'Argentina', '54'),
(12, 'AM', 'Armenia', '374'),
(13, 'AW', 'Aruba', '297'),
(14, 'AU', 'Australia', '61'),
(15, 'AT', 'Austria', '43'),
(16, 'AZ', 'Azerbaijan', '994'),
(17, 'BS', 'Bahamas', '1'),
(18, 'BH', 'Bahrain', '973'),
(19, 'BD', 'Bangladesh', '880'),
(20, 'BB', 'Barbados', '1'),
(21, 'BY', 'Belarus', '375'),
(22, 'BE', 'Belgium', '32'),
(23, 'BZ', 'Belize', '501'),
(24, 'BJ', 'Benin', '229'),
(25, 'BM', 'Bermuda', '1'),
(26, 'BT', 'Bhutan', '975'),
(27, 'BO', 'Bolivia, Plurinational State Of', '591'),
(28, 'BQ', 'Bonaire, Sint Eustatius And Saba', '599'),
(29, 'BA', 'Bosnia And Herzegovina', '387'),
(30, 'BW', 'Botswana', '267'),
(31, 'BV', 'Bouvet Island', '55'),
(32, 'BR', 'Brazil', '55'),
(33, 'IO', 'British Indian Ocean Territory', '246'),
(34, 'BN', 'Brunei Darussalam', '673'),
(35, 'BG', 'Bulgaria', '359'),
(36, 'BF', 'Burkina Faso', '226'),
(37, 'BI', 'Burundi', '257'),
(38, 'KH', 'Cambodia', '855'),
(39, 'CM', 'Cameroon', '237'),
(40, 'CA', 'Canada', '1'),
(41, 'CV', 'Cape Verde', '238'),
(42, 'KY', 'Cayman Islands', '1'),
(43, 'CF', 'Central African Republic', '236'),
(44, 'TD', 'Chad', '235'),
(45, 'CL', 'Chile', '56'),
(46, 'CN', 'China', '86'),
(47, 'CX', 'Christmas Island', '61'),
(48, 'CC', 'Cocos (keeling) Islands', '61'),
(49, 'CO', 'Colombia', '57'),
(50, 'KM', 'Comoros', '269'),
(51, 'CG', 'Congo', '242'),
(52, 'CD', 'Congo, The Democratic Republic Of The', '243'),
(53, 'CK', 'Cook Islands', '682'),
(54, 'CR', 'Costa Rica', '506'),
(55, 'CI', 'C', '225'),
(56, 'HR', 'Croatia', '385'),
(57, 'CU', 'Cuba', '53'),
(58, 'CW', 'Cura', '599'),
(59, 'CY', 'Cyprus', '357'),
(60, 'CZ', 'Czech Republic', '420'),
(61, 'DK', 'Denmark', '45'),
(62, 'DJ', 'Djibouti', '253'),
(63, 'DM', 'Dominica', '1'),
(64, 'DO', 'Dominican Republic', '1'),
(65, 'EC', 'Ecuador', '593'),
(66, 'EG', 'Egypt', '20'),
(67, 'SV', 'El Salvador', '503'),
(68, 'GQ', 'Equatorial Guinea', '240'),
(69, 'ER', 'Eritrea', '291'),
(70, 'EE', 'Estonia', '372'),
(71, 'ET', 'Ethiopia', '251'),
(72, 'FK', 'Falkland Islands (malvinas)', '500'),
(73, 'FO', 'Faroe Islands', '298'),
(74, 'FJ', 'Fiji', '679'),
(75, 'FI', 'Finland', '358'),
(76, 'FR', 'France', '33'),
(77, 'GF', 'French Guiana', '594'),
(78, 'PF', 'French Polynesia', '689'),
(79, 'TF', 'French Southern Territories', '262'),
(80, 'GA', 'Gabon', '241'),
(81, 'GM', 'Gambia', '220'),
(82, 'GE', 'Georgia', '995'),
(83, 'DE', 'Germany', '49'),
(84, 'GH', 'Ghana', '233'),
(85, 'GI', 'Gibraltar', '350'),
(86, 'GR', 'Greece', '30'),
(87, 'GL', 'Greenland', '299'),
(88, 'GD', 'Grenada', '1'),
(89, 'GP', 'Guadeloupe', '590'),
(90, 'GU', 'Guam', '1'),
(91, 'GT', 'Guatemala', '502'),
(92, 'GG', 'Guernsey', '44'),
(93, 'GN', 'Guinea', '224'),
(94, 'GW', 'Guinea-bissau', '245'),
(95, 'GY', 'Guyana', '592'),
(96, 'HT', 'Haiti', '509'),
(97, 'HM', 'Heard Island And Mcdonald Islands', '672'),
(98, 'VA', 'Holy See (vatican City State)', '39'),
(99, 'HN', 'Honduras', '504'),
(100, 'HK', 'Hong Kong', '852'),
(101, 'HU', 'Hungary', '36'),
(102, 'IS', 'Iceland', '354'),
(103, 'IN', 'India', '91'),
(104, 'ID', 'Indonesia', '62'),
(105, 'IR', 'Iran, Islamic Republic Of', '98'),
(106, 'IQ', 'Iraq', '964'),
(107, 'IE', 'Ireland', '353'),
(108, 'IM', 'Isle Of Man', '44'),
(109, 'IL', 'Israel', '972'),
(110, 'IT', 'Italy', '39'),
(111, 'JM', 'Jamaica', '1'),
(112, 'JP', 'Japan', '81'),
(113, 'JE', 'Jersey', '44'),
(114, 'JO', 'Jordan', '962'),
(115, 'KZ', 'Kazakhstan', '7'),
(116, 'KE', 'Kenya', '254'),
(117, 'KI', 'Kiribati', '686'),
(118, 'KP', 'Korea, Democratic People\'s Republic Of', '850'),
(119, 'KR', 'Korea, Republic Of', '82'),
(120, 'KW', 'Kuwait', '965'),
(121, 'KG', 'Kyrgyzstan', '996'),
(122, 'LA', 'Lao People\'s Democratic Republic', '856'),
(123, 'LV', 'Latvia', '371'),
(124, 'LB', 'Lebanon', '961'),
(125, 'LS', 'Lesotho', '266'),
(126, 'LR', 'Liberia', '231'),
(127, 'LY', 'Libya', '218'),
(128, 'LI', 'Liechtenstein', '423'),
(129, 'LT', 'Lithuania', '370'),
(130, 'LU', 'Luxembourg', '352'),
(131, 'MO', 'Macao', '853'),
(132, 'MK', 'Macedonia, The Former Yugoslav Republic Of', '389'),
(133, 'MG', 'Madagascar', '261'),
(134, 'MW', 'Malawi', '265'),
(135, 'MY', 'Malaysia', '60'),
(136, 'MV', 'Maldives', '960'),
(137, 'ML', 'Mali', '223'),
(138, 'MT', 'Malta', '356'),
(139, 'MH', 'Marshall Islands', '692'),
(140, 'MQ', 'Martinique', '596'),
(141, 'MR', 'Mauritania', '222'),
(142, 'MU', 'Mauritius', '230'),
(143, 'YT', 'Mayotte', '262'),
(144, 'MX', 'Mexico', '52'),
(145, 'FM', 'Micronesia, Federated States Of', '691'),
(146, 'MD', 'Moldova, Republic Of', '373'),
(147, 'MC', 'Monaco', '377'),
(148, 'MN', 'Mongolia', '976'),
(149, 'ME', 'Montenegro', '382'),
(150, 'MS', 'Montserrat', '1'),
(151, 'MA', 'Morocco', '212'),
(152, 'MZ', 'Mozambique', '258'),
(153, 'MM', 'Myanmar', '95'),
(154, 'NA', 'Namibia', '264'),
(155, 'NR', 'Nauru', '674'),
(156, 'NP', 'Nepal', '977'),
(157, 'NL', 'Netherlands', '31'),
(158, 'NC', 'New Caledonia', '687'),
(159, 'NZ', 'New Zealand', '64'),
(160, 'NI', 'Nicaragua', '505'),
(161, 'NE', 'Niger', '227'),
(162, 'NG', 'Nigeria', '234'),
(163, 'NU', 'Niue', '683'),
(164, 'NF', 'Norfolk Island', '672'),
(165, 'MP', 'Northern Mariana Islands', '1'),
(166, 'NO', 'Norway', '47'),
(167, 'OM', 'Oman', '968'),
(168, 'PK', 'Pakistan', '92'),
(169, 'PW', 'Palau', '680'),
(170, 'PS', 'Palestine, State Of', '970'),
(171, 'PA', 'Panama', '507'),
(172, 'PG', 'Papua New Guinea', '675'),
(173, 'PY', 'Paraguay', '595'),
(174, 'PE', 'Peru', '51'),
(175, 'PH', 'Philippines', '63'),
(176, 'PN', 'Pitcairn', '64'),
(177, 'PL', 'Poland', '48'),
(178, 'PT', 'Portugal', '351'),
(179, 'PR', 'Puerto Rico', '1'),
(180, 'QA', 'Qatar', '974'),
(181, 'RE', 'R', '262'),
(182, 'RO', 'Romania', '40'),
(183, 'RU', 'Russian Federation', '7'),
(184, 'RW', 'Rwanda', '250'),
(185, 'BL', 'Saint Barth', '590'),
(186, 'SH', 'Saint Helena, Ascension And Tristan Da Cunha', '290'),
(187, 'KN', 'Saint Kitts And Nevis', '1'),
(188, 'LC', 'Saint Lucia', '1'),
(189, 'MF', 'Saint Martin (french Part)', '590'),
(190, 'PM', 'Saint Pierre And Miquelon', '508'),
(191, 'VC', 'Saint Vincent And The Grenadines', '1'),
(192, 'WS', 'Samoa', '685'),
(193, 'SM', 'San Marino', '378'),
(194, 'ST', 'Sao Tome And Principe', '239'),
(195, 'SA', 'Saudi Arabia', '966'),
(196, 'SN', 'Senegal', '221'),
(197, 'RS', 'Serbia', '381'),
(198, 'SC', 'Seychelles', '248'),
(199, 'SL', 'Sierra Leone', '232'),
(200, 'SG', 'Singapore', '65'),
(201, 'SX', 'Sint Maarten (dutch Part)', '1'),
(202, 'SK', 'Slovakia', '421'),
(203, 'SI', 'Slovenia', '386'),
(204, 'SB', 'Solomon Islands', '677'),
(205, 'SO', 'Somalia', '252'),
(206, 'ZA', 'South Africa', '27'),
(207, 'GS', 'South Georgia And The South Sandwich Islands', '500'),
(208, 'SS', 'South Sudan', '211'),
(209, 'ES', 'Spain', '34'),
(210, 'LK', 'Sri Lanka', '94'),
(211, 'SD', 'Sudan', '249'),
(212, 'SR', 'Suriname', '597'),
(213, 'SJ', 'Svalbard And Jan Mayen', '47'),
(214, 'SZ', 'Swaziland', '268'),
(215, 'SE', 'Sweden', '46'),
(216, 'CH', 'Switzerland', '41'),
(217, 'SY', 'Syrian Arab Republic', '963'),
(218, 'TW', 'Taiwan, Province Of China', '886'),
(219, 'TJ', 'Tajikistan', '992'),
(220, 'TZ', 'Tanzania, United Republic Of', '255'),
(221, 'TH', 'Thailand', '66'),
(222, 'TL', 'Timor-leste', '670'),
(223, 'TG', 'Togo', '228'),
(224, 'TK', 'Tokelau', '690'),
(225, 'TO', 'Tonga', '676'),
(226, 'TT', 'Trinidad And Tobago', '1'),
(227, 'TN', 'Tunisia', '216'),
(228, 'TR', 'Turkey', '90'),
(229, 'TM', 'Turkmenistan', '993'),
(230, 'TC', 'Turks And Caicos Islands', '1'),
(231, 'TV', 'Tuvalu', '688'),
(232, 'UG', 'Uganda', '256'),
(233, 'UA', 'Ukraine', '380'),
(234, 'AE', 'United Arab Emirates', '971'),
(235, 'GB', 'United Kingdom', '44'),
(236, 'US', 'United States', '1'),
(237, 'UM', 'United States Minor Outlying Islands', '264'),
(238, 'UY', 'Uruguay', '598'),
(239, 'UZ', 'Uzbekistan', '998'),
(240, 'VU', 'Vanuatu', '678'),
(241, 'VE', 'Venezuela, Bolivarian Republic Of', '58'),
(242, 'VN', 'Viet Nam', '84'),
(243, 'VG', 'Virgin Islands, British', '1'),
(244, 'VI', 'Virgin Islands, U.s.', '1'),
(245, 'WF', 'Wallis And Futuna', '681'),
(246, 'EH', 'Western Sahara', '212'),
(247, 'YE', 'Yemen', '967'),
(248, 'ZM', 'Zambia', '260'),
(249, 'ZW', 'Zimbabwe', '263');


INSERT INTO `userpanel_usertypes` (`id`, `title`) VALUES
(1, 'مدیر'),
(2, 'مشتری');

INSERT INTO `userpanel_usertypes_permissions` (`type`, `name`) VALUES
(1, 'userpanel_profile_edit'),
(1, 'userpanel_profile_edit_privacy'),
(1, 'userpanel_profile_view'),
(1, 'userpanel_settings_usertypes_add'),
(1, 'userpanel_settings_usertypes_delete'),
(1, 'userpanel_settings_usertypes_edit'),
(1, 'userpanel_settings_usertypes_list'),
(1, 'userpanel_users_add'),
(1, 'userpanel_users_delete'),
(1, 'userpanel_users_edit'),
(1, 'userpanel_users_edit_privacy'),
(1, 'userpanel_users_list'),
(1, 'userpanel_users_view'),
(1, 'userpanel_users_view_invisibles'),
(1, 'userpanel_resetpwd_newpwd'),
(1, 'userpanel_users_settings'),
(2, 'userpanel_resetpwd_newpwd'),
(2, 'userpanel_profile_edit'),
(2, 'userpanel_profile_edit_privacy'),
(2, 'userpanel_profile_view'),
(2, 'userpanel_profile_settings'),
(2, 'userpanel_users_view');

INSERT INTO `userpanel_usertypes_priorities` (`parent`, `child`) VALUES
(1, 1),
(1, 2);


INSERT INTO `userpanel_users` (`id`, `name`, `lastname`, `email`, `cellphone`, `password`, `type`, `phone`, `city`, `country`, `zip`, `address`, `web`, `lastonline`, `remember_token`, `credit`, `avatar`, `registered_at`, `status`) VALUES
(1, 'مدیرکل', '', 'admin@jeyserver.com', '989387654321', '$2y$10$V4hoveZhY5Eq6N8bVEKu0OegWYLH/Fj2OekT1TYiYQ5HkB6p7HopO', 1, '03134420301', 'اصفهان', 105, 2147483647, 'خ رابط دوم ساختمان شمشاد واحد ۴', 'https://jeyserver.com', 1470755431, NULL, 307333, NULL, 1470755431, 1);

INSERT INTO `userpanel_users_options` (`id`, `user`, `name`, `value`) VALUES
(1, 1, 'visibilities', '["email","cellphone","phone","socialnetworks_5","socialnetworks_4","socialnetworks_6","socialnetworks_2","socialnetworks_1","socialnetworks_3"]');

INSERT INTO `options` (`name`, `value`, `autoload`) VALUES ('packages.userpanel.tos_url', '', '0');
