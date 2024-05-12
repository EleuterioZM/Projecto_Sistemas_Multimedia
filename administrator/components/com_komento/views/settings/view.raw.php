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

class KomentoViewSettings extends KomentoAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('komento.manage.settings');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}
	}

	/**
	 * Exports settings from Komento
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function export()
	{
		$model = KT::model('Settings');
		$data = $model->getRawData();


		// Get the file size
		$size = strlen($data);

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=komento_settings.json' );
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $size);
		ob_clean();
		flush();
		echo $data;
		exit;
	}
}
