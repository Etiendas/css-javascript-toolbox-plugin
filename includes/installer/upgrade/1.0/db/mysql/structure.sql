/*
* CJT Database Version 1.1 Upgrade structure.
*
* Owner: css-javascript-toolbox.com
* Author: Ahmed Said
* Date: 
* Description: 
*/

/* Upgrade database collation to utf8_general_ci */
ALTER TABLE #__cjtoolbox_backups CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE #__cjtoolbox_blocks CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE #__cjtoolbox_block_pins CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE #__cjtoolbox_authors CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE #__cjtoolbox_block_templates CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE #__cjtoolbox_templates CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE #__cjtoolbox_template_revisions CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

/* Packages holder table */
CREATE TABLE IF NOT EXISTS`#__cjtoolbox_packages` (
	`name` VARCHAR(100) NOT NULL,
	`author` VARCHAR(150) NOT NULL,
	`webSite` VARCHAR(300) NOT NULL,
	`description` TEXT NOT NULL,
	`license` TEXT NOT NULL,
	`readme` TEXT NOT NULL,
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`)
) CHARACTER SET = utf8, COLLATE=utf8_general_ci;

/* Package objects map */
CREATE TABLE IF NOT EXISTS `#__cjtoolbox_package_objects` (
	`packageId` INT UNSIGNED NOT NULL,
	`objectId` INT UNSIGNED NOT NULL,
	`objectType` ENUM('block','template') NOT NULL,
	`relType` ENUM('add','link') NOT NULL DEFAULT 'add',
	PRIMARY KEY (`packageId`, `objectId`, `objectType`)
) CHARACTER SET = utf8, COLLATE=utf8_general_ci