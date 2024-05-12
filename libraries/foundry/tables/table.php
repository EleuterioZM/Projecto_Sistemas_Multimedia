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
namespace Foundry\Tables;

defined('_JEXEC') or die('Unauthorized Access');

class Table extends \JTable
{
	protected $_supportNullValue = true;

	public function __construct($table, $key, $db, $dispatcher = null)
	{
		// Set internal variables.
		$this->_tbl = $table;
		$this->_tbl_key = $key;

		// For Joomla 3.2 onwards
		$this->_tbl_keys = [$key];

		$this->_db = $db;

		// Implement JObservableInterface:
		// Create observer updater and attaches all observers interested by $this class:
		if (\FH::isJoomla31() && class_exists('JObserverUpdater')) {
			$this->_observers = new \JObserverUpdater($this);
			\JObserverMapper::attachAllObservers($this);
		}

		if (\FH::isJoomla4()) {

			// Create or set a Dispatcher
			if (!is_object($dispatcher) || !($dispatcher instanceof DispatcherInterface)) {
				$dispatcher = \JFactory::getApplication()->getDispatcher();
			}

			$this->setDispatcher($dispatcher);

			$event = \Joomla\CMS\Event\AbstractEvent::create('onTableObjectCreate', ['subject' => $this]);

			$this->getDispatcher()->dispatch('onTableObjectCreate', $event);
		}
	}

	/**
	 * Bind the table properties
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function bind($src, $ignore = [])
	{
		if (!is_object($src) && !is_array($src)) {
			$src = new \stdClass();
		}

		// Joomla 4 compatibility:
		// To Ensure id column type is integer
		if (is_array($src) && isset($src['id'])) {
			$src['id'] = (int) $src['id'];
		}

		if (is_object($src) && property_exists($src, 'id')) {
			$src->id = (int) $src->id;
		}

		return parent::bind($src);
	}
	
	/**
	 * Tired of fixing conflicts with JTable::getInstance . We'll overload their method here.
	 *
	 * @param   string  $type    The type (name) of the JTable class to get an instance of.
	 * @param   string  $prefix  An optional prefix for the table class name.
	 * @param   array   $config  An optional array of configuration values for the JTable object.
	 *
	 * @return  mixed    A JTable object if found or boolean false if one could not be found.
	 *
	 * @link    http://docs.joomla.org/JTable/getInstance
	 * @since   11.1
	 */
	public static function getInstance($type, $prefix = 'JTable', $config = [])
	{
		// Sanitize and prepare the table class name.
		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$tableClass = $prefix . ucfirst($type);

		// Only try to load the class if it doesn't already exist.
		if (!class_exists($tableClass)) {
			$path = \FH::normalize($config, 'tablePath', dirname(__FILE__));

			// Search for the class file in the JTable include paths.
			$path = $path . '/' . strtolower($type) . '.php';

			// Import the class file.
			include_once($path);
		}

		return parent::getInstance($type, $prefix, $config);
	}

	/**
	 * Generic method to retrive raw json params from the table
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	final public function getParams()
	{
		$params = new \JRegistry($this->params);

		return $params;
	}

	/**
	 * Retrieves the translated title
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function getTitle()
	{
		return \JText::_($this->title);
	}

	/**
	 * On Joomla 4, if table object contains array or objects, storing is problematic unlike Joomla 3.
	 * To fix Joomla 4 storing issues, we override the store behavior and normalize the fields accordingly.
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function store($updateNulls = false)
	{
		if (!\FH::isJoomla4()) {
			return parent::store($updateNulls);
		}

		$properties = get_object_vars($this);

		foreach ($properties as $key => $value) {

			if ($key != $this->_tbl_key && strpos($key, '_') !== 0) {

				// For Joomla 4, it does not convert array / objects into json strings
				if (is_object($value) || is_array($value)) {
					$this->$key = json_encode($value);
				}

				// For Joomla 4, it does not convert the boolean value into 1 / 0
				if (is_bool($value)) {
					$this->$key = $value ? 1 : 0;
				}
			}
		}

		return parent::store($updateNulls);
	}

	/**
	 * Converts a table layer into an array
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function toArray()
	{
		$properties = get_class_vars(get_class($this));
		$result = array();

		foreach ($properties as $key => $value) {
			if ($key[0] != '_') {
				$result[$key] = is_null($this->get($key)) ? '' : $this->get($key);
			}
		}

		return $result;
	}
}
