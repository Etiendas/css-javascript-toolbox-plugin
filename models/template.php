<?php
/**
* 
*/

// Disllow direct access.
defined('ABSPATH') or die('Access denied');

/**
* 
*/
class CJTTemplateModel {
	
	/** */
	const TEMPLATES_DIR = 'wp-content/cjt-content/templates';
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	public $inputs;
	
	/**
	* put your comment there...
	* 
	*/
	public function getItem() {
		$tManager = CJTModel::create('templates-manager');
		$query = $tManager->getItemsQuery();
		$query['select'] = 'SELECT t.id, 
																					t.name, 
																					t.type, 
																					t.description, 
																					t.creationDate, 
																					t.keywords,
																					r.dateCreated lastModified,
																					t.state, 
																					a.name author,
																					r.changeLog,
																					r.version,
																					r.file,
																					r.state developmentState';
		$query['where'] .= " AND t.id = {$this->inputs['id']}";
		$query = "{$query['select']} {$query['from']} {$query['where']}";
		$item = array_shift(cssJSToolbox::getInstance()->getDBDriver()->select($query));
		// Get code.
		$item->code = file_get_contents(ABSPATH . "/{$item->file}");
		// Return PHP StdClass object.
		return $item;
	}
	
	/**
	* Query Block based on the passed paramaters.
	* 
	*/
	public function getTemplateBy() {
		// import dependencies.
		cssJSToolbox::import('framework:db:mysql:xtable.inc.php');
		return CJTxTable::getInstance('template')
		->set($this->inputs['filter']['field'], $this->inputs['filter']['value'])
		->load(array($this->inputs['filter']['field']))
		->getData();
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function save() {
		// import libraries.
		cssJSToolbox::import('framework:db:mysql:xtable.inc.php');
		// Initialize vars.
		$currentUser = get_userdata(get_current_user_id());
		$dbDriver = cssJSToolbox::getInstance()->getDBDriver();
		// Load template data is exists (load), change values (setData).
		$template = CJTxTable::getInstance('template')->set('id', $this->inputs['item']['template']['id'])
																																																			->load()
																																																			->setData($this->inputs['item']['template'])
																																																			->setQueueName();
		$templateDirName = $template->get('queueName');
		$templateDir = self::TEMPLATES_DIR . "/{$templateDirName}";
		if (!$template->get('id')) { // Add new Template
			// Search for author for the current local Wordpress user. 
			// If not created in the Authors table create one! If created get the ID!
			$author = CJTxTable::getInstance('author', null, "SELECT * FROM #__cjtoolbox_authors 
													 WHERE name = '{$currentUser->user_login}'
													 AND (attributes & 2);");
			// Create Wordpress user in Authors table.
			if (!$author->get('id')) {
				$author->setData(array(
						'name' => $currentUser->user_login,
						'email' => $currentUser->user_email,
						'url' => $currentUser->user_url,
						'attributes' => 2 // 2 For LOCAL AUTHORS!!
				))->save();
			}
			// Set template data
			$template->set('ownerId', $currentUser->ID)
														->set('creationDate', current_time('mysql'))
														->set('authorId', $author->get('id'));
		}
		// Make sure directory created even if updating templats.
		// This case is needed when revisioned built-in templates (e.g jquery)!!
		if (!file_exists(ABSPATH . "/{$templateDir}")) {
			mkdir(ABSPATH . "/{$templateDir}", 0755);
		}
		// Save template.
		if (!$template->save()->get('id')) {
			throw new Exception('Error saving template into database!!!');
		}
		/// Always create new revision. ///
		// Get last used Revision Number!
		$lastRevisionNo = ((int) (array_shift($dbDriver->select("SELECT max(revisionNo) revisionNo
																											FROM #__cjtoolbox_template_revisions
																											WHERE templateId = {$template->get('id')}"))->revisionNo));
		// Checki if there is a previous revision and if there is changes!!
		$lastRevision = CJTxTable::getInstance('template-revision', null, 
			"SELECT * 
			 FROM #__cjtoolbox_template_revisions 
			 WHERE templateId = {$template->get('id')} AND revisionNo = {$lastRevisionNo}"
		);
		// Only add Revision if it has any field changed!
		$revisionData = $this->inputs['item']['revision'];
		$lastRevisionCode = $lastRevision->get('id') ? file_get_contents(ABSPATH . "/{$lastRevision->get('file')}") : null;
		$revisionHasChanges 		= $lastRevisionCode !== $revisionData['code']
																											|| $lastRevision->get('state') !== $revisionData['state']
																											|| $lastRevision->get('version') !== $revisionData['version']
																											|| $lastRevision->get('changeLog') !== $revisionData['changeLog'];
		if ($revisionHasChanges) {
			// New added revision number!
			$revisionNo = $lastRevisionNo + 1;
			// Get template revision extension.
			$extension = cssJSToolbox::$config->templates->types[$template->get('type')]->extension;
			// Remove fields that not part of the revision table!
			$code = $this->inputs['item']['revision']['code'];
			unset($this->inputs['item']['revision']['code']);
			// Creae revision object.
			$revision = CJTxTable::getInstance('template-revision')
			->setData($this->inputs['item']['revision'])
			->set('templateId', $template->get('id'))
			->set('revisionNo', $revisionNo)
			->set('dateCreated', current_time('mysql'))
			->set('attributes', CJTTemplateRevisionTable::FLAG_LAST_REVISION) // Mark as last revision.
			->set('file', "{$templateDir}/{$revisionNo}.{$extension}")
			->save();
			// Write revision content into Disk File!
			 file_put_contents(ABSPATH . "/{$revision->get('file')}", $code);
			// Remove  FLAG_LAST_REVISION flag from last revision so
			// our new revision will be the last one!!
			if ($lastRevision->get('id')) {
				$flagsOff = $lastRevision->get('attributes') & (~CJTTemplateRevisionTable::FLAG_LAST_REVISION);
				$lastRevision->set('attributes', $flagsOff)->save();
			}
		}
		else {
			// Return last Revision!
			$revision = $lastRevision;
		}
		// Return revision object.
		return $revision->getData();
	}
	
} // End class.