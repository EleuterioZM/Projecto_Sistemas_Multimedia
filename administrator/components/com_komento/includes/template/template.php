<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoTemplate
{
	static $acl = null;
	static $user = null;
	static $tmplMode = null;

	public $vars = [];
	public $file = '';
	public $direction = null;

	protected $config = null;
	protected $app = null;
	protected $input = null;

	public function __construct()
	{
		// Define Joomla's app
		$this->config = KT::config();

		if (!defined('KOMENTO_CLI')) {
			$this->app = JFactory::getApplication();
			$this->input = $this->app->input;
		}

		if (is_null(self::$tmplMode)) {
			self::$tmplMode = $this->input->get('tmpl', '', 'default');
		}

		// Define the current logged in user or guest
		if (is_null(self::$user)) {
			self::$user = KT::user();
		}

		// // Define the current logged in user's access.
		if (is_null(self::$acl)) {
			self::$acl = KT::acl();
		}

		if (is_null(self::$tmplMode)) {
			self::$tmplMode = $this->input->get('tmpl', '', 'default');
		}

		// Get the current access
		$this->my = self::$user;
		$this->access = self::$acl;
		$this->tmpl = self::$tmplMode;
		$this->doc = JFactory::getDocument();
		$this->fd = KT::fd();
	}

	/**
	 * Retrieves the document direction
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getDirection()
	{
		if ($this->direction === null) {
			$this->direction = JFactory::getDocument()->getDirection();
		}

		return $this->direction;
	}

	/**
	 * Returns the metadata of a template file.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getFileStructure($namespace = null)
	{
		$template = new stdClass();
		$template->file = $this->resolve($namespace);
		$template->script = $this->resolve($namespace, 'js');

		return $template;
	}

	/**
	 * Cleaner extract method. All variables that are set in $this->vars would be extracted within this scope only.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function parse($vars = null)
	{
		ob_start();

		// If argument is passed in, we only want to load that into the scope.
		if (!is_array($vars) && !empty($this->vars)) {
			$vars = $this->vars;
		}

		if ($vars) {
			extract($vars);
		}

		// Magic happens here when we include the template file.
		include($this->file);

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Determines if this is a mobile layout
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isMobile()
	{
		$responsive = null;

		if (is_null($responsive)) {
			$responsive = FH::responsive()->isMobile();
		}

		return $responsive;
	}

	/**
	 * Determines if this is a tablet layout
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isTablet()
	{
		static $responsive = null;

		if (is_null($responsive)) {
			$responsive = FH::responsive()->isTablet();
		}
		
		return $responsive;
	}

	/**
	 * Resolves a given namespace
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function resolve($namespace, $extension = 'php')
	{
		if (defined('KOMENTO_CLI')) {
			$defaultPath = KOMENTO_THEMES . '/' . KOMENTO_THEME_BASE . '/' . $file;

			return $defaultPath;
		}

		$parts = explode('/', $namespace);
		$location  = $parts[0];

		// Remove the location
		unset($parts[0]);

		// Glue back the parts
		$file = implode('/', $parts) . '.' . $extension;

		// For admin theme files, we can discard the overrides for now
		if ($location === 'admin') {
			$path = JPATH_ADMINISTRATOR . '/components/com_komento/themes/default/' . $file;

			return $path;
		}

		// Get the base path
		$base = JPATH_ROOT . '/components/com_komento/themes';
		$currentThemePath = $base . '/wireframe';

		// Default path is the current theme's path
		$path = $currentThemePath . '/' . $file;

		// Check for template overrides
		$override = JPATH_ROOT . '/templates/' . FH::getCurrentTemplate() . '/html/com_komento/' . $file;

		if (JFile::exists($override)) {
			$path = $override;
		}

		// If file doesn't exists, we should fall back to the wireframe theme
		if (!JFile::exists($path)) {
			$path = $base . '/wireframe/' . $file;
		}
		
		return $path;
	}

	/**
	 * Assigns a value into the vars data.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function set($key, $value)
	{
		$this->vars[$key] = $value;

		return $this;
	}
}