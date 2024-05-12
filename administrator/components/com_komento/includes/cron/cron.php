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

class KomentoCron
{
	public function __construct()
	{
		$this->config = KT::config();
		$this->input = JFactory::getApplication()->input;
	}

	/**
	 * Determines if the cronjob request is allowed
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function allowed()
	{
		$requirePhrase = $this->config->get('secure_cron');
		$storedPhrase = $this->config->get('secure_cron_key');
		$phrase = $this->input->get('phrase', '');

		if ($requirePhrase && empty($phrase) || ($requirePhrase && $storedPhrase != $phrase)) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the honeypot key to be used in the form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function execute()
	{
		// Ensure that the request is valid
		$allowed = $this->allowed();

		if (!$allowed) {
			echo JText::_('COM_KT_CRONJOB_PASSPHRASE_INVALID');
			exit;			
		}

		$messages = [];

		// Process renewals for honeypot keys
		$renewed = $this->renewHoneypotKey();

		if ($renewed) {
			$messages['honeypot'] = JText::_('COM_KT_HONEYPOT_KEY_RENEWED_SUCCESSFULLY');
		}

		$total = $this->input->get('total', $this->config->get('notification_total_email'), 'int');

		// mailer
		$mailer = KT::mailer();
		$mailer->send($total);
		$messages['emails'] = JText::_('COM_KOMENTO_EMAIL_BATCH_PROCESS_FINISHED');

		// email digest
		if ($this->config->get('email_digest_enabled')) {

			$sub = KT::subscription();
			$sub->processDigest();
			$messages['digest'] = JText::_('COM_KT_DIGEST_PROCESS_FINISHED');
		}


		//[KOMENTO PAID START]
		// one signal push
		$push = KT::push();

		if ($push->isEnabled()) {
			$push->notifyQueue(KOMENTO_PUSH_NOTIFICATION_THRESHOLD);
			$messages['push'] = JText::_('COM_KOMENTO_PUSH_NOTTIFICATION_BATCH_PROCESS_FINISHED');
		}
		//[KOMENTO PAID END]

		// Save the last execution cron time
		$model = KT::model('Settings');
		$model->save([
			'cron_last_execute' => JFactory::getDate()->toSql()
		]);

		header('Content-type: application/json; UTF-8');

		echo json_encode($messages);
		exit;
	}

	/**
	 * Renews honeypot keys if necessary
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function renewHoneypotKey()
	{
		if (!$this->config->get('antispam_honeypot_daily')) {
			return false;
		}

		// Determine if it is time we need to update
		$last = $this->config->get('antispam_honeypot_last_switch');
		$update = false;
		$now = time();

		if (!$last) {
			$update = true;
		}

		if ($last) {
			$diff = $now - $last;
			$update = $diff > 86400;
		}

		// Update the honeypot key used
		if ($update) {
			$honeypot = KT::honeypot();
			$key = $honeypot->generateKey();

			$model = KT::model('Settings');
			$model->save([
				'antispam_honeypot_last_switch' => $now,
				'antispam_honeypot_key' => $key
			]);

			return true;
		}

		return false;
	}
}
