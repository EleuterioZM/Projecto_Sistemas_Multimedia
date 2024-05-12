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

class SendInBlue extends Integration
{
	/**
	 * Create a new instance
	 * @param array $options The service's required options
	 * @throws \Exception
	 */
	public function __construct($options)
	{
		parent::__construct();
		$this->setKey($options['api']);
		$this->setEndpoint('https://api.sendinblue.com/v2.0');
		$this->options->set('headers.api-key', $this->key);
	}

	/**
	 *  Subscribes a user to a SendinBlue Account
	 *
	 *  API Reference:
	 *  https://apidocs.sendinblue.com/user/#1
	 *
	 *  @param   string  $email   The user's email
	 *  @param   array   $params  All the form fields
	 *  @param   string  $listid  The List ID
	 *
	 *  @return  boolean
	 */
	public function subscribe($email, $params, $listid = false)
	{
		$data = array(
			'email'      => $email,
			'attributes' => $params,
		);

		if ($listid) 
		{
			$data['listid'] = array($listid);
		}

		$this->post('user/createdituser', $data);

		return true;
	}

	/**
	 *  Returns all Campaign  lists
	 *
	 *  https://apidocs.sendinblue.com/list/#1
	 *
	 *  @return  array
	 */
	public function getLists()
	{
		$data = array(
			'page' => 1,
			'page_limit' => 50
		);

		$lists = array();

		$data = $this->get('/list', $data);

		if (!isset($data['data']['lists']) || !is_array($data['data']['lists']) || $data['data']['total_list_records'] == 0)
		{
			return $lists;
		}

		foreach ($data['data']['lists'] as $key => $list)
		{
			$lists[] = array(
				'id'   => $list['id'],
				'name' => $list['name']
			);
		}

		return $lists;
		
	}

	/**
	 *  Get the last error returned by either the network transport, or by the API.
	 *
	 *  API Reference:
	 *  https://apidocs.sendinblue.com/response/
	 *
	 *  @return  string
	 */
	public function getLastError()
	{
		$body    = $this->last_response->body;
		$message = '';

		if (isset($body['code']) && ($body['code'] == 'failure'))
		{
			$message = $body['message'];
		}

		return $message;
	}
}