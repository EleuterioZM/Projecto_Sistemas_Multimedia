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

class KomentoController extends KomentoControllerBase
{
	/**
	 * Override parent controller's display behavior.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function display( $params = [] , $urlparams = false)
	{
		$type = $this->doc->getType();
		$name = $this->input->get('view', 'dashboard', 'cmd');
		$view = $this->getView($name, $type, '');

		// @task: Once we have the view, set the appropriate layout.
		$layout = $this->input->get('layout', 'default', 'cmd');
		$view->setLayout($layout);

		// For ajax methods, we just load the view methods.
		if ($type == 'ajax') {

			if (!method_exists($view, $layout)) {
				$view->display();
			} else {
				$params = $this->input->get('params', '', 'default');
				$params = json_decode($params);

				call_user_func_array([$view, $layout], $params);
			}
		} else {

			if ($layout != 'default') {

				if (!method_exists($view, $layout)) {
					$view->display();
				} else {
					call_user_func_array([$view, $layout], $params);
				}
			} else {
				$view->display();
			}
		}
	}

	public function clearCaptcha()
	{
		$days = $this->input->get('days', 7, 'int');
		$result = KT::clearCaptcha( $days );
		echo JText::_( 'COM_KOMENTO_CLEAR_CAPTCHA_PROCESS_FINISHED' );
		exit;
	}

	/**
	 * Comment approval from the email
	 *
	 * @since	4.0.4
	 * @access	public
	 */
	public function approveComment()
	{
		$this->moderateComment('approve');
	}

	/**
	 * Comment rejection from the email
	 *
	 * @since	4.0.4
	 * @access	public
	 */
	public function rejectComment()
	{
		$this->moderateComment('reject');
	}

	/**
	 * moderate the comment by the action given from the email
	 *
	 * @since	4.0.4
	 * @access	public
	 */
	private function moderateComment($action)
	{
		$token = $this->input->getVar('token', '');
		$prefix = 'COM_KOMENTO_' . strtoupper($action) . '_';

		if (empty($token)) {
			echo JText::_('COM_KOMENTO_INVALID_TOKEN');
			exit;
		}

		$hashkeys = KT::getTable('hashkeys');

		if (!$hashkeys->loadByKey($token)) {
			echo JText::_('COM_KOMENTO_INVALID_TOKEN');
			exit;
		}

		if (empty($hashkeys->uid)) {
			echo JText::_('COM_KT_INVALID_COMMENT_ID');
			exit;
		}

		$comment = KT::comment($hashkeys->uid);

		if (!$comment->id) {
			echo JText::_('COM_KT_INVALID_COMMENT_ID');
			exit;
		}

		// If this hashkey has been used, then either redirect to the comment or throw error
		if ($action === 'approve' && $hashkeys->state && $comment->published !== KOMENTO_STATE_PUBLISHED) {
			echo JText::_('COM_KOMENTO_APPROVE_COMMENT_ADMIN_REMOVED_COMMENT');
			exit;
		}

		$publishState = $action === 'approve' ? KOMENTO_STATE_PUBLISHED : KOMENTO_STATE_UNPUBLISHED;
		$state = $comment->publish($publishState);

		if (!$state) {
			$error = $comment->getError();

			if (empty($error)) {
				$error = JText::_($prefix . 'COMMENT_ERROR_OCCURED');
			}

			echo $error;
			exit;
		}

		$hashkeys->state = 1;
		$hashkeys->store();

		return $this->app->redirect($comment->getItemPermalink());
	}

	// clears captcha dated 7 days old.
	public function clear()
	{
		$db = KT::db();

		$query = 'SELECT * FROM ' . $db->nameQuote( '#__komento_captcha' ) . ' WHERE ' . $db->nameQuote( 'created' ) . ' <= DATE_SUB(NOW(), INTERVAL 7 DAY)';

		$db->setQuery($query);
		$db->query();
	}
}