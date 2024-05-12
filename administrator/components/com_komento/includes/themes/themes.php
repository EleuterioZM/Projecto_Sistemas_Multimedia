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

require_once(KOMENTO_LIB . '/template/template.php');

class KomentoThemes extends KomentoTemplate
{
	/**
	 * Outputs the data from a template file.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function output($namespace = null, $args = null)
	{
		// Try to get the template data.
		$template = $this->getFileStructure($namespace);

		// Template
		$this->file	= $template->file;

		// Get the output
		$output = $this->parse($args);

		// Script
		if (JFile::exists($template->script)) {

			$script = KT::script();
			$script->file = $template->script;
			$script->vars = $this->vars;
			$script->scriptTag	= true;

			$doc = JFactory::getDocument();

			if ($doc->getType() == 'html') {
				$jConfig = FH::jconfig();
				$caching = $jConfig->get('caching');
				
				// For joomla caching (conservative or progressive), we cannot rely on the system plugins to inject the scripts
				if ($caching == 1 || $caching == 2) {
					$output .= $script->parse($args);
				}

				if ($caching == 0) {
					KT::addScript($script->parse($args));
				}
			}

			if ($doc->getType() != 'html') {
				$output .= $script->parse($args);
			}
		}

		return $output;
	}

	/**
	 * Template helper
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function html($namespace)
	{
		static $language = false;

		if (!$language) {
			// Load language strings from back end.
			JFactory::getLanguage()->load('com_komento', JPATH_ROOT . '/administrator');
			$language = true;
		}

		$helper	= explode('.', $namespace);
		$helperName	= $helper[0];
		$methodName	= $helper[1];

		$file = dirname(__FILE__) . '/helpers/' . strtolower($helperName) . '.php';

		// Remove the first 2 arguments from the args.
		$args = func_get_args();
		$args = array_splice($args, 1);

		static $libraries = [];

		$class = 'KomentoThemes' . ucfirst($helperName);

		if (!isset($libraries[$class])) {
			include_once($file);

			$libraries[$class] = new $class();
		}

		if (!method_exists($libraries[$class], $methodName)) {
			return false;
		}

		return call_user_func_array([$libraries[$class], $methodName], $args);
	}
}
