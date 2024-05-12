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

class KomentoControllerSystem extends KomentoController
{
	/**
	 * Process Komento upgrades
	 *
	 * @since	3.1.3
	 * @access	public
	 */
	public function upgrade()
	{
		$model = KT::model('System');
		$state = $model->update();

		if ($state === false) {
			$this->info->set($model->getError(), KOMENTO_MSG_ERROR);
			return $this->app->redirect('index.php?option=com_komento');
		}

		$this->info->set('Komento updated to the latest version successfully', KOMENTO_MSG_SUCCESS);
		return $this->app->redirect('index.php?option=com_komento');
	}
}
