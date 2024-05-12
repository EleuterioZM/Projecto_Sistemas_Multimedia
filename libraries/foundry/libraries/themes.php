<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
namespace Foundry\Libraries;

defined('_JEXEC') or die('Unauthorized Access');

class Themes
{
	// Access to foundry library
	public $fd = null;
	public $vars = [];

	public function __construct($fd)
	{
		$this->fd = $fd;
	}

	/**
	 * Resolves a given namespace to the appropriate path
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function resolve($namespace = '', $extension = 'php')
	{
		static $cache = [];

		$extension = '.' . $extension;
		$index = $this->fd->getComponentName() . '/' . $namespace . $extension;

		if (!isset($cache[$index])) {

			// It needs to be in this ordering
			$paths = (object) [
				'default' => FD_THEMES . '/' . $namespace,
				'component' => $this->fd->getOverridePath() . '/' . $namespace,
				'override' => FD_THEMES_OVERRIDES . '/' . $namespace
			];

			// For modules / extensions which solely relies on theme files from foundry, we don't want to check for component / template overrides since we know it will not have them there
			$isFoundry = $this->fd->getComponentName() === 'com_foundry';

			// Check for component overrides
			$path = $paths->component . $extension;

			if (!$isFoundry && file_exists($path)) {
				$cache[$index] = $paths->component;

				return $cache[$index];
			}

			// Check for Joomla template overrides
			$path = $paths->override . $extension;

			if (!$isFoundry && file_exists($path)) {
				$cache[$index] = $paths->override;

				return $cache[$index];
			}

			$cache[$index] = $paths->default;
		}

		return $cache[$index];
	}

	/**
	 * Sets a variable on the template
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	/**
	 * New method to display contents from template files
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function output($namespace, $vars = [], $extension = 'php')
	{
		$path = $this->resolve($namespace, $extension);

		// Extract template variables
		if (!empty($vars)) {
			extract($vars);
		}

		if (isset($this->vars)) {
			extract($this->vars);
		}

		$templateFile = $path . '.' . $extension;
		$templateContent = '';

		ob_start();
			include($templateFile);
			$templateContent = ob_get_contents();
		ob_end_clean();

		// Embed script within template
		$scriptFile = $path . '.js';

		$scriptFileExists = file_exists($scriptFile);


		if ($scriptFileExists) {

			ob_start();
				echo '<script type="text/javascript">';
				include($scriptFile);
				echo '</script>';
				$scriptContent = ob_get_contents();
			ob_end_clean();

			// \EB::scripts()->add($scriptContent);
			$templateContent .= $scriptContent;
		}

		return $templateContent;
	}
}
