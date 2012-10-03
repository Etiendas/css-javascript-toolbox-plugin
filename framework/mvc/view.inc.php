<?php
/**
* @version view.inc.php
*/

/**
* No direct access.
*/
defined('ABSPATH') or die("Access denied");

/**
* CJT view base class.
*/
abstract class CJTView {
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $model = null;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	private $viewInfo = null;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	private $views = array();
	
	/**
	* put your comment there...
	* 
	*/
	public function __construct($info) {
		$this->viewInfo = $info;
	}
	
	/**
	* Create view object.
	* 
	* @param mixed $view
	*/
	public static function create($view) {
		return CJTController::getView($view);
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getModel() {
		return $this->model;	
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $destination
	*/
	public function getPath($destination) {
		return self::getViewPath($this->viewInfo['name'], $destination);
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $name
	*/
	public function getTemplate($name, $params = array(), $dir = 'tmpl', $extension = '.html.tmpl') {
		// Get template content into variable.
		ob_start();
		// Push params into the local scope.
		extract($params);
		// Templates collected under the view/tmpl directory.
		$templateFile = $this->getPath("{$dir}/{$name}{$extension}");
		require $templateFile;
		$template = ob_get_clean();
		return $template;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getURI($destination) {
		return self::getViewURI($this->viewInfo['name'], $destination);
	}	
	
	/**
	* put your comment there...
	* 
	* @param mixed $view
	* @param mixed $destination
	*/
	public static function getViewPath($view, $destination) {
		$viewPath = CJTOOLBOX_VIEWS_PATH . "/{$view}";
		$destination = $destination ? "/{$destination}" : '';
		return "{$viewPath}{$destination}";
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $file
	*/
	public static function getViewURI($view, $destination) {
		$viewURI = CJTOOLBOX_VIEWS_URL . "/{$view}/public";
		return "{$viewURI}/{$destination}";
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $file
	* @param mixed $destination
	*/
	public static function getURIFromViewFile($file, $destination) {
		$path = dirname($file);
		$viewPath = str_replace((CJTOOLBOX_VIEWS_PATH . '/'), '', $path);
		return self::getViewURI($viewPath, $destination);
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function importHelper($name, $helperDirectory = 'helpers') {
		$helperPath = "{$this->viewInfo['path']}/{$helperDirectory}/{$name}.inc.php";
		require_once $helperPath;
	}
	
	/**
	* 
	*/
	public static function import($path) {
		$viewInfo = CJTController::getViewInfo($path);
		// Import view.
		require_once $viewInfo['viewFile'];
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $model
	*/
	public function setModel($model) {
		$this->model = $model;
	}
	
	/**
	* put your comment there...
	* 
	*/
	protected static function useScripts() {
		wp_enqueue_script('Just Load Default Scripts, this works great!!!!');
		// Accept variable number of args of script list.
		$scripts = func_get_args();
		$stack =& $GLOBALS['wp_scripts']->registered;
		if (!$scripts) {
			throw new Exception('CJTView::useScripts method must has at least on script parameter passed!');
		}
		// Script name Reg Exp pattern.
		$nameExp = '/\:?(\{((\w+)-)\})?([\w\-\.]+)(\(.+\))?$/';
		// For every script, Enqueue and localize, only if localization file found/exists.
		foreach ($scripts as $script) {
			// Get script name.
			preg_match($nameExp, $script, $scriptObject);
			// [[2]Prefix], [4] name. Prefix may be not presented.
			$name = "{$scriptObject[2]}{$scriptObject[4]}";
			if (!$stack[$name]) {
				// Any JS lib file should named the same as the parent folder with the extension added.
				$libPath = ":{$scriptObject[4]}:{$scriptObject[4]}";
				// Pass virtual path to getURI and resolvePath to
				// get JS file URI and localization file path.
				$jsFile = cssJSToolbox::getURI(preg_replace($nameExp, "{$libPath}.js", $script));
				$localizationFile = cssJSToolbox::resolvePath(preg_replace($nameExp, "{$libPath}.localization.php", $script));
				// Enqueue script file.
				wp_enqueue_script($name, $jsFile);
				// Set script parameters.
				if (preg_match_all('/(\w+)=(\w+)/', $scriptObject[5], $params, PREG_SET_ORDER) ) {
					// Set parameters.
					foreach ($params as $param) {
						$stack[$name]->cjt[$param[1]] = $param[2];
					}
					// Initialize CJT for the script data object.
					// This object caryy other informations so that the other
					// Plugin parts/components can use it to know how script file work.
					$stack[$name]->cjt = (object) $stack[$name]->cjt;
				}
				
				// If localization file exists localize JS.
				if (file_exists($localizationFile)) {
					// Get localization text array.
					$localization = require $localizationFile;
					// Object name is the script name with .'s and -'s stripped.
					// Capitalize first char after each - or . and append I18N postfix.
					$objectName = str_replace(' ', '', ucwords(str_replace(array('.', '-'), ' ', "{$name}I18N")));
					// Ask Wordpress to localize the script file.
					wp_localize_script($name, $objectName, $localization);
				}
			}
			// Enqueue already registered scripts!
			else {
				wp_enqueue_script($name, $jsFile);	
			}
		}
	}
	
	/**
	* put your comment there...
	* 
	*/
	public static function useStyles() {
		wp_enqueue_style('Just Load Default Styles, this works great!!!!');
		// Accept variable number of args of script list.
		$styles = func_get_args();
		if (!$styles) {
			throw new Exception('CJTView::useStyles method must has at least on script parameter passed!');
		}
		// Script name Reg Exp pattern.
		$nameExp = '/\:?(\{((\w+)-)\})?([\w\-\.]+)$/';
		// For every script, Enqueue and localize, only if localization file found/exists.
		foreach ($styles as $style) {
			// Get script name.
			preg_match($nameExp, $style, $styleObject);
			// [[2]Prefix], [4] name. Prefix may be not presented.
			$name = "{$styleObject[2]}{$styleObject[4]}";
			if (!$GLOBALS['wp_styles']->registered[$name]) {
				// Make all enqueued styles names unique from enqueued scripts.
				// This is useful when merging styles & scripts is required.
				$name = "CSS-{$name}";
				// Any JS lib file should named the same as the parent folder with the extension added.
				$libPath = ":{$styleObject[4]}";
				// Get css file URI.
				$cssFile = cssJSToolbox::getURI(preg_replace($nameExp, "{$libPath}.css", $style));
				// Register + Enqueue style.
				wp_enqueue_style($name, $cssFile);
			}
			else {
				// Enqueue already registered styles.
				wp_enqueue_style($name, $cssFile);	
			}
		}
	}
	
} // End class.