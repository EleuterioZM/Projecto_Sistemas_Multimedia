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

use Foundry\Libraries\Akismet;

class KomentoAkismet
{
	private $enabled = null;
	private $lib = null;

	public function __construct()
	{
		$this->config = KT::config();
		$this->key = $this->config->get('antispam_akismet_key');
		$this->enabled = (bool) $this->config->get('antispam_akismet');
	}

	/**
	 * Generates a new akismet library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function getLibrary()
	{
		if (is_null($this->lib)) {
			$this->lib = new Akismet(JURI::root(), $this->key);
		}

		return $this->lib;
	}

	/**
	 * Determines if cleantalk is enabled
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isEnabled()
	{
		static $enabled = null;

		if (is_null($enabled)) {
			$enabled = $this->enabled && $this->key;
		}

		return $enabled;
	}

	/**
	 * Checks if a comment is spam
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isSpam($comment)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		$data = [
			'author' => $comment->table->name,
			'email' => $comment->table->email,
			'website' => $comment->table->url,
			'body' => $comment->table->comment
		];

		$lib = $this->getLibrary();
		$lib->setComment($data);

		// If there are errors, we just assume that everything is fine so the entire
		// operation will still work correctly.
		if ($lib->errorsExist()) {
			return false;
		}

		return $lib->isSpam();
	}

	/**
	 * Submits a known comment as spam
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function submitSpam($data)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		$lib = $this->getLibrary();
		$lib->akismet->setComment($data);

		$lib->akismet->submitSpam();

		if ($lib->akismet->errorsExist()) {
			return false;
		}

		return true;
	}

	/**
	 * Submit a false positive
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function submitHam($data)
	{
		if (!$this->isEnabled()) {
			return false;
		}
		
		$lib = $this->getLibrary();
		$lib->setComment($data);
		$lib->submitHam();

		if ($lib->errorsExist()) {
			return false;
		}

		return true;
	}
}
