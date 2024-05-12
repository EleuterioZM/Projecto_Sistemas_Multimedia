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

class KomentoControllerUsers extends KomentoController
{
	/**
	 * Archive user data 
	 *
	 * @since	3.1.4
	 * @access	public
	 */
	public function requestArchiveUserData()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure user logged on the site
		if (!$this->my->id) {
			throw FH::exception('COM_KT_PLEASE_LOGIN_INFO', 500);
		}

		$cid = $this->input->get('cid', [], '', 'array');
		FCArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		$params = [];

		foreach ($cid as $id) {

			$table = KT::table('Download');
			$exists = $table->load(['userid' => $id]);

			if ($exists) {
				continue;
			}

			$table->userid = $id;
			$table->state = KOMENTO_DOWNLOAD_REQ_NEW;
			$table->params = json_encode($params);
			$table->created = FH::date()->toSql();
			$table->store();
		}

		$this->info->set(JText::_('COM_KT_GDPR_DOWNLOAD_USER_INFORMATION_REQUEST_SUCCESS'), KOMENTO_MSG_SUCCESS);

		return $this->app->redirect('index.php?option=com_komento&view=downloads');
	}
}