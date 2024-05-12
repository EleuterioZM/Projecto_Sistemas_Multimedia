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

class Drip extends Integration
{
	/**
	 * Create a new instance
	 * 
	 * @param string $key Your Drip API key
	 * @param string $account_id Your Drip Account ID
	 */
	public function __construct($options)
	{
		parent::__construct();

		if (!(isset($options['api']) && isset($options['account_id']))) {
			return;
		}

		$this->setKey($options['api']);
		$this->setEndpoint('http://api.getdrip.com/v2/' . $options['account_id']);
		$this->options->set('headers.Authorization', 'Basic ' . base64_encode($this->key));
	}

	/**
	 *  Subscribe user to Drip
	 *
	 *  API References:
	 * 	https://developer.drip.com/#create-or-update-a-subscriber
	 *
	 *  @param   string   $email         	  User's email address
	 *  @param   string   $campaign_id     	  The Campaign ID
	 *  @param   string   $name            	  The name of the Contact (Name can be also declared in Custom Fields)
	 *  @param   Object   $custom_fields  	  Custom Fields
	 *  @param   mixed    $tags            	  Tags for this contact (comma-separated). Example: 'tag1, tag2, etc'
	 *  @param   boolean  $update_existing	  Update existing user
	 *  @param   boolean  $double_optin  	  Send MailChimp confirmation email?
	 *
	 *  @return  void
	 */
	public function subscribe($email, $campaign_id, $name = null, $custom_fields = array(), $tags = '', $update_existing = true, $double_optin = false)
	{
		// Detect name
		$name = (is_null($name) || empty($name)) ? $this->getNameFromCustomFields($custom_fields) : explode(' ', $name, 2);

		// We use this boolean to see if the user has subscribed the campaign
		// This is used for the `update_existing` parameter
		$subscriber_exists = $this->subscriberIsInCampaign($email, $campaign_id);

		// Check if we need to update the user
		if ($update_existing == false && $subscriber_exists)
		{
			throw new \Exception(\JText::_('PLG_CONVERTFORMS_DRIP_SUBSCRIBER_ALREADY_EXISTS'), 1);
		}

		// Remove tags from custom fields
		$custom_fields_parse = $custom_fields;
		if (isset($custom_fields_parse['tags']))
		{
			unset($custom_fields_parse['tags']);
		}
		
		// Create or Update a Subscriber
		$data = [
			'subscribers' => [
				[
					'email' => $email,
					'first_name' => isset($name[0]) ? $name[0] : '',
					'last_name' => isset($name[1]) ? $name[1] : '',
					'address1' => $this->getCustomFieldValue('address1', $custom_fields),
					'address2' => $this->getCustomFieldValue('address2', $custom_fields),
					'city' => $this->getCustomFieldValue('city', $custom_fields),
					'state' => $this->getCustomFieldValue('state', $custom_fields),
					'zip' => $this->getCustomFieldValue('zip', $custom_fields),
					'country' => $this->getCustomFieldValue('country', $custom_fields),
					'phone' => $this->getCustomFieldValue('phone', $custom_fields),
					'custom_fields' => $custom_fields_parse,
					'tags' => $this->getTags($tags)
				]
			]
		];
		
		$this->post('subscribers', $data);

		// If we are updating a user, dont try re-assigning him to a campaign
		// If we are updating a user but he just subscribed, then assign him to a campaign
		if ($update_existing == false || $subscriber_exists == false)
		{
			// Assign the newly created subscriber to the campaign
			$this->assignSubscriberToCampaign($email, $campaign_id, $double_optin);
		}

		
		return true;
	}

	/**
	 * Assign a Subscriber to a Campaign
	 * 
	 *  https://developer.drip.com/?shell#subscribe-someone-to-a-campaign
	 * 
	 * @return  void
	 */
	private function assignSubscriberToCampaign($email, $campaign_id, $double_optin)
	{

		// Subscribe user to a campaign
		$campaignSubAPI = 'campaigns/' . $campaign_id . '/subscribers';

		$data = [
			'subscribers' => [
				[
					'email' => $email,
					'double_optin' => (bool) $double_optin
				]
			]
		];

		$this->post($campaignSubAPI, $data);
	}

	/**
	 * Returns an array of tags or an empty string if no tags provided
	 * 
	 * @return  mixed
	 */
	private function getTags($tags) {

		if (empty($tags))
		{
			return;
		}

		if (is_string($tags))
		{
			$tags = array_map('trim', explode(',', $tags));
		}

		return $tags;
	}
	
	/**
	 * Returns whether the subscriber is in a campaign
	 * 
	 * https://developer.drip.com/?shell#list-all-of-a-subscriber-39-s-campaign-subscriptions
	 * 
	 * @return  bool
	 */
	private function subscriberIsInCampaign($email, $campaign_id)
	{
		$found_campaign = false;

		$subscriber_id = $this->getSubscriberIdFromEmail($email);

		// Use does not exist in Drip
		if (empty($subscriber_id))
		{
			return false;
		}
		
		$subscriber_campaigns = $this->getSubscriberCampaigns($subscriber_id);

		foreach ($subscriber_campaigns as $c)
		{
			if ($c['campaign_id'] == $campaign_id)
			{
				$found_campaign = true;
				break;
			}
		}

		return $found_campaign;

	}

	/**
	 * Returns the ID of the subscriber from email
	 * 
	 * https://developer.drip.com/?shell#fetch-a-subscriber
	 * 
	 * @return  string
	 */
	private function getSubscriberIdFromEmail($email)
	{
		$data = $this->get('subscribers/' . $email);

		return isset($data['subscribers']) ? $data['subscribers'][0]['id'] : '';
	}

	/**
	 * Returns all subscriber's campaigns
	 * 
	 * https://developer.drip.com/?javascript#list-all-of-a-subscriber-39-s-campaign-subscriptions
	 * 
	 * @return  array
	 */
	private function getSubscriberCampaigns($subscriberId)
	{
		$data = $this->get('subscribers/' . $subscriberId . '/campaign_subscriptions');

		return isset($data['campaign_subscriptions']) ? $data['campaign_subscriptions'] : array();
	}

	/**
	 *  Returns all available Drip campaigns
	 *
	 *  https://developer.drip.com/?shell#list-all-campaigns
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

		if (!isset($data['campaigns']) || !is_array($data['campaigns']))
		{
			return;
		}

		$campaigns = [];

		foreach ($data['campaigns'] as $key => $campaign)
		{
			$campaigns[] = array(
				'id'   => $campaign['id'],
				'name' => $campaign['name']
			);
		}

		return $campaigns;
	}

	/**
	 * Search for First Name and Last Name in Custom Fields and return an array with both values.
	 *
	 * @param	array	$custom_fields	The Custom Fields array passed by the user.
	 *
	 * @return	array
	 */
	private function getNameFromCustomFields($custom_fields)
	{
		return [
			(string) $this->getCustomFieldValue(['first_name', 'First Name'], $custom_fields),
			(string) $this->getCustomFieldValue(['last_name', 'Last Name'], $custom_fields)
		];
	}
	
	/**
	 *  Get the last error returned by either the network transport, or by the API.
	 *
	 *  @return  string
	 */
	public function getLastError()
	{
		$body = $this->last_response->body;

		$messages = '';

		if (isset($body['errors']))
		{
			foreach ($body['errors'] as $error)
			{
				$messages .= ' - ' . $error['message'];
			}
		}

		return $messages;
	}
}