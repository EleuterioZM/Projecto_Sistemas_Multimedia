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

abstract class Ajax
{
	protected $commands = [];
	private $fd = null;
	private $input = null;

	public function __construct($fd)
	{
		$this->fd = $fd;
		$this->input = \JFactory::getApplication()->input;
	}

	/**
	 * Proxy to handle all commands needed to be added into the command chain.
	 * e.g: success / fail / script
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function __call($method, $args)
	{
		$this->addCommand($method, $args);

		return $this;
	}

	/**
	 * Implemented by child class to implement their own execution methods
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	abstract public function execute($namespace);
	
	/**
	 * Determines if the current namespace is a valid namespace
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	abstract public function isValidNamespace($namespace);

	/**
	 * Allows caller to add commands to the ajax response chain
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function addCommand($type, &$data)
	{
		$this->commands[] = [
			'type' => $type, 
			'data' => &$data
		];

		return $this;
	}

	/**
	 * Determines if the current request is an ajax request
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isAjaxRequest($namespace)
	{
		static $ajax = null;

		if (is_null($ajax)) {
			$ajax = $this->input->get('format', '', 'cmd') == 'ajax' && !empty($namespace);
		}

		return $ajax;
	}

	/**
	 * Processes ajax calls made on the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function process()
	{
		// Get the namespace
		$namespace = $this->input->get('namespace', '', 'default');

		// Determines if this is an ajax call made to the site
		if (!$this->isAjaxRequest($namespace)) {
			return false;
		}

		// Determines if this is a valid namespace
		if (!$this->isValidNamespace($namespace)) {
			$this->fail(\JText::_('FD_INVALID_AJAX_CALLS'));
			return $this->send();
		}

		// Let the caller executes the way it needs to be executed
		$this->execute($namespace);

		return $this->send();
	}

	/**
	 * Sends a response back to the request
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function send()
	{
		// Isolate PHP errors and send it using notify command.
		$error_reporting = ob_get_contents();

		if (strlen(trim($error_reporting))) {
			$this->notify($error_reporting, 'debug');
		}
		ob_clean();

		// JSONP transport
		$callback = $this->input->get('callback', '');

		if ($callback) {
			header('Content-type: application/javascript; UTF-8');
			echo $callback . '(' . json_encode($this->commands) . ');';
			exit;
		}

		// IFRAME transport
		$transport = $this->input->get('transport');

		if ($transport === "iframe") {
			header('Content-type: text/html; UTF-8');
			echo '<textarea data-type="application/json" data-status="200" data-statusText="OK">' . json_encode($this->commands) . '</textarea>';
			exit;
		}

		if (!isset($this->commands)) {
			$this->commands = [];
		}

		// XHR transport
		header('Content-type: text/x-json; UTF-8');
		echo json_encode($this->commands);
		exit;
	}
}
