<?php
/**
* @version $ Id; ?FILE_NAME ?DATE ?TIME ?AUTHOR $
*/

// Disallow direct access.
defined('ABSPATH') or die("Access denied");

/**
* 
* DESCRIPTION
* 
* @author ??
* @version ??
*/
class CJTTemplatesManagerController extends CJTAjaxController {

	/**
	* 
	* Initialize new object.
	* 
	* @return void
	*/
	public function __construct($controllerInfo) {
		// Initialize parent!
		parent::__construct($controllerInfo);
		// Add actions.
		$this->registryAction('create');
		$this->registryAction('delete');
		$this->registryAction('display');
		$this->registryAction('publish');
		$this->registryAction('trash');
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function createAction() {
		// Display the requested view.
		$this->displayAction();
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function deleteAction() {
		
	}
	
	/**
	* Display templates manager form.
	* 
	*/
	protected function displayAction() {
		// Get view layout!
		$layout = isset($_REQUEST['layout']) ? $_REQUEST['layout'] : 'default';
		// Display the view.
		$this->httpContentType = 'text/html';
		ob_start();
		$this->view->display($layout);
		$this->response =  ob_get_clean(); 
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function publishAction() {
		
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function trashAction() {
		
	}
	
} // End class.