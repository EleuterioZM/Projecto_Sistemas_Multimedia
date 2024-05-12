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

class KomentoViewReports extends KomentoView
{
	/**
	 * Renders the report confirmation dialog
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function report()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		$comment = KT::comment($id);

		// Add the report
		$reports = KT::reports();
		$reports->add($comment);

		return $this->ajax->resolve();
	}

	/**
	 * Renders the dialog to confirm submitting report
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function confirmReport()
	{
		$reports = KT::reports();

		// Ensure that reporting is enabled
		if (!$reports->isEnabled()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_REPORT_COMMENT', 500);
		}

		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		$message = JText::_('COM_KOMENTO_REPORTS_SUBMITTED_DIALOG_CONTENT');
		$newReport = true;

		if ($comment->isReport()) {
			$message = JText::_('COM_KT_REPORTS_PREVIOUSLY_SUBMITTED');
			$newReport = false;
		}

		$theme = KT::themes();
		$theme->set('comment', $comment);
		$theme->set('message', $message);
		$theme->set('newReport', $newReport);
		$output = $theme->output('site/reports/dialogs/reported');

		return $this->ajax->resolve($output);
	}
}
