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

class KomentoTableMailq extends KomentoTable
{
	public $id = null;
	public $mailfrom = null;
	public $fromname = null;
	public $recipient = null;
	public $subject = null;
	public $body = null;
	public $created = null;
	public $type = null;
	public $status = null;
	public $template = null;
	public $data = null;
	public $params = null;

	public function __construct(&$db)
	{
		parent::__construct('#__komento_mailq', 'id', $db);
	}

	public function processTemplateContent()
	{
		$content = '';

		if ($this->template) {

			$notification = KT::notification();

			$data = '';
			$unsubscribe = false;

			if ($this->data) {
				$data = json_decode($this->data);
			}

			$recipient = (object) [
				'id' => 0,
				'fullname' => '',
				'email' => ''
			];

			if ($this->params) {
				$params = json_decode($this->params);

				$subscriptionId = FH::normalize($params, 'subscriptionid', false);

				// construct user object
				$recipient->id = $params->id;
				$recipient->fullname = $params->name;
				$recipient->email = $params->email;

				if ($subscriptionId) {
					$unsubscribeData = [
						// user id
						'id' => $params->id,

						// article id
						'cid' => $params->cid,
						'subscriptionid' => $subscriptionId,
						'component' => $params->component,
						'email' => $params->email,
						'token' => $params->token
					];

					// Generate the unsubscribe hash
					$hash = base64_encode(json_encode($unsubscribeData));
					$unsubscribe = rtrim(JURI::root(), '/') . '/index.php?option=com_komento&controller=subscriptions&task=unSubscribeFromEmail&data=' . $hash;
				}
			}

			$content = $notification->getTemplateContents($this->template, $data, ['recipient' => $recipient], $unsubscribe);
		}

		return $content;
	}

}
