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

use Foundry\Libraries\Themes;

class Info
{
	protected $fd = null;
	protected $namespace = null;

	public function __construct($fd)
	{
		$this->fd = $fd;
		$this->namespace = $fd->getComponentName();
	}

	/**
	 * Gets messages from the info queue
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function getMessage($clear = true)
	{
		$session = \JFactory::getSession();
		$messages = $session->get('messages', [], $this->getNamespace());

		if ($clear) {
			$session->clear('messages', $this->getNamespace());
		}

		if ($messages) {
			$items = [];

			foreach ($messages as $message) {
				$data = unserialize($message);

				$obj = new \stdClass();
				$obj->text = $data->message;
				$obj->type = $data->type;

				$items[] = $obj;
			}

			return $items;
		}

		return false;
	}

	/**
	 * Gets the current namespace
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function getNamespace()
	{
		$path = \FH::isFromAdmin() ? 'admin' : 'site';
		$namespace = $this->namespace . '.' . $path;

		return $namespace;
	}

	/**
	 * Sets a message in the queue.
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function set($message, $class = '', $namespace = null)
	{
		$session = \JFactory::getSession();

		$obj = new \stdClass();
		$obj->message = \JText::_($message);
		$obj->type = $class;

		$data = serialize($obj);

		$messages = $session->get('messages', [], $this->getNamespace());

		// Namespacing purposes to prevent duplication
		// Without namespacing (backwards/legacy), messages will just get queued indefinitely
		// With namespacing, only 1 instance of the same message should exist
		if (empty($namespace)) {
			$messages[]	= $data;
		} 

		if ($namespace) {
			$messages[$namespace] = $data;
		}

		$session->set('messages', $messages, $this->getNamespace());
	}

	/**
	 * Generates the info html block
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function html($admin = false)
	{
		$messages = $this->getMessage();

		// If there's nothing stored in the session, ignore this.
		if (!$messages) {
			return;
		}

		$output = '';

		foreach ($messages as $message) {
			$class = $message->type;

			// Decode "error" types
			if ($message->type === 'error') {
				$class = 'danger';
			}

			$theme = new Themes($this->fd);
			$theme->set('content', $message->text);
			$theme->set('class', $class);

			$section = $admin ? 'admin' : 'site';

			$output .= $theme->output('info/' . $section);
		}

		return $output;

	}
}
