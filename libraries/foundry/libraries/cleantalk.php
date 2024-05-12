<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
namespace Foundry\Libraries;

defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/cleantalk/Cleantalk.php');
require_once(__DIR__ . '/cleantalk/CleantalkAPI.php');
require_once(__DIR__ . '/cleantalk/CleantalkRequest.php');
require_once(__DIR__ . '/cleantalk/CleantalkResponse.php');
require_once(__DIR__ . '/cleantalk/CleantalkHelper.php');

use Cleantalk\CleantalkResponse;
use Cleantalk\CleantalkRequest;

class CleanTalk
{
	private $key = null;
	private $server = 'https://moderate.cleantalk.org';

	public function __construct($key, $server = '')
	{
		$this->key = $key;

		if ($server) {
			$this->server = $server;
		}
	}

	/**
	 * Creates a new request object
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	private function getCleanTalk()
	{
		$cleantalk = new \Cleantalk\Cleantalk();
		$cleantalk->server_url = $this->server;

		return $cleantalk;
	}

	/**
	 * Creates a new request object
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	private function getRequest()
	{
		$request = new \Cleantalk\CleantalkRequest();
		$request->auth_key = $this->key;
		$request->agent = 'php-api';
		$request->js_on = 1;

		return $request;
	}

	/**
	 * Validates a request
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function validate($submissionTime, $name = '', $email = '', $message = '', $ip = null)
	{
		$request = $this->getRequest();

		$request->sender_nickname = $name;
		$request->sender_email = $email;
		$request->submit_time = $submissionTime;
		$request->sender_ip = $ip;

		$validateMethod = 'isAllowUser';
		if (is_null($ip) && isset($_SERVER['REMOTE_ADDR'])) {
			$request->sender_ip = $_SERVER['REMOTE_ADDR']; 
		}

		if ($message) {
			$validateMethod = 'isAllowMessage';
			$request->message = $message;
		}

		$lib = $this->getCleanTalk();
		$response = $lib->$validateMethod($request);
		
		return $response;
	}
}