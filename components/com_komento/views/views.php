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

class KomentoView extends JViewLegacy
{
	public $config = null;
	public $jConfig = null;
	public $app = null;
	public $input = null;
	public $my = null;
	public $doc = null;

	public function __construct($config = [])
	{
		parent::__construct($config);

		if (!defined('KOMENTO_CLI')) {
			$this->doc = JFactory::getDocument();
			$this->app = JFactory::getApplication();
			$this->access = KT::acl();
			$this->input = KT::request();
			$this->jConfig = JFactory::getConfig();
			$this->my = KT::user();

			if ($this->doc->getType() == 'ajax') {
				$this->ajax = KT::ajax();
			}
		}

		$this->config = KT::config();
		$this->theme = KT::themes();
	}

	public function set($key, $value = null)
	{
		return $this->theme->set($key, $value);
	}

	/**
	 * Main method to output the contents
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$format = $this->input->get('format', 'html', 'word');

		if ($format == 'json') {
			header('Content-type: text/x-json; UTF-8');
			echo $this->theme->toJSON();
			exit;
		}

		$tpl = 'site/' . $tpl;
		
		/**
		 * For 'raw' types of output, we need to exit it after that
		 * as we do not want to process anything apart from our codes only.
		 */
		if ($format == 'raw') {
			echo $this->theme->output($tpl);
			return;
		}

		if ($format == 'ajax') {
		    return $this->theme->output($tpl);
		}

		if ($format == 'html') {
			// Load necessary css and javascript files.
			KT::initialize();
			
			echo $this->theme->output($tpl);
			return;
		}

		return parent::display($tpl);
	}

	public function setPathway($name, $link = '')
	{
		static $views = null;

		$pathway = JFactory::getApplication()->getPathway();

		return $pathway->addItem($name, $link);
	}
}
