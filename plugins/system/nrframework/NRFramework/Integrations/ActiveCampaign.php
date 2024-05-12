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

class ActiveCampaign extends Integration
{
	/**
	 * Create a new instance
	 * @param array $options The service's required options
	 */
	public function __construct($options)
	{
		parent::__construct();
		$this->setKey($options);
		$this->setEndpoint($options['endpoint'] . '/api/3');
		$this->options->set('headers.Api-Token', $this->key);
	}

	/**
	 *  Subscribe user to ActiveCampaign List
	 *
	 *  https://developers.activecampaign.com/v3/reference#create-contact
	 *
	 *  @param   string  $email           The Email of the Contact
	 *  @param   string  $name            The name of the Contact (Name can be also declared in Custom Fields)
	 *  @param   string  $list            List ID
	 *  @param   string  $tags            Tags for this contact (comma-separated). Example: "tag1, tag2, etc"
	 *  @param   array   $customfields    Custom Fields
	 *  @param   boolean $updateexisting  Update Existing User
	 *
	 *  @return  void                   
	 */
	public function subscribe($email, $name, $lists, $tags = '', $customfields = [], $updateexisting = true)
	{
		// Detect name
		$name = (is_null($name) || empty($name)) ? $this->getNameFromCustomFields($customfields) : explode(' ', $name, 2);
		$apiAction = ($updateexisting) ? 'contact/sync' : 'contacts';

		$data = [
			'contact' => [
				'email'     => $email,
				'firstName' => isset($name[0]) ? $name[0] : null,
			    'lastName'  => isset($name[1]) ? $name[1] : null,
				'phone'		=> $this->getCustomFieldValue('phone', $customfields),
				'ip4' 		=> \NRFramework\User::getIP()
			],
		];

		$this->post($apiAction, $data);

		if (!$this->request_successful)
		{
			return;
		}

		// Retrive the contact's ID
		$contact_id = $this->getContactIDFromResponse();
		
		// Add Lists to Contact
		$this->addListsToContact($contact_id, $lists);

		// Add Tags to Contact
		if (!empty($tags))
		{
			$tags = is_array($tags) ? $tags : explode(',', $tags);

			$tag_ids = $this->convertTagNamesToIDs($tags);

			if ($tag_ids && !empty($tag_ids))
			{
				$this->addTagsToContact($tag_ids, $contact_id);
			}
		}

		// Add Custom Fields to Contact
		$this->addCustomFieldsToContact($customfields, $contact_id);
	}

	/**
	 * Update Custom Field Values for a Contact
	 * 
	 * API Reference: https://developers.activecampaign.com/v3/reference#fieldvalues
	 *
	 * @param  array   $custom_fields	Array of custom field values
	 * @param  integer $contact_id		The contact's ID
	 *
	 * @return mixed	Null on failure, void on success
	 */
	private function addCustomFieldsToContact($custom_fields, $contact_id)
	{
		if (empty($custom_fields))
		{
			return;
		}
		
		$custom_fields = array_change_key_case($custom_fields);

		if (!$all_custom_fields = $this->getAllCustomFields())
		{
			return;
		}

		foreach ($custom_fields as $custom_field_key => $custom_field_value)
		{
			if (empty($custom_field_value))
			{
				continue;
			}

			$custom_field = strtolower(trim($custom_field_key));

			if (!array_key_exists($custom_field, $all_custom_fields))
			{
				continue;
			}

			// Let's add Custom Field to our contact
			$custom_field_data = $all_custom_fields[$custom_field];

			// Radio buttons expect a string. Not an array.
			if ($custom_field_data['type'] == 'checkbox' && is_array($custom_field_value))
			{
				$custom_field_value = implode('||', $custom_field_value);
				$custom_field_value = '||' . $custom_field_value . '||';
			}

			$this->post('fieldValues', [
				'fieldValue' => [
					'contact' => $contact_id,
					'field'   => $custom_field_data['id'],
					'value'   => $custom_field_value
				]
			]);
		}
	}

	/**
	 * Add tags to contact
	 * 
	 * API Reference: https://developers.activecampaign.com/v3/reference#create-contact-tag
	 *
	 * @param array   $tag_ids		Array of tag IDs
	 * @param integer $contact_id	The contact's ID
	 *
	 * @return void
	 */
	private function addTagsToContact($tag_ids, $contact_id)
	{
		foreach ($tag_ids as $tag_id)
		{
			$this->post('contactTags', [
				'contactTag' => [
					'contact' => $contact_id,
					'tag'     => $tag_id,
				]
			]);
		}
	}

	/**
	 * Convert a list of tag names to tag IDs
	 *
	 * @param  array $tags	Array ot tag names
	 *
	 * @return mixed	Null on failure, assosiative tag name-based array on success.
	 */
	private function convertTagNamesToIDs($tags)
	{
		if (!$account_tags = $this->getAllTags())
		{
			return; 
		}

		$account_tags = array_map('strtolower', $account_tags);

		$tag_ids = [];
		
		foreach ($tags as $tag)
		{
			if (empty($tag))
			{
				continue;
			}

			$tag = strtolower(trim($tag));

			if (!$tag_id = array_search($tag, $account_tags))
			{
				continue;
			}

			$tag_ids[] = $tag_id;
		}

		return $tag_ids;
	}

