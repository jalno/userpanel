
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
