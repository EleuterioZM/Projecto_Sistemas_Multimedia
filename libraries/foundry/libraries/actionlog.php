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
namespace Foundry\Libraries;

defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\Component\ComponentHelper;

class ActionLog
{
	private $extension = null;
	private $data = [
		'action' => '',
		'title' => '',
		'extension_name' => ''
	];

	public function __construct($extension)
	{
		$this->extension = $extension;
		$this->data['title'] = $extension;
		$this->data['extension_name'] = $extension;
	}

	/**
	 * Determines if actionlog feature is enabled or not from the 'Events To Log' option
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function enabled()
	{
		$params = ComponentHelper::getComponent('com_actionlogs')->getParams();

		$extensions = $params->get('loggable_extensions', []);

		if (in_array($this->extension, $extensions)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if actionlog feature exists on the current installed Joomla version
	 * since action log only exists from 3.9.x
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$file = JPATH_ADMINISTRATOR . '/components/com_actionlogs/models/actionlog.php';

			if (\FH::isJoomla4()) {
				$file = JPATH_ADMINISTRATOR . '/components/com_actionlogs/src/Model/ActionlogModel.php';
			}

			$exists = file_exists($file);
		}

		return $exists;
	}

	/**
	 * Performs the logging for the extension
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function log($actionString, $context, $data = [])
	{
		if (!$this->exists() || !$this->enabled()) {
			return false;
		}

		$user = isset($data['user']) && is_object($user) ? $user : \JFactory::getUser();

		$data = array_merge($data, $this->data);
		$data['userid'] = $user->id;
		$data['username'] = $user->username;
		$data['accountlink'] = "index.php?option=com_users&task=user.edit&id=" . $user->id;

		$context = $data['extension_name'] . '.' . $context;

		$model = $this->getModel();

		// Could be disabled
		if ($model === false) {
			return false;
		}

		return $model->addLog([$data], \JText::_($actionString), $context, $user->id);
	}

	/**
	 * Retrieve the action log model from Joomla
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getModel()
	{
		static $model = null;

		if (is_null($model)) {
			$config = [
				'ignore_request' => true
			];

			if (\FH::isJoomla4()) {
				$model = new \Joomla\Component\Actionlogs\Administrator\Model\ActionlogModel($config);

				return $model;
			}

			\Joomla\CMS\MVC\Model\ItemModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_actionlogs/models', 'ActionlogsModelActionlog');
			$model = \Joomla\CMS\MVC\Model\ItemModel::getInstance('Actionlog', 'ActionLogsModel', $config);
		}

		return $model;
	}
}