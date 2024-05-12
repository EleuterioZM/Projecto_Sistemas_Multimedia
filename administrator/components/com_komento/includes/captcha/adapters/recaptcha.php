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

/*
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class KomentoCaptchaRecaptcha extends KomentoBase
{
	const RECAPTCHA_API_SERVER = "https://www.google.com/recaptcha/api.js";
	private static $_siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";
	private static $_version = "php_1.0";

	public function verify($data, $params = array())
	{
		if (!array_key_exists('recaptcha_response_field', $data) || empty($data['recaptcha_response_field'])) {
			$this->setError(JText::_('COM_KT_CAPTCHA_INVALID_RESPONSE'));
			return false;
		}

		$config = KT::getConfig();
		$privatekey	= $config->get('antispam_recaptcha_private_key');
		$remoteip = FH::getIp();

		if (empty($privatekey)) {
			$this->setError( 'To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>' );
			return false;
		}

		$params = array(
					'secret' => $privatekey,
					'remoteip' => $remoteip,
					'response' => $data['recaptcha_response_field'],
					'v' => self::$_version
				) + $params;

		$response = $this->_recaptcha_http_post(self::$_siteVerifyUrl, $params);

		$answers = json_decode($response, true);

		if (trim($answers ['success']) == false) {
		   $this->setError(JText::_('COM_KT_CAPTCHA_INVALID_RESPONSE'));
		   return false;
		}

	   return true;
	}

	/**
	 * Generates the recaptcha output 
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function html()
	{
		$publicKey = $this->config->get('antispam_recaptcha_public_key');
		$language = $this->config->get('antispam_recaptcha_lang', 'en');
		$layout = $this->config->get('antispam_recaptcha_theme', 'clean');
		$invisible = $this->config->get('antispam_recaptcha_invisible');

		$server = self::RECAPTCHA_API_SERVER;
		$server = $server . '?hl=' . $language;

		$theme = KT::themes();
		$theme->set('server', $server);
		$theme->set('invisible', $invisible);
		$theme->set('key', $publicKey);
		$theme->set('language', $language);
		$theme->set('layout', $layout);

		// Use AJAX method to prevent operation aborted problems with IE
		return $theme->output('site/captcha/recaptcha/default');
	}

	/**
	 * Encodes the given data into a query string format
	 * @param $data - array of string elements to be encoded
	 * @return string - encoded request
	 */
	private function _recaptcha_qsencode($data)
	{
		$req = "";

		foreach ($data as $key => $value) {
			$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
		}

		// Cut the last '&'
		$req = rtrim($req, '&');
		return $req;
	}

	private function _recaptcha_http_post($path, $data, $port = 80)
	{
		$config = KT::getConfig();

		$req = $this->_recaptcha_qsencode ($data);

		$url = $path . $req;

		// We use Curl instead of file_get_contents for security reason
		$rCURL = curl_init();

		curl_setopt($rCURL, CURLOPT_URL, $url);
		curl_setopt($rCURL, CURLOPT_HEADER, 0);
		curl_setopt($rCURL, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($rCURL, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($rCURL);

		curl_close($rCURL);

		return $response;
	}

	public static function getReloadSyntax()
	{
		return 'Recaptcha.reload();';
	}
}
