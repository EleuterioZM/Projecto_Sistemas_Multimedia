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

use Foundry\Libraries\Scripts;

class KomentoViewMain extends JViewLegacy {}

class KomentoAdminView extends KomentoViewMain
{
	protected $heading = null;
	protected $description = null;
	protected $app = null;
	protected $my = null;
	protected $input = null;
	protected $ajax = null;
	protected $theme = null;
	protected $sidebar = true;
	protected $help = null;

	public function __construct($options = [])
	{
		$this->app = JFactory::getApplication();
		$this->my = JFactory::getUser();
		$this->input = KT::request();
		$this->ajax = KT::ajax();
		$this->config = KT::config();
		$this->theme = KT::themes();

		parent::__construct($options);
	}

	/**
	 * Adds help button on the page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function addHelpButton($url)
	{
		$url = 'https://stackideas.com/docs/komento/' . ltrim($url, '/');

		$this->help = $url;
	}

	public function display($tpl = null)
	{
		JToolbarHelper::title(JText::_('COM_KOMENTO'), 'komento');

		Scripts::initializeAdmin();
		
		// Set the appropriate namespace
		$namespace 	= 'admin/' . $tpl;

		// Get the child contents
		$output = $this->theme->output($namespace);

		// Get the sidebar
		$sidebar = $this->getSidebar();

		// Determine if this is a tmpl view
		$tmpl = $this->input->get('tmpl', '', 'word');
		$view = $this->input->get('view', '', 'cmd');

		$paidViews = ['languages'];

		$overlay = false;

		$updateTaskUrl = KT::isFreeVersion() ? 'javascript:void(0);' : JRoute::_('index.php?option=com_komento&controller=system&task=upgrade');

		if (KT::isFreeVersion() && in_array($view, $paidViews)) {
			$overlay = true;
		}

		$theme = KT::themes();
		$theme->set('help', $this->help);
		$theme->set('heading', $this->heading);
		$theme->set('description', $this->description);
		$theme->set('output', $output);
		$theme->set('sidebar', $sidebar);
		$theme->set('tmpl', $tmpl);
		$theme->set('overlay', $overlay);
		$theme->set('updateTaskUrl', $updateTaskUrl);

		$contents = $theme->output('admin/structure/default');

		echo $contents;
	}

	/**
	 * Proxy for setting a variable to the template.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function set($key, $value = '')
	{
		$this->theme->set($key, $value);
	}

	/**
	 * Allows child to set heading title
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function heading($title, $desc = '')
	{
		$this->heading = $title;
		$this->description = $desc;

		if (empty($desc)) {
			$this->description = $title . '_DESC';
		}
	}

	/**
	 * Hides back-end sidebar
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function hideSidebar()
	{
		if (FH::isJoomla4()) {
			$this->input->set('hidemainmenu', true);
		}

		$this->sidebar = false;
	}

	/**
	 * Checks if the current viewer can really access this section
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function checkAccess($rule)
	{
		if (!$this->my->authorise($rule , 'com_komento')) {
			$this->app->enqueueMessage('JERROR_ALERTNOAUTHOR', 'error');
			return $this->app->redirect('index.php?option=com_komento');
		}
	}

	/**
	 * Prepares the sidebar
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getSidebar()
	{
		if (!$this->sidebar) {
			return;
		}

		$view = $this->input->get('view', '', 'cmd');
		$layout = $this->input->get('layout', '', 'cmd');

		$model = KT::model('Sidebar');
		$menus = $model->getItems($view);

		$output = KT::fd()->html('admin.sidebar', $menus, $view, $layout);

		return $output;
	}

	/**
	 * Calls a specific method from the view.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function call($method)
	{
		if (!method_exists($this, $method)) {
			return false;
		}

		// Get a list of arguments since we do not know
		// how many arguments are passed in here.
		$args = func_get_args();

		// Remove the first argument since the first argument is the method.
		array_shift($args);

		return call_user_func_array(array($this, $method), $args);
	}
}
