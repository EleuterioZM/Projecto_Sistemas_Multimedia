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

class KomentoReports
{
	public function __construct()
	{
		$this->config = KT::config();
		$this->my = JFactory::getUser();
	}
	
	/**
	 * Determines if reporting is enabled
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isEnabled()
	{
		if (!$this->config->get('enable_report') || !KT::user()->allow('report_comment')) {
			return false;
		}

		return true;
	}

	/**
	 * Logs a new report against a comment item
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function add($comment)
	{
		KT::activity()->process('report', $comment->id);

		$table = KT::table('Actions');
		$table->type = 'report';
		$table->comment_id = $comment->id;
		$table->action_by = $this->my->id;
		$table->actioned = FH::date()->toSql();

		if (!$table->store()) {
			return false;
		}

		if ($this->config->get('notification_event_reported_comment')) {
			KT::notification()->push('report', 'author,usergroups', array('commentId' => $comment->id, 'actionId' => $table->id));
		}

		return true;
	}
}
