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

use Joomla\String\StringHelper;

class ConvertKit extends Integration
{
	/**
	 * Create a new instance
	 * 
	 * @param string $api_key Your ConvertKit API Key
	 */
	public function __construct($api_key)
	{
		parent::__construct();

		$this->setKey($api_key);
		$this->setEndpoint('https://api.convertkit.com/v3');
	}

	/**
	 *  Subscribe a user to a ConvertKit Form
	 *  
	 *  API Reference:
	 *  http://help.convertkit.com/article/33-api-documentation-v3
	 *
	 *  @param   string  $email   The subscriber's email
	 *  @param   string  $formid  The account owner's form id
	 *  @param   array   $params  The form's parameters
	 *
	 *  @return  boolean
	 */
	public function subscribe($email, $formid, $params)
	{
		$first_name = (isset($params['first_name'])) ? $params['first_name'] : '';
		$tags       = (isset($params['tags'])) ? $this->convertTagnamesToTagIDs($params['tags']) : '';
		$fields     = $this->validateCustomFields($params);

		$data = array(
			'api_key'    => $this->key,
			'email'      => $email,
			'first_name' => $first_name,
			'tags'       => $tags,
			'fields'     => $fields,
		);

		$this->post('forms/' . $formid . '/subscribe', $data);

		return true;
	}

	/**
	 *  Converts tag names to tag IDs for the subscribe method
	 *
	 *  @param   string  $tagnames  comma separated list of tagnames
	 *
	 *  @return  string             comma separated list of tag IDs
	 */
	public function convertTagnamesToTagIDs($tagnames)
	{
		if (empty($tagnames))
		{
			return;
		}

		$tagArray    = !is_array($tagnames) ? explode(',', $tagnames) : $tagnames;
		$tagnames    = array_map('trim', $tagArray);
		$accountTags = $this->get('tags', array('api_key' => $this->key));

		if (empty($accountTags) || !$this->request_successful)
		{
			return;
		}

		$tagIDs = array();

		foreach ($accountTags['tags'] as $tag)
		{
			foreach ($tagnames as $tagname)
			{
				if (StringHelper::strcasecmp($tag['name'], $tagname) == 0) 
				{
					$tagIDs[] = $tag['id'];
					break;
				}
			}
		}

		return implode(',', $tagIDs);
	}

	/**
	 *  Returns a new array with valid only custom fields
	 *
	 *  @param   array  $formCustomFields   Array of custom fields
	 *
	 *  @return  array  					Array of valid only custom fields
	 */
	public function validateCustomFields($formCustomFields)
	{
		if (!is_array($formCustomFields))
		{
			return;
		}

		$customFields = $this->get('custom_fields', array('api_key' => $this->key));

		if (!$this->request_successful)
		{
			return;
		}

		$fields = array();

		$formCustomFieldsKeys = array_keys($formCustomFields);

		foreach ($customFields['custom_fields'] as $customField)
		{
			if (in_array($customField['key'], $formCustomFieldsKeys))
			{
				$fields[$customField['key']] = $formCustomFields[$customField['key']];
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

		if (isset($body['error']) && !empty($body['error']))
		{
			$message = $body['error'];
		}

		if (isset($body['message']) && !empty($body['message']))
		{
			$message .= ' - ' . $body['message'];
		}

		return $message;
	}
}