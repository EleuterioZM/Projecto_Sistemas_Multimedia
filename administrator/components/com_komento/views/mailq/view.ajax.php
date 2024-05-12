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

class KomentoViewMailq extends KomentoAdminView
{
	/**
	 * Previews an email
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function preview()
	{
		$id = $this->input->get('id', 0, 'int');

		$table = KT::table('Mailq');
		$table->load($id);

		$theme = KT::themes();
		$theme->set('mailer', $table);
		$contents = $theme->output('admin/mailq/dialogs/preview');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Confirmation before purging everything
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmPurgeAll()
	{
		$theme 	= KT::themes();
		$contents = $theme->output('admin/mailq/dialogs/purge.all');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Confirmation before purging pending e-mails
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmPurgeSent()
	{
		$theme = KT::themes();
		$contents = $theme->output('admin/mailq/dialogs/purge.sent');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Confirmation before purging pending e-mails
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmPurgePending()
	{
		$theme = KT::themes();
		$contents = $theme->output('admin/mailq/dialogs/purge.pending');

		return $this->ajax->resolve($contents);
	}	

	/**
	 * Display confirmation dialog to reset email theme files
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmReset()
	{
		$files = $this->input->get('files', array(), 'default');

		$theme = KT::themes();
		$theme->set('files', $files);
		$contents = $theme->output('admin/mailq/dialogs/reset.default');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Renders an email template preview
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function templatePreview()
	{
		$file = $this->input->get('file', '', 'default');

		$url = JURI::root() . 'administrator/index.php?option=com_komento&view=mailq&layout=templatePreview&tmpl=component&file=' . $file;		

		$theme = KT::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/mailq/dialogs/templatePreview');

		return $this->ajax->resolve($output);
	}
}