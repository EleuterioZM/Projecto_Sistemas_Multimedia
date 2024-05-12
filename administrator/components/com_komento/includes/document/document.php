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

class KomentoDocument
{
	public $inlineScripts = [];

	public function __construct()
	{
		$this->input = JFactory::getApplication()->input;
		$this->doc = JFactory::getDocument();
	}

	public function start()
	{
		if (FH::isFromAdmin()) {
			KT::initialize('admin');
		}

		$section = FH::isFromAdmin() ? 'admin' : 'site';

		$option = $this->input->get('option', '', 'default');

		// Run initialization codes for javascript side of things.
		if ($option == 'com_komento' && $this->input->get('compile', false, 'bool') != false && KT::isSiteAdmin()) {

			// Determines if we should minify the output.
			$minify = $this->input->get('minify', false, 'bool');

			$compiler = KT::compiler();
			$results = $compiler->compile($section, $minify);

			header('Content-type: text/x-json; UTF-8');
			echo json_encode($results);
			exit;
		}	
	}

	/**
	 * Executes at the end of Komento to render any scripts in the queue on the page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function end($options = [])
	{
		$this->processScripts();
	}

	/**
	 * Allows caller to attach scripts to be added inline on the page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function addInlineScript($script)
	{
		if (!empty($script)) {
			$this->inlineScripts[] = $script;
		}
	}

	/**
	 * Internal method to build scripts to be embedded on the head or
	 * external script files to be added on the head.
	 *
	 * @access	private
	 * @since	4.0.0
	 */
	private function processScripts()
	{
		if (empty($this->inlineScripts)) {
			return;
		}

		// Inline scripts
		$script = KT::script();
		$script->file = KT_MEDIA . '/head.js';
		$script->scriptTag	= true;
		$script->CDATA = true;
		$script->set('contents', implode($this->inlineScripts));
		$inlineScript = $script->parse();

		if ($this->doc->getType() == 'html') {
			$this->doc->addCustomTag($inlineScript);
		}
	}
}