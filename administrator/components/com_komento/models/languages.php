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

class KomentoModelLanguages extends KomentoModel
{
	protected $element = 'languages';
	public $_pagination = null;
	public $_total;

	public function __construct($config = [])
	{
		parent::__construct($config);

		$limit = ($this->app->getCfg('list_limit') == 0) ? 5 : $this->app->getCfg('list_limit');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Populates the state
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function initStates()
	{
		$ordering = $this->getUserStateFromRequest('ordering', 'id');
		$direction = $this->getUserStateFromRequest('direction', 'asc');

		$this->setState('ordering', $ordering);
		$this->setState('direction', $direction);
	}

	/**
	 * Determines if the language rows has been populated
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function initialized()
	{
		$db = KT::db();
		$sql = $db->sql();

		$sql->select('#__komento_languages');
		$sql->column('COUNT(1)');

		$db->setQuery($sql);

		$initialized = $db->loadResult() > 0;

		return $initialized;
	}

	/**
	 * Retrieves languages
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getLanguages()
	{
		$db = KT::db();
		$sql = $db->sql();

		$sql->select('#__komento_languages');

		$order = $this->getState('ordering');

		if ($order) {
			$direction = $this->getState('direction');

			$sql->order($order, $direction);
		}

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Purges non installed languages
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function purge()
	{
		$db = KT::db();

		$sql = $db->sql();

		$sql->delete('#__komento_languages');
		$sql->where('state', KOMENTO_LANGUAGES_NOT_INSTALLED);

		$db->setQuery($sql);

		return $db->Query();
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		$this->_pagination = KT::pagination($this->_total, $this->getState('limitstart'), $this->getState('limit'));
		return $this->_pagination;
	}

	/**
	 * Discover new languages
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function discover()
	{
		$config = KT::config();
		
		$key = $config->get('main_apikey');

		$connector = FH::connector(KOMENTO_UPDATER_LANGUAGE);
		$connector->addQuery('key', $key);
		$connector->setMethod('POST');
		$connector->execute();

		$contents = $connector->getResult();

		if (!$contents) {
			$result = new stdClass();
			$result->message = 'No language found';
			return $result;
		}

		// Decode the result
		$result	= json_decode($contents);

		if ($result->code != 200) {
			$return = base64_encode('index.php?option=com_komento&view=languages');

			return $result;
		}

		foreach ($result->languages as $language) {

			// If it does, load it instead of overwriting it.
			$table  = KT::table('Language');
			$exists = $table->load(array('locale' => $language->locale));

			// We do not want to bind the id
			unset($language->id);

			// Since this is the retrieval, the state should always be disabled
			if (!$exists) {
				$table->state = KOMENTO_STATE_UNPUBLISHED;
			}

			// Then check if the language needs to be updated. If it does, update the ->state to KOMENTO_LANGUAGES_NEEDS_UPDATING
			// We need to check if the language updated time is greater than the local updated time
			if ($exists && $table->state == KOMENTO_LANGUAGES_INSTALLED) {
				$languageTime = strtotime($language->updated);
				$localLanguageTime = strtotime($table->updated);

				if ($languageTime > $localLanguageTime && $table->state == KOMENTO_LANGUAGES_INSTALLED) {
					$table->state = KOMENTO_LANGUAGES_NEEDS_UPDATING;
				}
			}

			// Set the title
			$table->title = $language->title;

			// Set the locale
			$table->locale = $language->locale;

			// Set the translator
			$table->translator = $language->translator;

			// Set the updated time
			$table->updated = $language->updated;

			// Update the progress
			$table->progress = $language->progress;

			// Update the table with the appropriate params
			$params = new JRegistry();

			$params->set('download', $language->download);
			$params->set('md5', $language->md5);
			$table->params = $params->toString();

			$table->store();
		}

		return true;
	}

	/**
	 * Retrieves the current domain
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getDomain()
	{
		$domain = rtrim(JURI::root(), '/');
		$domain = str_ireplace(array('http://', 'https://'), '', $domain);

		return $domain;
	}
}
