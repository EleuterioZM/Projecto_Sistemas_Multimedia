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

class KomentoPlugin
{
	public $plugin;
	public $pluginpath;
	public $pluginbase;
	public $vars = [];
	public $params;

	public function __construct()
	{
		$this->plugin = strtolower(str_replace('KomentoHelper', '', get_class($this)));
		$this->pluginbase = rtrim(JURI::root(), '/') . '/plugins/komento';
		$this->pluginpath = JPATH_ROOT . '/plugins/komento/' . $this->plugin . '/' . $this->plugin;

		// load plugin language
		JFactory::getLanguage()->load('plg_komento_' . $this->plugin, JPATH_ROOT);

		// load plugin params
		$this->params = new JRegistry(JPluginHelper::getPlugin('komento', $this->plugin)->params);
	}

	public function fetch($file)
	{
		$path = $this->pluginpath . '/themes/' . $this->plugin . '/' . $file;

		if (isset($this->vars)) {
			extract($this->vars);
		}

		ob_start();
		include($path);

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function set($key, $value)
	{
		$this->vars[$key] = $value;
	}
}
