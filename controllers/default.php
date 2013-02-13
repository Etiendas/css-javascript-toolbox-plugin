<?php
/**
*
*  
*/

/**
* The controller is the future controller that
* will be used when there is no action neeeded
* to be talked when outputs the view!
* 
* @author CJT Team
* @since version 6.0
*/
class CJTDefaultController extends CJTController {
	
	/**
	* put your comment there...
	* 
	*/
	public function displayAction() {
		echo parent::displayAction();
	}
	
	/**
	* Uninstall CJT Plugin
	* installaion flags, setup-flags
	* and user data!
	* 
	* @return void
	*/
	public function uninstallAction() {
		// Initializing!
		$model =& CJTModel::getInstance('uninstall');
		// Uninstall everything!
		die('uninstall');
	}
	
} // End class.
