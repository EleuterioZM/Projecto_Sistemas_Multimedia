<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__DIR__) . '/views.php');

class KomentoViewDashboard extends KomentoView
{
	/**
	 * Renders the delete confirmation dialog
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmDelete()
	{
		$items = $this->input->get('items');
		$return = $this->input->get('return');

		if (!$items) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		foreach ($items as &$id) {
			$id = (int) $id;
		}

		$theme = KT::themes();
		$theme->set('items', $items);
		$theme->set('return', $return);

		$output = $theme->output('site/dashboard/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the delete confirmation dialog
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmSpam()
	{
		$items = $this->input->get('items', [], 'array');
		$return = $this->input->get('return', '', 'string');

		if (!$items) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		foreach ($items as &$id) {
			$id = (int) $id;
		}

		$theme = KT::themes();
		$theme->set('items', $items);
		$theme->set('return', $return);
		$theme->set('action', 'add');

		$output = $theme->output('site/dashboard/dialogs/spam');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the delete confirmation dialog
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmRemoveSpam()
	{
		$items = $this->input->get('items');
		$return = $this->input->get('return');

		if (!$items) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		foreach ($items as &$id) {
			$id = (int) $id;
		}

		$theme = KT::themes();
		$theme->set('items', $items);
		$theme->set('return', $return);
		$theme->set('action', 'remove');

		$output = $theme->output('site/dashboard/dialogs/spam');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the approve confirmation dialog
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmModerate()
	{
		$items = $this->input->get('items');
		$return = $this->input->get('return');
		$action = $this->input->get('action');

		if (!$items) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		foreach ($items as &$id) {
			$id = (int) $id;
		}

		$theme = KT::themes();
		$theme->set('items', $items);
		$theme->set('return', $return);
		$theme->set('action', $action);

		$output = $theme->output('site/dashboard/dialogs/moderate');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the clear report confirmation dialog
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmClearReports()
	{
		$items = $this->input->get('items');
		$return = $this->input->get('return');

		if (!$items) {
			throw FH::exception('Invalid comment id provided', 500);
		}

		foreach ($items as &$id) {
			$id = (int) $id;
		}

		$theme = KT::themes();
		$theme->set('items', $items);
		$theme->set('return', $return);

		$output = $theme->output('site/dashboard/dialogs/clear.reports');

		return $this->ajax->resolve($output);
	}


	/**
	 * Show dialog confirmation for download
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function confirmDownload()
	{
		$userId = $this->my->id;

		$table = KT::table('download');
		$table->load(array('userid' => $userId));
		$state = $table->getState();

		$email = $this->my->email;
		$emailPart = explode('@', $email);
		$email = FCJString::substr($emailPart[0], 0, 2) . '****' . FCJString::substr($emailPart[0], -1) . '@' . $emailPart[1];

		$theme = KT::themes();
		$theme->set('userId', $userId);
		$theme->set('email', $email);
		$output = $theme->output('site/dashboard/dialogs/gdpr.confirm');

		return $this->ajax->resolve($output);
	}
}