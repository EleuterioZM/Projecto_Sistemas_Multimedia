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

class HubSpot extends Integration
{
	/**
	 * Create a new instance
	 * 
	 * @param string $key Your HubSpot API key
	 */
	public function __construct($options)
	{
		parent::__construct();

		$this->setKey(is_array($options) ? $options['api'] : $options);
		$this->setEndpoint('https://api.hubapi.com');
	}

	/**
	 *  Subscribe user to HubSpot
	 *
	 *  API References:
	 *  http://developers.hubspot.com/docs/methods/contacts/update_contact-by-email
	 *
	 *  @param   string   $email 	User's email address
	 *  @param   string   $params  	The forms extra fields
	 *
	 *  @return  void
	 */
	public function subscribe($email, $params)
	{
		$fields = $this->validateCustomFields($params);

		$fields[] = array('property' => 'email', 'value' => $email);

		$data = array(
			'properties' => $fields
		);

		$this->post('contacts/v1/contact/createOrUpdate/email/' . $email . '/?hapikey=' . $this->key, $data);

		return true;
	}

	/**
	 *  Get the last error returned by either the network transport, or by the API.
	 *
	 *  API References:
	 *  http://developers.hubspot.com/docs/faq/api-error-responses
	 *
	 *  @return  string
	 */
	public function getLastError()
	{
		$body = $this->last_response->body;

		$message = '';

		if ((isset($body['status'])) && ($body['status'] == 'error'))
		{
			$message = $body['message'];
		}

		if (isset($body['validationResults']) && is_array($body['validationResults']) && count($body['validationResults']))
		{	
			foreach ($body['validationResults'] as $key => $validation)
			{
				if ($validation['isValid'] === false)
				{
					$message .= ' - ' . $validation['message'];
				}
			}	
		}

		return $message;
	}

	/**
	 *  Returns a new array with valid only custom fields
	 *
	 *  API References:
	 *  http://developers.hubspot.com/docs/methods/contacts/v2/get_contacts_properties
	 *
	 *  @param   array  $formCustomFields   Array of custom fields
	 *
	 *  @return  array  					Array of valid only custom fields
	 */
	public function validateCustomFields($formCustomFields)
	{

		$fields = array();

		if (!is_array($formCustomFields))
		{
			return $fields;
		}

		$accountFields = $this->get('properties/v1/contacts/properties?hapikey='.$this->key);

		if (!$this->request_successful)
		{
			return $fields;
		}

		$accountFieldsNames = array_map(
			function ($ar)
			{
				return $ar['name'];
			}, $accountFields
		);

		$formCustomFieldsKeys = array_keys($formCustomFields);

		foreach ($accountFieldsNames as $accountFieldsName)
		{
			if (!in_array($accountFieldsName, $formCustomFieldsKeys))
			{
				continue;
			}

			$fields[] = array(
				"property" => $accountFieldsName,
				"value"    => $formCustomFields[$accountFieldsName],
			);
		}

		return $fields;
	}
}