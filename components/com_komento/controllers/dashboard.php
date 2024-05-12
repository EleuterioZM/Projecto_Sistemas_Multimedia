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

class KomentoControllerDashboard extends KomentoControllerBase
{
	/**
	 * Submit request to download information
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function download()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user is logged in
		if (!$this->my->id) {
			throw FH::exception('COM_KT_PLEASE_LOGIN_INFO', 500);
		}

		$table = KT::table('download');
		$exists = $table->load(['userid' => $this->my->id]);

		if ($exists) {
			throw FH::exception('COM_KT_GDPR_DOWNLOAD_ERROR_MULTIPLE_REQUEST', 500);
		}

		$params = [];

		$table->userid = $this->my->id;
		$table->state = KOMENTO_DOWNLOAD_REQ_NEW;
		$table->params = json_encode($params);
		$table->created = FH::date()->toSql();
		$table->store();

		$redirect = JRoute::_('index.php?option=com_komento&view=dashboard&layout=download', false);
		
		$this->app->enqueueMessage(JText::_('COM_KT_GDPR_REQUEST_DATA_SUCCESS'));

		return $this->app->redirect($redirect);
	}

	/**
	 * Publishes comments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function publish()
	{
		$items = $this->input->get('cid', [], 'int');

		if (!$items) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		foreach ($items as $id) {
			$comment = KT::comment((int) $id);

			if (!$id || !$comment->id) {
				throw FH::exception('COM_KT_INVALID_ID', 500);
			}

			if (!$comment->canPublish()) {
				throw FH::exception('COM_KT_NOT_ALLOWED_UNPUBLISH_COMMENT', 500);
			}

			// Unpublish the comment
			$comment->publish();
		}

		$return = $this->getReturnUrl('return');

		$this->app->enqueueMessage(JText::_('COM_KOMENTO_SELECTED_COMMENTS_PUBLISHED_SUCCESSFULLY'));

		return $this->app->redirect($return);
	}

	/**
	 * Unpublishes comments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function unpublish()
	{
		$items = $this->input->get('cid', [], 'int');

		if (!$items) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		foreach ($items as $id) {
			$comment = KT::comment((int) $id);

			if (!$id || !$comment->id) {
				throw FH::exception('COM_KT_INVALID_ID', 500);
			}

			if (!$comment->canUnpublish()) {
				throw FH::exception('COM_KT_NOT_ALLOWED_UNPUBLISH_COMMENT', 500);
			}

			$comment->publish(0);
		}

		$return = $this->getReturnUrl('return');

		$this->app->enqueueMessage(JText::_('COM_KOMENTO_SELECTED_COMMENTS_UNPUBLISHED_SUCCESSFULLY'));

		return $this->app->redirect($return);
	}
}
