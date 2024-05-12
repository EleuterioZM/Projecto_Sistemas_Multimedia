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

class KomentoCaptchaCaptcha extends KomentoBase
{
	/**
	 * Renders the captcha form
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function html()
	{
		$table = KT::table('Captcha');
		$table->created = FH::date()->toSql();
		$table->store();

		$theme = KT::themes();
		$theme->set('id', $table->id);
		$theme->set('url', $this->getCaptchaUrl($table->id));
		
		return $theme->output('site/captcha/core/default');
	}

	/**
	 * Verify captcha's response
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function verify($data, $params = array())
	{
		if (!array_key_exists('captcha-response', $data) || !array_key_exists('captcha-id', $data)) {
			return false;
		}

		$id = $data['captcha-id'];
		$response = $data['captcha-response'];

		$table = KT::table('Captcha');
		$table->load($id);

		if (!$table->response || !$table->verify($response)) {
			$this->setError(JText::_('COM_KOMENTO_CAPTCHA_INVALID_RESPONSE'));
			return false;
		}

		return true;
	}

	/**
	 * Generates the reload script
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getReloadSyntax()
	{
		$currentId = $this->input->get('id', 0, 'int');

		if ($currentId) {
			$table = KT::table('Captcha');
			$table->load($currentId);
			$table->delete();
		}

		// Regenerate a new captcha object
		$table = KT::table('Captcha');
		$table->created = FH::date()->toSql();
		$table->store();

		$url = $this->getCaptchaUrl($table->id);

		$reloadData = [
			'image'	=> $url,
			'id' => $table->id
		];

		return $reloadData;
	}

	/**
	 * Generates the link for the captcha image
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getCaptchaUrl($id)
	{
		$base = 'index.php?option=com_komento&controller=captcha&task=generate&id=' . $id . '&tmpl=component';

		$url = JURI::root() . $base;

		return $url;
	}
}
