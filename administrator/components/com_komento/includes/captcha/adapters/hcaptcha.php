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

use Foundry\Libraries\Connector;

class KomentoCaptchaHCaptcha extends KomentoBase
{
	/**
	 * Verifies the captcha response
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function verify($data, $params = [])
	{
		if (!array_key_exists('recaptcha_response_field', $data) || empty($data['recaptcha_response_field'])) {
			$this->setError(JText::_('COM_KT_CAPTCHA_INVALID_RESPONSE'));
			return false;
		}

		$private = $this->config->get('antispam_hcaptcha_secret');
		$ip = FH::getIp();
		$response = $data['recaptcha_response_field'];

		$url = 'https://hcaptcha.com/siteverify?secret=' . $private . '&response=' . $response . '&remoteip=' . $ip;

		$connector = new Connector($url);
		$response = $connector->execute()->getResult();

		$response = json_decode($response);

		if (!$response) {
			$this->setError('Unable to decode captcha response from hCaptcha server');
			return false;
		}

		if ($response->success === false) {
		   $this->setError(JText::_('COM_KT_CAPTCHA_INVALID_RESPONSE'));
		   return false;
		}

		return true;
	}

	/**
	 * Generates the hCaptcha input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function html()
	{
		$theme = KT::themes();

		return $theme->output('site/captcha/hcaptcha/default');
	}

	public static function getReloadSyntax()
	{
		return 'hcaptcha.reset();';
	}
}
