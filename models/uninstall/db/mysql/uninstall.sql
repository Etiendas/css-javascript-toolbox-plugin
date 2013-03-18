/*
* Delete CJT Tables
*/
DROP TABLE IF EXISTS #__cjtoolbox_authors;
DROP TABLE IF EXISTS #__cjtoolbox_backups;
DROP TABLE IF EXISTS #__cjtoolbox_blocks;
DROP TABLE IF EXISTS #__cjtoolbox_block_pins;
DROP TABLE IF EXISTS #__cjtoolbox_block_templates;
DROP TABLE IF EXISTS #__cjtoolbox_templates;
DROP TABLE IF EXISTS #__cjtoolbox_template_revisions;

/*  Database version number (By this option CJT Plugin detect installation state!) */
DELETE FROM  #__wordpress_options where option_name = 'cjtoolbox_db_version';

/* Clean up installer state */
DELETE FROM #__wordpress_options WHERE option_name = 'state.CJTInstallerModel.operations';
DELETE FROM #__wordpress_usermeta WHERE meta_key = '#__wordpress_settings.CJTInstallerModel.noticeDismissed';

/* Delete Cached Premium Licence keys */
DELETE FROM #__wordpress_options WHERE option_name = 'cache.CJTSetupModel.licenses';

/* Remove metabox order */
DELETE FROM #__wordpress_usermeta where meta_key = 'meta-box-order_cjtoolbox';
DELETE FROM #__wordpress_options where option_name = 'meta-box-order_cjtoolbox';

/* Closed metaboxes */
DELETE FROM #__wordpress_usermeta where meta_key = 'closedpostboxes_cjtoolbox';

/* User Settings */
DELETE FROM #__wordpress_options where option_name = 'cjt-settings.CJTSettingsMetaboxPage';

/*  Posts Metabox blocks */
DELETE FROM #__wordpress_postmeta where meta_key = '__CJT-BLOCK-ID';
DELETE FROM #__wordpress_postmeta where meta_key = '__CJT-BLOCK-STATUS'