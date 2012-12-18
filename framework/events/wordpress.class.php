<?php
/**
* 
*/

/**
* 
*/
class CJTWordpressEvents extends CJTEvents {
	
	/**
	* 
	*/
	const HOOK_ACTION = 'action';

	/**
	* 
	*/
	const HOOK_CUSTOM = 'custom';
	
	/**
	* 
	*/
	const HOOK_FILTER = 'filter';
	
	/**
	* put your comment there...
	* 
	* @param mixed $type
	*/
	protected function prepareEventTypeOptions($event) {
		$type =& $event['type'];
		switch ($type['hookType']) {
			case self::HOOK_ACTION:
				$type['subjectClass'] = 'CJTEEWordpressHookAction';
				$type['file'] = 'action.subject.php';
				$type['observerClass'] = 'CJTWordpressActionHookObserver';
				$type['observerFile'] = 'wordpress-hook-action.observer.php';
			break;
			case self::HOOK_FILTER:
				$type['subjectClass'] = 'CJTEEWordpressHookFilter';
				$type['file'] = 'filter.subject.php';
				$type['observerClass'] = 'CJTWordpressFilterHookObserver';
				$type['observerFile'] = 'wordpress-hook-filter.observer.php';
			break;
		}
		return $event;
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $type
	* @param mixed $params
	*/
	public function trigger($typeName, $params, $typePrefixed = true) {
		$result = false;
		// Get type object!
		$type = $this->parseEventType($typeName, $typePrefixed);
		// Get event type subject!
		$subject = $this->getSubject($type->name);
		// Notify observers!
		if ($subject) {
			$result = call_user_func(array(&$subject, 'callIndirect'), $params);
		}
		return $result;
	}
	
} // End class.