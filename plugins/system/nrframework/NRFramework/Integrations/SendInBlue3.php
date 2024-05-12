<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Integrations;

// No direct access
defined('_JEXEC') or die;

class SendInBlue3 extends Integration
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
		$this->setEndpoint('https://api.sendinblue.com/v3');
		$this->options->set('headers.api-key', $this->key);
	}

	/**
	 *  Subscribes a user to a SendinBlue Account
	 *
	 *  API Reference v3:
	 *  https://developers.sendinblue.com/reference#createcontact
	 *
	 *  @param   string   $email   The user's email
	 *  @param   array    $params  All the form fields
	 *  @param   string   $listid  The List ID
	 *  @param   boolean  $update_existing  Whether to update the existing contact (Only in v3)
	 *
	 *  @return  boolean
	 */
	public function subscribe($email, $params, $listid = false, $update_existing = true)
	{
		$data = [
			'email'      => $email,
			'attributes' => (object) $params,
			'updateEnabled' => $update_existing
		];

		if ($listid)
		{
			$data['listIds'] = [(int) $listid];
		}

		$this->post('contacts', $data);

		return true;
	}

	/**
	 *  Returns all Campaign  lists
	 *
	 *  API Reference v3:
	 *  https://developers.sendinblue.com/reference#getlists-1
	 *
	 *  @return  array
	 */
	public function getLists()
	{
		$data = [
			'page' => 1,
			'page_limit' => 50
		];

		$lists = [];

		$data = $this->get('contacts/lists', $data);

		// sanity check
		if (!isset($data['lists']) || !is_array($data['lists']) || $data['count'] == 0)
		{
			return $lists;
		}

		foreach ($data['lists'] as $key => $list)
		{
			$lists[] = [
				'id'   => $list['id'],
				'name' => $list['name']
			];
		}

		return $lists;
		
	}

	/**
	 *  Get the last error returned by either the network transport, or by the API.
	 *
	 *  API Reference:
	 *  https://developers.sendinblue.com/docs/how-it-works#error-codes
	 *
	 *  @return  string
	 */
	public function getLastError()
	{
		$body    = $this->last_response->body;
		$message = '';

		if (!isset($body['code']))
		{
			return $message;
		}

		return $body['message'];
	}
}