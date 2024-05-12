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

class CampaignMonitor extends Integration
{
	/**
	 * Create a new instance
	 * 
	 * @param array $options The service's required options
	 */
	public function __construct($options)
	{
		parent::__construct();
		$this->setKey($options);
		$this->setEndpoint('https://api.createsend.com/api/v3.1');
		$this->options->set('userauth', $this->key);
		$this->options->set('passwordauth', 'nopass');
	}

	/**
	 *  Subscribe user to Campaign Monitor
	 *
	 *  API References:
	 *  https://www.campaignmonitor.com/api/subscribers/#importing_many_subscribers
	 *  Reminder:
	 *  The classic add_subscriber method of Campaign Monitor's API is NOT instantaneous!
	 *  It is suggested to use their import method for instantaneous subscriptions!
	 *
	 *  @param   string   $email         	  User's email address
	 *  @param 	 string   $name 			  User's Name
	 *  @param   string   $list          	  The Campaign Monitor list unique ID
	 *  @param   array    $custom_fields  	  Custom Fields
	 *
	 *  @return  void
	 */
	public function subscribe($email, $name, $list, $customFields = array())
	{
		$data = array(
			'Subscribers' => array(
				array(
					'EmailAddress' => $email,
					'Name'         => $name,
					'Resubscribe'  => true,
				),
			),
		);

		if (is_array($customFields) && count($customFields))
		{
			$data['Subscribers'][0]['CustomFields'] = $this->validateCustomFields($customFields, $list);
		}

		$this->post('subscribers/' . $list . '/import.json', $data);

		return true;
	}

	/**
	 *  Returns a new array with valid only custom fields
	 *
	 *  @param   array  $formCustomFields   Array of custom fields
	 *
	 *  @return  array  					Array of valid only custom fields
	 */
	public function validateCustomFields($formCustomFields, $list)
	{
		$fields = array();

		if (!is_array($formCustomFields))
		{
			return $fields;
		}

		$listCustomFields = $this->get('lists/' . $list . '/customfields.json');

		if (!$this->request_successful)
		{
			return $fields;
		}

		$formCustomFieldsKeys = array_keys($formCustomFields);

		foreach ($listCustomFields as $listCustomField)
		{
			$field_name = $listCustomField['FieldName'];

			if (!in_array($field_name, $formCustomFieldsKeys))
			{
				continue;
			}

			$value = $formCustomFields[$field_name];

			// Always convert custom field value to array, to support multiple values in a custom field.
			$value = is_array($value) ? $value : (array) $value;

			foreach ($value as $val)
			{
				$fields[] = array(
					'Key'   => $field_name,
					'Value' => $val,
				);	
			}
		}

		return $fields;
	}

	/**
	 *  Get the last error returned by either the network transport, or by the API.
	 *
	 *  @return  string
	 */
	public function getLastError()
	{
		$body    = $this->last_response->body;
		$message = '';

		if (isset($body['Message']))
		{
			$message = $body['Message'];
		}

		if (isset($body['ResultData']['FailureDetails'][0]['Message']))
		{
			$message .= ' - ' . $body['ResultData']['FailureDetails'][0]['Message'];
		}

		return $message;
	}

	/**
	 *  Returns all Client lists
	 *
	 *  https://www.campaignmonitor.com/api/clients/#getting-subscriber-lists
	 *
	 *  @return  array
	 */
	public function getLists()
	{
		$clients = $this->getClients();

		if (!is_array($clients))
		{
			return;
		}

		$lists = array();

		foreach ($clients as $key => $client)
		{
			if (!isset($client['ClientID']))
			{
				continue;
			}

			$clientLists = $this->get('/clients/' . $client['ClientID'] . '/lists.json');

			if (!is_array($clientLists))
			{
				continue;
			}

			foreach ($clientLists as $key => $clientList)
			{
				$lists[] = array(
					'id'   => $clientList['ListID'],
					'name' => $clientList['Name']
				);
			}
		}

		return $lists;
	}

	/**
	 *  Get Clients
	 *
	 *  https://www.campaignmonitor.com/api/account/
	 *
	 *  @return  mixed   Array on success, Null on fail
	 */
	private function getClients()
	{
		$clients = $this->get('/clients.json');

		if (!$this->success())
		{
			return;
		}

		return $clients;
	}
}