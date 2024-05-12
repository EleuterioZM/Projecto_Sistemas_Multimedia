<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoComponents
{
	/**
	* Get Joomla extensions that can commentify!
	* return array
	*/
	public function getAvailableComponents()
	{
		static $components = [];

		if (empty($components)) {
			// find each component folders
			$folders = JFolder::folders(JPATH_ROOT .'/components', 'com_', false, false, ['.svn', 'CVS', '.DS_Store', '__MACOSX', 'com_komento']);

			foreach ($folders as $folder) {
				if (JFile::exists(JPATH_ROOT .  '/components/' . $folder . '/komento_plugin.php')) {
					$components[$folder] = $folder;
				}
			}

			// find in plugins folder
			foreach ($folders as $folder) {
				if (JFile::exists(KOMENTO_ROOT . '/komento_plugins/' . $folder . '.php')) {
					$components[$folder] = $folder;

					// Need to check which ohanah version has venue feature
					if (isset($components['com_ohanah'])) {
						$components['com_ohanahvenue'] = 'com_ohanahvenue';
					}
				}
			}

			// cleaning up duplicates
			$components = array_unique($components);

			// check against the Joomla extension table
			foreach ($components as $key => $component) {
				if ($key != 'com_ohanahvenue' && !JComponentHelper::isEnabled($component)) {
					unset($components[$key]);
				}
			}

		}

		return $components;
	}

	/**
	 * @access	public
	 * @param	string	$optionName	The component element
	 * @return	boolean	True if the component is installed
	 */
	public static function isInstalled($optionName)
	{
		self::_clean($optionName);
		$componentName = substr($optionName, 4);

		if ($componentName && (JFile::exists( JPATH_ADMINISTRATOR . '/components/'.$optionName.'/admin.'.$componentName.'.php') || JFile::exists( JPATH_ROOT.'/components/'.$optionName. '/' .$componentName.'.php'))) {
			return true;
		}

		return false;
	}

	/**
	 * @access	public
	 * @param	string	$optionName	The component element
	 * @return	boolean	True if the component is installed and enabled
	 */
	public static function isEnabled($componentName)
	{
		self::_clean($componentName);

		$sql = KT::sql();

		$sql->select('#__extensions')
			->column('enabled')
			->where('type', 'component')
			->where('element', $componentName);

		return $sql->loadResult();
	}

	private static function _clean(&$componentName)
	{
		$componentName	= preg_replace('/[^A-Z0-9_\.-]/i', '', $componentName);
	}

	public function getSupportedComponents()
	{
		static $supported = [];

		if (empty($supported)) {
			$files = JFolder::files( KOMENTO_PLUGINS, 'com_', false, false, ['.svn', 'CVS', '.DS_Store', '__MACOSX', 'com_sample.php', 'com_sampletemplate']);

			foreach($files as $file) {
				// Remove the .php from the filename
				$tmp = explode('.', $file);

				$supported[] = array_shift($tmp);
			}
		}

		return $supported;
	}
}
