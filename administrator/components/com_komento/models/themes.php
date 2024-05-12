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

class KomentoModelThemes extends KomentoModel
{
	protected $element = 'themes';

	/**
	 * Retrieves a list of installed themes on the site
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getThemes()
	{
		$path = KOMENTO_THEMES;

		$result	= JFolder::folders($path, '.', false, true, $exclude = array('.svn', 'CVS', '.', '.DS_Store'), array('.ignore'));
		
		$themes	= array();

		$config	= KT::config();

		// Cleanup output
		foreach ($result as $item) {
			$name = basename($item);

			$obj = KT::getThemeObject($name);

			if ($obj) {
				$obj->default = false;

				if ($config->get('layout_theme') == $obj->element) {
					$obj->default = true;
				}

				$themes[]	= $obj;
			}
			
		}

		return $themes;
	}

	/**
	 * Retrieves information about a single file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getFile($filePath, $element, $contents = false)
	{
		$path = $this->getThemePath($element);
		$filePath = base64_decode($filePath);
		$filePath = $path . $filePath;

		$file = new stdClass();
		$file->element = $element;
		$file->title = str_ireplace($path, '', $filePath);
		$file->absolute = $filePath;
		$file->relative = str_ireplace($path, '', $filePath);
		$file->id = base64_encode($file->relative);

		$file->override = $this->getOverridePath($file->relative);
		$file->modified = JFile::exists($file->override);
		$file->contents = '';

		if ($contents) {
			$location = $file->modified ? $file->override : $file->absolute;
			$file->contents = file_get_contents($location);
		}

		return $file;
	}

	/**
	 * add override file's notes
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function addFileNotes($file, $notes = '')
	{
		$themeConfig = KT::table('Configs');
		$themeConfig->load(['name' => 'themeoverride']);

		$id = $file->id;

		$obj = new stdClass();
		$obj->file_id = $file->override;
		$obj->notes = $notes;

		$data = [];
		if ($themeConfig->name) {
			// load all the notes first.
			$data = json_decode($themeConfig->params);
			$data = KT::makeArray($data);
		}

		$data[$id] = $obj;

		// now lets update into db.
		$themeConfig->params = json_encode($data);

		$state = $themeConfig->store('themeoverride');
		return $state;
	}

	/**
	 * remove override file's notes
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function removeFileNotes($file)
	{
		$themeConfig = KT::table('Configs');
		$themeConfig->load(['name' => 'themeoverride']);

		$id = $file->id;

		$data = [];
		if ($themeConfig->name) {
			// load all the notes first.
			$data = json_decode($themeConfig->params);
			$data = KT::makeArray($data);
		}

		$state = true;

		if ($data && isset($data[$id])) {

			// remove notes
			unset($data[$id]);

			// now lets update into db.
			$themeConfig->params = json_encode($data);
			$state = $themeConfig->store('themeoverride');
		}

		return $state;
	}

	/**
	 * add override file's notes
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getFileNotes($fileId)
	{
		$themeConfig = KT::table('Configs');
		$themeConfig->load(['name' => 'themeoverride']);

		$notes = '';

		if ($themeConfig->name) {
			// load all the notes first.
			$data = json_decode($themeConfig->params);

			$data = KT::makeArray($data);

			if ($data && isset($data[$fileId])) {
				$file = $data[$fileId];
				$notes = $file['notes'];
			}
		}

		return $notes;
	}

	/**
	 * Retrieves a list of files
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getFiles($element)
	{
		$path = $this->getThemePath($element);

		// We should exclude emails since we already have a email template editor
		$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'emails', 'images', 'styleguide', 'config');

		// Get a list of folers first
		$folders = JFolder::folders($path, '.', false, true, $exclude);
		$files = array();
		$filter = "^.*\.(php|js)$";

		foreach ($folders as $folder) {

			$group = basename($folder);

			$items = JFolder::files($folder, $filter, true, true, array('', '.svn', 'CVS', '.DS_Store', '__MACOSX', '.less', '.json', '_cache', '_log', 'index.html'));

			if (empty($items)) {
				continue;
			}

			if (!isset($files[$group])) {
				$files[$group] = array();
			}

			foreach ($items as $item) {
				$item = FH::normalizeSeparator($item);

				$item = str_ireplace($path, '', $item);
				$item = base64_encode($item);

				$file = $this->getFile($item, $element);

				$files[$group][] = $file;
			}
		}

		return $files;
	}

	/**
	 * Generates the path to the theme
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getThemePath($element)
	{
		$path = KOMENTO_THEMES . '/' . $element;
		$path = FH::normalizeSeparator($path);

		return $path;
	}

	/**
	 * Generates the override path for a theme file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getOverridePath($relativePath)
	{
		$template = FH::getCurrentTemplate();

		$path = JPATH_ROOT . '/templates/' . $template . '/html/com_komento/' . ltrim($relativePath, '/');

		return $path;
	}

	/**
	 * Retrieves the current site template
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getCurrentTemplate()
	{

		static $_cache = null;

		if (is_null($_cache)) {
			$db = KT::db();

			$query = 'SELECT ' . $db->nameQuote('template') . ' FROM ' . $db->nameQuote('#__template_styles');
			$query .= ' WHERE ' . $db->nameQuote('home') . '!=' . $db->Quote(0);
			$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

			$db->setQuery($query);

			$_cache = $db->loadResult();
		}

		return $_cache;
	}

	/**
	 * Allows caller to write contents 
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function write($file, $contents)
	{
		$state = JFile::write($file->override, $contents);
		
		return $state;
	}

	/**
	 * Allows caller to revert an overriden theme file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function revert($file)
	{
		$exists = JFile::exists($file->override);
		
		if (!$exists) {
			return false;
		}

		$state = JFile::delete($file->override);
		
		return $state;
	}

	/**
	 * Retrieve the current site css template path
	 *
	 * @since   3.0.13
	 * @access  public
	 */
	public function getCustomCssTemplatePath()
	{
		$template = FH::getCurrentTemplate();
		
		$path = JPATH_ROOT . '/templates/' . $template . '/html/com_komento/css/custom.css';

		return $path;
	}	
}