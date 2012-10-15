<?php
/**
* 
*/

// Disllow direct access.
defined('ABSPATH') or die('Access denied');

/**
* 
*/
class CJTTemplatesManagerModel {
	
	/**
	* put your comment there...
	* 
	*/
	public function __construct() {}
	
	/**
	* put your comment there...
	* 
	*/
	public function getItems() {
		//print_r($_GET);
		// Build query.
		$select = 'SELECT t.guid,
												t.name, 
												t.description, 
												t.type,
												a.name authorName, 
												o.user_login ownerName,
												MAX(tt.revisionNo) revisions,
												SUM(tt.isTagged) releases,
												MAX(tt.version) lastVersion';
		$queryBase = $this->getItemsQuery();
		// Paging.
		$itemsPerPage = $this->getItemsPerPage();
		// Get page no#.
		$page = !isset($_GET['paged']) ? 1 : $_GET['paged'];
		// Calculate start offset.
		$start = ($page - 1) * $itemsPerPage;
		$limit = " LIMIT {$start},{$itemsPerPage}";
		// Order.
		if (isset($_GET['orderby'])) {
			$orderBy = " ORDER BY {$_GET['orderby']} {$_GET['order']}";
		}
		// final query.
    $query = "{$select}{$queryBase['from']}{$queryBase['where']}{$queryBase['groupBy']}{$orderBy}{$limit}";
		// Execute our query using MYSQL queue driver.
		$dbDriver = new CJTMYSQLQueueDriver($GLOBALS['wpdb']);
		$result = $dbDriver->select($query);
		return $result;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getItemsPerPage() {
		return 2;	
	}
	
	/**
	* put your comment there...
	* 
	*/
	protected function getItemsQuery() {
		// From clause.
		$query['from'] = ' FROM #__cjtoolbox_templates t
													LEFT JOIN #__cjtoolbox_template_revisions tt ON t.guid = tt.guid
													LEFT JOIN 
													(SELECT name, attributes, guid FROM #__cjtoolbox_authors 
													UNION 
													SELECT CONCAT("Local (", user_login, ")") name, 2 attributes, id guid from #__users) a
													ON t.author = a.guid
													LEFT JOIN #__users o ON t.owner = o.id';
		// Build where clause based on the given filters!
		$filters = array(
			'Templatetypes' => array('table' => 't', 'name' =>'type'), 
			'Authors' => array('table' => 't', 'name' => 'author'),
			'Owners' => array('table' => 't', 'name' => 'owner'),
		);
		foreach ($filters as $name => $field) {
			$filterName = "filter_{$name}";
			// Add filter only if there is a value specified.
			if (!empty($_REQUEST[$filterName])) {
				$where[] = "{$field['table']}.{$field['name']} = '{$_REQUEST[$filterName]}' ";
			}
		}
		if (!empty($where)) {
			$query['where'] = ' WHERE ' .  implode(' AND ', $where);	
		}
		// Group by.
		$query['groupBy'] = ' GROUP BY t.guid';
		return $query;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getItemsTotal() {
		$queryBase = $this->getItemsQuery();
		$select = 'SELECT count(*) Total';
		$query = "{$select}{$queryBase['from']}{$queryBase['where']}{$queryBase['groupBy']}";
		// Get items total.
		$dbDriver = new CJTMYSQLQueueDriver($GLOBALS['wpdb']);
		$result = $dbDriver->select($query);
		return array_shift($result)->Total;
	}
	
} // End class.