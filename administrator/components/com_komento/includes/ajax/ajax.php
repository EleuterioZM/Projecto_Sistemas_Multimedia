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

use Foundry\Libraries\Ajax;

class KomentoAjax extends Ajax
{
	/**
	 * Determines if the current namespace is a valid namespace
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isValidNamespace($namespace)
	{
		$valid = false;

		// Legacy uses '.' as separator, we need to replace occurences of '.' with /
		$namespace = str_ireplace('.', '/', $namespace);
		$namespace = explode('/', $namespace);

		// All calls should be made a minimum out of 3 parts of dots (.)
		if (count($namespace) >= 4) {
			$valid = true;
		}

		return $valid;
	}

	/**
	 * Processes ajax requests made to Komento
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function execute($namespace)
	{
		/**
		 * Namespaces are broken into the following
		 *
		 * site/views/viewname/methodname - Front end ajax calls
		 * admin/views/viewname/methodname - Back end ajax calls
		 */
		$namespace = explode('/', $namespace);

		list($location, $type, $name, $method) = $namespace;

		// Komento only supports view and controllers
		if (!in_array($type, ['views', 'controllers'])) {
			$this->fail(JText::_('Ajax calls are currently only serving views and controllers.'));
			return $this->send();
		}

		// Get the absolute path of the initial location
		$path = $location === 'admin' ? KT_ADMIN : KT_ROOT;

		// Determine if this is a view or controller.
		if (in_array($location, ['site', 'admin'])) {
			if ($type === 'views') {
				$path = $path . '/views/' . $name . '/view.ajax.php';
			}
			
			if ($type === 'controllers') {
				$path = $path . '/controllers/' . $name . '.php';
			}
		}

		// Get the arguments from the query string if there is any.
		$input = JFactory::getApplication()->input;
		$args = $input->get('args', '', 'default');

		if (!JFile::exists($path)) {
			$this->reject(JText::sprintf('The file %1s does not exist.', $namespace));
			return $this->send();
		}

		include_once($path);

		// Process controllers
		if ($type === 'controllers') {
			$className = 'KomentoController' . preg_replace('/[^A-Z0-9_]/i', '', $name);
			$obj = new $className();

			// For controllers, use standard execute method
			return $obj->execute($method);
		}

		if ($type === 'views') {
			$className = 'KomentoView' . preg_replace('/[^A-Z0-9_]/i', '', $name);
			$obj = new $className();

			// If the method doesn't exist in this object, we know something is wrong.
			if (!method_exists($obj, $method)) {
				$this->fail(JText::sprintf('Method %1s does not exist', $method));
				return $this->ajax->send();
			}

			// When arguments are provided, we provide them as func arguments
			if (!empty($args)) {
				return call_user_func_array([$obj, $method], json_decode($args));
			}

			return $obj->$method();
		}

		return $this->send();
	}

	/**
	 * Retrieves an ajax adapter so that it knows how to resolve the calls
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAdapter($location)
	{
		$file = __DIR__ . '/adapters/' . strtolower($location) . '.php';

		require_once($file);

		$className = 'KomentoAjaxAdapter' . ucfirst($location);
		$adapter = new $className();

		return $adapter;
	}
}
