<?php
/**
* 
*/

// Disallow direct access.
defined('ABSPATH') or die("Access denied");

/**
* 
*/
class CJTManageAccessPoint extends CJTPageAccessPoint {

	/**
	* put your comment there...
	* 
	*/
	public function __construct() {
		// Initialize Access Point base!
		parent::__construct();
		// Set access point name!
		$this->name = 'manage';
	}

	/**
	* put your comment there...
	* 
	*/
	protected function doListen() {
		// Add menu pages.
		add_action('admin_menu', array(&$this, 'menu'));
		// Initialize installer access points!
		parent::doListen();
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function menu() {
		// Blocks Manager page! The only Wordpress menu item we've.
		$pageHookId= add_menu_page(
			cssJSToolbox::getText('CSS & Javascript Toolbox'), 
			cssJSToolbox::getText('CSS & Javascript Toolbox'), 
			'manage_options', 
			CJTPlugin::PLUGIN_REQUEST_ID, 
			array(&$this->controller, '_doAction')
		);
		// Process request if installed!
		add_action("load-{$pageHookId}", array($this, 'getPage'));
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function processRequest() {
		// Instantiate controller! & set the action, display manage page!
		$this->route();
	}
	
} // End class.

// Hookable!
CJTManageAccessPoint::define('CJTManageAccessPoint', array('hookType' => CJTWordpressEvents::HOOK_FILTER));