<?php
/**
* 
*/

// Disallow direct access.
defined('ABSPATH') or die("Access denied");

/**
* 
*/
abstract class CJTUpgradeNonTabledVersions extends CJTHookableClass {
	
	/**
	* 
	*/
	const BLOCKS_POINTER = 'cjtoolbox_data';
	
	/**
	* 
	*/
	const TEMPLATES_TABLE = '#__cjtoolbox_cjdata';
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $blocks;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $templates;
	
	/**
	* put your comment there...
	* 
	*/
	public function __construct() {
		// Import dependencies!
		cssJSToolbox::import('includes:installer:upgrade:block.class.php', 'includes:installer:upgrade:template.class.php');
		// Load blocks into installer iterator!
		$this->blocks = $this->getBlocksIterator(get_option(self::BLOCKS_POINTER));
		// Load templates into templates iterator!
		$driver = cssJSToolbox::getInstance()->getDBDriver();
		$templates = $driver->select('SELECT title as `name`, type, `code` FROM ' . self::TEMPLATES_TABLE . ';', ARRAY_A);
		$this->templates = new CJTInstallerTemplate($templates);
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function blocks() {
		// Upgrade all blocks.
		foreach ($this->blocks as $block) {
			// No customization neede, just upgrade!
			$this->blocks->upgrade();
		}
		// Process DB Driver queue!
		$this->blocks->model->save();
		// Version 0.2 and 0.3 use wrong algorithm for saving blocks order!
		// Every version  has its owen blocks order however the orders is not used to output the blocks!
		// Version 2.0 still has argument about this but for simplification sake and for time save
		// We just get orders from current runnign user! This is not 100% correct but we just need to advice 
		// to install the Plugin using the same author you need to inherits the order from!
		$page = CJTPlugin::PLUGIN_REQUEST_ID;
		// Get current logged-in user order!
		$order = get_user_option("meta-box-order_settings_page_{$page}");
		// Save it into GLOBAL/SHARED option to centralized all users!
		$this->blocks->model->setOrder($order);
		return true;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function finalize() {
		// Delete old blocks data!
		delete_option(self::BLOCKS_POINTER);
		// Drop cjdata table as is it no longed needed!
		$driver = cssJSToolbox::getInstance()->getDBDriver();
		$driver->exec('DROP TABLE #__cjtoolbox_cjdata;');
		// Update version number.
		update_option(CJTPlugin::DB_VERSION_OPTION_NAME, CJTPlugin::DB_VERSION);
		return $this;
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $blocks
	*/
	protected abstract function getBlocksIterator($blocks);
	
	/**
	* put your comment there...
	* 
	*/	
	public function templates() {
		// Tranform templates to the new table!
		foreach ($this->templates as $template) {
			$this->templates->upgrade();
		}
		// Chaining!
		return $this;
	}
	
} // End class.