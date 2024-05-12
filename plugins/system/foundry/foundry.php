<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$app = JFactory::getApplication();

// Do not proceed if this is on CLI environment #121
if ($app->isClient('cli')) {
	return;
}

jimport('joomla.plugin.plugin');

$file = JPATH_LIBRARIES . '/foundry/foundry.php';

if (!file_exists($file)) {
	return;
}

require_once($file);

use Foundry\Libraries\Stylesheets;
use Foundry\Libraries\Scripts;

class plgSystemFoundry extends JPlugin
{

	/**
	 * This event is triggered before the framework creates the Head section of the Document. So in this event, we will arrange the order of the css files based on what mentioned in #23
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onBeforeCompileHead()
	{
		$doc = JFactory::getDocument();

		// Only attach stylesheets on relevant documents
		if ($doc->getType() !== 'html') {
			return;
		}

		// Attach stylesheets
		$this->attachStylesheets();
	}

	/**
	 * Attaches stylesheets for foundry
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	private function attachStylesheets()
	{
		$doc = JFactory::getDocument();

		if (!StyleSheets::isRequired()) {
			return false;
		}

		$files = StyleSheets::getFiles();

		if (!$files) {
			return false;
		}

		$baseUrl = JURI::root(true);

		foreach ($files as $type => $cssFiles) {
			if ($cssFiles) {
				$cssFiles = array_unique($cssFiles);

				foreach ($cssFiles as $cssFile) {
					$this->attachFile($cssFile, 'stylesheet');
				}
			}
		}
	}

	/**
	 * Attaches an asset file on the page
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	private function attachFile($file, $type)
	{
		$doc = JFactory::getDocument();
		$baseUrl = JURI::root(true);

		// Do not append the base url if the file contains http:// or https:// or starts with //
		if (strpos($file, 'http://') === false && strpos($file, 'https://') === false && strpos($file, '//') !== 0) {
			$file = $baseUrl . '/' . $file;
		}

		if ($type === 'stylesheet') {
			$doc->addStylesheet($file);
		}

		if ($type === 'javascript') {
			$doc->addScript($file);
		}
	}
}
