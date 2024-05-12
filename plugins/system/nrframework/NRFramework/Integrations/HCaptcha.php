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
 *  The HCaptcha Wrapper
 */
class HCaptcha extends Integration
{
	/**
	 *  Service Endpoint
	 *
	 *  @var  string
	 */
	protected $endpoint = 'https://hcaptcha.com/siteverify';

	/**
	 * Create a new instance
	 * 
	 * @param   array $options
	 * 
	 * @throws \Exception
	 */
	public function __construct($options = [])
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
	 *  Calls the hCaptcha siteverify API to verify whether the user passes hCaptcha test.
	 *
	 *  @param   string   $response  Response string from hCaptcha verification.
	 *  @param   string   $remoteip  IP address of end user
	 *
	 *  @return  bool                Returns true if the user passes hCaptcha test
	 */
	public function validate($response, $remoteip = null)
	{
		if (empty($response) || is_null($response))
		{
			return $this->setError('NR_RECAPTCHA_PLEASE_VALIDATE');
		}

		// remove these headers in order for hCaptcha to be abl to process the request
		$this->options->remove('headers.Accept');
		$this->options->remove('headers.Content-Type');

		// do not encode request
		$this->setEncode(false);

		$data = [
			'secret'   => $this->key,
			'response' => $response,
		];
		
		$this->post('', $data);

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
		$this->last_error = \JText::_('NR_HCAPTCHA') . ': ' . \JText::_($error);
		return false;
	}
}