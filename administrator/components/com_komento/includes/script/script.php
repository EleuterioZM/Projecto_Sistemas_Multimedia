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

class KomentoScript extends KomentoTemplate
{
	public $scriptTag = false;
	public $openingTag = '<script>';
	public $closingTag = '</script>';

	public $CDATA = false;
	public $safeExecution = false;

	public $header = '';
	public $footer = '';

	/**
	 * Attaches files to the header.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function attach($path = null)
	{
		// Keep original file value
		if (!is_null($path)) {
			$_file = $this->file;
			$this->file = $this->resolve($path, 'js');
		}

		// Keep current value
		$_scriptTag = $this->scriptTag;
		$_CDATA = $this->CDATA;

		// Reset to false
		$this->scriptTag = false;
		$this->CDATA = false;

		$output = $this->parse();

		KT::document()->addInlineScript($output);

		// Restore current value
		$this->scriptTag = $_scriptTag;
		$this->CDATA = $_CDATA;

		// Restore original file value
		if (!is_null($path)) {
			$this->file = $_file;
		}
	}

	/**
	 * Returns the metadata of a template file.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getFileStructure($namespace = null)
	{
		// Explode the namespace
		$parts = explode(':', $namespace);

		// Legacy fixes.
		$hasProtocol = count($parts) > 1 ? true : false;

		if (!$hasProtocol) {
			$namespace = 'themes:/' . $namespace;
		}

		$template = new stdClass();
		$template->file = $this->resolve($namespace . '.' . 'js');
		$template->script = $this->resolve($namespace . '.js');

		return $template;
	}

	/**
	 * Allows inclusion of scripts within another script
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function output($file = null, $vars = null)
	{
		$template = $this->getFileStructure($file);

		// Ensure that the script file exists
		if (!JFile::exists($template->script)) {
			return;
		}

		$this->file = $template->script;

		$output = $this->parse();

		return $output;
	}

	/**
	 * Overrides parent's parse behavior
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function parse($vars = null)
	{
		// Pass to the parent to process the theme file
		$vars = parent::parse($vars);
		$script	= $this->header . $vars . $this->footer;

		// Do not reveal root folder path.
		$file = str_ireplace(JPATH_ROOT, '', $this->file);

		// Replace \ with / to avoid javascript syntax errors.
		$file = str_ireplace('\\', '/', $file);

		$cdata = $this->CDATA;

		$scriptTag = $this->scriptTag;
		$safeExecution = $this->safeExecution;

ob_start();
include(KT_MEDIA . '/scripts/template.php');
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}
}
