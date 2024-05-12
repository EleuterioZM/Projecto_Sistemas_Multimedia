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

class GetResponse extends Integration
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
		$this->endpoint = 'https://api.getresponse.com/v3';
		$this->options->set('headers.X-Auth-Token', 'api-key ' . $this->key);
		$this->options->set('headers.Accept-Encoding', 'gzip,deflate');
	}

	/**
	 *  Subscribe user to GetResponse Campaign
	 *
	 *  https://apidocs.getresponse.com/v3/resources/contacts#contacts.create
	 *
	 *  TODO: Update existing contact
	 *
	 *  @param   string   $email        	  Email of the Contact
	 *  @param   string   $name    			  The name of the Contact
	 *  @param   object   $campaign  		  Campaign ID
	 *  @param   object   $customFields  	  Collection of custom fields
	 *  @param   object   $update_existing    Update existing contact
	 *
	 *  @return  void
	 */
	public function subscribe($email, $name, $campaign, $customFields, $update_existing)
	{
		$data = array(
			'email' 			=> $email,
			'name'				=> $name,
			'dayOfCycle'		=> 0,
			'campaign' 			=> ['campaignId' => $campaign],
			'customFieldValues'	=> $this->validateCustomFields($customFields),
			'ipAddress' 		=> \NRFramework\User::getIP()
		);

		if (empty($name) || is_null($name))
		{
			unset($data['name']);
		}

		if ($update_existing) 
		{
			$contactId = $this->getContact($email);
		}

		if (!empty($contactId))
		{
			return $this->post('contacts/' . $contactId, $data);
		}

		$this->post('contacts', $data);
	}

	/**
	 *  Returns a new array with valid only custom fields
	 *
	 *  @param   array  $customFields   Array of custom fields
	 *
	 *  @return  array  Array of valid only custom fields
	 */
	public function validateCustomFields($customFields)
	{
		$fields = array();
	
		if (!is_array($customFields))
		{
			return $fields;
		}

		$accountCustomFields = $this->get('custom-fields');

		if (!$this->request_successful)
		{
			return $fields;
		}

		foreach ($accountCustomFields as $key => $customField)
		{
			if (!isset($customFields[$customField['name']]))
			{
				continue;
			}
				
			$fields[] = array(
				'customFieldId' => $customField['customFieldId'],
				'value'			=> array($customFields[$customField['name']])
			);
		}

		return $fields;
	}

	/**
	 * Get the last error returned by either the network transport, or by the API.
	 * If something didn't work, this should contain the string describing the problem.
	 * 
	 * @return  string  describing the error
	 */
	public function getLastError()
	{
		$body = $this->last_response->body;
		
		if (!isset($body['context']) || !isset($body['context'][0]))
		{
			return $body['codeDescription'] . ' - ' . $body['message'];
		}

		$error = $body['context'][0];

		if (is_array($error) && isset($error['fieldName'])) 
		{
			$errorFieldName = is_array($error['fieldName']) ? implode(' ', $error['fieldName']) : $error['fieldName'];
			return $errorFieldName . ': ' . $error['errorDescription'];
		}
		
		return (is_array($error)) ? implode(' ', $error) : $error;
		
	}

	/**
	 *  Returns all available GetResponse campaigns
	 *
	 *  https://apidocs.getresponse.com/v3/resources/campaigns#campaigns.get.all
	 *
	 *  @return  array
	 */
	public function getLists()
	{
		$data = $this->get('campaigns');

		if (!$this->success())
		{
			return;
		}

		if (!is_array($data) || !count($data))
		{
			return;
		}

		$lists = array();

		foreach ($data as $key => $list)
		{
			$lists[] = array(
				'id'   => $list['campaignId'],
				'name' => $list['name']
			);
		}

		return $lists;
	}

	/**
	 *  Get the Contact resource
	 *
	 *  @param   string  $email  The email of the contact which we want to retrieve
	 *
	 *  @return  string          The Contact ID
	 */
	public function getContact($email)
	{
		if (!isset($email)) 
		{
			return;
		}

		$data = $this->get('contacts', array('query[email]' => $email));

		if (empty($data)) 
		{
			return;
		}

		// the returned data is an array with only one contact
		$contactId = $data[0]['contactId'];

		return ($contactId) ? $contactId : null;
		
	}
}