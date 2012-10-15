<?php
/**
* 
*/

// Import dependencies.
cssJSToolbox::import('framework:html:list.php');

/**
* 
*/
class CJTTemplateTypesField extends CJTListField {
	
	/**
	* put your comment there...
	* 
	* @param mixed $name
	* @param mixed $value
	* @param mixed $id
	* @param mixed $classesList
	*/
	public static function getInstance($form, $name, $value, $id = null, $classesList = '') {
		return new CJTTemplateTypesField($form, $name, $value, $id, $classesList)	;
	}
	
	/**
	* put your comment there...
	* 
	*/
	protected function prepareItems() {
		$this->items['']['text'] = '---  ' . cssJSToolbox::getText('Type') . '  ---';
		$this->items['javascript']['text'] = cssJSToolbox::getText('Javascript');
		$this->items['css']['text'] = cssJSToolbox::getText('CSS');	
		$this->items['html']['text'] = cssJSToolbox::getText('HTML');	
		$this->items['php']['text'] = cssJSToolbox::getText('PHP');	
	}
	
} // End class.