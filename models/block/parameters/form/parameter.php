<?php
/**
* 
*/

/**
* 
*/
class CJT_Models_Block_Parameters_Form_Parameter
extends CJT_Models_Block_Parameters_Parameter {
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $description;

	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $groupDescription;

	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $groupName;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $renderer;

	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $values;
	
	/**
	* put your comment there...
	* 
	* @param mixed $name
	* @param mixed $type
	* @param mixed $required
	* @param mixed $defaultValue
	* @param mixed $parent
	* @param mixed $id
	* @return CJT_Models_Block_Parameters_Parameter
	*/
	public function __construct($parameter) {
		// Initialize parent model.
		parent::__construct($parameter);
		// Initialize.
		$this->groupName = isset($parameter['groupName']) ? $parameter['groupName'] : null;
		$this->groupDescription = isset($parameter['groupDescription']) ? $parameter['groupDescription'] : null;
		$this->renderer = isset($parameter['renderer']) ? $parameter['renderer'] : null;
		$this->description = isset($parameter['description']) ? $parameter['description'] : null;
	}

	/**
	* put your comment there...
	* 
	* @param mixed $value
	*/
	public function addValue($value) {
		// Add value model to the values list.
		$this->values[] = $value;
		// Chaining.
		return $this;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getDescription() {
		return $this->description;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getGroupDesciption() {
		return $this->groupDescription;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getGroupName() {
		return $this->groupName;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getOriginalType() {
		return parent::getType();
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getRenderer() {
		return $this->renderer;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getType() {
		return $this->getRenderer();
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getValues() {
		return $this->values;
	}

} // End class.
