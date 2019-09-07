
---
---	Commit:	1397fa971306a3155582a79652ce5c3ab03daf4b
---
ALTER TABLE `userpanel_users` CHANGE `credit` `credit` INT(11) NOT NULL DEFAULT '0';

--
-- Commit: e0384b52ee070f29103aa85610c3697e6e9af005
--
ALTER TABLE `userpanel_users` ADD `registered_at` INT UNSIGNED NOT NULL AFTER `avatar`; 
