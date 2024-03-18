
---
---	Commit:	1397fa971306a3155582a79652ce5c3ab03daf4b
---
ALTER TABLE `userpanel_users` CHANGE `credit` `credit` INT(11) NOT NULL DEFAULT '0';

--
-- Commit: e0384b52ee070f29103aa85610c3697e6e9af005
--
ALTER TABLE `userpanel_users` ADD `registered_at` INT UNSIGNED NOT NULL AFTER `avatar`; 

--
-- Commit: 1ad942c8499861fa2fdf536d8d8464a5052b53b4
--
UPDATE `userpanel_users` SET `status` = 3 WHERE `status` = 0;

--
--	Commit: 6720e0f95aef2a65b2b98e7db0498d5d8c495b21
--
ALTER TABLE `userpanel_logs` CHANGE `user` `user` INT(11) NULL;

---
---	Commit: 82fb3bc6e09ecbe8611e60c388331bada1d962c2
---
ALTER TABLE `userpanel_logs` CHANGE `parameters` `parameters` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

--
--	Commit: 6b5c7dd039f240ba0e910bbee1cb0760618001fe
--
ALTER TABLE `userpanel_users` ADD `has_custom_permissions` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `registered_at`;
CREATE TABLE `userpanel_users_permissions` (
	`user_id` int(11) NOT NULL,
	`permission` varchar(255) NOT NULL,
	PRIMARY KEY (`user_id`,`permission`),
	CONSTRAINT `userpanel_users_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `userpanel_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
--	Commit: df2c2fac833ee933e867d9ea5cea2b79adc9d0f7
--
ALTER TABLE `userpanel_countries` ADD `dialing_code` VARCHAR(3) NOT NULL AFTER `name`;
UPDATE `userpanel_countries` SET `dialing_code` = '93' WHERE `userpanel_countries`.`id` = 1;
UPDATE `userpanel_countries` SET `dialing_code` = '355' WHERE `userpanel_countries`.`id` = 3;
UPDATE `userpanel_countries` SET `dialing_code` = '213' WHERE `userpanel_countries`.`id` = 4;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 5;
UPDATE `userpanel_countries` SET `dialing_code` = '376' WHERE `userpanel_countries`.`id` = 6;
UPDATE `userpanel_countries` SET `dialing_code` = '244' WHERE `userpanel_countries`.`id` = 7;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 8;
UPDATE `userpanel_countries` SET `dialing_code` = '672' WHERE `userpanel_countries`.`id` = 9;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 10;
UPDATE `userpanel_countries` SET `dialing_code` = '54' WHERE `userpanel_countries`.`id` = 11;
UPDATE `userpanel_countries` SET `dialing_code` = '374' WHERE `userpanel_countries`.`id` = 12;
UPDATE `userpanel_countries` SET `dialing_code` = '297' WHERE `userpanel_countries`.`id` = 13;
UPDATE `userpanel_countries` SET `dialing_code` = '61' WHERE `userpanel_countries`.`id` = 14;
UPDATE `userpanel_countries` SET `dialing_code` = '43' WHERE `userpanel_countries`.`id` = 15;
UPDATE `userpanel_countries` SET `dialing_code` = '994' WHERE `userpanel_countries`.`id` = 16;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 17;
UPDATE `userpanel_countries` SET `dialing_code` = '973' WHERE `userpanel_countries`.`id` = 18;
UPDATE `userpanel_countries` SET `dialing_code` = '880' WHERE `userpanel_countries`.`id` = 19;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 20;
UPDATE `userpanel_countries` SET `dialing_code` = '375' WHERE `userpanel_countries`.`id` = 21;
UPDATE `userpanel_countries` SET `dialing_code` = '32' WHERE `userpanel_countries`.`id` = 22;
UPDATE `userpanel_countries` SET `dialing_code` = '501' WHERE `userpanel_countries`.`id` = 23;
UPDATE `userpanel_countries` SET `dialing_code` = '229' WHERE `userpanel_countries`.`id` = 24;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 25;
UPDATE `userpanel_countries` SET `dialing_code` = '975' WHERE `userpanel_countries`.`id` = 26;
UPDATE `userpanel_countries` SET `dialing_code` = '591' WHERE `userpanel_countries`.`id` = 27;
UPDATE `userpanel_countries` SET `dialing_code` = '599' WHERE `userpanel_countries`.`id` = 28;
UPDATE `userpanel_countries` SET `dialing_code` = '387' WHERE `userpanel_countries`.`id` = 29;
UPDATE `userpanel_countries` SET `dialing_code` = '267' WHERE `userpanel_countries`.`id` = 30;
UPDATE `userpanel_countries` SET `dialing_code` = '55' WHERE `userpanel_countries`.`id` = 31;
UPDATE `userpanel_countries` SET `dialing_code` = '55' WHERE `userpanel_countries`.`id` = 32;
UPDATE `userpanel_countries` SET `dialing_code` = '246' WHERE `userpanel_countries`.`id` = 33;
UPDATE `userpanel_countries` SET `dialing_code` = '673' WHERE `userpanel_countries`.`id` = 34;
UPDATE `userpanel_countries` SET `dialing_code` = '359' WHERE `userpanel_countries`.`id` = 35;
UPDATE `userpanel_countries` SET `dialing_code` = '226' WHERE `userpanel_countries`.`id` = 36;
UPDATE `userpanel_countries` SET `dialing_code` = '257' WHERE `userpanel_countries`.`id` = 37;
UPDATE `userpanel_countries` SET `dialing_code` = '855' WHERE `userpanel_countries`.`id` = 38;
UPDATE `userpanel_countries` SET `dialing_code` = '237' WHERE `userpanel_countries`.`id` = 39;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 40;
UPDATE `userpanel_countries` SET `dialing_code` = '238' WHERE `userpanel_countries`.`id` = 41;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 42;
UPDATE `userpanel_countries` SET `dialing_code` = '236' WHERE `userpanel_countries`.`id` = 43;
UPDATE `userpanel_countries` SET `dialing_code` = '235' WHERE `userpanel_countries`.`id` = 44;
UPDATE `userpanel_countries` SET `dialing_code` = '56' WHERE `userpanel_countries`.`id` = 45;
UPDATE `userpanel_countries` SET `dialing_code` = '86' WHERE `userpanel_countries`.`id` = 46;
UPDATE `userpanel_countries` SET `dialing_code` = '61' WHERE `userpanel_countries`.`id` = 47;
UPDATE `userpanel_countries` SET `dialing_code` = '61' WHERE `userpanel_countries`.`id` = 48;
UPDATE `userpanel_countries` SET `dialing_code` = '57' WHERE `userpanel_countries`.`id` = 49;
UPDATE `userpanel_countries` SET `dialing_code` = '269' WHERE `userpanel_countries`.`id` = 50;
UPDATE `userpanel_countries` SET `dialing_code` = '242' WHERE `userpanel_countries`.`id` = 51;
UPDATE `userpanel_countries` SET `dialing_code` = '243' WHERE `userpanel_countries`.`id` = 52;
UPDATE `userpanel_countries` SET `dialing_code` = '682' WHERE `userpanel_countries`.`id` = 53;
UPDATE `userpanel_countries` SET `dialing_code` = '506' WHERE `userpanel_countries`.`id` = 54;
UPDATE `userpanel_countries` SET `dialing_code` = '225' WHERE `userpanel_countries`.`id` = 55;
UPDATE `userpanel_countries` SET `dialing_code` = '385' WHERE `userpanel_countries`.`id` = 56;
UPDATE `userpanel_countries` SET `dialing_code` = '53' WHERE `userpanel_countries`.`id` = 57;
UPDATE `userpanel_countries` SET `dialing_code` = '599' WHERE `userpanel_countries`.`id` = 58;
UPDATE `userpanel_countries` SET `dialing_code` = '357' WHERE `userpanel_countries`.`id` = 59;
UPDATE `userpanel_countries` SET `dialing_code` = '420' WHERE `userpanel_countries`.`id` = 60;
UPDATE `userpanel_countries` SET `dialing_code` = '45' WHERE `userpanel_countries`.`id` = 61;
UPDATE `userpanel_countries` SET `dialing_code` = '253' WHERE `userpanel_countries`.`id` = 62;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 63;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 64;
UPDATE `userpanel_countries` SET `dialing_code` = '593' WHERE `userpanel_countries`.`id` = 65;
UPDATE `userpanel_countries` SET `dialing_code` = '20' WHERE `userpanel_countries`.`id` = 66;
UPDATE `userpanel_countries` SET `dialing_code` = '503' WHERE `userpanel_countries`.`id` = 67;
UPDATE `userpanel_countries` SET `dialing_code` = '240' WHERE `userpanel_countries`.`id` = 68;
UPDATE `userpanel_countries` SET `dialing_code` = '291' WHERE `userpanel_countries`.`id` = 69;
UPDATE `userpanel_countries` SET `dialing_code` = '372' WHERE `userpanel_countries`.`id` = 70;
UPDATE `userpanel_countries` SET `dialing_code` = '251' WHERE `userpanel_countries`.`id` = 71;
UPDATE `userpanel_countries` SET `dialing_code` = '500' WHERE `userpanel_countries`.`id` = 72;
UPDATE `userpanel_countries` SET `dialing_code` = '298' WHERE `userpanel_countries`.`id` = 73;
UPDATE `userpanel_countries` SET `dialing_code` = '679' WHERE `userpanel_countries`.`id` = 74;
UPDATE `userpanel_countries` SET `dialing_code` = '358' WHERE `userpanel_countries`.`id` = 75;
UPDATE `userpanel_countries` SET `dialing_code` = '33' WHERE `userpanel_countries`.`id` = 76;
UPDATE `userpanel_countries` SET `dialing_code` = '594' WHERE `userpanel_countries`.`id` = 77;
UPDATE `userpanel_countries` SET `dialing_code` = '689' WHERE `userpanel_countries`.`id` = 78;
UPDATE `userpanel_countries` SET `dialing_code` = '262' WHERE `userpanel_countries`.`id` = 79;
UPDATE `userpanel_countries` SET `dialing_code` = '241' WHERE `userpanel_countries`.`id` = 80;
UPDATE `userpanel_countries` SET `dialing_code` = '220' WHERE `userpanel_countries`.`id` = 81;
UPDATE `userpanel_countries` SET `dialing_code` = '995' WHERE `userpanel_countries`.`id` = 82;
UPDATE `userpanel_countries` SET `dialing_code` = '49' WHERE `userpanel_countries`.`id` = 83;
UPDATE `userpanel_countries` SET `dialing_code` = '233' WHERE `userpanel_countries`.`id` = 84;
UPDATE `userpanel_countries` SET `dialing_code` = '350' WHERE `userpanel_countries`.`id` = 85;
UPDATE `userpanel_countries` SET `dialing_code` = '30' WHERE `userpanel_countries`.`id` = 86;
UPDATE `userpanel_countries` SET `dialing_code` = '299' WHERE `userpanel_countries`.`id` = 87;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 88;
UPDATE `userpanel_countries` SET `dialing_code` = '590' WHERE `userpanel_countries`.`id` = 89;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 90;
UPDATE `userpanel_countries` SET `dialing_code` = '502' WHERE `userpanel_countries`.`id` = 91;
UPDATE `userpanel_countries` SET `dialing_code` = '44' WHERE `userpanel_countries`.`id` = 92;
UPDATE `userpanel_countries` SET `dialing_code` = '224' WHERE `userpanel_countries`.`id` = 93;
UPDATE `userpanel_countries` SET `dialing_code` = '245' WHERE `userpanel_countries`.`id` = 94;
UPDATE `userpanel_countries` SET `dialing_code` = '592' WHERE `userpanel_countries`.`id` = 95;
UPDATE `userpanel_countries` SET `dialing_code` = '509' WHERE `userpanel_countries`.`id` = 96;
UPDATE `userpanel_countries` SET `dialing_code` = '672' WHERE `userpanel_countries`.`id` = 97;
UPDATE `userpanel_countries` SET `dialing_code` = '39' WHERE `userpanel_countries`.`id` = 98;
UPDATE `userpanel_countries` SET `dialing_code` = '504' WHERE `userpanel_countries`.`id` = 99;
UPDATE `userpanel_countries` SET `dialing_code` = '852' WHERE `userpanel_countries`.`id` = 100;
UPDATE `userpanel_countries` SET `dialing_code` = '36' WHERE `userpanel_countries`.`id` = 101;
UPDATE `userpanel_countries` SET `dialing_code` = '354' WHERE `userpanel_countries`.`id` = 102;
UPDATE `userpanel_countries` SET `dialing_code` = '91' WHERE `userpanel_countries`.`id` = 103;
UPDATE `userpanel_countries` SET `dialing_code` = '62' WHERE `userpanel_countries`.`id` = 104;
UPDATE `userpanel_countries` SET `dialing_code` = '98' WHERE `userpanel_countries`.`id` = 105;
UPDATE `userpanel_countries` SET `dialing_code` = '964' WHERE `userpanel_countries`.`id` = 106;
UPDATE `userpanel_countries` SET `dialing_code` = '353' WHERE `userpanel_countries`.`id` = 107;
UPDATE `userpanel_countries` SET `dialing_code` = '44' WHERE `userpanel_countries`.`id` = 108;
UPDATE `userpanel_countries` SET `dialing_code` = '972' WHERE `userpanel_countries`.`id` = 109;
UPDATE `userpanel_countries` SET `dialing_code` = '39' WHERE `userpanel_countries`.`id` = 110;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 111;
UPDATE `userpanel_countries` SET `dialing_code` = '81' WHERE `userpanel_countries`.`id` = 112;
UPDATE `userpanel_countries` SET `dialing_code` = '44' WHERE `userpanel_countries`.`id` = 113;
UPDATE `userpanel_countries` SET `dialing_code` = '962' WHERE `userpanel_countries`.`id` = 114;
UPDATE `userpanel_countries` SET `dialing_code` = '7' WHERE `userpanel_countries`.`id` = 115;
UPDATE `userpanel_countries` SET `dialing_code` = '254' WHERE `userpanel_countries`.`id` = 116;
UPDATE `userpanel_countries` SET `dialing_code` = '686' WHERE `userpanel_countries`.`id` = 117;
UPDATE `userpanel_countries` SET `dialing_code` = '850' WHERE `userpanel_countries`.`id` = 118;
UPDATE `userpanel_countries` SET `dialing_code` = '82' WHERE `userpanel_countries`.`id` = 119;
UPDATE `userpanel_countries` SET `dialing_code` = '965' WHERE `userpanel_countries`.`id` = 120;
UPDATE `userpanel_countries` SET `dialing_code` = '996' WHERE `userpanel_countries`.`id` = 121;
UPDATE `userpanel_countries` SET `dialing_code` = '856' WHERE `userpanel_countries`.`id` = 122;
UPDATE `userpanel_countries` SET `dialing_code` = '371' WHERE `userpanel_countries`.`id` = 123;
UPDATE `userpanel_countries` SET `dialing_code` = '961' WHERE `userpanel_countries`.`id` = 124;
UPDATE `userpanel_countries` SET `dialing_code` = '266' WHERE `userpanel_countries`.`id` = 125;
UPDATE `userpanel_countries` SET `dialing_code` = '231' WHERE `userpanel_countries`.`id` = 126;
UPDATE `userpanel_countries` SET `dialing_code` = '218' WHERE `userpanel_countries`.`id` = 127;
UPDATE `userpanel_countries` SET `dialing_code` = '423' WHERE `userpanel_countries`.`id` = 128;
UPDATE `userpanel_countries` SET `dialing_code` = '370' WHERE `userpanel_countries`.`id` = 129;
UPDATE `userpanel_countries` SET `dialing_code` = '352' WHERE `userpanel_countries`.`id` = 130;
UPDATE `userpanel_countries` SET `dialing_code` = '853' WHERE `userpanel_countries`.`id` = 131;
UPDATE `userpanel_countries` SET `dialing_code` = '389' WHERE `userpanel_countries`.`id` = 132;
UPDATE `userpanel_countries` SET `dialing_code` = '261' WHERE `userpanel_countries`.`id` = 133;
UPDATE `userpanel_countries` SET `dialing_code` = '265' WHERE `userpanel_countries`.`id` = 134;
UPDATE `userpanel_countries` SET `dialing_code` = '60' WHERE `userpanel_countries`.`id` = 135;
UPDATE `userpanel_countries` SET `dialing_code` = '960' WHERE `userpanel_countries`.`id` = 136;
UPDATE `userpanel_countries` SET `dialing_code` = '223' WHERE `userpanel_countries`.`id` = 137;
UPDATE `userpanel_countries` SET `dialing_code` = '356' WHERE `userpanel_countries`.`id` = 138;
UPDATE `userpanel_countries` SET `dialing_code` = '692' WHERE `userpanel_countries`.`id` = 139;
UPDATE `userpanel_countries` SET `dialing_code` = '596' WHERE `userpanel_countries`.`id` = 140;
UPDATE `userpanel_countries` SET `dialing_code` = '222' WHERE `userpanel_countries`.`id` = 141;
UPDATE `userpanel_countries` SET `dialing_code` = '230' WHERE `userpanel_countries`.`id` = 142;
UPDATE `userpanel_countries` SET `dialing_code` = '262' WHERE `userpanel_countries`.`id` = 143;
UPDATE `userpanel_countries` SET `dialing_code` = '52' WHERE `userpanel_countries`.`id` = 144;
UPDATE `userpanel_countries` SET `dialing_code` = '691' WHERE `userpanel_countries`.`id` = 145;
UPDATE `userpanel_countries` SET `dialing_code` = '373' WHERE `userpanel_countries`.`id` = 146;
UPDATE `userpanel_countries` SET `dialing_code` = '377' WHERE `userpanel_countries`.`id` = 147;
UPDATE `userpanel_countries` SET `dialing_code` = '976' WHERE `userpanel_countries`.`id` = 148;
UPDATE `userpanel_countries` SET `dialing_code` = '382' WHERE `userpanel_countries`.`id` = 149;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 150;
UPDATE `userpanel_countries` SET `dialing_code` = '212' WHERE `userpanel_countries`.`id` = 151;
UPDATE `userpanel_countries` SET `dialing_code` = '258' WHERE `userpanel_countries`.`id` = 152;
UPDATE `userpanel_countries` SET `dialing_code` = '95' WHERE `userpanel_countries`.`id` = 153;
UPDATE `userpanel_countries` SET `dialing_code` = '264' WHERE `userpanel_countries`.`id` = 154;
UPDATE `userpanel_countries` SET `dialing_code` = '674' WHERE `userpanel_countries`.`id` = 155;
UPDATE `userpanel_countries` SET `dialing_code` = '977' WHERE `userpanel_countries`.`id` = 156;
UPDATE `userpanel_countries` SET `dialing_code` = '31' WHERE `userpanel_countries`.`id` = 157;
UPDATE `userpanel_countries` SET `dialing_code` = '687' WHERE `userpanel_countries`.`id` = 158;
UPDATE `userpanel_countries` SET `dialing_code` = '64' WHERE `userpanel_countries`.`id` = 159;
UPDATE `userpanel_countries` SET `dialing_code` = '505' WHERE `userpanel_countries`.`id` = 160;
UPDATE `userpanel_countries` SET `dialing_code` = '227' WHERE `userpanel_countries`.`id` = 161;
UPDATE `userpanel_countries` SET `dialing_code` = '234' WHERE `userpanel_countries`.`id` = 162;
UPDATE `userpanel_countries` SET `dialing_code` = '683' WHERE `userpanel_countries`.`id` = 163;
UPDATE `userpanel_countries` SET `dialing_code` = '672' WHERE `userpanel_countries`.`id` = 164;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 165;
UPDATE `userpanel_countries` SET `dialing_code` = '47' WHERE `userpanel_countries`.`id` = 166;
UPDATE `userpanel_countries` SET `dialing_code` = '968' WHERE `userpanel_countries`.`id` = 167;
UPDATE `userpanel_countries` SET `dialing_code` = '92' WHERE `userpanel_countries`.`id` = 168;
UPDATE `userpanel_countries` SET `dialing_code` = '680' WHERE `userpanel_countries`.`id` = 169;
UPDATE `userpanel_countries` SET `dialing_code` = '970' WHERE `userpanel_countries`.`id` = 170;
UPDATE `userpanel_countries` SET `dialing_code` = '507' WHERE `userpanel_countries`.`id` = 171;
UPDATE `userpanel_countries` SET `dialing_code` = '675' WHERE `userpanel_countries`.`id` = 172;
UPDATE `userpanel_countries` SET `dialing_code` = '595' WHERE `userpanel_countries`.`id` = 173;
UPDATE `userpanel_countries` SET `dialing_code` = '51' WHERE `userpanel_countries`.`id` = 174;
UPDATE `userpanel_countries` SET `dialing_code` = '63' WHERE `userpanel_countries`.`id` = 175;
UPDATE `userpanel_countries` SET `dialing_code` = '64' WHERE `userpanel_countries`.`id` = 176;
UPDATE `userpanel_countries` SET `dialing_code` = '48' WHERE `userpanel_countries`.`id` = 177;
UPDATE `userpanel_countries` SET `dialing_code` = '351' WHERE `userpanel_countries`.`id` = 178;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 179;
UPDATE `userpanel_countries` SET `dialing_code` = '974' WHERE `userpanel_countries`.`id` = 180;
UPDATE `userpanel_countries` SET `dialing_code` = '262' WHERE `userpanel_countries`.`id` = 181;
UPDATE `userpanel_countries` SET `dialing_code` = '40' WHERE `userpanel_countries`.`id` = 182;
UPDATE `userpanel_countries` SET `dialing_code` = '7' WHERE `userpanel_countries`.`id` = 183;
UPDATE `userpanel_countries` SET `dialing_code` = '250' WHERE `userpanel_countries`.`id` = 184;
UPDATE `userpanel_countries` SET `dialing_code` = '590' WHERE `userpanel_countries`.`id` = 185;
UPDATE `userpanel_countries` SET `dialing_code` = '290' WHERE `userpanel_countries`.`id` = 186;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 187;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 188;
UPDATE `userpanel_countries` SET `dialing_code` = '590' WHERE `userpanel_countries`.`id` = 189;
UPDATE `userpanel_countries` SET `dialing_code` = '508' WHERE `userpanel_countries`.`id` = 190;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 191;
UPDATE `userpanel_countries` SET `dialing_code` = '685' WHERE `userpanel_countries`.`id` = 192;
UPDATE `userpanel_countries` SET `dialing_code` = '378' WHERE `userpanel_countries`.`id` = 193;
UPDATE `userpanel_countries` SET `dialing_code` = '239' WHERE `userpanel_countries`.`id` = 194;
UPDATE `userpanel_countries` SET `dialing_code` = '966' WHERE `userpanel_countries`.`id` = 195;
UPDATE `userpanel_countries` SET `dialing_code` = '221' WHERE `userpanel_countries`.`id` = 196;
UPDATE `userpanel_countries` SET `dialing_code` = '381' WHERE `userpanel_countries`.`id` = 197;
UPDATE `userpanel_countries` SET `dialing_code` = '248' WHERE `userpanel_countries`.`id` = 198;
UPDATE `userpanel_countries` SET `dialing_code` = '232' WHERE `userpanel_countries`.`id` = 199;
UPDATE `userpanel_countries` SET `dialing_code` = '65' WHERE `userpanel_countries`.`id` = 200;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 201;
UPDATE `userpanel_countries` SET `dialing_code` = '421' WHERE `userpanel_countries`.`id` = 202;
UPDATE `userpanel_countries` SET `dialing_code` = '386' WHERE `userpanel_countries`.`id` = 203;
UPDATE `userpanel_countries` SET `dialing_code` = '677' WHERE `userpanel_countries`.`id` = 204;
UPDATE `userpanel_countries` SET `dialing_code` = '252' WHERE `userpanel_countries`.`id` = 205;
UPDATE `userpanel_countries` SET `dialing_code` = '27' WHERE `userpanel_countries`.`id` = 206;
UPDATE `userpanel_countries` SET `dialing_code` = '500' WHERE `userpanel_countries`.`id` = 207;
UPDATE `userpanel_countries` SET `dialing_code` = '211' WHERE `userpanel_countries`.`id` = 208;
UPDATE `userpanel_countries` SET `dialing_code` = '34' WHERE `userpanel_countries`.`id` = 209;
UPDATE `userpanel_countries` SET `dialing_code` = '94' WHERE `userpanel_countries`.`id` = 210;
UPDATE `userpanel_countries` SET `dialing_code` = '249' WHERE `userpanel_countries`.`id` = 211;
UPDATE `userpanel_countries` SET `dialing_code` = '597' WHERE `userpanel_countries`.`id` = 212;
UPDATE `userpanel_countries` SET `dialing_code` = '47' WHERE `userpanel_countries`.`id` = 213;
UPDATE `userpanel_countries` SET `dialing_code` = '268' WHERE `userpanel_countries`.`id` = 214;
UPDATE `userpanel_countries` SET `dialing_code` = '46' WHERE `userpanel_countries`.`id` = 215;
UPDATE `userpanel_countries` SET `dialing_code` = '41' WHERE `userpanel_countries`.`id` = 216;
UPDATE `userpanel_countries` SET `dialing_code` = '963' WHERE `userpanel_countries`.`id` = 217;
UPDATE `userpanel_countries` SET `dialing_code` = '886' WHERE `userpanel_countries`.`id` = 218;
UPDATE `userpanel_countries` SET `dialing_code` = '992' WHERE `userpanel_countries`.`id` = 219;
UPDATE `userpanel_countries` SET `dialing_code` = '255' WHERE `userpanel_countries`.`id` = 220;
UPDATE `userpanel_countries` SET `dialing_code` = '66' WHERE `userpanel_countries`.`id` = 221;
UPDATE `userpanel_countries` SET `dialing_code` = '670' WHERE `userpanel_countries`.`id` = 222;
UPDATE `userpanel_countries` SET `dialing_code` = '228' WHERE `userpanel_countries`.`id` = 223;
UPDATE `userpanel_countries` SET `dialing_code` = '690' WHERE `userpanel_countries`.`id` = 224;
UPDATE `userpanel_countries` SET `dialing_code` = '676' WHERE `userpanel_countries`.`id` = 225;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 226;
UPDATE `userpanel_countries` SET `dialing_code` = '216' WHERE `userpanel_countries`.`id` = 227;
UPDATE `userpanel_countries` SET `dialing_code` = '90' WHERE `userpanel_countries`.`id` = 228;
UPDATE `userpanel_countries` SET `dialing_code` = '993' WHERE `userpanel_countries`.`id` = 229;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 230;
UPDATE `userpanel_countries` SET `dialing_code` = '688' WHERE `userpanel_countries`.`id` = 231;
UPDATE `userpanel_countries` SET `dialing_code` = '256' WHERE `userpanel_countries`.`id` = 232;
UPDATE `userpanel_countries` SET `dialing_code` = '380' WHERE `userpanel_countries`.`id` = 233;
UPDATE `userpanel_countries` SET `dialing_code` = '971' WHERE `userpanel_countries`.`id` = 234;
UPDATE `userpanel_countries` SET `dialing_code` = '44' WHERE `userpanel_countries`.`id` = 235;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 236;
UPDATE `userpanel_countries` SET `dialing_code` = '264' WHERE `userpanel_countries`.`id` = 237;
UPDATE `userpanel_countries` SET `dialing_code` = '598' WHERE `userpanel_countries`.`id` = 238;
UPDATE `userpanel_countries` SET `dialing_code` = '998' WHERE `userpanel_countries`.`id` = 239;
UPDATE `userpanel_countries` SET `dialing_code` = '678' WHERE `userpanel_countries`.`id` = 240;
UPDATE `userpanel_countries` SET `dialing_code` = '58' WHERE `userpanel_countries`.`id` = 241;
UPDATE `userpanel_countries` SET `dialing_code` = '84' WHERE `userpanel_countries`.`id` = 242;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 243;
UPDATE `userpanel_countries` SET `dialing_code` = '1' WHERE `userpanel_countries`.`id` = 244;
UPDATE `userpanel_countries` SET `dialing_code` = '681' WHERE `userpanel_countries`.`id` = 245;
UPDATE `userpanel_countries` SET `dialing_code` = '212' WHERE `userpanel_countries`.`id` = 246;
UPDATE `userpanel_countries` SET `dialing_code` = '967' WHERE `userpanel_countries`.`id` = 247;
UPDATE `userpanel_countries` SET `dialing_code` = '260' WHERE `userpanel_countries`.`id` = 248;
UPDATE `userpanel_countries` SET `dialing_code` = '263' WHERE `userpanel_countries`.`id` = 249;

ALTER TABLE `userpanel_users` CHANGE `cellphone` `cellphone` VARCHAR(14) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `userpanel_users` CHANGE `phone` `phone` VARCHAR(14) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

--
--	Commit:	6e49175c4e45141d2132a23b3df698c95e6bd9a2
--
ALTER TABLE `userpanel_users` CHANGE `zip` `zip` VARCHAR(11) NULL DEFAULT NULL;

--
-- Commit: df2c2fac833ee933e867d9ea5cea2b79adc9d0f7
--
UPDATE userpanel_users SET cellphone = CONCAT("IR.", SUBSTRING(`cellphone`, 3)) WHERE cellphone LIKE "98%";


--
-- Commit: 85e29ec8e452ef2e0f3fe8c0de58c0f965349c81
--
ALTER TABLE `userpanel_users` ADD INDEX( `type`,`time`,`user` );

--
-- Commit: 98da6363cbd76c263f720f8785dacbd9b02f9659
--
ALTER TABLE `userpanel_users` CHANGE `lastname` `lastname` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

--
--	Commit: d9d1cf4b054aed7be9ab5895f2e65d3aafaf7334
--
INSERT INTO `options` (`name`, `value`, `autoload`) VALUES (
	'packages.userpanel.login_and_reset_password.bruteforce_throttle', '{\"period\":3600, \"total-limit\": 7, \"session-limit\": 5}', '1'
);

--
--
--	Commit:	656be610fcd08e1bd6ab1d9b0df18bcb3fe5bdce
--REPLACE INTO `options` (`name`, `value`, `autoload`) VALUES (
--	'packages.userpanel.login_and_reset_password.bruteforce_throttle', '{\"period\":3600, \"total-limit\": 7, \"session-limit\": 5, \"ignore-ips\":[]}', '1'
--);

--
--	Commit: ef7993afb3607c9b34290e6fce6b5ed28acbff80
--
ALTER TABLE `userpanel_users`
	CHANGE `name` `name` varchar(100) NULL AFTER `id`,
	CHANGE `email` `email` varchar(100) NULL AFTER `lastname`,
	CHANGE `cellphone` `cellphone` varchar(15) NULL AFTER `email`;
