<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Integrations;

// No direct access
defined('_JEXEC') or die;

/**
 *  The reCAPTCHA Wrapper
 */
class ReCaptcha extends Integration
{
	/**
	 *  Service Endpoint
	 *
	 *  @var  string
	 */
	protected $endpoint = 'https://www.google.com/recaptcha/api/siteverify';

	/**
	 * Create a new instance
	 * 
	 * @param   array $options
	 * 
	 * @throws \Exception
	 */
	public function __construct($options = array())
	{
		parent::__construct();

		if (!array_key_exists('secret', $options))
		{
			$this->setError('NR_RECAPTCHA_INVALID_SECRET_KEY');
			throw new \Exception($this->getLastError());
		}

		$this->setKey($options['secret']);
	}

	/**
	 *  Calls the reCAPTCHA siteverify API to verify whether the user passes reCAPTCHA test.
	 *
	 *  @param   string   $response  Response string from recaptcha verification.
	 *  @param   string   $remoteip  IP address of end user
	 *
	 *  @return  bool                Returns true if the user passes reCAPTCHA test
	 */
	public function validate($response, $remoteip = null)
	{
		if (empty($response) || is_null($response))
		{
			return $this->setError('NR_RECAPTCHA_PLEASE_VALIDATE');
		}

		$data = array(
			'secret'   => $this->key,
			'response' => $response,
			'remoteip' => $remoteip ?: \NRFramework\User::getIP(),
		);

		$this->get('', $data);

		return true;
	}

	/**
	 * Check if the response was successful or a failure. If it failed, store the error.
	 *
	 * @return bool     If the request was successful
	 */
	protected function determineSuccess()
	{
		$success = parent::determineSuccess();
		$body    = $this->last_response->body;

		if ($body['success'] == false && array_key_exists('error-codes', $body) && count($body['error-codes']) > 0)
		{
			$success = $this->setError(implode(', ', $body['error-codes']));
		}

		return ($this->request_successful = $success);
	}

	/**
	 *  Set wrapper error text
	 *
	 *  @param  String  $error  The error message to display
	 */
	private function setError($error)
	{
		$this->last_error = \JText::_('NR_RECAPTCHA') . ': ' . \JText::_($error);
		return false;
	}
}