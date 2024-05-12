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

require_once(__DIR__ . '/controller.php');

class KomentoControllerInstallSql extends KomentoSetupController
{
	/**
	 * Perform installation of SQL queries
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function execute()
	{
		// Get the temporary path from the server.
		$tmpPath = $this->input->get('path', '', 'default');

		// There should be a queries.zip archive in the archive.
		$tmpQueriesPath = $tmpPath . '/queries.zip';

		// Extract the queries
		$path = $tmpPath . '/queries';

		// If on development mode, skip this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_KOMENTO_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Check if this folder exists.
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}

		// Extract the archive now
		$state = $this->ktExtract($tmpQueriesPath, $path);

		if (!$state) {
			$this->setInfo('COM_KOMENTO_INSTALLATION_ERROR_UNABLE_EXTRACT_QUERIES', false);
			return $this->output();
		}

		// Get the list of files in the folder.
		$queryFiles = JFolder::files($path, '.', true, true);

		// When there are no queries file, we should just display a proper warning instead of exit
		if (!$queryFiles) {
			$this->setInfo('COM_KOMENTO_INSTALLATION_ERROR_EMPTY_QUERIES_FOLDER', false);
			return $this->output();
		}

		$db = JFactory::getDBO();
		$total = 0;

		foreach ($queryFiles as $file) {
			// Get the contents of the file
			$contents = file_get_contents($file);
			$queries = $this->splitSql($contents);

			foreach ($queries as $query) {
				$query = trim($query);

				if (!empty($query)) {
					$db->setQuery($query);
					$state = $db->execute();
				}
			}
			$total += 1;
		}

		$this->setInfo(JText::sprintf('COM_KOMENTO_INSTALLATION_SQL_EXECUTED_SUCCESS', $total), true);
		return $this->output();
	}
}