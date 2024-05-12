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
namespace Foundry\Models;

defined('_JEXEC') or die('Unauthorized Access');

class Base extends \JModelAdmin
{
	protected $fd = null;
	protected $extension = null;
	protected $key = null;

	// Implemented by child
	protected $element = null;

	public function __construct($config = [])
	{
		$this->fd = \FH::normalize($config, 'fd', null);

		if (!$this->fd) {
			die('Foundry library is required');
		}

		$this->extension = $this->fd->getComponentName();
		
		// Generate the key that needs to be retrieved from session states
		$namespace = \FH::normalize($config, 'namespace', '');
		$this->key = $this->generateIndex($namespace);

		// We don't want to load any of the tables path because we use our own FD::table method.
		$options = [
			'table_path' => JPATH_ROOT . '/libraries/joomla/database/table'
		];

		parent::__construct($options);
	}

	/**
	 * Generates the index for the current model
	 *
	 * @since	1.1.0
	 * @access	private
	 */
	final public function generateIndex($namespace = '')
	{
		$index = $this->extension;

		if ($namespace) {
			$index .= '.' . $config['namespace'];
		}

		$index .= '.' . $this->element;

		return $index;
	}

	/**
	 * Get state from the current request
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function getUserStateFromRequest($key, $default = '', $type = 'none')
	{
		$app = \JFactory::getApplication();

		$value = $app->getUserStateFromRequest($this->key . '.' . $key, $key, $default, $type);

		return $value;
	}

	/**
	 * To satisfy the requirements from \JModelAdmin
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function getForm($data = [], $loadData = true)
	{
	}

	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @since   1.1.2
	 * @access	protected
	 */
	protected function populateState()
	{
		// Load the parameters.
		$value = \JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
	}
}