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

use Foundry\Libraries\Cleantalk;

class KomentoCleantalk
{
	private $enabled = null;
	private $lib = null;

	public function __construct()
	{
		$this->config = KT::config();
	}

	/**
	 * Determines if cleantalk is enabled
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isEnabled()
	{
		if (is_null($this->enabled)) {
			$this->key = $this->config->get('cleantalk_key');
			$this->enabled = (bool) $this->config->get('cleantalk_enabled') && $this->key;

			$this->enabled = $this->enabled && $this->key;
		}

		return $this->enabled;
	}

	/**
	 * Validates a comment
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function validate(KomentoComment $comment)
	{
		$lib = new Cleantalk($this->config->get('cleantalk_key'));	

		// Determines how long it took to submit the comment
		$submissionTime = time() - KT::session()->getTime();

		$response = $lib->validate($submissionTime, $comment->name, $comment->email, $comment->getContent(), $comment->ip);

		// 3 - 100% spam
		// 2 - possible spam
		if ($response->allow === 0 && $response->stop_queue === 1) {
			return KOMENTO_CLEANTALK_SPAM;
		}

		if ($response->allow === 0 && $response->stop_queue === 0 && $response->spam === 1) {
			return KOMENTO_CLEANTALK_POSSIBLE_SPAM;
		}

		return false;
	}
}
