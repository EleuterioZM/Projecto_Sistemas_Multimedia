<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

KT::import('admin:/views/views');

class KomentoViewAcl extends KomentoAdminView
{
	/**
	 * Acl View
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('komento.manage.acl');
		$this->heading('COM_KOMENTO_SETTINGS_HEADING_ACL');

		$usergroups = KT::getUsergroups();

		$this->set('usergroups', $usergroups);

		parent::display('acl/default');
	}

	/**
	 * Renders the ACL form
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function form()
	{
		if (!$this->my->authorise('komento.manage.acl', 'com_komento')) {
			$this->app->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			return $this->app->close();
		}

		JToolBarHelper::apply('apply');
		JToolBarHelper::save();
		JToolBarHelper::cancel();

		$id	= $this->app->getUserStateFromRequest('com_komento.acl.id', 'id', '0');
		$type = $this->input->get('type', 'usergroup');

		$model = KT::model('acl');

		$usergroup = '';

		if ($type === 'usergroup') {
			$usergroup = $model->getGroupTitle($id);
		}

		$this->heading(JText::sprintf('COM_KOMENTO_SETTINGS_ACL_HEADING', $usergroup), 'COM_KOMENTO_SETTINGS_ACL_HEADING_DESC');

		
		$model->updateUserGroups();

		$rulesets = $model->getData($type, $id);
		$tabs = [];

		$current = $this->input->get('current', 'basic', 'word');
		
		// For now we will assume that id 1 is the public group
		$guestGroup = JComponentHelper::getParams('com_users')->get('guest_usergroup');

		if ($rulesets) {
			foreach ($rulesets as $key => $value) {
				$tab = new stdClass();
				$tab->title = JText::_('COM_KOMENTO_ACL_TAB_' . strtoupper($key));
				$tab->id = str_ireplace(['.', ' ', '_'], '-', strtolower($key));
				$tab->active = $tab->id === $current;

				$tabs[] = $tab;
			}

			// check for the public and guest user group
			if ($guestGroup == $id || $id == 1) {

				// Ensure that shouldn't render the like comment in ACL rule
				if (isset($rulesets->basic->like_comment)) {
					unset($rulesets->basic->like_comment);
				}
			}
		}

		$this->set('current', $current);
		$this->set('rulesets', $rulesets);
		$this->set('id', $id);
		$this->set('type', $type);
		$this->set('usergroup', $usergroup);
		$this->set('tabs', $tabs);

		parent::display('acl/form');
	}
}