	/**
	 * Retrieve all contact-based tags
	 * 
	 * API Reference: https://developers.activecampaign.com/v3/reference#list-all-tasks
	 *
	 * @return mixed	Null on failure, assosiative array on success
	 */
	private function getAllTags()
	{
		$tags = $this->get('tags');

		if (!$tags || !is_array($tags) || !isset($tags['tags']))
		{
			return;
		}

		$tags_ = [];

		foreach ($tags['tags'] as $tag)
		{
			if ($tag['tagType'] != 'contact')
			{
				continue;
			}

			$tags_[$tag['id']] = $tag['tag'];
		}

		return $tags_;
	}

	/**
	 * Add lists to contact
	 *
	 * @param  integer $contact_id	The Active Campaign Contact ID
	 * @param  mixed   $lists		The list ID to add the contact to.
	 *
	 * @return void
	 */
	private function addListsToContact($contact_id, $lists)
	{
		$lists = is_array($lists) ? $lists : explode(',', $lists);

		foreach ($lists as $list)
		{
			$this->post('contactLists', [
				'contactList' => [
					'list' => $list,
					'contact' => $contact_id,
					'status' => 1
				]
			]);
		}
	}

	/**
	 * Determine the newly created contact's ID
	 *
	 * @return string
	 */
	private function getContactIDFromResponse() 
	{
		$response = $this->last_response;

		if (isset($response->body) && isset($response->body['contact']) && isset($response->body['contact']['id']))
		{
			return $response->body['contact']['id'];
		}
	}

	/**
	 * Search for First Name and Last Name in Custom Fields and return an array with both values.
	 *
	 * @param	array	$customfields	The Custom Fields array passed by the user.
	 *
	 * @return	array
	 */
	private function getNameFromCustomFields($customfields)
	{
		return [
			(string) $this->getCustomFieldValue(['first_name', 'First Name'], $customfields),
			(string) $this->getCustomFieldValue(['last_name', 'Last Name'], $customfields)
		];
	}

	/**
	 *  Retrieve all account lists
	 *  
	 *  API Reference: https://developers.activecampaign.com/v3/reference#retrieve-all-lists
	 *  
	 *  @return  mixed  Null on failure, Array on success
	 */
	public function getLists()
	{
		$data = $this->get('lists');

		if (!$data || !isset($data['lists']) || count($data['lists']) == 0)
		{
			return;
		}

		$lists = [];

		foreach ($data['lists'] as $list)
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
	 *  API Reference: https://developers.activecampaign.com/v3/reference#errors
	 *
	 *  @return  string
	 */
	public function getLastError()
	{
		$error_code = $this->last_response->code;
		$error_message = 'Active Campaign Error';

		switch ((int) $error_code)
		{
			case 403:
				$error_message = 'The request could not be authenticated or the authenticated user is not authorized to access the requested resource.';
				break;
			case 404:
				$error_message = 'The requested resource does not exist.';
				break;
			case 422:
				$error_message = 'The request could not be processed, usually due to a missing or invalid parameter.';

				if (isset($this->last_response->body['errors']) && isset($this->last_response->body['errors'][0]))
				{
					$error_message = $this->last_response->body['errors'][0]['title'];
				}

				break;
		}

		return $error_message;
	}

	/**
	 *  Returns the Active Campaign Account's Custom Fields
	 *
	 *  API Reference: https://developers.activecampaign.com/v3/reference#retrieve-fields-1
	 *
	 *  @return  array
	 */
	public function getAllCustomFields()
	{
		$fields = $this->get('fields');

		if (!$fields || !isset($fields['fields'])) 
		{
			return;
		}


		// Make our life easier by creating a title-based assosiative array 
		$f = [];

		foreach ($fields['fields'] as $key => $field)
		{
			if (!$field || !isset($field['title']))
			{
				continue;
			}

			$key = strtolower(trim($field['title']));

			$f[$key] = $field;
		}

		return $f;
	}

	/**
	 * Make an HTTP GET request for retrieving data.
	 * 
	 * ActiveCampaign has a limit of max 100 results per page.
	 * https://developers.activecampaign.com/reference#pagination
	 * 
	 * @param   string 		  $method URL of the API request method
	 * @param   array		  $args Assoc array of arguments (usually your data)
	 * 
	 * @return  array|false   Assoc array of API response, decoded from JSON
	 */
	public function get($method, $args = array())
	{
		$args['limit']  = isset($args['limit']) ? $args['limit'] : 100;
		$args['offset'] = isset($args['offset']) ? $args['offset'] : 0;

		$response = parent::get($method, $args);

		if ($args['offset'] < (int) $response['meta']['total'])
		{
			$args['offset'] += $args['limit'];
			$response_next = $this->get($method, $args);
			$response[$method] = array_merge($response[$method], $response_next[$method]);
		}

		return $response;
	}
}