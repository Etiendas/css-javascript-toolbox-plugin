<?php
/**
* 
*/

// Disallow direct access.
defined('ABSPATH') or die("Access denied");

/**
* 
*/
class CJTInstaller extends CJTHookableClass {
	
	/**
	* put your comment there...
	* 
	*/
	public function builtinAuthors() {
		// Dependencies!
		cssJSToolbox::import('framework:db:mysql:xtable.inc.php');
		// Add Wordpress author if its not already added!
		$wpAuthor = CJTxTable::getInstance('author')
														->set('id', CJTAuthorTable::WORDPRESS_AUTHOR_ID)
														->load();
		// Make sure built-in (attributes = 1) Wordpress author (id = 1) is there!
		if (!$wpAuthor->get('id') || ($wpAuthor->get('attributes') != 1)) {
			$wpAuthor->setData(array(
				'id' => CJTAuthorTable::WORDPRESS_AUTHOR_ID, 
				'name' => 'Wordpress',
				'attributes' => CJTAuthorTable::FLAG_SYS_AUTHOR)
			)->save(true);
		}
		return $this;
	}
	
	/**
	* put your comment there...
	* 
	* 
	* @CJTInstallerReflection<optional=true>
	*/
	public function builtinTemplates() {
		
		return $this;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function database() {
		// Install Database structure!
		cssJSToolbox::import('framework:installer:dbfile.class.php');
		CJTDBFileInstaller::getInstance(cssJSToolbox::resolvePath('includes:installer:installer:db:mysql:structure.sql'))->exec();
		// Install built-in authors.
		$this->builtinAuthors();
		// Install built-in templates!
		$this->builtinTemplates();
		return $this;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function fileSystem() {
		// Directories to create!
		$directories = array(
			'wp-content/cjt-content',
			'wp-content/cjt-content/templates',
		);
		foreach ($directories as $dir) {
			// Create directory only if not exists!
			$dirPath = ABSPATH . "/{$dir}";
			if (!file_exists($dirPath)) {
				// Make sure we've permission to do!
				if (is_writeable(dirname($dirPath))) {
					if (!mkdir($dirPath, 0775, true)) {
						throw new Exception('Could not create filesystem directory!! CJT Installation halted!');
					}
				}
			}
		}
		return $this;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function finalize() {
		// Nothing here for now!
		return $this;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public static function getInstance() {
		return new CJTInstaller();
	}
	
	/**
	* put your comment there...
	* 
	* 
	* @CJTInstallerReflection<optional=true>
	*/
	public function sampleBlocks() {
		
		
		return $this;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function wordpressTemplates() {
		// Dependencies!
		cssJSToolbox::import(
			'framework:db:mysql:xtable.inc.php', 
			'includes:installer:installer::includes:templates:wordpress-queue.inc.php'
		);
		// Install Wordpress build-in templates (scripts shipped out with Wordpress installation)!
		foreach (array('javascript', 'css') as $type) {
			$wpTemplates = new CJTInstallerWordpressQueueTemplates($type);
			foreach ($wpTemplates as $wpTemplate) {
				// Just install not custom updates needed!
				$wpTemplates->install();
			}
		}
		return $this;
	}

} // End class.