<?php
/*
Plugin Name: CSS & JavaScript Toolbox
Plugin URI: http://wipeoutmedia.com/wordpress-plugins/css-javascript-toolbox
Description: WordPress plugin to easily add custom CSS and JavaScript to individual pages
Version: V6
Author: Wipeout Media 
Author URI: http://wipeoutmedia.com/wordpress-plugins/css-javascript-toolbox

Copyright (c) 2011, Wipeout Media.
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Disallow direct access.
defined('ABSPATH') or die("Access denied");

/** CJT version */
define('CJTOOLBOX_VERSION', '6.0');

/** CJT Name */
define('CJTOOLBOX_NAME', plugin_basename(dirname(__FILE__)));

/** CJT Text Domain used for localize texts */
define('CJTOOLBOX_TEXT_DOMAIN', CJTOOLBOX_NAME);

/** CJT Absoulte path */
define('CJTOOLBOX_PATH', dirname(__FILE__));

/** Dont use!! @deprecated */
define('CJTOOLBOX_INCLUDE_PATH', CJTOOLBOX_PATH . '/framework'); 

/** Frmaework path */
define('CJTOOLBOX_FRAMEWORK', CJTOOLBOX_INCLUDE_PATH); // Alias to include pa

// Import dependencies
require_once CJTOOLBOX_FRAMEWORK . '/types/attributes.class.php';
require_once CJTOOLBOX_FRAMEWORK . '/php/includes.class.php';
require_once CJTOOLBOX_FRAMEWORK . '/events/events-engine.class.php';
require_once CJTOOLBOX_FRAMEWORK . '/events/wordpress/events-engine.class.php';

/**
* CJT main controller -- represent Wordpress Plugin interface.
* 
* @package CJT
* @author Ahmed Said
* @version 6
*/
class CJTPlugin extends CJTWPEE {
	
	/**
	* Target controller object.
	* 
	* @var CJTController
	*/
	public $controller;
	
	/**
	* put your comment there...
	* 
	* @var CJTPlugin
	*/
	protected static $instance;
	
	/**
	* put your comment there...
	* 
	* @access public
	* @var CJTPluginOnPrePrcoessRequestWPAction
	*/
	protected $onpreprocessrequest;
	
	/**
	* Triggered when the request is actually served by CJT Plugin.
	* 
	* @access public
	* @var CJTPluginOnPrcoessRequestWPAction
	*/
	protected $onprocessrequest = array(
		'parameters' => array('itsAjaxRequest', 'itsCJTRequest', 'isProcessed')
	);
	
	/**
	* Triggered when the request is about to be checked!
	* 
	* @access public
	* @var CJTPluginOnPrcoessRequestCheckWPAction
	*/
	protected $onprocessrequestcheck = array(
		'parameters' => array('itsAjaxRequest', 'itsCJTRequest')
	);
	
	/**
	* put your comment there...
	* 
	*/
	protected function __construct() {
		// Configure CJT Events Engine!
		parent::$paths['subjects']['core'] = CJTOOLBOX_FRAMEWORK . '/events/subjects';
		parent::$paths['observers']['core'] = CJTOOLBOX_FRAMEWORK . '/events/observers';
		parent::__construct();
		// Configure bootstrap point!
		add_action('plugins_loaded', array($this, 'bootstrap'));
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function addMenuPages() {
		$menuTitle = __('CSS & JavaScript Toolbox', CJTOOLBOX_TEXT_DOMAIN);
		// Blocks Manager page! The only Wordpress menu item we've.
		// All the other forms/grids (e.g templates-manager, etc...) is liked through this pages.
		add_options_page($menuTitle, $menuTitle, 10, 'cjtoolbox', array(&$this->controller, '_doAction'));
	}

	/**
	* put your comment there...
	* 
	*/
	public function bootstrap() {
		// Process request
		$this->preProcessRequest();
		$this->processRequest();
		// Add menu pages!
		if (is_admin()) {
			add_action('admin_menu', array(&$this, 'addMenuPages'));
		}
	}
	
	/**
	* put your comment there...
	* 
	*/
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new CJTPlugin();
		}
		return self::$instance;
	}
	
	/**
	* put your comment there...
	* 
	*/
	protected function preProcessRequest() {
		$this->onpreprocessrequest();
		// Imporsenate request if it for edit post/page.
		if (strpos($_SERVER['REQUEST_URI'], 'wp-admin/post.php') !== false) {
			$_REQUEST['page'] = 'cjtoolbox';
			$_REQUEST['controller'] = 'metabox';
		}	
	}
	
	/**
	* Check if the request is for CJT controller,
	* if so create constroller object to server the request.
	* 
	* @return void
	*/
	protected function processRequest() {
		/// Tri-Cases to run a controller! ///
		// case #1. We always have the Coupling controller running unless its ajax request!
		// case #2. If the $_REQUEST['page] == 'cjtoolbox' we'll run another controller!
		// case #3. Edit Post/Page page for metabox!!
		$itsAjaxRequest = (strpos($_SERVER['REQUEST_URI'], '/wp-admin/admin-ajax.php') !== false);
		$itsCJTRequest = isset($_REQUEST['page']) && ($_REQUEST['page'] == 'cjtoolbox');
		$this->onprocessrequestcheck($itsAjaxRequest, $itsCJTRequest);
		if ($isProcessed = (!$itsAjaxRequest || $itsCJTRequest)) {
			// Import CJT Core class,
			require_once 'css-js-toolbox.class.php';
			cssJSToolbox::getInstance();
			// The following dependencies is always needed!
			require_once CJTOOLBOX_MVC_FRAMEWOK . '/model.inc.php';
			require_once CJTOOLBOX_MVC_FRAMEWOK . '/controller.inc.php';
			// run the coupling!
			if (!$itsAjaxRequest) {
				CJTController::getInstance('blocks-coupling');
			}
			// Dispath the other controller.
			if ($itsCJTRequest) {
				//CJTView shouldnt be alwaus involved but for now do it!!
				require_once CJTOOLBOX_MVC_FRAMEWOK . '/view.inc.php';
				// Default controller is "blocks" controller!
				$controller = isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'blocks';
				$this->controller = CJTController::getInstance($controller);
			}
		}
		$this->onprocessrequest($itsAjaxRequest, $itsCJTRequest, $isProcessed);
	}
	
}// End Class

// Let's Go!
CJTPlugin::getInstance();