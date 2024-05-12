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

require_once(__DIR__ . '/base.php');

class KomentoControllerFile extends KomentoControllerBase
{
	/**
	 * Process upload of attachments
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function upload()
	{
		FH::checkToken();

		$component = $this->input->get('component', '', 'string');

		if (!$this->profile->canUploadAttachments()) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		$file = $this->input->files->get('file', '', 'raw');

		// Check for file size
		if ($file['size'] > ($this->config->get('upload_max_size') * 1024 * 1024)) {
			echo json_encode(['status' => 'exceedfilesize']); 
			exit;
		}

		$id = KT::file()->upload($file);

		// Default option
		$result = ['status' => 0, 'id' => 0];

		if ($id !== false) {
			$result['status'] = 1;
			$result['id'] = $id;
		}

		echo json_encode($result);
		exit;
	}

	/**
	 * Allows caller to download a file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function download()
	{
		$id = $this->input->get('id', 0, 'int');

		$table = KT::table('Uploads');
		$table->load($id);

		if (!$id || !$table->id) {
			throw FH::exception('COM_KOMENTO_ATTACHMENT_INVALID_ID', 500);
		}

		$comment = KT::comment($table->uid);

		if (!$this->profile->allow('download_attachment')) {
			throw FH::exception('COM_KOMENTO_ATTACHMENT_NO_PERMISSION', 500);
		}

		return $table->download();
	}
}
