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

class KomentoTableDownload extends KomentoTable
{
	public $id = null;
	public $userid = null;
	public $state = null;
	public $params = null;
	public $created = null;

	public function __construct(&$db)
	{
		parent::__construct('#__komento_download', 'id', $db);
	}

	/**
	 * Determine whether user has requested.
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function isExists()
	{
		if (is_null($this->id)) {
			return false;
		}

		return true;
	}

	/**
	 * Determine when the state is ready
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function isNew()
	{
		return $this->state == KOMENTO_DOWNLOAD_REQ_NEW;
	}

	/**
	 * Determine when the state is ready
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function isProcessing()
	{
		return $this->state == KOMENTO_DOWNLOAD_REQ_PROCESS;
	}

	/**
	 * Determine when the state is ready
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function isReady()
	{
		return $this->state == KOMENTO_DOWNLOAD_REQ_READY;
	}

	/**
	 * Method used to update the request state.
	 *
	 * @since 3.1
	 * @access public
	 */
	public function updateState($state)
	{
		$this->state = $state;

		// debug. need to uncomment.
		return $this->store();
	}

	/**
	 * Method used to set filepath.
	 *
	 * @since 3.1
	 * @access public
	 */
	public function setFilePath($filepath)
	{
		$params = new JRegistry($this->params);
		$params->set('path', $filepath);
		$this->params = $params->toString();
	}

	/**
	 * Request state of the download. Return false if not exist.
	 *
	 * @since 3.1
	 * @access public
	 */
	public function getState()
	{
		if (!$this->isExists()) {
			return false;
		}

		return $this->state;
	}

	/**
	 * Retrieves the label for the state (used for display purposes)
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function getStateLabel()
	{
		if ($this->getState() == KOMENTO_DOWNLOAD_REQ_READY) {
			return JText::_('COM_KT_DOWNLOAD_STATE_READY');
		}

		return JText::_('COM_KT_DOWNLOAD_STATE_PROCESSING');
	}

	/**
	 * Retrieves the requester
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function getRequester()
	{
		$user = KT::user($this->userid);

		return $user;
	}

	/**
	 * Method used to send email notification to user who requested to download GDPR details.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function sendNotification()
	{
		$jConfig = FH::jconfig();
		$my = KT::user($this->userid);

		$emailData = array('downloadLink' => $this->getDownloadLink(true));
		$subject = JText::_('COM_KT_EMAILS_GDPR_DOWNLOAD_SUBJECT');

		$state = KT::notification()->insertMailQueue($subject, 'site/emails/gdpr.ready', $emailData, $my, false);

		return true;
	}

	/**
	 * Method to ouput the zip file to browser for download.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function showArchiveDownload()
	{
		$param = new JRegistry($this->params);
		$file = $param->get('path', '');

		if (!$file) {
			return false;
		}

		$user = KT::user($this->userid);

		$fileName =  JFilterOutput::stringURLSafe($user->getName());
		$fileName .= '.zip';

		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=$fileName");
		header("Content-Length: " . filesize($file));

		echo file_get_contents($file);
		exit;
	}

	/**
	 * Method generate the download link of this request
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function getDownloadLink()
	{
		$link = JRoute::_('index.php?option=com_komento&view=dashboard&layout=downloaddata');

		// remove relatiave path if exist
		$relpath = JURI::root(true);

		if ($relpath != '' && strpos($link, $relpath) === 0) {
			$link = substr($link, strlen($relpath));
		}

		$link = rtrim(JURI::root(), '/') . '/' . ltrim($link, '/');

		return $link;
	}

	/**
	 * Retrieves the expiration in days
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function getExpireDays()
	{
		$days = KT::config()->get('userdownload_expiry');

		return $days;
	}

	/**
	 * Override parent delete method to manually delete archive file as well.
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function delete($pk = null)
	{
		// delete archive file if there is any.
		$param = new JRegistry($this->params);
		$file = $param->get('path', '');

		if ($file) {
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}

		return parent::delete($pk);
	}

}
