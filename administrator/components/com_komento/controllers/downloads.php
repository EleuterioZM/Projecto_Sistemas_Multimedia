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

class KomentoControllerDownloads extends KomentoController
{
	/**
	 * Invoke purge method
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function purgeAll()
	{
		FH::checkToken();

		$model = KT::model('Download');
		$model->purgeRequests();

		$this->info->set(JText::_('COM_KT_DOWNLOADS_ALL_REQUESTS_PURGED_SUCCESSFULLY'), KOMENTO_MSG_SUCCESS);
		return $this->app->redirect('index.php?option=com_komento&view=downloads');
	}

	/**
	 * Delete user download request
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function removeRequest()
	{
		// Check for request forgeries
		FH::checkToken();

		$cid = $this->input->get('cid', [], '', 'array');
		FCArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		$result = null;

		foreach ($cid as $id) {
			$table = KT::table('Download');
			$table->load($id);

			$table->delete();
		}

		$this->info->set(JText::_('COM_KT_USER_DOWNLOAD_DELETE_SUCCESS'), KOMENTO_MSG_SUCCESS);
		return $this->app->redirect('index.php?option=com_komento&view=downloads');
	}
}