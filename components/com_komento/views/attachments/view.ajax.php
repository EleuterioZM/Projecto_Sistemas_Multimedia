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

class KomentoViewAttachments extends KomentoView
{
	/**
	 * Renders the dialog to confirm before deletion
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmDelete()
	{
		// Attachments id
		$id = $this->input->get('id', 0, 'int');

		$table = KT::table('Uploads');
		$table->load($id);

		if (!$id || !$table->id) {
			throw FH::exception('COM_KT_NOT_ALLOWED_UPLOAD', 500);
		}

		if (!$this->access->allow('delete_attachment', $table->uid)) {
			throw FH::exception('COM_KT_NOT_ALLOWED_DELETE_ATTACHMENTS', 500);
		}

		$theme = KT::themes();
		$theme->set('file', $table);
		$output = $theme->output('site/attachments/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allows caller to delete an attachment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function delete()
	{
		// Comment id
		$id = $this->input->get('id', 0, 'int');

		$file = KT::table('Uploads');
		$file->load($id);

		if (!$id || !$file->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		if (!$this->access->allow('delete_attachment', $file->uid)) {
			throw FH::exception('COM_KT_NOT_ALLOWED_DELETE_ATTACHMENTS', 500);
		}

		if (!$file->delete()) {
			return $this->exception($file->getError());
		}

		return $this->ajax->resolve();
	}
}